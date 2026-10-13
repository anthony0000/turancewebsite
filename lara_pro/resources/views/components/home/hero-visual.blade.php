<div class="tt-hero-visual tt-hero-visual--robot" data-hero-robot data-gesture="wave">
    <div class="tt-hero-visual__orbit" aria-hidden="true">
        <span class="tt-hero-visual__orbit-inner"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--one"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--two"></span>
    </div>
    <span class="tt-ai-robot__aura" aria-hidden="true"></span>
    <div class="tt-ai-robot__track">
        <div class="tt-ai-robot__pose">
            <button class="tt-ai-robot__gesture" type="button" data-robot-gesture
                aria-label="Make the robot do a funny gesture">
                <img class="tt-ai-robot__frame is-active" data-robot-pose="wave"
                    src="{{ asset('/assets/img/hero/turance-ai-robot.webp') }}"
                    width="1024" height="1536" alt="" decoding="async" fetchpriority="high">
                <img class="tt-ai-robot__frame" data-robot-pose="thumbs"
                    src="{{ asset('/assets/img/hero/turance-ai-robot-thumbs.webp') }}"
                    width="1024" height="1536" alt="" decoding="async">
                <img class="tt-ai-robot__frame" data-robot-pose="shrug"
                    src="{{ asset('/assets/img/hero/turance-ai-robot-shrug.webp') }}"
                    width="1024" height="1536" alt="" decoding="async">
                <span class="tt-ai-robot__signal" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <span class="tt-ai-robot__shadow" aria-hidden="true"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-robot.css') }}?v=3">
    @endpush
    @push('scripts')
        <script src="{{ asset('/assets/js/ai-robot.js') }}?v=2" defer></script>
    @endpush
@endonce
