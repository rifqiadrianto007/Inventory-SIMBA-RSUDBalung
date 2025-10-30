<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .btn-sso {
            display: inline-block;
            background: #0052cc;
            color: white;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            text-decoration: none;
        }

        .btn-sso:hover {
            background: #003f99;
        }

        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Selamat Datang 👋</h2>
        <p>Silakan login menggunakan akun SSO untuk melanjutkan.</p>

        <a href="{{ route('login') }}" class="btn-sso">
            Login dengan akun SSO
        </a>

        @if(session('error'))
            <p style="margin-top:15px; color:#e63946;">
                {{ session('error') }}
            </p>
        @endif

        <div class="footer">
            Sistem Inventaris & Pemesanan Barang
        </div>
    </div>
</body>
</html>
