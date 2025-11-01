<h2>Edit Jabatan</h2>

<form method="POST" action="{{ route('jabatan.update', $jabatan->id_jabatan) }}">
    @csrf @method('PUT')
    <input type="text" name="nama_jabatan" value="{{ $jabatan->nama_jabatan }}" required>
    <br><br>
    <button type="submit">Update</button>
</form>
