<?php
require_once __DIR__ . '/../includes/functions.php';
wajibRole('siswa');
$pageTitle = 'Buat Laporan';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $fotoTersimpan = null;
    try {
        $jenis = $_POST['jenis'] ?? '';
        $nama = trim($_POST['nama_barang'] ?? '');
        $kat = (int)($_POST['id_kategori'] ?? 0);
        $lok = (int)($_POST['id_lokasi'] ?? 0);
        $tgl = $_POST['tanggal_kejadian'] ?? '';
        $desk = trim($_POST['deskripsi'] ?? '');
        if (!in_array($jenis, ['hilang', 'ditemukan'], true) || $nama === '' || !$kat || !$lok || $tgl === '') throw new Exception('Lengkapi seluruh data wajib.');
        if (mb_strlen($nama) > 150) throw new Exception('Nama barang maksimal 150 karakter.');
        $tanggal = DateTime::createFromFormat('Y-m-d', $tgl);
        if (!$tanggal || $tanggal->format('Y-m-d') !== $tgl || $tgl > date('Y-m-d')) throw new Exception('Tanggal kejadian tidak valid.');

        $foto = null;
        $file = $_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE];
        $uploadError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_NO_FILE) {
            if ($uploadError !== UPLOAD_ERR_OK) {
                $pesan = [
                    UPLOAD_ERR_INI_SIZE => 'Foto melebihi batas upload PHP di server. Coba foto maksimal 2 MB.',
                    UPLOAD_ERR_FORM_SIZE => 'Foto melebihi batas ukuran formulir.',
                    UPLOAD_ERR_PARTIAL => 'Foto hanya terunggah sebagian. Silakan coba lagi.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload PHP tidak ditemukan.',
                    UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file foto.',
                    UPLOAD_ERR_EXTENSION => 'Upload foto dihentikan oleh konfigurasi PHP.'
                ];
                throw new Exception($pesan[$uploadError] ?? 'Foto gagal diunggah.');
            }
            if (($file['size'] ?? 0) < 1 || $file['size'] > 2 * 1024 * 1024) throw new Exception('Ukuran foto maksimal 2 MB.');
            if (!is_uploaded_file($file['tmp_name'] ?? '')) throw new Exception('File foto tidak valid.');
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
            if (!$ext || @getimagesize($file['tmp_name']) === false) throw new Exception('Foto harus JPG, PNG, atau WEBP yang valid.');
            $folder = __DIR__ . '/../uploads/barang';
            if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) throw new Exception('Folder uploads/barang tidak dapat dibuat.');
            if (!is_writable($folder)) throw new Exception('Folder uploads/barang tidak memiliki izin tulis.');
            $foto = bin2hex(random_bytes(16)) . '.' . $ext;
            if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $foto)) throw new Exception('Foto gagal disimpan ke folder uploads/barang.');
            $fotoTersimpan = $folder . DIRECTORY_SEPARATOR . $foto;
        }

        $token = bin2hex(random_bytes(32));
        $uid = (int)currentUser()['id_user'];
        $stmt = $koneksi->prepare('INSERT INTO laporan(id_user,id_kategori,id_lokasi,jenis_laporan,nama_barang,deskripsi,tanggal_kejadian,foto_barang,qr_token) VALUES(?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('iiissssss', $uid, $kat, $lok, $jenis, $nama, $desk, $tgl, $foto, $token);
        $stmt->execute();
        $lid = $koneksi->insert_id;
        logActivity($koneksi, $uid, 'Membuat laporan', 'Laporan #' . $lid . ' ' . $nama);
        $admins = $koneksi->query("SELECT id_user FROM users WHERE role='admin' AND status_akun='aktif'");
        while ($a = $admins->fetch_assoc()) notifyUser($koneksi, (int)$a['id_user'], 'Laporan baru', 'Ada laporan baru: ' . $nama, 'admin/laporan.php');
        flash('Laporan berhasil dikirim dan menunggu pemeriksaan.');
        redirect('siswa/laporan_saya.php');
    } catch (Throwable $e) {
        if ($fotoTersimpan && is_file($fotoTersimpan)) @unlink($fotoTersimpan);
        $err = $e->getMessage();
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="panel" style="max-width:850px">
  <div class="panel-head"><div><h2>Laporkan barang hilang atau ditemukan</h2><small class="muted">Berikan informasi yang jelas agar barang lebih mudah ditemukan.</small></div><span class="badge">Form laporan</span></div>
  <?php if ($err): ?><div class="alert error"><?=e($err)?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
    <div class="form-grid">
      <div class="field"><label>Jenis laporan *</label><select name="jenis" required><option value="">Pilih jenis</option><option value="hilang">Barang hilang</option><option value="ditemukan">Barang ditemukan</option></select></div>
      <div class="field"><label>Nama barang *</label><input name="nama_barang" maxlength="150" required placeholder="Contoh: Dompet hitam"></div>
      <div class="field"><label>Kategori *</label><select name="id_kategori" required><option value="">Pilih kategori</option><?php $q=$koneksi->query('SELECT * FROM kategori ORDER BY nama_kategori'); while ($x=$q->fetch_assoc()): ?><option value="<?=$x['id_kategori']?>"><?=e($x['nama_kategori'])?></option><?php endwhile; ?></select></div>
      <div class="field"><label>Lokasi kejadian *</label><select name="id_lokasi" required><option value="">Pilih lokasi</option><?php $q=$koneksi->query('SELECT * FROM lokasi ORDER BY nama_lokasi'); while ($x=$q->fetch_assoc()): ?><option value="<?=$x['id_lokasi']?>"><?=e($x['nama_lokasi'])?></option><?php endwhile; ?></select></div>
      <div class="field"><label>Tanggal kejadian *</label><input type="date" name="tanggal_kejadian" max="<?=date('Y-m-d')?>" required></div>
      <div class="field"><label>Foto barang (opsional)</label><input type="file" name="foto" accept="image/jpeg,image/png,image/webp"><small class="muted">JPG, PNG, atau WEBP · maksimal 2 MB.</small></div>
    </div>
    <div class="field"><label>Deskripsi / ciri-ciri barang</label><textarea name="deskripsi" placeholder="Tuliskan warna, merek, ciri khusus, atau informasi lain..."></textarea></div>
    <button class="btn">Kirim laporan</button> <a class="btn secondary" href="dashboard.php">Batal</a>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
