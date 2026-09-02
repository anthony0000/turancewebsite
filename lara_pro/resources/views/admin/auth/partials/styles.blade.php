/* Shared admin authentication surface. Keep this partial free of page-specific
 * selectors so future recovery or invitation screens can use the same system. */
:root {
    --auth-ink: #17191d;
    --auth-muted: #747a82;
    --auth-line: #e4e6e8;
    --auth-surface: #ffffff;
    --auth-canvas: #f4f4f1;
    --auth-gold: #b8860b;
    --auth-gold-strong: #8f6508;
    --auth-danger: #a5433b;
    --auth-success: #2f8054;
    --auth-radius: 10px;
    --auth-font: "Urbanist", "Aptos", "Segoe UI Variable Text", "Segoe UI", ui-sans-serif, system-ui, sans-serif;
}

body.is-auth {
    min-height: 100dvh;
    overflow-x: hidden;
    background: var(--auth-canvas);
    color: var(--auth-ink);
    font-family: var(--auth-font);
}

body.is-auth .admin-shell--auth,
body.is-auth .admin-workspace,
body.is-auth .admin-main {
    width: 100%;
    max-width: none;
    min-height: 100dvh;
}

body.is-auth .admin-shell--auth {
    display: block;
    padding: 0;
}

body.is-auth .admin-workspace,
body.is-auth .admin-main {
    display: block;
}

body.is-auth .admin-main {
    position: relative;
    padding: 0;
}

body.is-auth .admin-alert-stack {
    position: fixed;
    z-index: 20;
    top: 20px;
    left: 50%;
    width: min(calc(100% - 40px), 520px);
    margin: 0;
    transform: translateX(-50%);
}

body.is-auth .admin-alert-stack .alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 0;
    padding: 13px 14px;
    border: 1px solid rgba(23, 25, 29, .1);
    border-radius: var(--auth-radius);
    background: rgba(255, 255, 255, .96);
    color: var(--auth-ink);
    box-shadow: 0 16px 36px rgba(23, 25, 29, .13);
    font-size: 12px;
    line-height: 1.45;
    backdrop-filter: blur(16px);
}

body.is-auth .admin-alert-stack .alert-success {
    border-color: rgba(47, 128, 84, .25);
}

body.is-auth .admin-alert-stack .alert-warning,
body.is-auth .admin-alert-stack .alert-danger {
    border-color: rgba(165, 67, 59, .25);
}

body.is-auth .admin-alert-close {
    flex: 0 0 auto;
    margin: -3px -3px 0 auto;
    padding: 2px 4px;
    border: 0;
    background: transparent;
    color: var(--auth-muted);
    font-size: 20px;
    line-height: 1;
}

body.is-auth .admin-alert-close:hover,
body.is-auth .admin-alert-close:focus-visible {
    color: var(--auth-ink);
}

.auth-page {
    width: 100%;
    min-height: 100dvh;
}

body.is-auth .auth-grid {
    display: grid;
    grid-template-columns: minmax(390px, 44fr) minmax(0, 56fr);
    gap: 0;
    width: 100%;
    min-height: 100dvh;
    margin: 0;
    overflow: hidden;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}

body.is-auth .auth-card {
    order: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    min-height: 100dvh;
    padding: clamp(36px, 6vw, 88px) clamp(28px, 6vw, 96px);
    border: 0;
    border-right: 1px solid var(--auth-line);
    border-radius: 0;
    background: var(--auth-surface);
    box-shadow: none;
}

body.is-auth .auth-card::before {
    display: none;
}

.auth-card__inner {
    width: min(100%, 440px);
    min-width: 0;
    max-width: 100%;
    margin: auto;
}

.auth-brand-lockup {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    color: inherit;
    text-decoration: none;
}

.auth-brand-lockup:focus-visible {
    outline: 3px solid rgba(184, 134, 11, .25);
    outline-offset: 6px;
    border-radius: 6px;
}

body.is-auth .auth-brand-lockup .admin-brand-mark {
    display: grid;
    width: 44px;
    height: 44px;
    place-items: center;
    flex: 0 0 auto;
    border: 1px solid rgba(184, 134, 11, .26);
    border-radius: 10px;
    background: #1b1d21;
    box-shadow: 0 8px 18px rgba(23, 25, 29, .12);
}

body.is-auth .auth-brand-lockup .admin-brand-mark img {
    width: 23px;
    height: auto;
    object-fit: contain;
}

