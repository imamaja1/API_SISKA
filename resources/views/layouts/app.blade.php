<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'API Documentation')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/docs.css') }}">
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('api_panel.home') }}">API SISKA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth('api_users_web')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('api_panel.home') ? 'active' : '' }}"
                            href="{{ route('api_panel.home') }}">📄 API Docs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('obe_test') ? 'active' : '' }}"
                            href="{{ route('obe_test') }}">🧪 OBE Test</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('api_panel.log_report') ? 'active' : '' }}"
                            href="{{ route('api_panel.log_report') }}">📊 Log Report</a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth('api_users_web')
                    <li class="nav-item">
                        <form action="{{ route('api_user.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit"
                                style="color: var(--color-text-light); text-decoration: none;">Logout</button>
                        </form>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if (session('status'))
        <div class="alert alert-success"
            style="position: fixed; top: 80px; right: 20px; z-index: 1000; max-width: 400px;">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger"
            style="position: fixed; top: 80px; right: 20px; z-index: 1000; max-width: 400px;">
            {{ $errors->first() }}
        </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    <footer class="api-footer">
        <p>API SISKA <span class="version">v1.0</span> &bull; {{ date('Y') }} &bull; Documentation</p>
    </footer>

    <!-- jQuery (required by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>

</html>