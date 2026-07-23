@php
    $lineItemRows = collect($lineItems ?? [])
        ->map(fn ($item) => [
            'description' => is_array($item) ? ($item['description'] ?? '') : (string) $item,
            'amount' => is_array($item) ? ($item['amount'] ?? '') : '',
        ])
        ->values();

    if ($lineItemRows->isEmpty()) {
        $lineItemRows = collect([
            [
                'description' => '',
                'amount' => '',
            ],
        ]);
    }

    $lineItemTotal = $lineItemRows->sum(fn ($item) => is_numeric($item['amount']) ? (float) $item['amount'] : 0);
    $exchangeRateValue = is_numeric($exchangeRate ?? null) ? (float) $exchangeRate : 1370;
    $nairaTotal = $lineItemTotal * $exchangeRateValue;
@endphp

<div class="field-full">
    <label>Line Items</label>
    <div class="line-items-editor" data-line-items-editor>
        <div class="line-items-editor-head">
            <p class="field-hint">
                Add services and costs. Totals update automatically.
            </p>
            <button type="button" class="ghost-button" data-line-item-add>Add Item</button>
        </div>

        <div class="line-item-rows" data-line-item-rows>
            @foreach ($lineItemRows as $index => $item)
                <div class="line-item-row" data-line-item-row>
                    <span class="line-item-index" data-line-item-index>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="field">
                        <label for="line_item_description_{{ $index }}">Service</label>
                        <input id="line_item_description_{{ $index }}" type="text"
                            name="line_items[{{ $index }}][description]"
                            value="{{ $item['description'] }}"
                            placeholder="Strategic discovery and positioning alignment" required>
                    </div>
                    <div class="field">
                        <label for="line_item_amount_{{ $index }}">Cost</label>
                        <input id="line_item_amount_{{ $index }}" type="number"
                            name="line_items[{{ $index }}][amount]"
                            value="{{ $item['amount'] }}"
                            min="0.01" max="{{ $priceBounds['max'] }}" step="0.01"
                            placeholder="500" data-line-item-amount required>
                    </div>
                    <button type="button" class="ghost-button line-item-remove" data-line-item-remove>Remove</button>
                </div>
            @endforeach
        </div>

        <div class="line-items-total">
            <span>Total Due</span>
            <strong data-line-item-total-display>${{ number_format($lineItemTotal, 0) }}</strong>
        </div>

        <div class="line-items-currency-grid">
            <div class="field">
                <label for="exchange_rate">USD to NGN Rate</label>
                <input id="exchange_rate" type="number" name="exchange_rate"
                    value="{{ number_format($exchangeRateValue, 4, '.', '') }}"
                    min="1" max="1000000" step="0.0001" data-exchange-rate required>
            </div>
            <div class="naira-total-card">
                <span>Naira Equivalent</span>
                <strong data-naira-total-display>NGN {{ number_format($nairaTotal, 0) }}</strong>
                <small>Based on the saved exchange rate.</small>
            </div>
        </div>

        <input id="investment_amount" type="hidden" name="investment_amount"
            value="{{ number_format($lineItemTotal, 2, '.', '') }}" data-line-item-total-input>

        <template data-line-item-template>
            <div class="line-item-row" data-line-item-row>
                <span class="line-item-index" data-line-item-index>__NUMBER__</span>
                <div class="field">
                    <label for="line_item_description___INDEX__">Service</label>
                    <input id="line_item_description___INDEX__" type="text"
                        name="line_items[__INDEX__][description]"
                        placeholder="Strategic discovery and positioning alignment" required>
                </div>
                <div class="field">
                    <label for="line_item_amount___INDEX__">Cost</label>
                    <input id="line_item_amount___INDEX__" type="number"
                        name="line_items[__INDEX__][amount]"
                        min="0.01" max="{{ $priceBounds['max'] }}" step="0.01"
                        placeholder="500" data-line-item-amount required>
                </div>
                <button type="button" class="ghost-button line-item-remove" data-line-item-remove>Remove</button>
            </div>
        </template>
    </div>
</div>
