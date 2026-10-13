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
                aria-label="Make the robot react">
                <svg class="tt-ai-robot__puppet" viewBox="0 0 1024 1536" aria-hidden="true">
                    <defs>
                        <clipPath id="tt-robot-lower-body"><polygon points="325,695 755,695 790,1536 145,1536 180,1210 300,1050 315,845" /></clipPath>
                        <clipPath id="tt-robot-arm-left"><polygon points="90,390 365,390 390,560 310,670 285,830 315,1010 305,1070 95,1070 90,585" /></clipPath>
                        <clipPath id="tt-robot-arm-right"><polygon points="755,170 1024,170 1024,710 705,710 610,625 600,455 690,405 755,405" /></clipPath>
                        <clipPath id="tt-robot-torso"><polygon points="365,390 650,390 680,475 665,620 650,705 715,805 665,895 320,895 340,800 355,675 345,545 340,470" /></clipPath>
                        <clipPath id="tt-robot-head"><polygon points="125,0 760,0 760,385 720,445 270,445 150,390" /></clipPath>
                        <radialGradient id="tt-robot-eye-cover">
                            <stop offset="0" stop-color="#061326" />
                            <stop offset=".7" stop-color="#061326" />
                            <stop offset="1" stop-color="#061326" stop-opacity="0" />
                        </radialGradient>
                    </defs>
                    <g class="tt-ai-robot__lower-body"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-lower-body)" /></g>
                    <g class="tt-ai-robot__arm tt-ai-robot__arm--left"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-left)" /></g>
                    <g class="tt-ai-robot__arm tt-ai-robot__arm--right"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-right)" /></g>
                    <g class="tt-ai-robot__torso"><image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-torso)" /></g>
                    <g class="tt-ai-robot__head">
                        <image href="{{ $robotImage }}" width="1024" height="1536" clip-path="url(#tt-robot-head)" />
                        <g class="tt-ai-robot__expression tt-ai-robot__expression--wink">
                            <ellipse cx="574" cy="258" rx="67" ry="55" fill="url(#tt-robot-eye-cover)" />
                            <path d="M541 263 Q571 272 605 249" />
                        </g>
                        <g class="tt-ai-robot__expression tt-ai-robot__expression--surprised">
                            <ellipse cx="401" cy="228" rx="69" ry="58" fill="url(#tt-robot-eye-cover)" />
                            <ellipse cx="574" cy="258" rx="67" ry="55" fill="url(#tt-robot-eye-cover)" />
                            <ellipse class="tt-ai-robot__round-eye" cx="402" cy="229" rx="18" ry="23" />
                            <ellipse class="tt-ai-robot__round-eye" cx="575" cy="257" rx="18" ry="23" />
                            <circle class="tt-ai-robot__round-eye" cx="492" cy="315" r="9" />
                        </g>
                        <g class="tt-ai-robot__expression tt-ai-robot__expression--blink">
                            <ellipse cx="401" cy="228" rx="69" ry="58" fill="url(#tt-robot-eye-cover)" />
                            <ellipse cx="574" cy="258" rx="67" ry="55" fill="url(#tt-robot-eye-cover)" />
                            <path d="M366 232 Q402 245 438 226 M540 262 Q573 274 606 251" />
                        </g>
                    </g>
                </svg>
            </button>
        </div>
    </div>
    <span class="tt-ai-robot__shadow" aria-hidden="true"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-robot.css') }}?v=9">
    @endpush
    @push('scripts')
        <script src="{{ asset('/assets/js/ai-robot.js') }}?v=4" defer></script>
    @endpush
@endonce
