@php($robotBody = asset('/assets/img/hero/turance-ai-robot-body.webp'))
@php($robotArm = asset('/assets/img/hero/turance-ai-robot-neutral.webp'))

<div class="tt-hero-visual tt-hero-visual--robot" data-hero-robot>
    <div class="tt-hero-visual__orbit" aria-hidden="true">
        <span class="tt-hero-visual__orbit-inner"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--one"></span>
        <span class="tt-ai-robot__orbit-node tt-ai-robot__orbit-node--two"></span>
    </div>
    <span class="tt-ai-robot__aura" aria-hidden="true"></span>
    <div class="tt-ai-robot__track">
        <button class="tt-ai-robot__gesture" type="button" data-robot-gesture aria-label="Make the robot wave">
            <svg class="tt-ai-robot__puppet" viewBox="0 0 1024 1536" aria-hidden="true">
                <defs>
                    <clipPath id="tt-robot-shoulder-cap"><path d="M575 430 Q632 415 682 459 L691 520 Q688 552 664 564 L617 566 Q584 561 576 532 Z" /></clipPath>
                    <clipPath id="tt-robot-arm-upper"><path d="M612 535 L678 535 Q687 585 685 631 L706 665 705 701 Q676 719 625 693 L612 650 Q605 596 612 535 Z" /></clipPath>
                    <clipPath id="tt-robot-arm-forearm"><path d="M621 651 Q666 631 704 670 750 683 773 757 L779 841 Q796 865 794 951 L790 999 Q781 1033 744 1046 706 1047 682 1025 L678 898 Q675 858 671 825 L652 780 635 737 Q618 700 621 651 Z" /></clipPath>
                    <radialGradient id="tt-robot-eye-cover">
                        <stop offset="0" stop-color="#061326" />
                        <stop offset=".7" stop-color="#061326" />
                        <stop offset="1" stop-color="#061326" stop-opacity="0" />
                    </radialGradient>
                </defs>
                <image href="{{ $robotBody }}" width="1024" height="1536" />
                <image href="{{ $robotArm }}" width="1024" height="1536" clip-path="url(#tt-robot-shoulder-cap)" />
                <g class="tt-ai-robot__arm">
                    <image href="{{ $robotArm }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-upper)" />
                    <g class="tt-ai-robot__forearm">
                        <image href="{{ $robotArm }}" width="1024" height="1536" clip-path="url(#tt-robot-arm-forearm)" />
                    </g>
                </g>
                <g class="tt-ai-robot__expression tt-ai-robot__expression--wink">
                    <ellipse cx="574" cy="258" rx="67" ry="55" fill="url(#tt-robot-eye-cover)" />
                    <path d="M541 263 Q571 272 605 249" />
                </g>
                <g class="tt-ai-robot__expression tt-ai-robot__expression--blink">
                    <ellipse cx="401" cy="228" rx="69" ry="58" fill="url(#tt-robot-eye-cover)" />
                    <ellipse cx="574" cy="258" rx="67" ry="55" fill="url(#tt-robot-eye-cover)" />
                    <path d="M366 232 Q402 245 438 226 M540 262 Q573 274 606 251" />
                </g>
            </svg>
        </button>
    </div>
    <span class="tt-ai-robot__shadow" aria-hidden="true"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-robot.css') }}?v=13">
    @endpush
    @push('scripts')
        <script src="{{ asset('/assets/js/ai-robot.js') }}?v=6" defer></script>
    @endpush
@endonce
