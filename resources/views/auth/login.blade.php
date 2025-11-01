<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <h2>Login</h2>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

    @if ($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif
</form>
