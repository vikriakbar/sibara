SIBARA - SISTEM INFORMASI BARANG HILANG DAN TEMUAN
SMK NEGERI 2 BANGKALAN

PAKET PROJECT
- PHP Native + MySQL (mysqli)
- Antarmuka modern, colorful, responsif untuk desktop dan ponsel
- Role: Admin, Guru, Siswa
- Login, registrasi, profil, ubah password
- Laporan barang hilang dan ditemukan dengan unggah foto
- Daftar barang temuan, klaim, persetujuan klaim, pengambilan
- Pencocokan kandidat, moderasi, pengguna, data master, statistik
- Notifikasi, bookmark, bukti klaim

INSTALASI DENGAN LARAGON
1. Ekstrak folder sibara ke C:\laragon\www\sibara
2. Jalankan Laragon dan nyalakan Apache serta MySQL.
3. Buka HeidiSQL atau phpMyAdmin.
4. Jalankan file database/db_sibara.sql. Perhatian: file SQL ini membuat ulang tabel aplikasi dengan nama db_barang_hilang. Jangan jalankan pada database produksi yang berisi data penting.
5. Periksa koneksi.php. Default Laragon: host localhost, user root, password kosong, database db_barang_hilang.
6. Buka http://localhost/sibara/buat_akun_awal.php untuk membuat admin pertama.
7. Setelah admin dibuat, halaman setup admin otomatis terkunci.
8. Login melalui http://localhost/sibara/login.php

PENDAFTARAN
- Akun siswa langsung aktif setelah registrasi.
- Akun guru berstatus menunggu dan perlu diaktifkan admin melalui menu Pengguna.
- Akun admin tidak dapat dibuat melalui form registrasi publik.

MENU ADMIN
Dashboard, Laporan Barang, Klaim Barang, Pengambilan, Pencocokan, Moderasi, Pengguna, Data Master, Statistik, Notifikasi, Profil.

MENU GURU
Dashboard, Laporan, Klaim Barang, Notifikasi, Profil.

MENU SISWA
Dashboard, Buat Laporan, Barang Ditemukan, Laporan Saya, Klaim Saya, Bukti Klaim, Bookmark, Notifikasi, Profil.

CATATAN KEAMANAN
- Ganti kredensial database sebelum hosting.
- Gunakan HTTPS di server publik.
- Jangan mengunggah file .env, password, atau backup database berisi data pribadi ke repositori publik.
- Folder uploads memblokir eksekusi file PHP melalui .htaccess (Apache).
- setup_password.php sengaja dinonaktifkan karena pola penggantian password demo yang menampilkan password plaintext tidak aman.
- Buat backup database sebelum menjalankan pembaruan SQL.

PERSYARATAN
PHP 8.1+ direkomendasikan, ekstensi mysqli dan fileinfo aktif, MySQL 5.7+/MariaDB 10.4+.

SIBARA - SMK NEGERI 2 BANGKALAN

PEMBARUAN V6
- Ikon notifikasi pada header kini menggunakan ikon SVG, bukan tombol kosong.
- Admin dapat membuka menu Pengguna untuk melihat seluruh akun terdaftar.
- Admin dapat menambah akun melalui menu Pengguna > Tambah pengguna.
- Pilihan role Admin hanya tersedia bagi admin yang sedang login.
- Jumlah akun admin dibatasi maksimal 3. Pengecekan dilakukan kembali di server saat penyimpanan.
- Tidak ada perubahan struktur database pada pembaruan ini.
