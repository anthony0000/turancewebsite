<section class="credential-workspace" id="project-credentials">
    <header class="credential-workspace__header">
        <div class="credential-workspace__intro">
            <span class="eyebrow">Secure handover vault</span>
            <h2>Project credentials</h2>
            <p>Keep every project login, key, and access note in one encrypted handover workspace.</p>
        </div>

        <div class="credential-workspace__actions">
            <div class="credential-workspace__count" aria-label="Stored credential count">
                <strong>{{ number_format($credentials->count()) }}</strong>
                <span>{{ \Illuminate\Support\Str::plural('credential', $credentials->count()) }}</span>
            </div>
            @if ($credentials->isNotEmpty())
                <a class="ghost-button credential-export-button" href="{{ route('admin.credentials.pdf', $project) }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                    Download handover PDF
                </a>
            @endif
            <button class="button credential-add-button" type="button" data-credential-modal-open aria-controls="credential-create-modal">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add credential
            </button>
        </div>
    </header>

    <div class="credential-workspace__notice">
        <span aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M12 3 20 6v5c0 5.1-3.4 8.6-8 10-4.6-1.4-8-4.9-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/></svg>
        </span>
        <div><strong>Encrypted at rest</strong><small>Secrets are hidden by default and every reveal is recorded in the project activity log.</small></div>
        <i>{{ $credentials->isNotEmpty() ? 'Handover ready' : 'Add the first access detail' }}</i>
    </div>

    @if ($credentials->isNotEmpty())
        <div class="credential-grid">
            @foreach ($credentials as $credential)
                <article class="credential-card" data-credential-card>
                    <header class="credential-card__header">
                        <div class="credential-card__identity">
                            <span class="credential-card__marker" aria-hidden="true">{{ strtoupper(substr($credential->service_name, 0, 1)) }}</span>
                            <div>
                                <span>{{ $credential->credential_type }}</span>
                                <h3>{{ $credential->service_name }}</h3>
                            </div>
                        </div>
                        <span class="file-access-badge file-access-badge--private">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            Encrypted
                        </span>
                    </header>

                    <dl class="credential-card__details">
                        <div>
                            <dt>Access URL</dt>
                            <dd>
                                @if ($credential->access_url)
                                    <a href="{{ $credential->access_url }}" target="_blank" rel="noopener noreferrer">
                                        {{ preg_replace('#^https?://#', '', $credential->access_url) }}
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5h5v5M19 5l-8 8M19 13v6H5V5h6"/></svg>
                                    </a>
                                @else
                                    <span class="credential-card__empty">Not provided</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt>Username / email</dt>
                            <dd>{{ $credential->username ?: 'Not provided' }}</dd>
                        </div>
                    </dl>

                    <div class="credential-secret">
                        <div>
                            <span>Password / secret</span>
                            <code data-credential-secret>&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;</code>
                        </div>
                        <div class="credential-secret__actions">
                            <button class="credential-text-button" type="button" data-credential-reveal data-url="{{ route('admin.credentials.reveal', [$project, $credential]) }}">Reveal</button>
                            <button class="credential-text-button" type="button" data-credential-copy hidden>Copy</button>
                        </div>
                    </div>

                    @if ($credential->notes)
                        <p class="credential-card__notes">{{ $credential->notes }}</p>
                    @endif

                    <footer class="credential-card__footer">
                        <details class="credential-edit">
                            <summary>Edit details</summary>
                            <form method="POST" action="{{ route('admin.credentials.update', [$project, $credential]) }}" class="credential-edit__form">
                                @csrf
                                @method('PUT')
                                <label>System or service
                                    <input type="text" name="service_name" value="{{ $credential->service_name }}" required maxlength="255">
                                </label>
                                <label>Credential type
                                    <input type="text" name="credential_type" value="{{ $credential->credential_type }}" required maxlength="80">
                                </label>
                                <label>Access URL
                                    <input type="url" name="access_url" value="{{ $credential->access_url }}" maxlength="2048" placeholder="https://">
                                </label>
                                <label>Username or email
                                    <input type="text" name="username" value="{{ $credential->username }}" maxlength="1000" autocomplete="off">
                                </label>
                                <label>New password or secret
                                    <input type="password" name="secret" maxlength="10000" autocomplete="new-password" placeholder="Leave blank to keep the current secret">
                                </label>
                                <label>Notes
                                    <textarea name="notes" rows="3" maxlength="5000">{{ $credential->notes }}</textarea>
                                </label>
                                <button class="button" type="submit">Save changes</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('admin.credentials.destroy', [$project, $credential]) }}" onsubmit="return confirm('Permanently remove this project credential?');">
                            @csrf
                            @method('DELETE')
                            <button class="credential-remove-button" type="submit">Remove</button>
                        </form>
                    </footer>
                </article>
            @endforeach
        </div>
    @else
        <div class="credential-empty">
            <span class="credential-empty__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 11V8a5 5 0 0 1 10 0v3M5 11h14v10H5z"/><path d="M12 15v2"/></svg>
            </span>
            <span class="eyebrow">Empty vault</span>
            <h3>No credentials stored yet</h3>
            <p>Add hosting, domain, CMS, repository, analytics, or other access details. The handover PDF becomes available after the first entry.</p>
            <button class="button" type="button" data-credential-modal-open aria-controls="credential-create-modal">Add first credential</button>
        </div>
    @endif
