@php($robotImage = asset('/assets/img/hero/turance-ai-robot.webp'))

<div class="tt-hero-visual tt-hero-visual--robot" data-hero-robot>
    <div class="tt-hero-visual__orbit" aria-hidden="true">
        <span class="tt-hero-visual__orbit-inner"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--one"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--two"></span>
    </div>
    <span class="tt-ai-robot__aura" aria-hidden="true"></span>
    <div class="tt-ai-robot__track">
        <div class="tt-ai-robot__pose">
            <button class="tt-ai-robot__gesture" type="button" data-robot-gesture
                aria-label="Make the robot dance">
                <svg class="tt-ai-robot__puppet" viewBox="0 0 1024 1536" aria-hidden="true">
                    <defs>
                        <clipPath id="tt-robot-leg-left"><polygon points="0,700 525,700 525,1536 0,1536" /></clipPath>
                        <clipPath id="tt-robot-leg-right"><polygon points="475,700 1024,700 1024,1536 475,1536" /></clipPath>
                        <clipPath id="tt-robot-arm-left"><polygon points="0,380 345,380 410,455 385,570 345,690 310,850 340,1090 0,1090" /></clipPath>
                        <clipPath id="tt-robot-arm-right"><polygon points="755,170 1024,170 1024,710 705,710 610,625 600,455 690,405 755,405" /></clipPath>
                        <clipPath id="tt-robot-torso"><polygon points="325,382 675,382 715,475 690,655 730,815 660,895 315,895 270,805 300,650 265,475" /></clipPath>
                        <clipPath id="tt-robot-head"><polygon points="125,0 760,0 760,385 720,445 270,445 150,390" /></clipPath>
                    </defs>
                    <g class="tt-ai-robot__leg tt-ai-robot__leg--left"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-leg-left)" /></g>
                    <g class="tt-ai-robot__leg tt-ai-robot__leg--right"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-leg-right)" /></g>
                    <g class="tt-ai-robot__arm tt-ai-robot__arm--left"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-left)" /></g>
                    <g class="tt-ai-robot__arm tt-ai-robot__arm--right"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-right)" /></g>
                    <g class="tt-ai-robot__torso"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-torso)" /></g>
                    <g class="tt-ai-robot__head"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-head)" /></g>
                </svg>
            </button>
        </div>
    </div>
    <span class="tt-ai-robot__shadow" aria-hidden="true"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-robot.css') }}?v=5">
    @endpush
    @push('scripts')
        <script src="{{ asset('/assets/js/ai-robot.js') }}?v=3" defer></script>
    @endpush
@endonce
