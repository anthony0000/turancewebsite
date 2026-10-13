<div class="tt-hero-visual tt-hero-visual--robot" data-hero-robot>
    <div class="tt-hero-visual__orbit" aria-hidden="true">
        <span class="tt-hero-visual__orbit-inner"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--one"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--two"></span>
    </div>
    <span class="tt-ai-robot__aura" aria-hidden="true"></span>
    <div class="tt-ai-robot__track">
        <button class="tt-ai-robot__gesture" type="button" data-robot-gesture aria-label="Make the robot react">
            <span class="tt-ai-robot__stage" data-robot-stage aria-hidden="true">
                <img class="tt-ai-robot__fallback" src="{{ asset('/assets/img/hero/turance-ai-robot-neutral.webp') }}"
                    alt="" draggable="false">
            </span>
        </button>
    </div>
    <span class="tt-ai-robot__shadow" aria-hidden="true"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-robot.css') }}?v=12">
    @endpush
    @push('scripts')
        <script type="module" src="{{ asset('/assets/js/ai-robot.js') }}?v=5"></script>
    @endpush
@endonce
