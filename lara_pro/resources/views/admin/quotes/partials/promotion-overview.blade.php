<section class="tt-page tt-page--promotion" id="promotion-control">
    <header class="tt-subpage-head">
        <div>
            <span class="eyebrow">Campaign control</span>
            <h1>Promotion control</h1>
            <p>Manage the public anniversary offer and the default invoice discount in one place.</p>
        </div>
        <span class="tt-page-badge"><i class="tt-page-badge__gold"></i>{{ ($anniversaryPromo['is_active'] ?? false) ? 'Live campaign' : 'Campaign inactive' }}</span>
    </header>

    <div class="tt-promo-grid">
        <section class="tt-section">
            <div class="tt-section-head">
                <div><span class="eyebrow">Anniversary campaign</span><h2>Offer settings</h2><p>These values update the landing-page message, countdown, pricing, and invoice defaults.</p></div>
            </div>
            <form method="POST" action="{{ route('admin.quotes.promotion.update') }}" class="promotion-form">
                @csrf
                <div class="field-full promotion-form__status">
                    <label class="admin-discount-toggle">
                        <input type="checkbox" name="enabled" value="1" @checked($anniversaryPromo['enabled'] ?? false)>
                        <span>Show this offer publicly</span>
                    </label>
                    <p class="field-hint">When disabled, the landing page hides the offer and countdown.</p>
                </div>
                <div class="promotion-form__grid">
                    <div class="field"><label for="promotion_years">Years celebrated</label><input id="promotion_years" type="number" name="years" value="{{ old('years', $anniversaryPromo['years'] ?? 7) }}" min="1" max="100" required></div>
                    <div class="field"><label for="promotion_discount">Discount percentage</label><input id="promotion_discount" type="number" name="discount_percent" value="{{ old('discount_percent', $anniversaryPromo['discount_percent'] ?? 50) }}" min="0" max="100" step="0.01" required></div>
                    <div class="field"><label for="promotion_code">Promo code</label><input id="promotion_code" type="text" name="promo_code" value="{{ old('promo_code', $anniversaryPromo['code'] ?? 'TURANCE7') }}" maxlength="80" required></div>
                    <div class="field"><label for="promotion_ends_at">Offer ends</label><input id="promotion_ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $anniversaryPromo['ends_at_input'] ?? '') }}" required></div>
                </div>
                <div class="wizard-actions"><span class="admin-pill">Current: {{ ($anniversaryPromo['is_active'] ?? false) ? 'Live' : 'Inactive' }}</span><button type="submit" class="button">Save promotion</button></div>
            </form>
        </section>

        <aside class="tt-section tt-promo-preview">
            <div class="tt-section-head"><div><span class="eyebrow">Public preview</span><h2>How it reads</h2></div></div>
            <strong class="tt-promo-discount">{{ $anniversaryPromo['discount_percent'] ?? 50 }}% off</strong>
            <h3>{{ $anniversaryPromo['years'] ?? 7 }} years of Turance</h3>
            <p>Landing-page offer code <b>{{ $anniversaryPromo['code'] ?? 'TURANCE7' }}</b>.</p>
            <span class="data-note">Ends {{ $anniversaryPromo['ends_at_formatted'] ?? 'Not set' }}</span>
        </aside>
    </div>
</section>
