<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>API Panel Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-wrap" role="main">
        <div class="login-brand">
            <h1>API SISKA</h1>
            <div class="login-sub">Administrator API Panel</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('api_user.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}"
                    required autofocus />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control" required />
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-dark btn-lg">Sign in</button>
            </div>
        </form>

        <div class="footer-small">API access for authorized personnel only</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
</body>

</html>
