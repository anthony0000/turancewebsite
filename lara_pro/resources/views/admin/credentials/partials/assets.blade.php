@push('styles')
    <style>
        .credential-workspace { padding: 24px; border: 1px solid #e1e5e9; border-radius: 16px; background: #fff; box-shadow: 0 12px 32px rgba(20, 27, 38, .055); }
        .credential-workspace__header { display: flex; align-items: center; justify-content: space-between; gap: 28px; }
        .credential-workspace__intro { max-width: 650px; }
        .credential-workspace__intro h2 { margin: 5px 0 0; color: var(--text); font-size: clamp(22px, 2.4vw, 29px); letter-spacing: -.035em; }
        .credential-workspace__intro p { margin: 7px 0 0; color: var(--muted); font-size: 12px; line-height: 1.6; }
        .credential-workspace__actions { display: flex; flex: 0 0 auto; align-items: center; gap: 9px; }
        .credential-workspace__count { display: grid; min-width: 72px; padding-right: 16px; border-right: 1px solid var(--line-soft); text-align: right; }
        .credential-workspace__count strong { color: var(--text); font-size: 20px; line-height: 1; }
        .credential-workspace__count span { margin-top: 4px; color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        .credential-add-button, .credential-export-button { display: inline-flex; align-items: center; gap: 7px; }
        .credential-add-button svg, .credential-export-button svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.8; }
        .credential-workspace__notice { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 11px; margin: 22px 0 18px; padding: 12px 14px; border: 1px solid #eadfbf; border-radius: 10px; background: #fffbf1; }
        .credential-workspace__notice > span { display: grid; width: 32px; height: 32px; place-items: center; border-radius: 9px; background: #f4e3b6; color: #86600f; }
        .credential-workspace__notice svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }
        .credential-workspace__notice div { display: grid; gap: 2px; }
        .credential-workspace__notice strong { color: #403718; font-size: 11px; }
        .credential-workspace__notice small { color: #827653; font-size: 10px; }
        .credential-workspace__notice i { padding: 5px 8px; border-radius: 999px; background: rgba(255, 255, 255, .8); color: #896410; font-size: 9px; font-style: normal; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }

        .credential-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .credential-workspace .credential-card { display: flex; min-width: 0; min-height: 300px; flex-direction: column; padding: 18px; border: 1px solid #e3e6ea; border-radius: 13px; background: #fff; box-shadow: 0 6px 18px rgba(20, 27, 38, .04); transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
        .credential-workspace .credential-card:hover { border-color: #d7c590; box-shadow: 0 12px 26px rgba(20, 27, 38, .075); transform: translateY(-1px); }
        .credential-card__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .credential-card__identity { display: flex; min-width: 0; align-items: center; gap: 11px; }
        .credential-card__marker { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border-radius: 11px; background: #fff3d2; color: #8a620a; font-size: 14px; font-weight: 800; }
        .credential-card__identity > div { min-width: 0; }
        .credential-card__identity span:not(.credential-card__marker) { display: block; overflow: hidden; color: var(--muted); font-size: 8px; font-weight: 800; letter-spacing: .09em; text-overflow: ellipsis; text-transform: uppercase; white-space: nowrap; }
        .credential-card__identity h3 { margin: 3px 0 0; overflow: hidden; color: var(--text); font-size: 15px; letter-spacing: -.015em; text-overflow: ellipsis; white-space: nowrap; }
        .credential-card__details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin: 18px 0 0; padding-top: 15px; border-top: 1px solid #eef0f2; }
        .credential-card__details dt, .credential-secret span { margin-bottom: 5px; color: var(--muted); font-size: 8px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .credential-card__details dd { min-width: 0; margin: 0; overflow: hidden; color: #3e4651; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
        .credential-card__details a { display: inline-flex; max-width: 100%; align-items: center; gap: 5px; overflow: hidden; color: #8b630d; text-decoration: none; text-overflow: ellipsis; white-space: nowrap; }
        .credential-card__details a svg { width: 12px; height: 12px; flex: 0 0 12px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }
        .credential-card__empty { color: #9aa0a8; }
        .credential-secret { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 15px; padding: 11px 12px; border: 1px solid #e5e7ea; border-radius: 9px; background: #f7f8f9; }
        .credential-secret > div:first-child { display: grid; min-width: 0; }
        .credential-secret span { margin-bottom: 2px; }
        .credential-secret code { overflow: hidden; color: #2e343d; font-size: 12px; letter-spacing: .07em; text-overflow: ellipsis; white-space: nowrap; }
        .credential-secret__actions { display: flex; flex: 0 0 auto; align-items: center; gap: 5px; }
        .credential-text-button { padding: 5px 7px; border: 0; border-radius: 6px; background: #fff; color: #76540c; cursor: pointer; font: inherit; font-size: 9px; font-weight: 800; }
        .credential-text-button:hover { background: #fff0c8; }
        .credential-card__notes { margin: 12px 0 0; padding-left: 10px; border-left: 2px solid #dfc77e; color: var(--muted); font-size: 10px; line-height: 1.55; white-space: pre-line; }
        .credential-card__footer { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: auto; padding-top: 15px; }
        .credential-card__footer form { margin: 0; }
        .credential-edit { position: relative; }
        .credential-edit > summary, .credential-remove-button { border: 0; background: transparent; cursor: pointer; font: inherit; font-size: 10px; font-weight: 750; list-style: none; }
        .credential-edit > summary { color: #555e69; }
        .credential-edit > summary::-webkit-details-marker { display: none; }
        .credential-edit > summary:hover { color: #8a620a; }
        .credential-remove-button { padding: 0; color: #b33c35; }
        .credential-edit__form { position: absolute; z-index: 10; bottom: calc(100% + 8px); left: 0; display: grid; width: min(440px, calc(100vw - 80px)); max-height: min(630px, 72vh); gap: 9px; overflow-y: auto; padding: 16px; border: 1px solid #dfe3e8; border-radius: 11px; background: #fff; box-shadow: 0 20px 50px rgba(15, 20, 28, .18); }

        .credential-create__form label, .credential-edit__form label { display: grid; gap: 6px; color: #414955; font-size: 10px; font-weight: 750; }
        .credential-create__form label span { color: var(--muted); font-weight: 500; }
        .credential-create__form input, .credential-create__form textarea, .credential-edit__form input, .credential-edit__form textarea { width: 100%; min-height: 43px; padding: 10px 11px; border: 1px solid #dfe3e8; border-radius: 8px; background: #f9fafb; color: var(--text); font: inherit; font-size: 12px; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; }
        .credential-create__form textarea, .credential-edit__form textarea { min-height: 88px; resize: vertical; }
        .credential-create__form input:focus, .credential-create__form textarea:focus, .credential-edit__form input:focus, .credential-edit__form textarea:focus { border-color: #bd8a20; background: #fff; box-shadow: 0 0 0 3px rgba(189, 138, 32, .12); outline: none; }
        .credential-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .credential-form-grid__wide { grid-column: 1 / -1; }
        .credential-form-errors { margin-top: 14px; padding: 11px 13px; border: 1px solid rgba(185, 74, 61, .22); border-radius: 8px; background: rgba(185, 74, 61, .06); color: var(--danger); font-size: 10px; }
        .credential-form-errors ul { margin: 5px 0 0; padding-left: 18px; }

        .credential-empty { display: grid; min-height: 320px; place-items: center; align-content: center; padding: 35px; border: 1px dashed #d9dde2; border-radius: 13px; background: #fafbfc; text-align: center; }
        .credential-empty__icon { display: grid; width: 54px; height: 54px; margin-bottom: 13px; place-items: center; border-radius: 15px; background: #fff3d2; color: #86600f; }
        .credential-empty__icon svg { width: 25px; height: 25px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }
        .credential-empty h3 { margin: 5px 0 0; color: var(--text); font-size: 19px; }
        .credential-empty p { max-width: 480px; margin: 8px 0 17px; color: var(--muted); font-size: 11px; line-height: 1.6; }

        .credential-modal[hidden] { display: none !important; }
        .credential-modal { position: fixed; z-index: 1200; inset: 0; display: grid; padding: 24px; place-items: center; }
        .credential-modal__backdrop { position: absolute; inset: 0; background: rgba(14, 18, 24, .7); opacity: 0; backdrop-filter: blur(4px); transition: opacity .18s ease; }
        .credential-modal__dialog { position: relative; width: min(720px, 100%); max-height: min(780px, calc(100vh - 48px)); overflow-y: auto; border: 1px solid rgba(255, 255, 255, .7); border-radius: 16px; background: #fff; box-shadow: 0 28px 80px rgba(6, 9, 14, .32); opacity: 0; transform: translateY(14px) scale(.985); transition: opacity .18s ease, transform .18s ease; }
        .credential-modal.is-open .credential-modal__backdrop, .credential-modal.is-open .credential-modal__dialog { opacity: 1; }
        .credential-modal.is-open .credential-modal__dialog { transform: translateY(0) scale(1); }
        .credential-modal__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding: 23px 24px 19px; border-bottom: 1px solid #eceef1; }
        .credential-modal__header h2 { margin: 5px 0 0; color: var(--text); font-size: 23px; letter-spacing: -.025em; }
        .credential-modal__header p { margin: 6px 0 0; color: var(--muted); font-size: 11px; line-height: 1.55; }
        .credential-modal__close { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border: 1px solid #e2e5e9; border-radius: 9px; background: #f8f9fa; color: #535b66; cursor: pointer; }
        .credential-modal__close:hover { background: #fff4d7; color: #805b0d; }
        .credential-modal__close svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }
        .credential-modal .credential-create__form { padding: 22px 24px 24px; }
        .credential-modal__footer { display: flex; align-items: center; justify-content: flex-end; gap: 9px; margin-top: 18px; padding-top: 17px; border-top: 1px solid #eceef1; }
        body.credential-modal-open { overflow: hidden; }

        @media (max-width: 1050px) {
            .credential-grid { grid-template-columns: 1fr; }
            .credential-workspace .credential-card { min-height: 0; }
        }
        @media (max-width: 760px) {
            .credential-workspace { padding: 18px; }
            .credential-workspace__header { align-items: flex-start; flex-direction: column; }
            .credential-workspace__actions { width: 100%; flex-wrap: wrap; }
            .credential-workspace__count { margin-right: auto; text-align: left; }
            .credential-workspace__notice { grid-template-columns: auto minmax(0, 1fr); }
            .credential-workspace__notice i { display: none; }
        }
        @media (max-width: 560px) {
            .credential-workspace__actions .ghost-button, .credential-workspace__actions .button { flex: 1 1 auto; justify-content: center; }
            .credential-workspace__count { width: 100%; padding: 0 0 10px; border-right: 0; border-bottom: 1px solid var(--line-soft); }
            .credential-card__header { align-items: flex-start; }
            .credential-card__details, .credential-form-grid { grid-template-columns: 1fr; }
            .credential-form-grid__wide { grid-column: auto; }
            .credential-secret { align-items: flex-start; flex-direction: column; }
            .credential-modal { padding: 10px; }
            .credential-modal__dialog { max-height: calc(100vh - 20px); border-radius: 13px; }
            .credential-modal__header, .credential-modal .credential-create__form { padding: 18px; }
            .credential-modal__footer { align-items: stretch; flex-direction: column-reverse; }
            .credential-modal__footer .button, .credential-modal__footer .ghost-button { width: 100%; justify-content: center; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('[data-credential-card]').forEach((card) => {
            const revealButton = card.querySelector('[data-credential-reveal]');
            const copyButton = card.querySelector('[data-credential-copy]');
            const secret = card.querySelector('[data-credential-secret]');
            const maskedValue = '\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022';
            let revealedValue = null;

            revealButton?.addEventListener('click', async () => {
                if (revealedValue !== null) {
                    revealedValue = null;
                    secret.textContent = maskedValue;
                    revealButton.textContent = 'Reveal';
                    copyButton.hidden = true;
                    return;
                }

                revealButton.disabled = true;
                revealButton.textContent = 'Revealing...';

                try {
                    const response = await fetch(revealButton.dataset.url, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    if (!response.ok) throw new Error('Unable to reveal this credential.');
                    const payload = await response.json();
                    revealedValue = payload.secret;
                    secret.textContent = revealedValue;
                    revealButton.textContent = 'Hide';
                    copyButton.hidden = false;
                } catch (error) {
                    revealButton.textContent = 'Try again';
                } finally {
                    revealButton.disabled = false;
                }
            });

            copyButton?.addEventListener('click', async () => {
                if (revealedValue === null) return;
                try {
                    await navigator.clipboard.writeText(revealedValue);
                    copyButton.textContent = 'Copied';
                    window.setTimeout(() => { copyButton.textContent = 'Copy'; }, 1600);
                } catch {
                    copyButton.textContent = 'Copy failed';
                }
            });
        });

        (() => {
            const modal = document.querySelector('[data-credential-modal]');
            if (!modal) return;

            const dialog = modal.querySelector('[role="dialog"]');
            const focusableSelector = 'button:not([disabled]), a[href], input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';
            let previouslyFocused = null;
            let closeTimer = null;

            const openModal = () => {
                window.clearTimeout(closeTimer);
                previouslyFocused = document.activeElement;
                modal.hidden = false;
                document.body.classList.add('credential-modal-open');
                window.requestAnimationFrame(() => {
                    modal.classList.add('is-open');
                    const firstInput = modal.querySelector('input:not([type="hidden"])');
                    (firstInput || dialog).focus();
                });
            };

            const closeModal = () => {
                modal.classList.remove('is-open');
                document.body.classList.remove('credential-modal-open');
                closeTimer = window.setTimeout(() => {
                    modal.hidden = true;
                    previouslyFocused?.focus();
                }, 180);
            };

            document.querySelectorAll('[data-credential-modal-open]').forEach((button) => button.addEventListener('click', openModal));
            modal.querySelectorAll('[data-credential-modal-close]').forEach((button) => button.addEventListener('click', closeModal));

            modal.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeModal();
                    return;
                }
                if (event.key !== 'Tab') return;
                const focusable = [...modal.querySelectorAll(focusableSelector)];
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            });

            if (modal.hasAttribute('data-open-on-load')) openModal();
        })();
    </script>
@endpush