.auth-brand-copy strong,
.auth-brand-copy span {
    display: block;
}

.auth-brand-copy strong {
    color: var(--auth-ink);
    font-family: var(--auth-font);
    font-size: 15px;
    font-weight: 700;
    letter-spacing: -.02em;
    line-height: 1.15;
}

.auth-brand-copy span {
    margin-top: 4px;
    color: var(--auth-gold-strong);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    line-height: 1;
    text-transform: uppercase;
}

.auth-card-head {
    margin: clamp(52px, 8vh, 84px) 0 30px;
}

.auth-card-head .eyebrow,
.auth-visual-copy .eyebrow {
    display: block;
    color: var(--auth-gold-strong);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .16em;
    line-height: 1.2;
    text-transform: uppercase;
}

body.is-auth .auth-card-head h1 {
    margin: 13px 0 0;
    color: var(--auth-ink);
    font-family: var(--auth-font);
    font-size: clamp(34px, 3.5vw, 48px);
    font-weight: 600;
    letter-spacing: -.055em;
    line-height: .98;
}

body.is-auth .auth-card-head p {
    max-width: 390px;
    margin: 16px 0 0;
    color: var(--auth-muted);
    font-size: 14px;
    line-height: 1.65;
}

.auth-form {
    display: grid;
    min-width: 0;
    gap: 18px;
}

.auth-field {
    display: grid;
    gap: 8px;
}

.auth-field__label,
body.is-auth .auth-card .field label {
    color: var(--auth-ink);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .01em;
}

.auth-input-shell {
    position: relative;
}

body.is-auth .auth-field input.auth-input {
    width: 100%;
    min-width: 0;
    min-height: 54px;
    padding: 0 15px 0 44px;
    border: 1px solid #dfe2e4;
    border-radius: var(--auth-radius);
    outline: 0;
    background: #fbfbfa;
    color: var(--auth-ink);
    font-family: var(--auth-font);
    font-size: 14px;
    font-weight: 500;
    transition: border-color 180ms ease, background-color 180ms ease, box-shadow 180ms ease;
}

body.is-auth .auth-field input.auth-input::placeholder {
    color: #a0a5aa;
}

body.is-auth .auth-field input.auth-input:hover {
    border-color: #c9cdd0;
}

body.is-auth .auth-field input.auth-input:focus {
    border-color: rgba(184, 134, 11, .72);
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(184, 134, 11, .11);
}

body.is-auth .auth-field input.auth-input[aria-invalid="true"] {
    border-color: rgba(165, 67, 59, .64);
}

.auth-input-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    width: 17px;
    height: 17px;
    color: #969ca1;
    pointer-events: none;
    transform: translateY(-50%);
}

.auth-input-icon svg,
.auth-password-toggle svg,
.auth-button__spinner {
    display: block;
    width: 100%;
    height: 100%;
}

.auth-input-icon svg,
.auth-password-toggle svg {
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.6;
}

body.is-auth .auth-field input.auth-input.auth-input--password {
    padding-right: 70px;
}

.auth-password-toggle {
    position: absolute;
    top: 50%;
    right: 13px;
    display: inline-grid;
    width: 32px;
    height: 32px;
    place-items: center;
    padding: 7px;
    border: 0;
    border-radius: 7px;
    background: transparent;
    color: #858b90;
    cursor: pointer;
    transform: translateY(-50%);
    transition: color 180ms ease, background-color 180ms ease;
}

.auth-password-toggle:hover,
.auth-password-toggle:focus-visible {
    background: #f0f0ed;
    color: var(--auth-ink);
}

.auth-password-toggle:focus-visible,
.auth-check input:focus-visible,
.auth-text-link:focus-visible,
.auth-button:focus-visible {
    outline: 3px solid rgba(184, 134, 11, .24);
    outline-offset: 2px;
}

.auth-field-error {
    margin: 0;
    color: var(--auth-danger);
    font-size: 11px;
    line-height: 1.4;
}

.auth-alert {
    display: grid;
    gap: 4px;
    margin: 0 0 22px;
    padding: 13px 14px;
    border: 1px solid rgba(165, 67, 59, .24);
    border-radius: var(--auth-radius);
    background: #fff8f7;
    color: var(--auth-danger);
    font-size: 12px;
    line-height: 1.45;
}

.auth-alert strong {
    color: #79352f;
    font-weight: 700;
}

.auth-alert ul {
    margin: 0;
    padding-left: 17px;
}

