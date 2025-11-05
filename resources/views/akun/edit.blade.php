@extends('app')

@section('content')
<div style="padding:20px;">
    <h2>Edit Akun</h2>

    <form action="{{ route('akun.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nama</label><br>
        <input type="text" name="name" value="{{ $user->name }}"><br><br>

        <label>Email</label><br>
        <input type="email" name="email" value="{{ $user->email }}"><br><br>

        <label>Role</label><br>
        <select name="role">
            @foreach($roles as $role)
            <option value="{{ $role }}" {{ $role == $user->role ? 'selected' : '' }}>
                {{ $role }}
            </option>
            @endforeach
        </select><br><br>

        <label>Password (opsional)</label><br>
        <input type="password" name="password" placeholder="Kosongkan jika tidak ganti"><br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <br>
    <a href="{{ route('akun.index') }}">Kembali</a>
</div>
@endsection
