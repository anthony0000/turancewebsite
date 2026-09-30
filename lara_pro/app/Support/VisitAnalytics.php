<?php

namespace App\Support;

use App\Models\PageVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class VisitAnalytics
{
    public const DEVICES = ['Desktop', 'Mobile', 'Tablet', 'Bot', 'Unknown'];

    // These are estimates from the recorded user agent, not fingerprinting.
    private function botExpression(): string
    {
        $agent = "LOWER(COALESCE(user_agent, ''))";

        return implode(' OR ', array_map(
            fn (string $token) => "$agent LIKE '%$token%'",
            ['bot', 'spider', 'crawler', 'headless', 'slurp', 'facebookexternalhit', 'preview', 'curl/', 'wget/'],
        ));
    }

    public function dimension(string $name): string
    {
        $ua = "LOWER(COALESCE(user_agent, ''))";
        $bot = $this->botExpression();

        return match ($name) {
            'device' => "CASE WHEN $bot THEN 'Bot'
                WHEN $ua LIKE '%ipad%' OR $ua LIKE '%tablet%' OR ($ua LIKE '%android%' AND $ua NOT LIKE '%mobile%') THEN 'Tablet'
                WHEN $ua LIKE '%mobi%' OR $ua LIKE '%iphone%' OR $ua LIKE '%ipod%' THEN 'Mobile'
                WHEN $ua LIKE '%windows%' OR $ua LIKE '%macintosh%' OR $ua LIKE '%linux%' OR $ua LIKE '%cros%' THEN 'Desktop'
                ELSE 'Unknown' END",
            'browser' => "CASE WHEN $bot THEN 'Bot / automation'
                WHEN $ua LIKE '%edg/%' OR $ua LIKE '%edga/%' OR $ua LIKE '%edgios/%' THEN 'Edge'
                WHEN $ua LIKE '%opr/%' OR $ua LIKE '%opera%' THEN 'Opera'
                WHEN $ua LIKE '%samsungbrowser%' THEN 'Samsung Internet'
                WHEN $ua LIKE '%firefox%' OR $ua LIKE '%fxios%' THEN 'Firefox'
                WHEN $ua LIKE '%chrome%' OR $ua LIKE '%crios%' THEN 'Chrome'
                WHEN $ua LIKE '%safari%' THEN 'Safari' ELSE 'Other / unknown' END",
            'os' => "CASE WHEN $bot THEN 'Bot / automation'
                WHEN $ua LIKE '%android%' THEN 'Android'
                WHEN $ua LIKE '%iphone%' OR $ua LIKE '%ipad%' OR $ua LIKE '%ipod%' THEN 'iOS / iPadOS'
                WHEN $ua LIKE '%windows%' THEN 'Windows'
                WHEN $ua LIKE '%cros%' THEN 'ChromeOS'
                WHEN $ua LIKE '%macintosh%' OR $ua LIKE '%mac os%' THEN 'macOS'
                WHEN $ua LIKE '%linux%' THEN 'Linux' ELSE 'Other / unknown' END",
        };
    }

    public function query(array $filters, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = PageVisit::query()->whereBetween('created_at', [
            $start ?? Carbon::parse($filters['start'])->startOfDay(),
            $end ?? Carbon::parse($filters['end'])->endOfDay(),
        ]);

        foreach (['path', 'page_group'] as $field) {
            if ($filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        if ($filters['device'] !== '') {
            $query->whereRaw('('.$this->dimension('device').') = ?', [$filters['device']]);
        }
        if ($filters['traffic'] !== 'all') {
            $query->whereRaw(($filters['traffic'] === 'people' ? 'NOT ' : '').'('.$this->botExpression().')');
        }

        return $query;
    }

    private function summary(Builder $query): array
    {
        $row = (clone $query)->selectRaw("COUNT(*) AS views,
            COUNT(DISTINCT NULLIF(session_id, '')) AS visitors,
            COUNT(DISTINCT path) AS pages,
            SUM(CASE WHEN session_id IS NOT NULL AND session_id <> '' THEN 1 ELSE 0 END) AS identified_views")
            ->first();

        return [
            'views' => (int) $row->views,
            'visitors' => (int) $row->visitors,
            'pages' => (int) $row->pages,
            'identified_views' => (int) $row->identified_views,
            'views_per_visitor' => $row->visitors ? round($row->identified_views / $row->visitors, 2) : 0,
        ];
    }

    private function daily(Builder $query): array
    {
        $date = DB::connection()->getDriverName() === 'sqlsrv' ? 'CONVERT(date, created_at)' : 'DATE(created_at)';

        return (clone $query)->selectRaw("$date AS day, COUNT(*) AS total")
            ->groupByRaw($date)->orderByRaw($date)->pluck('total', 'day')->all();
    }

    public function report(array $filters): array
    {
        $start = Carbon::parse($filters['start'])->startOfDay();
        $end = Carbon::parse($filters['end'])->endOfDay();
        $days = (int) $start->diffInDays($end->copy()->startOfDay()) + 1;
        $previousEnd = $start->copy()->subDay()->endOfDay();
        $previousStart = $start->copy()->subDays($days);
        $query = $this->query($filters);
        $previousQuery = $this->query($filters, $previousStart, $previousEnd);
        $summary = $this->summary($query);
        $previous = $this->summary($previousQuery);
        $daily = $this->daily($query);
        $previousDaily = $this->daily($previousQuery);
        $trend = [];
        $weekdays = array_fill(0, 7, 0);

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $start->copy()->addDays($offset);
            $count = (int) ($daily[$date->toDateString()] ?? 0);
            $trend[] = [
                'date' => $date->toDateString(),
                'label' => $date->format('M j'),
                'views' => $count,
                'previous' => (int) ($previousDaily[$previousStart->copy()->addDays($offset)->toDateString()] ?? 0),
            ];
            $weekdays[$date->dayOfWeek] += $count;
        }

        $hour = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)",
            'pgsql' => 'EXTRACT(HOUR FROM created_at)',
            'sqlsrv' => 'DATEPART(hour, created_at)',
            default => 'HOUR(created_at)',
        };
        $hourCounts = (clone $query)->selectRaw("$hour AS hour, COUNT(*) AS total")
            ->groupByRaw($hour)->pluck('total', 'hour');
        $hours = collect(range(0, 23))->map(fn ($hour) => [
            'label' => sprintf('%02d:00', $hour), 'count' => (int) ($hourCounts[$hour] ?? 0),
        ])->all();

        $breakdowns = [];
        foreach (['device', 'browser', 'os'] as $dimension) {
            $expression = $this->dimension($dimension);
            $breakdowns[$dimension] = (clone $query)->selectRaw("$expression AS label, COUNT(*) AS total")
                ->groupByRaw($expression)->orderByDesc('total')->get()
                ->map(fn ($row) => ['label' => $row->label, 'count' => (int) $row->total])->all();
        }

        // Group in SQL and stream the groups: do not load visit records to build charts.
        // Referrer paths and query parameters are intentionally excluded from display/export.
        $sourceCounts = [];
        foreach ((clone $query)->select('referrer')->selectRaw('COUNT(*) AS total')->groupBy('referrer')->cursor() as $row) {
            $source = $this->source($row->referrer);
            $sourceCounts[$source] = ($sourceCounts[$source] ?? 0) + (int) $row->total;
        }
        arsort($sourceCounts);
        $sources = collect($sourceCounts)->map(fn ($count, $label) => compact('label', 'count'))->values()->take(15)->all();

        $returning = (clone $query)->whereNotNull('session_id')->where('session_id', '<>', '')
            ->whereExists(function ($history) use ($start) {
                $history->selectRaw('1')->from('page_visits as history')
                    ->whereColumn('history.session_id', 'page_visits.session_id')
                    ->where('history.created_at', '<', $start);
            })->distinct()->count('session_id');

        $pages = (clone $query)->select('path')->selectRaw("COUNT(*) AS views, COUNT(DISTINCT NULLIF(session_id, '')) AS visitors, MAX(created_at) AS last_seen")
            ->groupBy('path')->orderByDesc('views')->orderBy('path')->paginate(15, ['*'], 'pages_page')->withQueryString();
        $groups = (clone $query)->selectRaw("COALESCE(NULLIF(page_group, ''), 'Uncategorised') AS label, COUNT(*) AS total")
            ->groupByRaw("COALESCE(NULLIF(page_group, ''), 'Uncategorised')")->orderByDesc('total')->get()
            ->map(fn ($row) => ['label' => $row->label, 'count' => (int) $row->total])->all();
        $recent = $this->withDimensions(clone $query)->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(25, ['*'], 'visits_page')->withQueryString();

        return compact('summary', 'previous', 'previousStart', 'previousEnd', 'trend', 'hours', 'weekdays', 'breakdowns', 'sources', 'groups', 'pages', 'recent', 'returning') + [
            'newVisitors' => $summary['visitors'] - $returning,
            'sourceCount' => count($sourceCounts),
            'peakDay' => collect($trend)->sortByDesc('views')->first(),
            'peakHour' => collect($hours)->sortByDesc('count')->first(),
        ];
    }

    public function withDimensions(Builder $query): Builder
    {
        return $query->select(['id', 'created_at', 'path', 'page_group', 'session_id', 'referrer'])
            ->selectRaw($this->dimension('device').' AS device')
            ->selectRaw($this->dimension('browser').' AS browser')
            ->selectRaw($this->dimension('os').' AS os');
    }

    public function source(?string $referrer): string
    {
        if (! filled($referrer)) {
            return 'Direct / unavailable';
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? strtolower($host) : 'Unknown referrer';
    }

    public function visitor(?string $session): string
    {
        return filled($session)
            ? 'Visitor '.strtoupper(substr(hash_hmac('sha256', $session, (string) config('app.key')), 0, 10))
            : 'Unidentified';
    }
}