.auth-config-alert {
    border-color: rgba(184, 134, 11, .3);
    background: #fffaf0;
    color: #74530c;
}

.auth-form__options {
    display: flex;
    min-width: 0;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-top: -1px;
}

.auth-check {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-height: 44px;
    color: var(--auth-muted);
    font-size: 12px;
    cursor: pointer;
}

.auth-check input {
    width: 17px;
    height: 17px;
    margin: 0;
    accent-color: var(--auth-gold);
}

.auth-text-link {
    min-width: 0;
    color: var(--auth-gold-strong);
    font-size: 12px;
    font-weight: 700;
    text-decoration: underline;
    text-decoration-color: rgba(143, 101, 8, .35);
    text-underline-offset: 3px;
    transition: color 180ms ease, text-decoration-color 180ms ease;
}

.auth-text-link:hover {
    color: var(--auth-ink);
    text-decoration-color: currentColor;
}

.auth-button {
    display: inline-flex;
    width: 100%;
    min-height: 54px;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 0 20px;
    border: 1px solid #1b1d21;
    border-radius: var(--auth-radius);
    background: #1b1d21;
    color: #ffffff;
    cursor: pointer;
    font-family: var(--auth-font);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .01em;
    transition: background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}

.auth-button:hover {
    border-color: #30343a;
    background: #30343a;
    box-shadow: 0 10px 22px rgba(23, 25, 29, .16);
    transform: translateY(-1px);
}

.auth-button:active {
    box-shadow: none;
    transform: translateY(0);
}

.auth-button:disabled {
    cursor: wait;
    opacity: .78;
    transform: none;
}

.auth-button__spinner {
    display: none;
    width: 15px;
    height: 15px;
    border: 2px solid rgba(255, 255, 255, .36);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: auth-spin 700ms linear infinite;
}

.auth-button[aria-busy="true"] .auth-button__spinner {
    display: block;
}

.auth-support-note {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 26px 0 0;
    color: #969ba0;
    font-size: 11px;
    line-height: 1.5;
}

.auth-support-note svg {
    width: 14px;
    height: 14px;
    flex: 0 0 auto;
    fill: none;
    stroke: var(--auth-gold-strong);
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.6;
}

.auth-visual-panel {
    position: relative;
    order: 2;
    display: flex;
    min-width: 0;
    min-height: 100dvh;
    align-items: flex-end;
    overflow: hidden;
    isolation: isolate;
    background: #20262a;
}

.auth-visual-panel picture,
.auth-visual-panel picture::after,
.auth-visual-image,
.auth-visual-overlay {
    position: absolute;
    inset: 0;
}

.auth-visual-panel picture {
    z-index: -3;
    display: block;
}

.auth-visual-panel picture::after {
    content: "";
    z-index: 1;
    background: linear-gradient(135deg, rgba(18, 24, 27, .5), rgba(18, 24, 27, .17) 44%, rgba(18, 24, 27, .76));
}

.auth-visual-image {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 58% center;
    animation: auth-image-drift 18s ease-in-out infinite alternate;
}

.auth-visual-overlay {
    z-index: -1;
    background:
        linear-gradient(90deg, rgba(184, 134, 11, .16), transparent 34%),
        radial-gradient(circle at 72% 16%, rgba(212, 175, 55, .2), transparent 30%);
    mix-blend-mode: screen;
    pointer-events: none;
}

.auth-visual-panel::after {
    position: absolute;
    z-index: -1;
    top: -20%;
    right: 10%;
    width: 44%;
    height: 62%;
    border: 1px solid rgba(212, 175, 55, .28);
    border-radius: 50%;
    content: "";
    opacity: .75;
    transform: rotate(-24deg) scaleX(1.4);
    pointer-events: none;
}

.auth-visual-content {
    width: min(100%, 620px);
    min-width: 0;
    max-width: 100%;
    padding: clamp(30px, 6vw, 82px);
    color: #ffffff;
}

.auth-visual-brand {
    position: absolute;
    top: clamp(28px, 5vw, 64px);
    left: clamp(30px, 6vw, 82px);
    display: flex;
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
}

.auth-visual-brand img {
    display: block;
    width: clamp(178px, 17vw, 220px);
    height: auto;
}

