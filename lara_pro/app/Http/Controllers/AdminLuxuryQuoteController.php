<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\LuxuryQuote;
use App\Models\PageVisit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminLuxuryQuoteController extends Controller
{
    private const MIN_INVESTMENT_AMOUNT = 1;

    private const MAX_INVESTMENT_AMOUNT = 12000;

    private const DEFAULT_EXCHANGE_RATE = 1370;

    public function index(): View
    {
        return $this->renderDashboardSection('overview');
    }

    public function activity(): View
    {
        return $this->renderDashboardSection('activity');
    }

    public function insights(): View
    {
        return $this->renderDashboardSection('insights');
    }

    public function create(): View
    {
        return $this->renderDashboardSection('builder');
    }

    public function archive(): View
    {
        return $this->renderDashboardSection('archive');
    }

    private function renderDashboardSection(string $section): View
    {
        return view('admin.quotes.index', array_merge(
            $this->dashboardViewData(),
            ['adminSection' => $section],
        ));
    }

    private function dashboardViewData(): array
    {
        $templates = config('luxury-quotes.templates', []);
        $categories = config('luxury-quotes.categories', []);
        $defaults = config('luxury-quotes.defaults', []);
        $brand = config('luxury-quotes.brand', []);

        $quoteTableReady = $this->tableExists('luxury_quotes');
        $messageTableReady = $this->tableExists('contact_messages');
        $visitTableReady = $this->tableExists('page_visits');

        $quotes = $quoteTableReady
            ? LuxuryQuote::query()->latest()->limit(12)->get()
            : collect();

        $quoteCount = $quoteTableReady ? LuxuryQuote::query()->count() : 0;
        $contactCount = $messageTableReady ? ContactMessage::query()->count() : 0;
        $visitCount = $visitTableReady ? PageVisit::query()->count() : 0;

        $totalPipeline = $quoteTableReady
            ? (float) LuxuryQuote::query()->sum('investment_amount')
            : 0.0;

        $averageQuoteValue = $quoteCount > 0
            ? (float) LuxuryQuote::query()->avg('investment_amount')
            : 0.0;

        $currentMonthStart = now()->copy()->startOfMonth();
        $currentMonthEnd = now()->copy()->endOfDay();
        $previousMonthStart = now()->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = now()->copy()->subMonthNoOverflow()->endOfMonth();
        $last30Start = now()->copy()->subDays(29)->startOfDay();
        $last30End = now()->copy()->endOfDay();
        $previous30Start = $last30Start->copy()->subDays(30);
        $previous30End = $last30Start->copy()->subSecond();

        $quotesThisMonth = $quoteTableReady
            ? $this->countWithinRange(LuxuryQuote::class, $currentMonthStart, $currentMonthEnd)
            : 0;
        $messagesThisMonth = $messageTableReady
            ? $this->countWithinRange(ContactMessage::class, $currentMonthStart, $currentMonthEnd)
            : 0;
        $visitsThisMonth = $visitTableReady
            ? $this->countWithinRange(PageVisit::class, $currentMonthStart, $currentMonthEnd)
            : 0;

        $quotesLast30 = $quoteTableReady
            ? $this->countWithinRange(LuxuryQuote::class, $last30Start, $last30End)
            : 0;
        $messagesLast30 = $messageTableReady
            ? $this->countWithinRange(ContactMessage::class, $last30Start, $last30End)
            : 0;
        $visitsLast30 = $visitTableReady
            ? $this->countWithinRange(PageVisit::class, $last30Start, $last30End)
            : 0;

        $quotesPrevious30 = $quoteTableReady
            ? $this->countWithinRange(LuxuryQuote::class, $previous30Start, $previous30End)
            : 0;
        $messagesPrevious30 = $messageTableReady
            ? $this->countWithinRange(ContactMessage::class, $previous30Start, $previous30End)
            : 0;
        $visitsPrevious30 = $visitTableReady
            ? $this->countWithinRange(PageVisit::class, $previous30Start, $previous30End)
            : 0;

        $currentPipeline = $quoteTableReady
            ? $this->sumWithinRange(LuxuryQuote::class, 'investment_amount', $currentMonthStart, $currentMonthEnd)
            : 0.0;
        $previousPipeline = $quoteTableReady
            ? $this->sumWithinRange(LuxuryQuote::class, 'investment_amount', $previousMonthStart, $previousMonthEnd)
            : 0.0;

        $recentMessages = $messageTableReady
            ? ContactMessage::query()->latest()->limit(5)->get()
            : collect();

        $dailyOverview = $this->buildDailyOverview(
            quoteTableReady: $quoteTableReady,
            messageTableReady: $messageTableReady,
            visitTableReady: $visitTableReady,
        );

        $templateBreakdown = $this->buildQuoteBreakdown(
            field: 'template',
            templateMap: $templates,
            limit: 5,
            quoteTableReady: $quoteTableReady,
        );

        $categoryBreakdown = $this->buildQuoteBreakdown(
            field: 'project_category',
            templateMap: [],
            limit: 6,
            quoteTableReady: $quoteTableReady,
        );

        $topPages = $this->buildTopPages(6, $visitTableReady);
        $monthlyPipeline = $this->buildMonthlyPipeline(6, $quoteTableReady);

        $topTemplate = $templateBreakdown[0]['label'] ?? 'No invoice activity yet';
        $topTemplateCount = $templateBreakdown[0]['count'] ?? 0;
        $topCategory = $categoryBreakdown[0]['label'] ?? 'No category data yet';
        $topCategoryCount = $categoryBreakdown[0]['count'] ?? 0;
        $peakTrafficDay = $dailyOverview['peak'];

        $quoteConversionRate = $visitsLast30 > 0
            ? round(($quotesLast30 / $visitsLast30) * 100, 1)
            : null;

        $leadCaptureRate = $visitsLast30 > 0
            ? round(($messagesLast30 / $visitsLast30) * 100, 1)
            : null;

        $kpiCards = [
            $this->buildKpiCard(
                'Total Visits',
                $visitTableReady ? number_format($visitCount) : 'Setup',
                $visitTableReady
                    ? 'Public page views recorded across the landing, service, and contact pages.'
                    : 'Run the latest migration to begin collecting visit analytics.',
                $visitTableReady
                    ? $this->formatTrend($visitsLast30, $visitsPrevious30, 'vs previous 30 days')
                    : $this->neutralTrend('Awaiting migration', 'Tracking status'),
                'traffic',
            ),
            $this->buildKpiCard(
                'Invoices Generated',
                number_format($quoteCount),
                'All saved invoices remain available for preview and PDF export.',
                $this->formatTrend($quotesLast30, $quotesPrevious30, 'vs previous 30 days'),
                'quotes',
            ),
            $this->buildKpiCard(
                'Contact Leads',
                number_format($contactCount),
                'New enquiries coming through the public contact channel.',
                $messageTableReady
                    ? $this->formatTrend($messagesLast30, $messagesPrevious30, 'vs previous 30 days')
                    : $this->neutralTrend('No storage', 'Contact table unavailable'),
                'leads',
            ),
            $this->buildKpiCard(
                'Pipeline Value',
                $this->formatCompactCurrency($totalPipeline),
                'Combined commercial value of every invoice currently stored.',
                $this->formatTrend($currentPipeline, $previousPipeline, 'vs previous month'),
                'pipeline',
            ),
        ];

        $dashboardHighlights = [
            [
                'label' => 'Leading Template',
                'value' => $topTemplate,
                'meta' => $topTemplateCount > 0
                    ? number_format($topTemplateCount).' invoices created'
                    : 'No saved invoices yet',
            ],
            [
                'label' => 'Strongest Category',
                'value' => $topCategory,
                'meta' => $topCategoryCount > 0
                    ? number_format($topCategoryCount).' invoice requests'
                    : 'No category activity yet',
            ],
            [
                'label' => 'Invoice Conversion',
                'value' => $quoteConversionRate !== null ? $quoteConversionRate.'%' : 'No visit data',
                'meta' => 'Invoices generated over the last 30 days versus tracked visits',
            ],
            [
                'label' => 'Lead Capture',
                'value' => $leadCaptureRate !== null ? $leadCaptureRate.'%' : 'No visit data',
                'meta' => 'Contact enquiries over the last 30 days versus tracked visits',
            ],
        ];

        $sidebarStats = [
            [
                'label' => 'Average Invoice Value',
                'value' => $quoteCount > 0 ? '$'.number_format($averageQuoteValue, 0) : '$0',
                'meta' => 'Current average across every saved invoice',
            ],
            [
                'label' => 'This Month',
                'value' => number_format($quotesThisMonth).' invoices / '.number_format($messagesThisMonth).' leads',
                'meta' => number_format($visitsThisMonth).' visits recorded this month',
            ],
            [
                'label' => 'Peak Traffic Day',
                'value' => $peakTrafficDay !== null ? $peakTrafficDay['full_label'] : 'No traffic yet',
                'meta' => $peakTrafficDay !== null
                    ? number_format($peakTrafficDay['visits']).' visits in the last 14 days'
                    : 'Visits will appear here once tracking starts collecting data',
            ],
        ];

        return [
            'templates' => $templates,
            'categories' => $categories,
            'defaults' => $defaults,
            'brand' => $brand,
            'quotes' => $quotes,
            'quoteCount' => $quoteCount,
            'contactCount' => $contactCount,
            'visitCount' => $visitCount,
            'visitTrackingReady' => $visitTableReady,
            'quotesThisMonth' => $quotesThisMonth,
            'messagesThisMonth' => $messagesThisMonth,
            'visitsThisMonth' => $visitsThisMonth,
            'kpiCards' => $kpiCards,
            'dailyOverview' => $dailyOverview,
            'templateBreakdown' => $templateBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'topPages' => $topPages,
            'monthlyPipeline' => $monthlyPipeline,
            'recentMessages' => $recentMessages,
            'dashboardHighlights' => $dashboardHighlights,
            'sidebarStats' => $sidebarStats,
            'priceBounds' => [
                'min' => self::MIN_INVESTMENT_AMOUNT,
                'max' => self::MAX_INVESTMENT_AMOUNT,
            ],
            'defaultExchangeRate' => self::DEFAULT_EXCHANGE_RATE,
            'defaultLineItems' => $this->defaultLineItemsForForm($defaults['scope_items'] ?? []),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateInvoiceRequest($request);

        $quote = LuxuryQuote::query()->create(array_merge([
            'quote_number' => $this->generateQuoteNumber(),
        ], $this->invoiceAttributes($validated)));

        return redirect()
            ->route('admin.quotes.show', $quote)
            ->with('status', "Invoice {$quote->quote_number} created.");
    }

    public function edit(LuxuryQuote $luxuryQuote): View
    {
        return view('admin.quotes.edit', [
            'quote' => $luxuryQuote,
            'templates' => config('luxury-quotes.templates', []),
            'categories' => config('luxury-quotes.categories', []),
            'brand' => config('luxury-quotes.brand', []),
            'priceBounds' => [
                'min' => self::MIN_INVESTMENT_AMOUNT,
                'max' => self::MAX_INVESTMENT_AMOUNT,
            ],
            'defaultExchangeRate' => self::DEFAULT_EXCHANGE_RATE,
            'lineItems' => $this->lineItemsForForm($luxuryQuote),
        ]);
    }

    public function update(Request $request, LuxuryQuote $luxuryQuote): RedirectResponse
    {
        $validated = $this->validateInvoiceRequest($request, requireCurrentValidity: false);

        $luxuryQuote->update($this->invoiceAttributes($validated));

        return redirect()
            ->route('admin.quotes.show', $luxuryQuote)
            ->with('status', "Invoice {$luxuryQuote->quote_number} updated. Download the PDF again to regenerate it with the latest details.");
    }

    public function show(LuxuryQuote $luxuryQuote): View
    {
        return view('admin.quotes.show', [
            'quote' => $luxuryQuote,
            'template' => $this->resolveTemplate($luxuryQuote->template),
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function downloadPdf(LuxuryQuote $luxuryQuote): Response
    {
        $pdf = Pdf::loadView('admin.quotes.pdf', [
            'quote' => $luxuryQuote,
            'template' => $this->resolveTemplate($luxuryQuote->template),
            'brand' => config('luxury-quotes.brand', []),
        ])->setPaper('a4');

        $fileName = Str::slug($luxuryQuote->company_name.' '.$luxuryQuote->quote_number).'.pdf';

        return $pdf->download($fileName);
    }

    public function downloadMouPdf(LuxuryQuote $luxuryQuote): Response
    {
        $mouNumber = $this->mouNumberFor($luxuryQuote);

        $pdf = Pdf::loadView('admin.quotes.mou-pdf', [
            'quote' => $luxuryQuote,
            'template' => $this->resolveTemplate($luxuryQuote->template),
            'brand' => config('luxury-quotes.brand', []),
            'mouNumber' => $mouNumber,
        ])->setPaper('a4');

        $fileName = Str::slug($luxuryQuote->company_name.' '.$mouNumber).'.pdf';

        return $pdf->download($fileName);
    }

    private function resolveTemplate(string $template): array
    {
        $templates = config('luxury-quotes.templates', []);

        return $templates[$template] ?? (reset($templates) ?: []);
    }

    private function mouNumberFor(LuxuryQuote $quote): string
    {
        $mouNumber = str_replace('-INV-', '-MOU-', $quote->quote_number);

        return $mouNumber !== $quote->quote_number
            ? $mouNumber
            : 'MOU-'.$quote->quote_number;
    }

    private function validateInvoiceRequest(Request $request, bool $requireCurrentValidity = true): array
    {
        $request->merge([
            'exchange_rate' => $request->filled('exchange_rate')
                ? $request->input('exchange_rate')
                : self::DEFAULT_EXCHANGE_RATE,
        ]);

        $validUntilRules = ['required', 'date'];

        if ($requireCurrentValidity) {
            $validUntilRules[] = 'after_or_equal:today';
        }

        $validated = $request->validate([
            'template' => ['required', Rule::in(array_keys(config('luxury-quotes.templates', [])))],
            'project_category' => ['required', 'string', Rule::in(config('luxury-quotes.categories', []))],
            'company_name' => ['required', 'string', 'max:255'],
            'company_industry' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_title' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:80'],
            'project_title' => ['required', 'string', 'max:255'],
            'executive_summary' => ['required', 'string', 'min:30', 'max:5000'],
            'exchange_rate' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'timeline' => ['required', 'string', 'max:255'],
            'valid_until' => $validUntilRules,
            'line_items' => ['required', 'array', 'min:1', 'max:30'],
            'line_items.*.description' => ['required', 'string', 'max:500'],
            'line_items.*.amount' => ['required', 'numeric', 'min:0.01', 'max:'.self::MAX_INVESTMENT_AMOUNT],
            'outcomes' => ['nullable', 'string', 'max:5000'],
            'milestones' => ['nullable', 'string', 'max:5000'],
            'optional_addons' => ['nullable', 'string', 'max:5000'],
            'intro_message' => ['nullable', 'string', 'max:2000'],
            'closing_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['line_items'] = $this->normalizeLineItems($validated['line_items']);
        $validated['investment_amount'] = $this->sumLineItems($validated['line_items']);

        if ($validated['investment_amount'] < self::MIN_INVESTMENT_AMOUNT) {
            throw ValidationException::withMessages([
                'line_items' => 'The invoice line item total must be at least $'.number_format(self::MIN_INVESTMENT_AMOUNT).'.',
            ]);
        }

        if ($validated['investment_amount'] > self::MAX_INVESTMENT_AMOUNT) {
            throw ValidationException::withMessages([
                'line_items' => 'The invoice line item total may not be greater than $'.number_format(self::MAX_INVESTMENT_AMOUNT).'.',
            ]);
        }

        return $validated;
    }

    private function invoiceAttributes(array $validated): array
    {
        return [
            'template' => $validated['template'],
            'project_category' => $validated['project_category'],
            'company_name' => $validated['company_name'],
            'company_industry' => $validated['company_industry'] ?? null,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'recipient_title' => $validated['recipient_title'] ?? null,
            'recipient_email' => $validated['recipient_email'] ?? null,
            'recipient_phone' => $validated['recipient_phone'] ?? null,
            'project_title' => $validated['project_title'],
            'executive_summary' => $this->sanitizeRichText($validated['executive_summary']),
            'investment_amount' => $validated['investment_amount'],
            'exchange_rate' => round((float) $validated['exchange_rate'], 4),
            'timeline' => $validated['timeline'],
            'valid_until' => Carbon::parse($validated['valid_until']),
            'scope_items' => array_column($validated['line_items'], 'description'),
            'line_items' => $validated['line_items'],
            'outcomes' => $this->normalizeList(
                $validated['outcomes'] ?? '',
                config('luxury-quotes.defaults.outcomes', [])
            ),
            'milestones' => $this->normalizeList(
                $validated['milestones'] ?? '',
                config('luxury-quotes.defaults.milestones', [])
            ),
            'optional_addons' => $this->normalizeList(
                $validated['optional_addons'] ?? '',
                config('luxury-quotes.defaults.optional_addons', [])
            ),
            'intro_message' => filled($validated['intro_message'] ?? null)
                ? $this->sanitizeRichText($validated['intro_message'])
                : null,
            'closing_note' => filled($validated['closing_note'] ?? null)
                ? $this->sanitizeRichText($validated['closing_note'])
                : null,
        ];
    }

    private function normalizeLineItems(array $lineItems): array
    {
        return collect($lineItems)
            ->map(function (array $item): array {
                return [
                    'description' => trim((string) ($item['description'] ?? '')),
                    'amount' => round((float) ($item['amount'] ?? 0), 2),
                ];
            })
            ->filter(fn (array $item): bool => $item['description'] !== '' && $item['amount'] > 0)
            ->values()
            ->all();
    }

    private function sumLineItems(array $lineItems): float
    {
        return round((float) collect($lineItems)->sum('amount'), 2);
    }

    private function defaultLineItemsForForm(array $scopeItems): array
    {
        $items = collect($scopeItems)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): array => [
                'description' => (string) $item,
                'amount' => '',
            ])
            ->values()
            ->all();

        return $items !== [] ? $items : [
            [
                'description' => '',
                'amount' => '',
            ],
        ];
    }

    private function lineItemsForForm(LuxuryQuote $quote): array
    {
        $storedLineItems = collect($quote->line_items ?? [])
            ->filter(fn ($item): bool => is_array($item) && filled($item['description'] ?? null))
            ->map(fn (array $item): array => [
                'description' => (string) $item['description'],
                'amount' => isset($item['amount']) ? (float) $item['amount'] : '',
            ])
            ->values()
            ->all();

        if ($storedLineItems !== []) {
            return $storedLineItems;
        }

        $scopeItems = collect($quote->scope_items ?? [])
            ->filter(fn ($item): bool => filled($item))
            ->values();

        if ($scopeItems->isEmpty()) {
            return $this->defaultLineItemsForForm([]);
        }

        $lineCount = $scopeItems->count();
        $investmentAmount = (float) $quote->investment_amount;
        $allocatedAmount = 0.0;
        $baseAmount = floor(($investmentAmount / $lineCount) * 100) / 100;

        return $scopeItems
            ->map(function ($item, int $index) use ($lineCount, $investmentAmount, &$allocatedAmount, $baseAmount): array {
                $amount = $index === $lineCount - 1
                    ? round($investmentAmount - $allocatedAmount, 2)
                    : $baseAmount;

                $allocatedAmount += $amount;

                return [
                    'description' => (string) $item,
                    'amount' => $amount,
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeList(?string $value, array $fallback = []): array
    {
        $html = $this->sanitizeRichText($value);

        if ($html !== strip_tags($html)) {
            $html = preg_replace('/<li>(.*?)<\/li>/is', "\n$1\n", $html) ?? $html;
            $html = preg_replace('/<\/?(ul|ol)>/i', '', $html) ?? $html;
            $html = preg_replace('/<(p|div)>/i', '', $html) ?? $html;
            $html = preg_replace('/<\/(p|div)>/i', "\n", $html) ?? $html;
            $html = preg_replace('/<br\s*\/?>/i', "\n", $html) ?? $html;
        }

        $items = collect(preg_split('/\r\n|\r|\n/', trim($html)))
            ->map(function (string $item): string {
                $item = trim((string) preg_replace('/^[-*\d\.\)\s]+/', '', $item));

                return $this->sanitizeRichText($item);
            })
            ->filter(fn (string $item): bool => trim(strip_tags($item)) !== '')
            ->values()
            ->all();

        return $items !== [] ? $items : array_values($fallback);
    }

    private function sanitizeRichText(?string $value): string
    {
        $html = trim((string) $value);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $html) ?? '';
        $html = strip_tags($html, '<p><div><br><ul><ol><li><strong><b><em><i><u>');
        $html = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $html) ?? $html;
        $html = str_ireplace(
            ['<b>', '</b>', '<i>', '</i>', '<br/>', '<br />'],
            ['<strong>', '</strong>', '<em>', '</em>', '<br>', '<br>'],
            $html
        );

        return trim($html);
    }

    private function generateQuoteNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = LuxuryQuote::query()
            ->whereDate('created_at', today())
            ->count() + 1;

        do {
            $quoteNumber = sprintf('TT-INV-%s-%03d', $date, $sequence);
            $sequence++;
        } while (LuxuryQuote::query()->where('quote_number', $quoteNumber)->exists());

        return $quoteNumber;
    }

    private function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function countWithinRange(string $modelClass, Carbon $start, Carbon $end): int
    {
        return $modelClass::query()
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function sumWithinRange(string $modelClass, string $column, Carbon $start, Carbon $end): float
    {
        return (float) $modelClass::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum($column);
    }

    private function buildKpiCard(
        string $label,
        string $value,
        string $hint,
        array $trend,
        string $tone
    ): array {
        return compact('label', 'value', 'hint', 'trend', 'tone');
    }

    private function neutralTrend(string $label, string $context): array
    {
        return [
            'label' => $label,
            'context' => $context,
            'direction' => 'flat',
        ];
    }

    private function formatTrend(float|int $current, float|int $previous, string $context): array
    {
        if ($current == 0 && $previous == 0) {
            return $this->neutralTrend('No change', $context);
        }

        if ($previous == 0) {
            return [
                'label' => 'New activity',
                'context' => $context,
                'direction' => 'up',
            ];
        }

        $delta = (($current - $previous) / $previous) * 100;

        if (abs($delta) < 0.5) {
            return $this->neutralTrend('Flat', $context);
        }

        return [
            'label' => sprintf('%s%s%%', $delta > 0 ? '+' : '', number_format($delta, abs($delta) < 10 ? 1 : 0)),
            'context' => $context,
            'direction' => $delta > 0 ? 'up' : 'down',
        ];
    }

    private function buildDailyOverview(
        bool $quoteTableReady,
        bool $messageTableReady,
        bool $visitTableReady,
        int $days = 14
    ): array {
        $start = now()->copy()->subDays($days - 1)->startOfDay();
        $end = now()->copy()->endOfDay();

        $visits = $visitTableReady
            ? $this->countMapByDay(PageVisit::class, $start, $end)
            : collect();
        $quotes = $quoteTableReady
            ? $this->countMapByDay(LuxuryQuote::class, $start, $end)
            : collect();
        $messages = $messageTableReady
            ? $this->countMapByDay(ContactMessage::class, $start, $end)
            : collect();

        $daysCollection = collect(range(0, $days - 1))->map(function (int $offset) use ($start, $visits, $quotes, $messages): array {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('D'),
                'full_label' => $date->format('M d'),
                'visits' => (int) ($visits[$key] ?? 0),
                'quotes' => (int) ($quotes[$key] ?? 0),
                'messages' => (int) ($messages[$key] ?? 0),
            ];
        });

        $maxValue = max(
            1,
            (int) $daysCollection->max('visits'),
            (int) $daysCollection->max('quotes'),
            (int) $daysCollection->max('messages'),
        );

        $peakTrafficDay = $daysCollection->sum('visits') > 0
            ? $daysCollection->sortByDesc('visits')->first()
            : null;

        return [
            'days' => $daysCollection->values()->all(),
            'totals' => [
                'visits' => $daysCollection->sum('visits'),
                'quotes' => $daysCollection->sum('quotes'),
                'messages' => $daysCollection->sum('messages'),
            ],
            'peak' => $peakTrafficDay,
            'visit_points' => $this->buildPolylinePoints($daysCollection, 'visits', $maxValue),
            'quote_points' => $this->buildPolylinePoints($daysCollection, 'quotes', $maxValue),
            'message_points' => $this->buildPolylinePoints($daysCollection, 'messages', $maxValue),
        ];
    }

    private function countMapByDay(string $modelClass, Carbon $start, Carbon $end): Collection
    {
        $dateExpression = $this->dateKeyExpression('created_at');

        return $modelClass::query()
            ->selectRaw($dateExpression.' as date_key, COUNT(*) as aggregate')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw($dateExpression))
            ->pluck('aggregate', 'date_key')
            ->map(fn ($count): int => (int) $count);
    }

    private function buildPolylinePoints(
        Collection $days,
        string $key,
        int $maxValue,
        int $width = 640,
        int $height = 220
    ): string {
        $paddingX = 18;
        $paddingY = 18;
        $usableWidth = $width - ($paddingX * 2);
        $usableHeight = $height - ($paddingY * 2);
        $divisor = max(1, $days->count() - 1);

        return $days->values()->map(function (array $day, int $index) use (
            $divisor,
            $height,
            $key,
            $maxValue,
            $paddingX,
            $paddingY,
            $usableHeight,
            $usableWidth
        ): string {
            $x = $paddingX + ($usableWidth * ($index / $divisor));
            $ratio = $maxValue > 0 ? ($day[$key] / $maxValue) : 0;
            $y = $height - $paddingY - ($usableHeight * $ratio);

            return number_format($x, 2, '.', '').','.number_format($y, 2, '.', '');
        })->implode(' ');
    }

    private function buildQuoteBreakdown(
        string $field,
        array $templateMap,
        int $limit,
        bool $quoteTableReady
    ): array {
        if (! $quoteTableReady) {
            return [];
        }

        $counts = LuxuryQuote::query()
            ->select($field)
            ->selectRaw('COUNT(*) as aggregate')
            ->whereNotNull($field)
            ->where($field, '<>', '')
            ->groupBy($field)
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get()
            ->mapWithKeys(fn (LuxuryQuote $quote): array => [
                trim((string) $quote->{$field}) => (int) $quote->aggregate,
            ]);

        $maxCount = max(1, (int) $counts->max());

        return $counts->map(function (int $count, string $value) use ($field, $maxCount, $templateMap): array {
            $label = $field === 'template'
                ? ($templateMap[$value]['name'] ?? Str::headline(str_replace(['-', '_'], ' ', $value)))
                : $value;

            return [
                'label' => $label,
                'count' => $count,
                'meta' => $field === 'template'
                    ? ($templateMap[$value]['badge'] ?? 'Template')
                    : ($count === 1 ? '1 invoice' : number_format($count).' invoices'),
                'width' => round(($count / $maxCount) * 100, 1),
            ];
        })->values()->all();
    }

    private function buildTopPages(int $limit, bool $visitTableReady): array
    {
        if (! $visitTableReady) {
            return [];
        }

        $visitKeyExpression = 'COALESCE(NULLIF(route_name, \'\'), path)';

        $visits = PageVisit::query()
            ->selectRaw($visitKeyExpression.' as visit_key, route_name, path, page_group')
            ->where('created_at', '>=', now()->copy()->subDays(30)->startOfDay());

        $pages = DB::query()
            ->fromSub($visits, 'visits')
            ->select('visit_key')
            ->selectRaw('MAX(route_name) as route_name, MAX(path) as path, MAX(page_group) as page_group, COUNT(*) as aggregate')
            ->groupBy('visit_key')
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get()
            ->map(function (object $visit): array {
                return [
                    'label' => $this->resolveVisitLabel((string) $visit->route_name, (string) $visit->path),
                    'meta' => filled($visit->page_group)
                        ? (string) $visit->page_group
                        : 'Marketing',
                    'count' => (int) $visit->aggregate,
                ];
            })
            ->values();

        $maxCount = max(1, (int) $pages->max('count'));

        return $pages->map(function (array $page) use ($maxCount): array {
            $page['width'] = round(($page['count'] / $maxCount) * 100, 1);

            return $page;
        })->all();
    }

    private function resolveVisitLabel(string $routeName, string $path): string
    {
        if ($routeName !== '') {
            return match (true) {
                $routeName === 'home' => 'Landing Page',
                $routeName === 'service.show' => 'Service Overview',
                $routeName === 'contact.show' => 'Contact Page',
                str_starts_with($routeName, 'services.') => Str::headline(str_replace('services.', '', $routeName)),
                default => Str::headline(str_replace(['.', '-'], ' ', $routeName)),
            };
        }

        if ($path === '/' || $path === '') {
            return 'Landing Page';
        }

        return Str::headline(trim(str_replace('/', ' ', $path)));
    }

    private function buildMonthlyPipeline(int $months, bool $quoteTableReady): array
    {
        if (! $quoteTableReady) {
            return [];
        }

        $start = now()->copy()->startOfMonth()->subMonths($months - 1);
        $monthExpression = $this->monthKeyExpression('created_at');

        $quotes = LuxuryQuote::query()
            ->selectRaw($monthExpression.' as month_key, COUNT(*) as quote_count, COALESCE(SUM(investment_amount), 0) as total')
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw($monthExpression))
            ->get()
            ->keyBy('month_key');

        $monthsCollection = collect(range(0, $months - 1))->map(function (int $offset) use ($quotes, $start): array {
            $month = $start->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $bucket = $quotes->get($key);
            $total = round((float) ($bucket->total ?? 0), 2);
            $count = (int) ($bucket->quote_count ?? 0);

            return [
                'label' => $month->format('M'),
                'full_label' => $month->format('M Y'),
                'count' => $count,
                'total' => $total,
            ];
        });

        $maxTotal = max(1, (float) $monthsCollection->max('total'));

        return $monthsCollection->map(function (array $month) use ($maxTotal): array {
            $month['height'] = round(($month['total'] / $maxTotal) * 100, 1);
            $month['formatted_total'] = '$'.number_format($month['total'], 0);

            return $month;
        })->all();
    }

    private function dateKeyExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlsrv' => 'CONVERT(date, '.$column.')',
            default => 'DATE('.$column.')',
        };
    }

    private function monthKeyExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', ".$column.')',
            'pgsql' => "to_char(".$column.", 'YYYY-MM')",
            'sqlsrv' => "FORMAT(".$column.", 'yyyy-MM')",
            default => "DATE_FORMAT(".$column.", '%Y-%m')",
        };
    }

    private function formatCompactCurrency(float $amount): string
    {
        if ($amount >= 1000000) {
            return '$'.number_format($amount / 1000000, 1).'m';
        }

        if ($amount >= 1000) {
            return '$'.number_format($amount / 1000, $amount >= 100000 ? 0 : 1).'k';
        }

        return '$'.number_format($amount, 0);
    }
}
