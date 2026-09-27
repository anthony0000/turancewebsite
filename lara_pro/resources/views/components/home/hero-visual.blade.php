@props([
    'color' => '#c5ad7c',
    'particleDensity' => 22000,
    'motionIntensity' => 1,
    'expressionIntensity' => 1,
    'horizontalLimit' => 12,
    'verticalLimit' => 8,
])

<div class="tt-hero-visual tt-hero-visual--ai" aria-hidden="true">
    <div class="tt-hero-visual__orbit">
        <span class="tt-hero-visual__orbit-inner"></span>
        <span class="tt-ai-face__orbit-node tt-ai-face__orbit-node--one"></span>
        <span class="tt-ai-face__orbit-node tt-ai-face__orbit-node--two"></span>
    </div>
    <div class="tt-ai-face" data-ai-face
        data-model="{{ asset('/assets/models/ai-face/female-head.bin') }}"
        data-color="{{ $color }}" data-particle-density="{{ $particleDensity }}"
        data-motion-intensity="{{ $motionIntensity }}"
        data-expression-intensity="{{ $expressionIntensity }}"
        data-horizontal-limit="{{ $horizontalLimit }}" data-vertical-limit="{{ $verticalLimit }}">
        <script>
            // Before first paint: the inline cloud needs neither an image nor WebGL.
            (() => {
                const face = document.currentScript.parentElement;
                let seen = window.__ttAIFaceFormed;
                try { seen ||= localStorage.getItem('tt-ai-face-formed-v1') === '1'; } catch (_) {}
                face.dataset.formation = seen || matchMedia('(prefers-reduced-motion: reduce)').matches
                    || Number(face.dataset.motionIntensity) === 0 ? 'complete' : 'pending';
                face.aiFallbackTimer = setTimeout(() => {
                    if (!face.classList.contains('is-ready')) face.dataset.formation = 'complete';
                }, 6000);
            })();
        </script>
        <svg class="tt-ai-face__cloud" viewBox="0 0 550 550" aria-hidden="true">
            <g fill="#b99b59">
                @for ($i = 0; $i < 260; $i++)
                    @php
                        $angle = $i * 2.399963;
                        $radius = 225 * sqrt(($i + .5) / 260);
                    @endphp
                    <circle cx="{{ 275 + cos($angle) * $radius }}" cy="{{ 265 + sin($angle) * $radius }}"
                        r="{{ .55 + ($i % 4) * .16 }}" opacity="{{ .25 + ($i % 7) * .07 }}" />
                @endfor
            </g>
        </svg>
        <img class="tt-ai-face__fallback" src="{{ asset('/assets/img/hero/turance-ai-face.png') }}"
            width="960" height="960" alt="" decoding="async" fetchpriority="high">
    </div>
    <span class="tt-ai-face__shadow"></span>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/assets/css/ai-face.css') }}?v=3">
    @endpush
    @push('scripts')
        <script type="module" src="{{ asset('/assets/js/ai-face.js') }}?v=6"></script>
    @endpush
@endonce
