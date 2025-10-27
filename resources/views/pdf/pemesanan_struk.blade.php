<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pemesanan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h3>Struk Pemesanan Barang</h3>
    <p>No. Struk: {{ $no_struk }}</p>
    <p>Instalasi: {{ $instalasi }}</p>
    <p>Tanggal: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Volume</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemesanan->details as $d)
                <tr>
                    <td>{{ $d->satuan->name ?? '-' }}</td>
                    <td>{{ $d->volume }}</td>
                    <td>{{ $d->satuan->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:20px;">Struk ini dibuat otomatis oleh sistem.</p>
</body>
</html>
