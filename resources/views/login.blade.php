<!DOCTYPE html>
<html>
<head>
    <title>Login Sementara</title>
</head>
<body>
<h3>Login Sementara</h3>

<form method="POST" action="{{ route('login.submit') }}">
    @csrf
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

@if ($errors->any())
    <p style="color:red;">{{ $errors->first() }}</p>
@endif

</body>
</html>
