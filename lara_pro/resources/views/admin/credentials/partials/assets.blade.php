@push('styles')
    <style>
        .project-credentials__head { align-items: flex-start; }
        .project-credentials__head-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 10px; }
        .project-credentials__layout { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(300px, .7fr); gap: 22px; align-items: start; }
        .credential-list { display: grid; gap: 12px; }
        .credential-card { display: grid; grid-template-columns: 42px minmax(0, 1fr); gap: 13px; padding: 15px; border: 1px solid var(--line-soft); border-radius: 9px; background: linear-gradient(135deg, var(--surface), var(--surface-soft)); }
        .credential-card__marker { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 12px; background: var(--primary-soft); color: var(--primary-strong); font-size: 11px; font-weight: 800; }
        .credential-card__body { min-width: 0; }
        .credential-card__title { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .credential-card__title span:first-child { color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .credential-card__title h3 { margin: 3px 0 0; color: var(--text); font-size: 15px; }
        .credential-card__details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 16px; margin: 14px 0 0; }
        .credential-card__details > div:last-child { grid-column: 1 / -1; }
        .credential-card__details dt { margin-bottom: 4px; color: var(--muted); font-size: 9px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        .credential-card__details dd { min-width: 0; margin: 0; overflow-wrap: anywhere; color: var(--muted-strong); font-size: 12px; }
        .credential-card__details a { color: var(--primary-strong); }
        .credential-secret { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .credential-secret code { min-width: 130px; padding: 8px 10px; overflow-wrap: anywhere; border: 1px solid var(--line); border-radius: 6px; background: #f7f5f1; color: #3f454c; font-size: 11px; }
        .credential-secret .ghost-button { min-height: 32px; padding-inline: 10px; font-size: 10px; }
        .credential-card__notes { margin: 13px 0 0; padding: 10px 11px; border-left: 2px solid var(--line); background: rgba(255, 255, 255, .62); color: var(--muted); font-size: 11px; line-height: 1.55; white-space: pre-line; }
        .credential-card__actions { display: flex; align-items: flex-start; gap: 8px; margin-top: 12px; }
        .credential-card__actions form { margin: 0; }
        .credential-edit { position: relative; }
        .credential-edit > summary { display: inline-flex; min-height: 34px; align-items: center; padding: 0 11px; cursor: pointer; font-size: 11px; list-style: none; }
        .credential-edit > summary::-webkit-details-marker { display: none; }
        .credential-edit__form { display: grid; width: min(460px, calc(100vw - 70px)); gap: 9px; margin-top: 8px; padding: 14px; border: 1px solid var(--line-soft); border-radius: 9px; background: var(--surface); box-shadow: 0 10px 25px rgba(24, 29, 35, .08); }
        .credential-create { padding: 18px; border: 1px solid var(--line-soft); border-radius: 10px; background: var(--surface-soft); }
        .credential-create h3 { margin: 0; font-size: 17px; }
        .credential-create > p { margin: 7px 0 0; color: var(--muted); font-size: 11px; line-height: 1.55; }
        .credential-create__form, .credential-edit__form { margin-top: 16px; }
        .credential-create__form { display: grid; gap: 10px; }
        .credential-create__form label, .credential-edit__form label { display: grid; gap: 5px; color: var(--muted-strong); font-size: 10px; font-weight: 700; }
        .credential-create__form label span { color: var(--muted); font-weight: 500; }
        .credential-create__form input, .credential-create__form textarea, .credential-edit__form input, .credential-edit__form textarea { width: 100%; min-height: 40px; padding: 9px 10px; border: 1px solid var(--line); border-radius: 7px; background: var(--surface); color: var(--text); font: inherit; font-size: 12px; }
        .credential-create__form textarea, .credential-edit__form textarea { min-height: 76px; resize: vertical; }
        .credential-form-errors { padding: 10px 12px; border: 1px solid rgba(185, 74, 61, .22); border-radius: 7px; background: rgba(185, 74, 61, .06); color: var(--danger); font-size: 11px; }
        .credential-form-errors ul { margin: 5px 0 0; padding-left: 18px; }
        .credential-empty { min-height: 260px; }
        @media (max-width: 1000px) { .project-credentials__layout { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .project-credentials__head { display: block; } .project-credentials__head-actions { justify-content: flex-start; margin-top: 14px; } .credential-card { grid-template-columns: 1fr; } .credential-card__details { grid-template-columns: 1fr; } .credential-card__details > div:last-child { grid-column: auto; } }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('[data-credential-card]').forEach((card) => {
            const revealButton = card.querySelector('[data-credential-reveal]');
            const copyButton = card.querySelector('[data-credential-copy]');
            const secret = card.querySelector('[data-credential-secret]');
            const maskedValue = '••••••••••••';
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
                revealButton.textContent = 'Revealing…';

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
    </script>
@endpush
