<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prakruti Organic') - Online Grocery Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2d5a27; --secondary: #193a15; }
        body { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; background-color: #faf8f3; }
        .navbar { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        .navbar-brand, .nav-link { color: white !important; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; }
        .product-card { transition: transform .2s; border: 1px solid #e3ddcf; box-shadow: 0 2px 8px rgba(45,90,39,.06); }
        .product-card:hover { transform: translateY(-4px); }
        .price { color: var(--primary); font-weight: 700; }
        .sale-price { color: #e74c3c; }
        footer { background: #193a15; color: #a0aec0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}"><i class="fas fa-leaf text-warning me-1"></i> Prakruti Organic</a>
            <div class="navbar-nav ms-auto align-items-center gap-2">
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                @auth
                    <span class="nav-link text-white small me-2"><i class="fas fa-user-circle me-1"></i> Hello, {{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light text-primary fw-bold px-3">Logout</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link btn btn-sm btn-light text-primary fw-bold px-3 ms-2" href="{{ route('register') }}">Register</a>
                @endauth
                <a class="nav-link ms-3" href="{{ route('admin.login') }}"><i class="fas fa-lock"></i> Admin</a>
            </div>
        </div>
    </nav>
    <main class="container mb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </main>
    <footer class="py-4 mt-5"><div class="container text-center"><p class="mb-0">&copy; {{ date('Y') }} Prakruti Organic. All rights reserved.</p></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
