<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Account - Prakruti Organic</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6578e8, #7850ad);
        }

        .account-card {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #222222;
        }

        .welcome-text {
            margin-bottom: 30px;
            color: #666666;
        }

        .detail {
            padding: 15px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .detail strong {
            display: block;
            margin-bottom: 5px;
            color: #333333;
        }

        .detail span {
            color: #666666;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .button {
            display: inline-block;
            flex: 1;
            padding: 13px 20px;
            border: 0;
            border-radius: 7px;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            background: linear-gradient(135deg, #6578e8, #7850ad);
        }

        form {
            flex: 1;
            margin: 0;
        }

        form .button {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="account-card">
        <h1>My Account</h1>

        <p class="welcome-text">
            Welcome, {{ $user->name }}
        </p>

        <div class="detail">
            <strong>Name</strong>
            <span>{{ $user->name }}</span>
        </div>

        <div class="detail">
            <strong>Email</strong>
            <span>{{ $user->email }}</span>
        </div>

        <div class="detail">
            <strong>Phone</strong>
            <span>{{ $user->phone ?? 'Not provided' }}</span>
        </div>

        <div class="buttons">
            <a href="{{ route('home') }}" class="button">
                Visit Website
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="button">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
