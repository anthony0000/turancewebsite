<section class="panel panel-padded project-credentials" id="project-credentials">
    <div class="panel-head panel-head--row project-credentials__head">
        <div>
            <span class="eyebrow">Secure handover vault</span>
            <h2>Project credentials</h2>
            <p>Store access details encrypted at rest, reveal a secret only when needed, and export the complete handover on Turance Technologies letterhead.</p>
        </div>
        <div class="project-credentials__head-actions">
            <span class="admin-pill">{{ number_format($credentials->count()) }} {{ \Illuminate\Support\Str::plural('entry', $credentials->count()) }}</span>
            @if ($credentials->isNotEmpty())
                <a class="button" href="{{ route('admin.credentials.pdf', $project) }}">Download handover PDF</a>
            @endif
        </div>
    </div>

    <div class="project-credentials__layout">
        <div>
            @if ($credentials->isNotEmpty())
                <div class="credential-list">
                    @foreach ($credentials as $credential)
                        <article class="credential-card" data-credential-card>
                            <div class="credential-card__marker" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="credential-card__body">
                                <div class="credential-card__title">
                                    <div>
                                        <span>{{ $credential->credential_type }}</span>
                                        <h3>{{ $credential->service_name }}</h3>
                                    </div>
                                    <span class="file-access-badge file-access-badge--private">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                        Encrypted
                                    </span>
                                </div>

                                <dl class="credential-card__details">
                                    <div>
                                        <dt>Access URL</dt>
                                        <dd>
                                            @if ($credential->access_url)
                                                <a href="{{ $credential->access_url }}" target="_blank" rel="noopener noreferrer">{{ $credential->access_url }}</a>
                                            @else
                                                <span>Not provided</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Username / email</dt>
                                        <dd>{{ $credential->username ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Password / secret</dt>
                                        <dd class="credential-secret">
                                            <code data-credential-secret>••••••••••••</code>
                                            <button class="ghost-button" type="button" data-credential-reveal data-url="{{ route('admin.credentials.reveal', [$project, $credential]) }}">Reveal</button>
                                            <button class="ghost-button" type="button" data-credential-copy hidden>Copy</button>
                                        </dd>
                                    </div>
                                </dl>

                                @if ($credential->notes)
                                    <p class="credential-card__notes">{{ $credential->notes }}</p>
                                @endif

                                <div class="credential-card__actions">
                                    <details class="credential-edit">
                                        <summary class="ghost-button">Update</summary>
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
                                        <button class="file-delete-button" type="submit">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="project-files-empty credential-empty">
                    <span class="project-files-empty__icon" aria-hidden="true">⌁</span>
                    <h3>No project credentials stored yet.</h3>
                    <p>Add hosting, domain, CMS, email, analytics, repository, or other access details as the project progresses. The handover PDF becomes available after the first entry.</p>
                </div>
            @endif
        </div>

        <aside class="credential-create">
            <span class="eyebrow">Add access detail</span>
            <h3>Store a credential</h3>
            <p>Secrets, usernames, and private notes are encrypted before they are written to the database.</p>

            <form method="POST" action="{{ route('admin.credentials.store', $project) }}" class="credential-create__form">
                @csrf
                <label for="credential-service-name">System or service
                    <input id="credential-service-name" type="text" name="service_name" value="{{ old('service_name') }}" required maxlength="255" placeholder="e.g. Production hosting">
                </label>
                <label for="credential-type">Credential type
                    <input id="credential-type" type="text" name="credential_type" value="{{ old('credential_type', 'Login') }}" required maxlength="80" placeholder="e.g. Login, API key, SSH">
                </label>
                <label for="credential-url">Access URL <span>(optional)</span>
                    <input id="credential-url" type="url" name="access_url" value="{{ old('access_url') }}" maxlength="2048" placeholder="https://">
                </label>
                <label for="credential-username">Username or email <span>(optional)</span>
                    <input id="credential-username" type="text" name="username" value="{{ old('username') }}" maxlength="1000" autocomplete="off">
                </label>
                <label for="credential-secret">Password or secret
                    <input id="credential-secret" type="password" name="secret" required maxlength="10000" autocomplete="new-password">
                </label>
                <label for="credential-notes">Notes <span>(optional)</span>
                    <textarea id="credential-notes" name="notes" rows="4" maxlength="5000" placeholder="Recovery steps, account owner, or handover instructions.">{{ old('notes') }}</textarea>
                </label>

                @if ($errors->any())
                    <div class="credential-form-errors" role="alert">
                        <strong>Check the credential details.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button class="button" type="submit">Store encrypted credential</button>
            </form>
        </aside>
    </div>
</section>
