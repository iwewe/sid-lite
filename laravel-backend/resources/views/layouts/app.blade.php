<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SID Lite') - Sistem Informasi Desa</title>

    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --secondary: #764ba2;
            --success: #38a169;
            --danger: #e53e3e;
            --warning: #dd6b20;
            --info: #3182ce;
            --light: #f7fafc;
            --dark: #1a202c;
            --gray: #718096;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f0f4f8;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-menu {
            display: flex;
            gap: 20px;
            align-items: center;
            list-style: none;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .navbar-menu a:hover {
            background: rgba(255,255,255,0.2);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.875rem;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 200px);
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-secondary { background: var(--gray); color: white; }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #f0fff4;
            border: 1px solid var(--success);
            color: var(--success);
        }

        .alert-danger {
            background: #fff5f5;
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            background: #f7fafc;
            font-weight: 600;
        }

        .table tr:hover {
            background: #f7fafc;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Footer */
        .footer {
            background: white;
            padding: 2rem 0;
            text-align: center;
            color: var(--gray);
            margin-top: 3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-menu {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="{{ route('dashboard') }}" class="navbar-brand">
                    🏛️ SID Lite
                </a>
                <ul class="navbar-menu">
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('form') }}">Form Pendataan</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('users.index') }}">Kelola User</a></li>
                    @endif
                </ul>
                <div class="navbar-user">
                    <span class="user-badge">{{ auth()->user()->role }}</span>
                    <span>{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.875rem;">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <div class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    ✗ {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} SID Lite - Sistem Informasi Desa. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
