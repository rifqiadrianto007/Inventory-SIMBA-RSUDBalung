<h2>Daftar Jabatan</h2>

<a href="{{ route('jabatan.create') }}">Tambah Jabatan</a>

@if(session('success'))
<p style="color: green">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="8">
    <tr>
        <th>#</th>
        <th>Nama Jabatan</th>
        <th>Aksi</th>
    </tr>
    @foreach($data as $row)
    <tr>
        <td>{{ $row->id_jabatan }}</td>
        <td>{{ $row->nama_jabatan }}</td>
        <td>
            <a href="{{ route('jabatan.edit', $row->id_jabatan) }}">Edit</a>
            <form action="{{ route('jabatan.destroy', $row->id_jabatan) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus jabatan?')" type="submit">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
