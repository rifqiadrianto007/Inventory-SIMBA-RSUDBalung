@extends('app')

@section('content')
<div style="padding:20px;">
    <h2>Profil Saya</h2>

    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role }}</p>

    <a href="{{ route('akun.edit', $user->id) }}">Edit Profil</a><br><br>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
@endsection
