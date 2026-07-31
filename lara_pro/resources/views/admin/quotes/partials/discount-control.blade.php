@php
    $promoIsActive = ! empty($anniversaryPromo['is_active']);
    $discountEnabled = (float) ($discountPercent ?? 0) > 0;
@endphp

<div class="field-full admin-discount-control">
    <div class="admin-discount-control__head">
        <div>
            <label for="discount_percent">Discount and price control</label>
            <p class="field-hint">Apply a controlled discount to the invoice total. Line-item prices remain visible as the original scope value.</p>
        </div>
        @if ($promoIsActive)
            <span class="admin-pill">{{ $anniversaryPromo['years'] }}-year offer live</span>
        @endif
    </div>
    <div class="admin-discount-control__grid">
        <label class="admin-discount-toggle">
            <input type="checkbox" name="discount_enabled" value="1" data-discount-enabled {{ $discountEnabled ? 'checked' : '' }}>
            <span>Apply discount</span>
        </label>
        <div class="field">
            <label for="discount_percent">Discount %</label>
            <input id="discount_percent" type="number" name="discount_percent" value="{{ number_format((float) ($discountPercent ?? 0), 2, '.', '') }}" min="0" max="100" step="0.01" data-discount-percent {{ $discountEnabled ? '' : 'disabled' }}>
        </div>
        <div class="field">
            <label for="promo_code">Offer code</label>
            <input id="promo_code" type="text" name="promo_code" value="{{ $promoCode ?? '' }}" maxlength="80" placeholder="TURANCE7" data-discount-code {{ $discountEnabled ? '' : 'disabled' }}>
        </div>
    </div>
    @if ($promoIsActive)
        <p class="field-hint">Public landing offer: {{ $anniversaryPromo['discount_percent'] }}% off with code {{ $anniversaryPromo['code'] }}, ending {{ $anniversaryPromo['ends_at_formatted'] }}.</p>
    @endif
</div>
