@extends('app')

@section('content')
<div style="padding:20px;">
    <h2>Detail Akun</h2>

    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role }}</p>

    <a href="{{ route('akun.index') }}">Kembali</a>
</div>
@endsection
