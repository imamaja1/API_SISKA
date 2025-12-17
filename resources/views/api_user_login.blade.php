<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>API Panel Login</title>
</head>

<body>
    <h1>API Panel Login</h1>

    @if ($errors->any())
        <div style="color:red;">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('api_user.login.submit') }}">
        @csrf
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required />
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required />
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>

</html>
