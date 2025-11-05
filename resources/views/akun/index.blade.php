@extends('app')

@section('content')
<div style="padding:20px;">
    <h2>Daftar Akun</h2>
    <a href="{{ route('akun.profile') }}">Lihat Profil Saya</a>

    <table border="1" cellpadding="10" style="margin-top:20px; width:100%">
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>

        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>
                <a href="{{ route('akun.show', $user->id) }}">Detail</a> |
                <a href="{{ route('akun.edit', $user->id) }}">Edit</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
