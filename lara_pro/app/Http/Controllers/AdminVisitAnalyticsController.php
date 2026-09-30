<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use App\Support\VisitAnalytics;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminVisitAnalyticsController extends Controller
{
    public function index(Request $request, VisitAnalytics $analytics): View
    {
        $filters = $this->filters($request);
        $trackingReady = Schema::hasTable('page_visits');

        return view('admin.visits.index', [
            'filters' => $filters,
            'trackingReady' => $trackingReady,
            'report' => $trackingReady ? $analytics->report($filters) : null,
            'pageGroups' => $trackingReady ? PageVisit::query()->whereNotNull('page_group')->where('page_group', '<>', '')->distinct()->orderBy('page_group')->pluck('page_group') : collect(),
            'analytics' => $analytics,
            'generatedAt' => now(),
        ]);
    }

    public function export(Request $request, VisitAnalytics $analytics): StreamedResponse
    {
        $filters = $this->filters($request);
        abort_unless(Schema::hasTable('page_visits'), 503, 'Visit tracking is not available yet.');
        $query = $analytics->withDimensions($analytics->query($filters));

        return response()->streamDownload(function () use ($query, $analytics) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Recorded at ('.config('app.timezone').')', 'Page', 'Page group', 'Visitor', 'Device', 'Browser', 'Operating system', 'Referrer host', 'Country'], ',', '"', '');

            foreach ($query->lazyById(1000) as $visit) {
                $row = [$visit->created_at->format('Y-m-d H:i:s'), $visit->path, $visit->page_group,
                    $analytics->visitor($visit->session_id), $visit->device, $visit->browser, $visit->os, $analytics->source($visit->referrer), $visit->country_name ?: 'Unknown'];
                // A downloaded page name must never become a spreadsheet formula.
                $row = array_map(fn ($value) => preg_match('/^[\s]*[=+@-]|^[\t\r\n]/u', (string) $value) ? "'".$value : (string) $value, $row);
                fputcsv($file, $row, ',', '"', '');
            }

            fclose($file);
        }, 'website-visits-'.$filters['start'].'-to-'.$filters['end'].'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function filters(Request $request): array
    {
        $values = $request->validate([
            'period' => ['nullable', Rule::in([7, 30, 90, 365])],
            'start' => ['nullable', 'required_with:end', 'date_format:Y-m-d', 'before_or_equal:today'],
            'end' => ['nullable', 'required_with:start', 'date_format:Y-m-d', 'after_or_equal:start', 'before_or_equal:today'],
            'path' => ['nullable', 'string', 'max:255'],
            'page_group' => ['nullable', 'string', 'max:50'],
            'device' => ['nullable', Rule::in(VisitAnalytics::DEVICES)],
            'traffic' => ['nullable', Rule::in(['all', 'people', 'bots'])],
        ]);
        $end = filled($values['end'] ?? null) ? Carbon::parse($values['end']) : today();
        $start = filled($values['start'] ?? null) ? Carbon::parse($values['start']) : $end->copy()->subDays((int) ($values['period'] ?? 30) - 1);

        if ($start->diffInDays($end) > 365) {
            throw ValidationException::withMessages(['start' => 'Choose a date range of 366 days or fewer.']);
        }

        return [
            'start' => $start->toDateString(), 'end' => $end->toDateString(),
            'path' => $values['path'] ?? '', 'page_group' => $values['page_group'] ?? '',
            'device' => $values['device'] ?? '', 'traffic' => $values['traffic'] ?? 'all',
        ];
    }
}
