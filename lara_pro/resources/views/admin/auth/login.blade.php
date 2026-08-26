@extends('admin.layouts.app')

@section('title', 'Admin Login | Invoice Generator')

@push('styles')
    <style>
        body.is-auth {
            min-height: 100dvh;
            overflow-x: hidden;
            background:
                radial-gradient(ellipse 42% 52% at 17% 20%, rgba(184, 134, 11, .08), transparent 72%),
                radial-gradient(ellipse 36% 48% at 84% 78%, rgba(93, 83, 135, .09), transparent 72%),
                #f8f8fc;
        }

        body.is-auth::before,
        body.is-auth::after {
            position: fixed;
            z-index: 0;
            display: block;
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        body.is-auth::before {
            top: -148px;
            left: 50%;
            width: 430px;
            height: 290px;
            background: rgba(184, 134, 11, .055);
            filter: blur(1px);
            transform: translateX(-50%) rotate(-17deg);
        }

        body.is-auth::after {
            top: 12%;
            left: 12%;
            width: 12px;
            height: 12px;
            background: rgba(184, 134, 11, .32);
            box-shadow:
                740px 10px 0 rgba(184, 134, 11, .46),
                985px 350px 0 rgba(93, 83, 135, .2),
                185px 500px 0 rgba(184, 134, 11, .2);
        }

        body.is-auth .admin-shell--auth {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: none;
            min-height: 100dvh;
            padding: 24px;
            place-items: start center;
        }

        body.is-auth .admin-workspace,
        body.is-auth .admin-main {
            width: 100%;
        }

        body.is-auth .admin-workspace { max-width: 1040px; }
        body.is-auth .admin-main { gap: 0; }

        .login-page {
            width: min(100%, 760px);
            margin: clamp(42px, 11vh, 98px) auto 0;
        }

        .login-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            min-height: 470px;
            overflow: hidden;
            border: 1px solid #e2e4e9;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 26px 68px rgba(39, 43, 53, .13);
        }

        .login-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            padding: 48px 50px;
            background: #fff;
        }

        .login-form-head { margin-bottom: 26px; }

        .login-form-head h1 {
            margin: 0;
            color: #17191d;
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 650;
            letter-spacing: -.045em;
            line-height: 1.05;
        }

        .login-form-head p {
            max-width: 250px;
            margin: 10px 0 0;
            color: #858b94;
            font-size: 11px;
            line-height: 1.55;
        }

        .login-form { display: grid; gap: 15px; }
        .login-form .field { display: grid; gap: 6px; }

        .login-form label {
            color: #17191d;
            font-size: 10px;
            font-weight: 700;
        }

        .login-form input {
            width: 100%;
            min-height: 42px;
            padding: 0 12px;
            border: 1px solid #dfe3e8;
            border-radius: 7px;
            background: #fbfcfd;
            color: #17191d;
            font-size: 11px;
            outline: none;
            transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .login-form input::placeholder { color: #a6adb6; }

        .login-form input:focus {
            border-color: rgba(184, 134, 11, .62);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(184, 134, 11, .1);
        }

        .login-form .button {
            width: 100%;
            min-height: 44px;
            margin-top: 2px;
            border: 0;
            border-radius: 7px;
            background: #1c1e22;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            box-shadow: none;
        }

        .login-form .button:hover { background: #34373c; }

        .login-visual-panel {
            position: relative;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 22px;
            min-width: 0;
            padding: 32px 30px;
            overflow: hidden;
            border-left: 1px solid rgba(255, 255, 255, .12);
            background:
                radial-gradient(circle at 72% 30%, rgba(184, 134, 11, .25), transparent 28%),
                linear-gradient(135deg, #353126, #1c2026 58%, #17191d);
        }

        .login-visual-panel::before {
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(135deg, rgba(255, 255, 255, .055) 0 1px, transparent 1px 19px);
            content: "";
            opacity: .72;
            pointer-events: none;
        }

        .login-visual-panel::after {
            position: absolute;
            right: -104px;
            bottom: -126px;
            width: 310px;
            height: 310px;
            border: 1px solid rgba(212, 175, 55, .3);
            border-radius: 50%;
            content: "";
            transform: scaleX(1.4) rotate(-18deg);
        }

        .login-visual-panel > * { position: relative; z-index: 1; }

        .login-visual-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-visual-brand .admin-brand-mark {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(145deg, #d5a72c, #8f6508);
            box-shadow: none;
        }

        .login-visual-brand strong {
            display: block;
            color: #fff;
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
        }

        .login-visual-brand span {
            display: block;
            margin-top: 4px;
            color: #d5a72c;
            font-size: 8px;
            font-weight: 750;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .login-scene {
            position: relative;
            min-height: 300px;
            border: 1px solid rgba(255, 255, 255, .17);
            border-radius: 15px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .02));
        }

        .login-scene::before {
            position: absolute;
            inset: 24px;
            border: 1px solid rgba(212, 175, 55, .25);
            border-radius: 11px;
            content: "";
        }

        .login-scene-orbit {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 205px;
            height: 205px;
            border: 1px solid rgba(212, 175, 55, .42);
            border-radius: 50%;
            transform: translate(-50%, -50%) scaleX(1.5) rotate(-20deg);
        }

        .login-scene-orbit::after {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d5a72c;
            box-shadow: 0 0 0 7px rgba(212, 175, 55, .12), 0 0 28px rgba(212, 175, 55, .75);
            content: "";
            transform: translate(-50%, -50%);
        }

        .login-scene-window {
            position: absolute;
            top: 52%;
            left: 50%;
            width: calc(100% - 54px);
            height: 172px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 10px;
            background: #f8fafc;
            box-shadow: 0 20px 35px rgba(0, 0, 0, .25);
            transform: translate(-50%, -50%) rotate(-4deg);
        }

        .login-scene-window__top {
            display: flex;
            align-items: center;
            gap: 4px;
            height: 25px;
            padding: 0 9px;
            border-bottom: 1px solid #e2e6eb;
            background: #fff;
        }

        .login-scene-window__top i {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #cbd1d8;
        }

        .login-scene-window__top i:first-child { background: #d5a72c; }

        .login-scene-window__body {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            height: calc(100% - 25px);
        }

        .login-scene-window__rail {
            border-right: 1px solid #e2e6eb;
            background:
                linear-gradient(#d5a72c 0 0) 9px 20px / 15px 4px no-repeat,
                repeating-linear-gradient(180deg, #d6dce2 0 4px, transparent 4px 14px) 9px 42px / 15px 4px no-repeat,
                #f0f3f5;
        }

        .login-scene-window__content {
            position: relative;
            padding: 19px 17px;
            background: linear-gradient(145deg, #fff, #f1f4f7);
        }

        .login-scene-window__content::before,
        .login-scene-window__content::after {
            position: absolute;
            left: 17px;
            right: 17px;
            height: 1px;
            background: #e2e6eb;
            content: "";
        }

        .login-scene-window__content::before { top: 52px; }
        .login-scene-window__content::after { top: 83px; }

        .login-scene-line {
            width: 42%;
            height: 7px;
            border-radius: 999px;
            background: #42474f;
        }

        .login-scene-bars {
            position: absolute;
            right: 17px;
            bottom: 14px;
            display: flex;
            align-items: end;
            gap: 5px;
            height: 68px;
        }

        .login-scene-bars i {
            display: block;
            width: 10px;
            height: 30px;
            border-radius: 3px 3px 0 0;
            background: #262a30;
        }

        .login-scene-bars i:nth-child(2) { height: 47px; background: #b8860b; }
        .login-scene-bars i:nth-child(3) { height: 38px; opacity: .6; }
        .login-scene-bars i:nth-child(4) { height: 60px; background: #b8860b; opacity: .74; }
        .login-scene-bars i:nth-child(5) { height: 49px; opacity: .66; }

        .login-scene-dot {
            position: absolute;
            display: block;
            width: 12px;
            height: 12px;
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 50%;
            background: #d5a72c;
            box-shadow: 0 0 0 6px rgba(213, 167, 44, .1);
        }

        .login-scene-dot--one { top: 30px; right: 26px; }
        .login-scene-dot--two { bottom: 25px; left: 24px; width: 8px; height: 8px; opacity: .62; }

        @media (max-width: 680px) {
            body.is-auth .admin-shell--auth { padding: 14px; }
            .login-page { margin-top: 20px; }
            .login-card { grid-template-columns: 1fr; min-height: 0; border-radius: 17px; }
            .login-form-panel { order: 1; padding: 34px 24px; }
            .login-visual-panel { order: 2; min-height: 310px; padding: 26px 22px; border-top: 1px solid rgba(255, 255, 255, .12); border-left: 0; }
            .login-scene { min-height: 205px; }
            .login-scene-window { height: 140px; }
        }

        @media (max-width: 420px) {
            .login-form-panel { padding: 30px 20px; }
            .login-visual-panel { min-height: 280px; }
            .login-visual-brand strong { font-size: 13px; }
        }
    </style>
@endpush

@section('content')
    <div class="login-page">
        <div class="login-card" data-component="auth-card">
            <section class="login-form-panel" aria-labelledby="login-title">
                @if (! $configured)
                    <div class="alert alert-warning">
                        Admin credentials are not configured.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="login-form-head">
                    <h1 id="login-title">Welcome back</h1>
                    <p>Sign in to start managing your workspace.</p>
                </div>

                <form method="POST" action="{{ route('admin.login.store') }}" class="login-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            autocomplete="username" required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="field">
                        <button type="submit" class="button">Sign in</button>
                    </div>
                </form>
            </section>

            <section class="login-visual-panel" aria-label="Admin workspace preview">
                <div class="login-visual-brand">
                    <span class="admin-brand-mark">
                        <img src="{{ asset('/assets/img/logo/favicon.png') }}" alt="" aria-hidden="true">
                    </span>
                    <div>
                        <strong>{{ config('luxury-quotes.brand.studio_name', 'Turance Technologies') }}</strong>
                        <span>Admin Workspace</span>
                    </div>
                </div>

                <div class="login-scene" aria-hidden="true">
                    <div class="login-scene-orbit"></div>
                    <div class="login-scene-window">
                        <div class="login-scene-window__top"><i></i><i></i><i></i></div>
                        <div class="login-scene-window__body">
                            <div class="login-scene-window__rail"></div>
                            <div class="login-scene-window__content">
                                <div class="login-scene-line"></div>
                                <div class="login-scene-bars"><i></i><i></i><i></i><i></i><i></i></div>
                            </div>
                        </div>
                    </div>
                    <span class="login-scene-dot login-scene-dot--one"></span>
                    <span class="login-scene-dot login-scene-dot--two"></span>
                </div>
            </section>
        </div>
    </div>
@endsection
