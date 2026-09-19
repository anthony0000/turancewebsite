<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Credential Collation | {{ $project->project_number }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        @include('admin.partials.document-fonts')
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; color: #17202b; background: #fff; font-family: 'Urbanist', sans-serif; font-size: 10px; line-height: 1.45; }
        .letterhead-background { position: fixed; z-index: 0; inset: 0; width: 210mm; height: 297mm; object-fit: contain; object-position: center top; }
        .document { position: relative; z-index: 1; padding: 68mm 18mm 43mm; }
        .document-header { margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #b8860b; }
        .confidential { margin: 0 0 6px; color: #9b6a00; font-size: 8px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; font-size: 22px; line-height: 1.1; }
        .subtitle { margin: 6px 0 0; color: #59636f; }
        .project-meta { width: 100%; margin: 14px 0 18px; border-collapse: collapse; }
        .project-meta td { width: 50%; padding: 8px 10px; border: 1px solid #e2ded6; vertical-align: top; }
        .project-meta span { display: block; color: #7c838b; font-size: 7.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .project-meta strong { display: block; margin-top: 3px; font-size: 10px; }
        .security-note { margin: 0 0 16px; padding: 9px 11px; border-left: 3px solid #b8860b; background: #fff9e9; color: #504a3d; }
        .credential { margin-bottom: 12px; padding: 11px 12px; border: 1px solid #dedbd5; border-radius: 5px; page-break-inside: avoid; background: rgba(255,255,255,.93); }
        .credential-head { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
        .credential-head td { padding: 0; vertical-align: top; }
        .credential-index { width: 26px; color: #b8860b; font-size: 8px; font-weight: 700; }
        .credential h2 { margin: 0; font-size: 13px; }
        .credential-type { color: #737b84; font-size: 8px; text-align: right; text-transform: uppercase; }
        .detail-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .detail-table th, .detail-table td { padding: 5px 7px; border-top: 1px solid #ece9e4; text-align: left; vertical-align: top; overflow-wrap: anywhere; word-wrap: break-word; }
        .detail-table th { width: 24%; color: #737b84; font-size: 7.5px; letter-spacing: .05em; text-transform: uppercase; }
        .secret { color: #111820; font-family: 'DejaVu Sans Mono', monospace; font-size: 9px; white-space: pre-wrap; }
        .notes { white-space: pre-line; }
        .empty { color: #969ba1; }
        .footer-note { margin: 16px 0 0; color: #707780; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    @if ($backgroundSrc)
        <img class="letterhead-background" src="{{ $backgroundSrc }}" alt="">
    @endif

    <main class="document">
        <header class="document-header">
            <p class="confidential">Confidential · Project handover</p>
            <h1>Project Credential Collation</h1>
            <p class="subtitle">A consolidated access record for {{ $project->name }}.</p>
        </header>

        <table class="project-meta">
            <tr>
                <td><span>Project</span><strong>{{ $project->name }}</strong></td>
                <td><span>Project number</span><strong>{{ $project->project_number }}</strong></td>
            </tr>
            <tr>
                <td><span>Client</span><strong>{{ $project->client_company ?: ($project->client_name ?: 'Not provided') }}</strong></td>
                <td><span>Generated</span><strong>{{ $generatedAt->format('F j, Y · g:i A') }}</strong></td>
            </tr>
        </table>

        <p class="security-note"><strong>Handle securely.</strong> This document contains live access details. Share it only with the authorised recipient, store it in an approved secure location, and rotate temporary passwords after handover.</p>

        @foreach ($credentials as $credential)
            <section class="credential">
                <table class="credential-head">
                    <tr>
                        <td class="credential-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td><h2>{{ $credential->service_name }}</h2></td>
                        <td class="credential-type">{{ $credential->credential_type }}</td>
                    </tr>
                </table>
                <table class="detail-table">
                    <tr><th>Access URL</th><td class="{{ blank($credential->access_url) ? 'empty' : '' }}">{{ $credential->access_url ?: 'Not provided' }}</td></tr>
                    <tr><th>Username / email</th><td class="{{ blank($credential->username) ? 'empty' : '' }}">{{ $credential->username ?: 'Not provided' }}</td></tr>
                    <tr><th>Password / secret</th><td class="secret">{{ $credential->secret }}</td></tr>
                    @if (filled($credential->notes))
                        <tr><th>Notes</th><td class="notes">{{ $credential->notes }}</td></tr>
                    @endif
                </table>
            </section>
        @endforeach

        <p class="footer-note">Prepared by {{ $generatedBy }} · {{ $credentials->count() }} {{ \Illuminate\Support\Str::plural('credential', $credentials->count()) }} · Turance Technologies</p>
    </main>
</body>
</html>
