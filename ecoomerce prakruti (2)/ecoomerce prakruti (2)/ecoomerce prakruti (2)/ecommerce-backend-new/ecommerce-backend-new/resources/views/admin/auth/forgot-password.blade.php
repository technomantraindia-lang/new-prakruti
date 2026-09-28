<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Prakruti Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; margin: 0; background: linear-gradient(135deg, #f8f3e7, #edf6e9); font-family: 'Segoe UI', system-ui, sans-serif; color: #173416; }
        .card { width: min(460px, calc(100% - 32px)); border: 0; border-radius: 24px; box-shadow: 0 24px 70px rgba(21,53,22,.16); }
        .btn-prakruti { min-height: 52px; border: 0; border-radius: 16px; color: #fff; font-weight: 800; background: linear-gradient(135deg, #1d4f20, #0f2d14); }
        a { color: #1d4f20; font-weight: 700; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .password-field { position: relative; }
        .password-field .form-control { padding-right: 52px; }
        .password-toggle-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 999px;
            background: rgba(29, 79, 32, .08);
            color: #1d4f20;
        }
    </style>
</head>
<body>
    <div class="card p-4 p-md-5">
        <h1 class="h3 fw-bold mb-2">Change admin password</h1>
        <p class="text-muted mb-4">Enter your admin email and the business phone number saved in Store Settings, then create a new password.</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Admin Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autofocus>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-bold">Store Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">New Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required autocomplete="new-password">
                    <button type="button" class="password-toggle-btn" data-toggle-password="#password" aria-label="Show password">👁</button>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                <div class="password-field">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" required autocomplete="new-password">
                    <button type="button" class="password-toggle-btn" data-toggle-password="#password_confirmation" aria-label="Show password">👁</button>
                </div>
            </div>

            <button type="submit" class="btn btn-prakruti w-100">Change Password</button>
        </form>

        <p class="mt-4 mb-0 text-center"><a href="{{ route('admin.login') }}">Back to admin login</a></p>
    </div>
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.querySelector(button.getAttribute('data-toggle-password'));
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? '🙈' : '👁';
                button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
