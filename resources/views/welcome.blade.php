<!DOCTYPE html>
<html>
<head>
    <title>Aplikasi Client</title>
</head>
<body>
    <h1>Selamat Datang di Aplikasi Client</h1>

    @auth
        <p>Halo, {{ auth()->user()->name }}!</p>
        <p><a href="/dashboard">Masuk ke Dashboard</a></p>
    @else
        <a href="{{ route('login') }}">
            Login dengan Akun SSO
        </a>
    @endauth
</body>
</html>
