@once
    <div class="file-preview-modal" data-file-preview-modal hidden>
        <button class="file-preview-modal__backdrop" type="button" data-file-preview-close aria-label="Close file preview"></button>
        <section class="file-preview-dialog" role="dialog" aria-modal="true" aria-labelledby="file-preview-title">
            <header class="file-preview-dialog__head">
                <div>
                    <span class="eyebrow">File preview</span>
                    <h2 id="file-preview-title" data-file-preview-title>Document preview</h2>
                </div>
                <div class="file-preview-dialog__actions">
                    <a class="ghost-button" href="#" data-file-preview-download>Download</a>
                    <button class="file-preview-dialog__close" type="button" data-file-preview-close aria-label="Close preview">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </div>
            </header>
            <div class="file-preview-dialog__body" data-file-preview-body aria-busy="true">
                <div class="file-preview-dialog__loading" data-file-preview-loading>
                    <span aria-hidden="true"></span>
                    <strong>Loading preview…</strong>
                </div>
                <iframe data-file-preview-frame title="Document preview"></iframe>
            </div>
        </section>
    </div>

    @push('styles')
        <style>
            body.file-preview-open{overflow:hidden}.file-preview-modal[hidden]{display:none}.file-preview-modal{position:fixed;z-index:1100;inset:0;display:grid;place-items:center;padding:22px}.file-preview-modal__backdrop{position:absolute;inset:0;width:100%;height:100%;border:0;background:rgba(22,20,17,.68);backdrop-filter:blur(4px);cursor:default}.file-preview-dialog{position:relative;z-index:1;display:grid;width:min(1120px,100%);height:min(820px,calc(100vh - 44px));grid-template-rows:auto minmax(0,1fr);overflow:hidden;border:1px solid #e2ddd4;border-radius:18px;background:#fff;box-shadow:0 32px 90px rgba(20,18,14,.32)}.file-preview-dialog__head{display:flex;min-height:76px;align-items:center;justify-content:space-between;gap:20px;padding:14px 18px 14px 22px;border-bottom:1px solid #e9e5de;background:#fff}.file-preview-dialog__head h2{max-width:min(650px,55vw);margin:3px 0 0;overflow:hidden;font-size:18px;text-overflow:ellipsis;white-space:nowrap}.file-preview-dialog__actions{display:flex;align-items:center;gap:9px}.file-preview-dialog__actions .ghost-button{min-height:38px}.file-preview-dialog__close{display:grid;width:38px;height:38px;place-items:center;padding:0;border:1px solid #e2ddd4;border-radius:9px;background:#fff;color:#71695f;cursor:pointer}.file-preview-dialog__close:hover{border-color:#c9a44f;background:#fff9eb;color:#8b6509}.file-preview-dialog__close svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round}.file-preview-dialog__body{position:relative;min-height:0;background:#edeae4}.file-preview-dialog__body iframe{display:block;width:100%;height:100%;border:0;background:#f7f5f1}.file-preview-dialog__loading{position:absolute;z-index:2;inset:0;display:grid;place-content:center;justify-items:center;gap:12px;background:#f7f5f1;color:#676158;font-size:12px}.file-preview-dialog__loading span{width:30px;height:30px;border:3px solid #e2d9c4;border-top-color:#a97908;border-radius:50%;animation:file-preview-spin .8s linear infinite}.file-preview-dialog__body:not([aria-busy=true]) .file-preview-dialog__loading{display:none}@keyframes file-preview-spin{to{transform:rotate(360deg)}}@media(max-width:680px){.file-preview-modal{padding:8px}.file-preview-dialog{height:calc(100vh - 16px);border-radius:13px}.file-preview-dialog__head{min-height:68px;padding:11px 12px 11px 15px}.file-preview-dialog__head h2{max-width:48vw;font-size:15px}.file-preview-dialog__actions .ghost-button{display:none}.file-preview-dialog__close{width:36px;height:36px}}
        </style>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const modal = document.querySelector('[data-file-preview-modal]');
                const frame = modal?.querySelector('[data-file-preview-frame]');
                const body = modal?.querySelector('[data-file-preview-body]');
                const title = modal?.querySelector('[data-file-preview-title]');
                const download = modal?.querySelector('[data-file-preview-download]');
                let activeTrigger = null;

                if (!modal || !frame) return;

                const closePreview = () => {
                    modal.hidden = true;
                    document.body.classList.remove('file-preview-open');
                    frame.removeAttribute('src');
                    body?.setAttribute('aria-busy', 'true');
                    activeTrigger?.focus();
                };

                document.querySelectorAll('[data-file-preview]').forEach((trigger) => {
                    trigger.addEventListener('click', (event) => {
                        event.preventDefault();
                        activeTrigger = trigger;
                        if (title) title.textContent = trigger.dataset.fileName || 'Document preview';
                        if (download) download.href = trigger.dataset.downloadUrl || trigger.href;
                        body?.setAttribute('aria-busy', 'true');
                        modal.hidden = false;
                        document.body.classList.add('file-preview-open');
                        frame.src = trigger.href;
                        modal.querySelector('[data-file-preview-close]')?.focus();
                    });
                });

                frame.addEventListener('load', () => body?.setAttribute('aria-busy', 'false'));
                modal.querySelectorAll('[data-file-preview-close]').forEach((button) => button.addEventListener('click', closePreview));
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.hidden) closePreview();
                });
            })();
        </script>
    @endpush
@endonce
