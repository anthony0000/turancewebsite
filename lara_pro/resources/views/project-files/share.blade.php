@extends('layouts.master')

@section('title', $projectFile->original_name.' · Shared project file')

@section('content')
    <style>
        .shared-file-page { max-width: 780px; margin: 0 auto; padding: 112px 24px 96px; }
        .shared-file-card { padding: clamp(28px, 6vw, 64px); border: 1px solid rgba(184, 134, 11, .2); border-radius: 24px; background: rgba(255, 255, 255, .92); box-shadow: 0 24px 70px rgba(36, 25, 10, .1); }
        .shared-file-mark { display: grid; width: 56px; height: 56px; place-items: center; margin-bottom: 26px; border-radius: 16px; background: #b8860b; color: #fff; font-weight: 800; letter-spacing: .08em; }
        .shared-file-card h1 { max-width: 620px; margin: 0; color: #24190a; font-size: clamp(32px, 6vw, 58px); line-height: 1.02; overflow-wrap: anywhere; }
        .shared-file-card p { max-width: 600px; margin: 20px 0 0; color: #786a57; font-size: 16px; line-height: 1.7; }
        .shared-file-meta { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 34px 0; }
        .shared-file-meta div { padding: 16px; border-radius: 14px; background: #fff8ea; }
        .shared-file-meta span { display: block; color: #786a57; font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .shared-file-meta strong { display: block; margin-top: 7px; color: #24190a; overflow-wrap: anywhere; }
        .shared-file-actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .shared-file-actions a { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; padding: 0 20px; border-radius: 12px; background: #b8860b; color: #fff; font-weight: 700; text-decoration: none; }
        .shared-file-actions a.secondary { border: 1px solid rgba(184, 134, 11, .3); background: transparent; color: #8f6508; }
        .shared-file-footnote { margin-top: 30px !important; font-size: 13px !important; }
        @media (max-width: 620px) { .shared-file-meta { grid-template-columns: 1fr; } .shared-file-page { padding-top: 80px; } }
    </style>

    <main class="shared-file-page">
        <section class="shared-file-card">
            <div class="shared-file-mark" aria-hidden="true">TT</div>
            <span class="eyebrow">Secure project handoff</span>
            <h1>{{ $projectFile->original_name }}</h1>
            <p>{{ $projectFile->description ?: 'A project file shared securely by Turance Technologies.' }}</p>

            <div class="shared-file-meta">
                <div><span>Project</span><strong>{{ $projectFile->project->name }}</strong></div>
                <div><span>File type</span><strong>{{ $projectFile->fileKind() }}</strong></div>
                <div><span>Size</span><strong>{{ $projectFile->sizeLabel() }}</strong></div>
            </div>

            <div class="shared-file-actions">
                <a href="{{ route('project-files.download', $projectFile->share_token) }}">Download file</a>
                <a class="secondary" href="{{ route('home') }}">Visit Turance Technologies</a>
            </div>
            <p class="shared-file-footnote">This link only gives access to this file. It does not expose the rest of the project workspace.</p>
        </section>
    </main>
@endsection
