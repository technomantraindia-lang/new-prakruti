<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Prakruti Organic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --prakruti-green: #1d4f20;
            --prakruti-deep: #0f2d14;
            --prakruti-gold: #d89322;
            --prakruti-cream: #fbf8f0;
            --prakruti-border: #e7decf;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #173416;
            background:
                radial-gradient(circle at 18% 18%, rgba(216,147,34,.22), transparent 26%),
                radial-gradient(circle at 82% 12%, rgba(83,139,75,.28), transparent 30%),
                linear-gradient(135deg, #f8f3e7 0%, #fffdf8 48%, #edf6e9 100%);
        }

        .login-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
            position: relative;
            overflow: hidden;
        }

        .login-shell::before,
        .login-shell::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(29,79,32,.08);
            filter: blur(2px);
        }

        .login-shell::before { width: 220px; height: 220px; left: -80px; bottom: 9%; }
        .login-shell::after { width: 170px; height: 170px; right: -52px; top: 14%; }

        .login-card {
            width: min(1020px, 100%);
            min-height: 620px;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            overflow: hidden;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(231,222,207,.95);
            border-radius: 30px;
            background: rgba(255,255,255,.88);
            box-shadow: 0 30px 80px rgba(21,53,22,.18);
            backdrop-filter: blur(16px);
        }

        .brand-panel {
            padding: 48px;
            color: #fff;
            background:
                linear-gradient(150deg, rgba(15,45,20,.96), rgba(29,79,32,.92)),
                url("data:image/svg+xml,%3Csvg width='160' height='160' viewBox='0 0 160 160' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.09' stroke-width='2'%3E%3Cpath d='M28 92c34-8 58-28 72-62 8 36 28 58 62 72-36 7-58 28-72 62-7-36-28-58-62-72Z'/%3E%3Ccircle cx='52' cy='46' r='8'/%3E%3Ccircle cx='118' cy='113' r='8'/%3E%3C/g%3E%3C/svg%3E");
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -.02em;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--prakruti-deep);
            background: linear-gradient(135deg, #fff7d9, #d89322);
            box-shadow: 0 16px 34px rgba(0,0,0,.18);
        }
        .brand-icon img { width: 44px; height: 50px; object-fit: contain; }

        .brand-content h1 {
            max-width: 440px;
            margin: 42px 0 18px;
            font-size: clamp(34px, 5vw, 58px);
            line-height: 1;
            font-family: Georgia, 'Times New Roman', serif;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .brand-content p {
            max-width: 470px;
            margin: 0;
            color: rgba(255,255,255,.78);
            font-size: 16px;
            line-height: 1.75;
        }

        .trust-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 34px;
        }

        .trust-strip div {
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 18px;
            padding: 14px;
            background: rgba(255,255,255,.08);
        }

        .trust-strip strong {
            display: block;
            font-size: 18px;
        }

        .trust-strip span {
            display: block;
            margin-top: 3px;
            color: rgba(255,255,255,.68);
            font-size: 12px;
        }

        .form-panel {
            padding: 54px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(251,248,240,.96));
        }

        .form-box {
            width: 100%;
            max-width: 410px;
        }

        .login-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            color: var(--prakruti-green);
            background: #eef7ea;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .form-box h2 {
            margin: 20px 0 8px;
            font-size: 32px;
            font-weight: 850;
            letter-spacing: -.03em;
        }

        .form-box .subtitle {
            margin: 0 0 28px;
            color: #687466;
            line-height: 1.6;
        }

        .alert {
            border: 0;
            border-radius: 16px;
            padding: 13px 15px;
        }

        .form-group { margin-bottom: 18px; }

        label {
            color: #294226;
            font-weight: 750;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .field-wrap {
            position: relative;
        }

        .field-wrap i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #7d8b79;
        }

        .form-control {
            min-height: 54px;
            border-radius: 16px;
            border: 1px solid var(--prakruti-border);
            padding: 14px 16px 14px 46px;
            color: #173416;
            background: #fffdf8;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(45,90,39,.13);
            border-color: #2d5a27;
            background: #ffffff;
        }

        .btn-login {
            width: 100%;
            min-height: 56px;
            border: none;
            border-radius: 18px;
            color: #fff;
            font-weight: 850;
            letter-spacing: .01em;
            background: linear-gradient(135deg, var(--prakruti-green) 0%, var(--prakruti-deep) 100%);
            box-shadow: 0 16px 28px rgba(29,79,32,.24);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(29,79,32,.32);
        }

        .security-note {
            margin-top: 28px;
            padding: 14px 16px;
            border: 1px solid #e8eadf;
            border-radius: 18px;
            color: #647160;
            background: #fbfaf5;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 900px) {
            .login-card { grid-template-columns: 1fr; }
            .brand-panel { padding: 34px; }
            .brand-content h1 { margin-top: 28px; }
            .form-panel { padding: 36px 24px; }
        }

        @media (max-width: 560px) {
            .trust-strip { grid-template-columns: 1fr; }
            .brand-content h1 { font-size: 36px; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-card" aria-label="Prakruti Organic admin login">
            <div class="brand-panel">
                <div>
                    <div class="brand-mark">
                        <span class="brand-icon"><img src="{{ asset('assets/prakruti-logo.png') }}" alt="Prakruti Organic logo"></span>
                        <span>Prakruti Organic</span>
                    </div>

                    <div class="brand-content">
                        <h1>Admin command centre</h1>
                        <p>Securely manage organic products, orders, customers, stock, consultations, and daily store operations from one clean dashboard.</p>
                    </div>
                </div>

                <div class="trust-strip">
                    <div>
                        <strong>Pure</strong>
                        <span>Organic catalogue</span>
                    </div>
                    <div>
                        <strong>Fast</strong>
                        <span>Order handling</span>
                    </div>
                    <div>
                        <strong>Secure</strong>
                        <span>Admin access</span>
                    </div>
                </div>
            </div>

            <div class="form-panel">
                <div class="form-box">
                    <span class="login-eyebrow"><i class="fas fa-shield-alt"></i> Staff Access</span>
                    <h2>Welcome back</h2>
                    <p class="subtitle">Sign in to continue to the Prakruti Organic admin panel.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-circle-exclamation me-2"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-circle-exclamation me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.post') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="field-wrap">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@prakruti.com" autocomplete="email" required autofocus>
                            </div>
                            @error('email')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="field-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                            </div>
                            @error('password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="btn btn-login mt-2">
                            Login to Admin Panel <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="security-note">
                        <i class="fas fa-lock me-2 text-success"></i>
                        This area is restricted to authorized Prakruti Organic team members only.
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