.auth-visual-brand span {
    display: inline-flex;
    align-self: center;
    align-items: center;
    gap: 10px;
    padding: 0 4px;
    color: rgba(255, 255, 255, .76);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.auth-visual-brand span::before {
    width: 24px;
    height: 1px;
    background: #d4af37;
    content: "";
}

.auth-visual-brand span::after {
    width: 24px;
    height: 1px;
    background: rgba(212, 175, 55, .72);
    content: "";
}

.auth-visual-copy {
    max-width: 500px;
}

.auth-visual-copy .eyebrow {
    color: #e0b846;
}

.auth-visual-copy h2 {
    max-width: 100%;
    overflow-wrap: break-word;
    margin: 14px 0 0;
    color: #ffffff;
    font-family: var(--auth-font);
    font-size: clamp(32px, 4.2vw, 58px);
    font-weight: 500;
    letter-spacing: -.06em;
    line-height: 1.02;
}

.auth-visual-copy p {
    max-width: 390px;
    margin: 20px 0 0;
    color: rgba(255, 255, 255, .77);
    font-size: 14px;
    line-height: 1.65;
}

.auth-visual-rule {
    display: block;
    width: 56px;
    height: 2px;
    margin-top: 28px;
    background: #d5a72c;
}

@keyframes auth-rise {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes auth-image-drift {
    from { transform: scale(1); }
    to { transform: scale(1.04); }
}

@keyframes auth-spin {
    to { transform: rotate(360deg); }
}

body.is-auth .auth-brand-lockup,
body.is-auth .auth-card-head,
body.is-auth .auth-form,
body.is-auth .auth-support-note,
body.is-auth .auth-visual-brand,
body.is-auth .auth-visual-copy {
    animation: auth-rise 420ms cubic-bezier(.2, .7, .2, 1) both;
}

body.is-auth .auth-card-head { animation-delay: 70ms; }
body.is-auth .auth-form { animation-delay: 130ms; }
body.is-auth .auth-support-note { animation-delay: 200ms; }
body.is-auth .auth-visual-brand { animation-delay: 120ms; }
body.is-auth .auth-visual-copy { animation-delay: 220ms; }

@media (max-width: 980px) {
    body.is-auth .auth-card {
        padding-inline: clamp(24px, 4vw, 52px);
    }

    .auth-visual-content {
        padding-inline: clamp(28px, 5vw, 54px);
    }

    .auth-visual-brand {
        left: clamp(28px, 5vw, 54px);
    }
}

@media (max-width: 760px) {
    body.is-auth .auth-grid {
        grid-template-columns: 1fr;
        min-height: 0;
        overflow: visible;
    }

    body.is-auth .auth-card {
        order: 2;
        min-height: auto;
        padding: 44px 24px 48px;
        border-right: 0;
    }

    body.is-auth .auth-card__inner {
        width: 100%;
        margin: 0 auto;
    }

    body.is-auth .auth-card-head p {
        max-width: 100%;
    }

    .auth-card-head {
        margin-top: 48px;
    }

    .auth-visual-panel {
        order: 1;
        min-height: 230px;
        align-items: flex-end;
    }

    .auth-visual-brand {
        top: 24px;
        left: 24px;
    }

    .auth-visual-brand img {
        width: 156px;
    }

    .auth-visual-brand span {
        display: none;
    }

    .auth-visual-content {
        padding: 28px 24px;
    }

    .auth-visual-copy h2 {
        max-width: 100%;
        margin-top: 10px;
        font-size: clamp(28px, 8vw, 40px);
    }

    .auth-visual-copy p,
    .auth-visual-rule {
        display: none;
    }
}

@media (max-width: 430px) {
    body.is-auth .admin-alert-stack {
        top: 12px;
        width: calc(100% - 24px);
    }

    body.is-auth .auth-card {
        padding: 34px 18px 40px;
    }

    .auth-visual-panel {
        min-height: 195px;
    }

    .auth-visual-brand {
        top: 20px;
        left: 18px;
    }

    .auth-visual-content {
        padding: 22px 18px;
    }

    body.is-auth .auth-form__options {
        align-items: flex-start;
        flex-direction: column !important;
        gap: 2px;
    }
}

@media (prefers-reduced-motion: reduce) {
    body.is-auth .auth-brand-lockup,
    body.is-auth .auth-card-head,
    body.is-auth .auth-form,
    body.is-auth .auth-support-note,
    body.is-auth .auth-visual-brand,
    body.is-auth .auth-visual-copy,
    .auth-visual-image,
    .auth-button__spinner {
        animation: none;
    }

    .auth-button,
    body.is-auth .auth-field input.auth-input,
    .auth-password-toggle,
    .auth-text-link {
        transition-duration: 0.01ms;
    }
}
