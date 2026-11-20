CREATE DATABASE IF NOT EXISTS db_barang_hilang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_barang_hilang;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS log_matching, aktivitas_user, pengaturan_sistem, pengambilan, moderasi_laporan, pengaduan_laporan, bookmark, matching, notifikasi, riwayat, klaim, laporan, lokasi, kategori, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(150) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 email VARCHAR(100) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','guru','siswa') NOT NULL DEFAULT 'siswa',
 kelas VARCHAR(50) DEFAULT NULL,
 no_hp VARCHAR(20) DEFAULT NULL,
 foto VARCHAR(255) DEFAULT NULL,
 status_akun ENUM('aktif','nonaktif','menunggu') NOT NULL DEFAULT 'aktif',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE kategori (
 id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama_kategori VARCHAR(80) NOT NULL UNIQUE,
 deskripsi TEXT DEFAULT NULL
) ENGINE=InnoDB;
CREATE TABLE lokasi (
 id_lokasi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama_lokasi VARCHAR(100) NOT NULL,
 keterangan TEXT DEFAULT NULL
) ENGINE=InnoDB;
CREATE TABLE laporan (
 id_laporan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_user INT UNSIGNED NOT NULL,
 id_kategori INT UNSIGNED NOT NULL,
 id_lokasi INT UNSIGNED NOT NULL,
 jenis_laporan ENUM('hilang','ditemukan') NOT NULL,
 nama_barang VARCHAR(150) NOT NULL,
 deskripsi TEXT,
 tanggal_kejadian DATE NOT NULL,
 foto_barang VARCHAR(255) DEFAULT NULL,
 status ENUM('menunggu','diproses','selesai','ditolak') NOT NULL DEFAULT 'menunggu',
 moderation_status ENUM('aktif','ditinjau','disembunyikan') NOT NULL DEFAULT 'aktif',
 qr_token CHAR(64) NOT NULL UNIQUE,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_laporan_user FOREIGN KEY (id_user) REFERENCES users(id_user),
 CONSTRAINT fk_laporan_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
 CONSTRAINT fk_laporan_lokasi FOREIGN KEY (id_lokasi) REFERENCES lokasi(id_lokasi),
 INDEX idx_laporan_status (status, moderation_status), INDEX idx_laporan_jenis (jenis_laporan)
) ENGINE=InnoDB;
CREATE TABLE klaim (
 id_klaim INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_laporan INT UNSIGNED NOT NULL,
 id_user INT UNSIGNED NOT NULL,
 bukti_kepemilikan TEXT NOT NULL,
 status_klaim ENUM('menunggu','disetujui','ditolak','diambil') NOT NULL DEFAULT 'menunggu',
 catatan_admin TEXT DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan), FOREIGN KEY (id_user) REFERENCES users(id_user),
 INDEX idx_klaim_status (status_klaim)
) ENGINE=InnoDB;
CREATE TABLE riwayat (
 id_riwayat INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_laporan INT UNSIGNED NOT NULL,
 id_admin INT UNSIGNED NOT NULL,
 status_lama VARCHAR(30) DEFAULT NULL,
 status_baru VARCHAR(30) NOT NULL,
 keterangan TEXT,
 tanggal_perubahan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan), FOREIGN KEY (id_admin) REFERENCES users(id_user)
) ENGINE=InnoDB;
CREATE TABLE notifikasi (
 id_notifikasi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_user INT UNSIGNED NOT NULL,
 judul VARCHAR(150) NOT NULL,
 pesan TEXT NOT NULL,
 tautan VARCHAR(255) DEFAULT NULL,
 dibaca TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
 INDEX idx_notif_user (id_user,dibaca)
) ENGINE=InnoDB;
CREATE TABLE matching (
 id_matching INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_laporan_hilang INT UNSIGNED NOT NULL,
 id_laporan_temuan INT UNSIGNED NOT NULL,
 skor DECIMAL(5,2) NOT NULL DEFAULT 0,
 status ENUM('kandidat','diperiksa','cocok','tidak_cocok') NOT NULL DEFAULT 'kandidat',
 catatan_admin TEXT,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_matching_pair (id_laporan_hilang,id_laporan_temuan),
 FOREIGN KEY (id_laporan_hilang) REFERENCES laporan(id_laporan), FOREIGN KEY (id_laporan_temuan) REFERENCES laporan(id_laporan)
) ENGINE=InnoDB;
CREATE TABLE bookmark (
 id_bookmark INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_user INT UNSIGNED NOT NULL,
 id_laporan INT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_bookmark (id_user,id_laporan),
 FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
 FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE pengaduan_laporan (
 id_pengaduan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_laporan INT UNSIGNED NOT NULL,
 id_user INT UNSIGNED NOT NULL,
 alasan VARCHAR(120) NOT NULL,
 keterangan TEXT,
 status ENUM('menunggu','ditindaklanjuti','ditolak') NOT NULL DEFAULT 'menunggu',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan), FOREIGN KEY (id_user) REFERENCES users(id_user)
) ENGINE=InnoDB;
CREATE TABLE moderasi_laporan (
 id_moderasi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_laporan INT UNSIGNED NOT NULL,
 id_admin INT UNSIGNED NOT NULL,
 tindakan ENUM('sembunyikan','pulihkan','tinjau') NOT NULL,
 alasan TEXT,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_laporan) REFERENCES laporan(id_laporan), FOREIGN KEY (id_admin) REFERENCES users(id_user)
) ENGINE=InnoDB;
CREATE TABLE pengambilan (
 id_pengambilan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_klaim INT UNSIGNED NOT NULL UNIQUE,
 id_admin INT UNSIGNED NOT NULL,
 nama_penerima VARCHAR(150) NOT NULL,
 catatan TEXT,
 tanggal_pengambilan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_klaim) REFERENCES klaim(id_klaim), FOREIGN KEY (id_admin) REFERENCES users(id_user)
) ENGINE=InnoDB;
CREATE TABLE aktivitas_user (
 id_aktivitas INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_user INT UNSIGNED NOT NULL,
 aktivitas VARCHAR(150) NOT NULL,
 detail TEXT,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE log_matching (
 id_log_matching INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 id_matching INT UNSIGNED NOT NULL,
 hasil VARCHAR(50) NOT NULL,
 detail TEXT,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_matching) REFERENCES matching(id_matching) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE pengaturan_sistem (
 id_pengaturan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama_pengaturan VARCHAR(100) NOT NULL UNIQUE,
 nilai TEXT,
 updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kategori(nama_kategori,deskripsi) VALUES
('Elektronik','HP, laptop, charger, headset, dan perangkat elektronik'),
('Alat Tulis & Buku','Buku, pulpen, tempat pensil, dan perlengkapan belajar'),
('Pakaian & Aksesoris','Jaket, topi, sepatu, tas, dan aksesoris'),
('Dokumen / Kartu','Kartu pelajar, kartu identitas, dan dokumen'),
('Lainnya','Barang lain yang tidak termasuk kategori di atas');
INSERT INTO lokasi(nama_lokasi,keterangan) VALUES
('Kantin Sekolah','Area kantin'),('Perpustakaan','Ruang perpustakaan'),('Lapangan Upacara','Lapangan utama'),('Masjid Sekolah','Area masjid'),('Ruang Kelas','Ruang belajar'),('Laboratorium','Lab komputer dan praktik'),('Parkiran','Area parkir sekolah');
INSERT INTO pengaturan_sistem(nama_pengaturan,nilai) VALUES ('nama_sekolah','SMK Negeri 2 Bangkalan'),('nama_aplikasi','SIBARA');
-- Buat akun admin pertama melalui halaman buat_akun_awal.php setelah impor database.
