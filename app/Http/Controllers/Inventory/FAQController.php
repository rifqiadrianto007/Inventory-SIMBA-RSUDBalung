<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FAQController extends Controller
{
    /**
     * Menampilkan halaman FAQ sesuai role pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? 'guest');

        // Sementara data FAQ disimulasikan
        // Bisa diganti nanti dengan tabel faq di database
        $faqList = [
            'super-admin' => [
                ['q' => 'Bagaimana cara menambah akun baru?', 'a' => 'Masuk ke menu Manajemen Akun, lalu klik tombol Tambah (+).'],
                ['q' => 'Bagaimana cara menghapus akun?', 'a' => 'Klik ikon tempat sampah di daftar akun.']
            ],
            'instalasi' => [
                ['q' => 'Bagaimana cara memesan barang?', 'a' => 'Buka menu Pemesanan, klik tombol + untuk menambahkan pemesanan baru.'],
                ['q' => 'Bagaimana cara melihat status pesanan?', 'a' => 'Masuk ke menu Pemesanan, lalu buka tab Status.']
            ],
            'tim-ppk' => [
                ['q' => 'Bagaimana memverifikasi pemesanan?', 'a' => 'Masuk ke halaman Pemesanan, pilih pesanan, dan klik Konfirmasi.'],
            ],
            'default' => [
                ['q' => 'Bagaimana login ke sistem?', 'a' => 'Gunakan akun SSO yang telah diberikan oleh admin.'],
                ['q' => 'Bagaimana mengganti password?', 'a' => 'Masuk ke menu Profil, klik Ubah Password.'],
            ]
        ];

        // Pilih FAQ sesuai role, fallback ke default
        $faq = $faqList[$role] ?? $faqList['default'];

        return view('faq.index', [
            'faq' => $faq,
            'role' => ucfirst($role),
        ]);
    }
}