</section>

<div class="credential-modal" id="credential-create-modal" data-credential-modal @if (old('_credential_action') === 'create' && $errors->any()) data-open-on-load @endif hidden>
    <div class="credential-modal__backdrop" data-credential-modal-close></div>
    <section class="credential-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="credential-create-title" aria-describedby="credential-create-description" tabindex="-1">
        <header class="credential-modal__header">
            <div>
                <span class="eyebrow">New access detail</span>
                <h2 id="credential-create-title">Add a credential</h2>
                <p id="credential-create-description">The secret and private account details will be encrypted before storage.</p>
            </div>
            <button class="credential-modal__close" type="button" data-credential-modal-close aria-label="Close add credential dialog">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17"/></svg>
            </button>
        </header>

        <form method="POST" action="{{ route('admin.credentials.store', $project) }}" class="credential-create__form">
            @csrf
            <input type="hidden" name="_credential_action" value="create">
            <div class="credential-form-grid">
                <label for="credential-service-name">System or service
                    <input id="credential-service-name" type="text" name="service_name" value="{{ old('service_name') }}" required maxlength="255" placeholder="e.g. Production hosting">
                </label>
                <label for="credential-type">Credential type
                    <input id="credential-type" type="text" name="credential_type" value="{{ old('credential_type', 'Login') }}" required maxlength="80" placeholder="e.g. Login, API key, SSH">
                </label>
                <label class="credential-form-grid__wide" for="credential-url">Access URL <span>(optional)</span>
                    <input id="credential-url" type="url" name="access_url" value="{{ old('access_url') }}" maxlength="2048" placeholder="https://">
                </label>
                <label for="credential-username">Username or email <span>(optional)</span>
                    <input id="credential-username" type="text" name="username" value="{{ old('username') }}" maxlength="1000" autocomplete="off">
                </label>
                <label for="credential-secret">Password or secret
                    <input id="credential-secret" type="password" name="secret" required maxlength="10000" autocomplete="new-password">
                </label>
                <label class="credential-form-grid__wide" for="credential-notes">Notes <span>(optional)</span>
                    <textarea id="credential-notes" name="notes" rows="4" maxlength="5000" placeholder="Recovery steps, account owner, or handover instructions.">{{ old('notes') }}</textarea>
                </label>
            </div>

            @if (old('_credential_action') === 'create' && $errors->any())
                <div class="credential-form-errors" role="alert">
                    <strong>Check the credential details.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <footer class="credential-modal__footer">
                <button class="ghost-button" type="button" data-credential-modal-close>Cancel</button>
                <button class="button" type="submit">Store encrypted credential</button>
            </footer>
        </form>
    </section>
</div>
