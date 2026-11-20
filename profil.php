<?php
require_once __DIR__ . '/includes/functions.php';
wajibLogin();
$u = currentUser();
$uid = (int)$u['id_user'];
$err = '';
$ok = '';

function simpanFotoProfil(array $file, string $folder): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return '';
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        $pesan = [
            UPLOAD_ERR_INI_SIZE => 'Ukuran foto melebihi batas upload server. Coba foto maksimal 2 MB.',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran foto terlalu besar.',
            UPLOAD_ERR_PARTIAL => 'Foto hanya terunggah sebagian. Coba ulangi.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload PHP tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'Server tidak dapat menulis file foto.',
            UPLOAD_ERR_EXTENSION => 'Upload foto dihentikan oleh konfigurasi PHP.'
        ];
        throw new Exception($pesan[$error] ?? 'Foto gagal diunggah.');
    }
    if (($file['size'] ?? 0) < 1 || $file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Ukuran foto maksimal 2 MB.');
    }
    if (!is_uploaded_file($file['tmp_name'] ?? '')) throw new Exception('File foto tidak valid.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext || @getimagesize($file['tmp_name']) === false) throw new Exception('Foto harus berupa JPG, PNG, atau WEBP yang valid.');
    if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) throw new Exception('Folder penyimpanan foto tidak dapat dibuat.');
    if (!is_writable($folder)) throw new Exception('Folder foto tidak memiliki izin tulis.');
    $nama = 'profil_' . $GLOBALS['uid'] . '_' . bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $folder . DIRECTORY_SEPARATOR . $nama)) throw new Exception('Foto tidak berhasil disimpan ke folder uploads/profil.');
    return $nama;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    try {
        $aksi = $_POST['aksi'] ?? 'profil';
        if ($aksi === 'profil') {
            $nama = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $kelas = trim($_POST['kelas'] ?? '');
            $hp = trim($_POST['no_hp'] ?? '');
            if ($nama === '' || mb_strlen($nama) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Nama dan email wajib diisi dengan format yang valid.');
            $cek = $koneksi->prepare('SELECT id_user FROM users WHERE email=? AND id_user<>? LIMIT 1');
            $cek->bind_param('si', $email, $uid);
            $cek->execute();
            if ($cek->get_result()->num_rows) throw new Exception('Email sudah digunakan akun lain.');

            $folderFoto = __DIR__ . '/uploads/profil';
            $fotoBaru = simpanFotoProfil($_FILES['foto_profil'] ?? [], $folderFoto);
            if ($fotoBaru !== '') {
                $stmt = $koneksi->prepare('UPDATE users SET nama=?,email=?,kelas=?,no_hp=?,foto=? WHERE id_user=?');
                $stmt->bind_param('sssssi', $nama, $email, $kelas, $hp, $fotoBaru, $uid);
            } else {
                $stmt = $koneksi->prepare('UPDATE users SET nama=?,email=?,kelas=?,no_hp=? WHERE id_user=?');
                $stmt->bind_param('ssssi', $nama, $email, $kelas, $hp, $uid);
            }
            $stmt->execute();
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
            if ($fotoBaru !== '') $_SESSION['user']['foto'] = $fotoBaru;
            flash('Profil berhasil diperbarui.');
            redirect('profil.php');
        }

        $old = $_POST['password_lama'] ?? '';
        $new = $_POST['password_baru'] ?? '';
        $conf = $_POST['konfirmasi'] ?? '';
        $stmt = $koneksi->prepare('SELECT password FROM users WHERE id_user=?');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $hash = $stmt->get_result()->fetch_row()[0] ?? '';
        if (!password_verify($old, $hash)) throw new Exception('Password lama tidak sesuai.');
        if (strlen($new) < 8 || $new !== $conf) throw new Exception('Password baru minimal 8 karakter dan konfirmasinya harus sama.');
        $hashBaru = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare('UPDATE users SET password=? WHERE id_user=?');
        $stmt->bind_param('si', $hashBaru, $uid);
        $stmt->execute();
        flash('Password berhasil diubah.');
        redirect('profil.php');
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$stmt = $koneksi->prepare('SELECT * FROM users WHERE id_user=?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();
$pageTitle = 'Profil Saya';
require __DIR__ . '/includes/header.php';
?>
<style>
.profile-photo-wrap{display:flex;align-items:center;gap:16px;padding:16px;border-radius:18px;background:linear-gradient(120deg,#f0edff,#e8f6ff);margin:14px 0 20px}
.profile-photo{width:76px;height:76px;border-radius:22px;object-fit:cover;border:3px solid #fff;box-shadow:0 8px 22px #394a8520;background:#fff}
.profile-photo-fallback{display:grid;place-items:center;font-size:27px;font-weight:800;color:#5b49c9;background:linear-gradient(135deg,#d9d2ff,#c8edff)}
.file-control{display:block;width:100%;padding:11px;border:1px dashed #a9a4df;border-radius:12px;background:#fbfaff;font-size:12px}
@media(max-width:600px){.profile-photo-wrap{align-items:flex-start;flex-direction:column}}
html[data-theme="dark"] .profile-photo-wrap{background:linear-gradient(120deg,#29264b,#18364a)}
html[data-theme="dark"] .file-control{background:#101a2e;color:#edf1ff;border-color:#48547b}

.profile-hero{display:flex;align-items:center;gap:18px;padding:24px 27px;margin-bottom:20px;border-radius:22px;background:linear-gradient(112deg,#30206f 0%,#5a42c7 48%,#268ed0 100%);color:#fff;box-shadow:0 16px 35px rgba(67,57,155,.2);position:relative;overflow:hidden}
.profile-hero:after{content:"";position:absolute;width:180px;height:180px;border:1px solid #ffffff35;border-radius:50%;right:8%;top:-100px;box-shadow:0 0 0 22px #ffffff0d,0 0 0 45px #ffffff0a}
.profile-hero-mark{width:56px;height:56px;flex:0 0 56px;display:grid;place-items:center;border-radius:18px;background:#ffffff20;border:1px solid #ffffff45;backdrop-filter:blur(8px)}
.profile-hero-mark svg{width:27px;height:27px}
.profile-eyebrow{font-size:9px;letter-spacing:1.6px;font-weight:800;color:#d9e4ff}
.profile-hero h1{font:800 clamp(20px,2.2vw,27px) 'Plus Jakarta Sans';margin:4px 0 5px;letter-spacing:-.6px}
.profile-hero p{margin:0;color:#e2eaff;font-size:12px}
.profile-role{margin-left:auto;align-self:flex-start;padding:7px 11px;border:1px solid #ffffff45;border-radius:999px;background:#ffffff1c;font-size:9px;font-weight:800;letter-spacing:.8px;z-index:1}
.profile-layout{grid-template-columns:minmax(0,1.15fr) minmax(280px,.85fr);align-items:start}
.profile-layout .panel{border-radius:21px;padding:24px}
.profile-layout .panel h2{font-size:16px;margin-bottom:16px}
.profile-photo-wrap{border:1px solid #e4e5fb;box-shadow:inset 0 1px #fff}
.profile-photo{width:88px;height:88px;border-radius:25px}
.profile-layout .field label{font-size:12px;color:#39466b}
.profile-layout .field input{min-height:44px;font-size:13px}
.profile-layout .btn{min-height:43px;padding:11px 18px}
html[data-theme="dark"] .profile-layout .panel{background:#1b2340;border-color:#353c60}
html[data-theme="dark"] .profile-layout .field label{color:#d7def5}
html[data-theme="dark"] .profile-layout .field input{background:#121a31;border-color:#3b4569;color:#edf2ff}
html[data-theme="dark"] .profile-photo-wrap{border-color:#3b4569}
@media(max-width:850px){.profile-layout{grid-template-columns:1fr}.profile-hero{padding:20px}.profile-hero:after{right:-50px}}
@media(max-width:480px){.profile-hero{align-items:flex-start;gap:12px}.profile-hero-mark{width:45px;height:45px;flex-basis:45px}.profile-role{font-size:8px;padding:6px 8px}.profile-hero p{font-size:11px}.profile-layout .panel{padding:17px}}

</style>
<div class="profile-hero"><div class="profile-hero-mark"><?=sibaraIcon('user','')?></div><div><span class="profile-eyebrow">PENGATURAN AKUN</span><h1>Kelola profil kamu</h1><p>Perbarui identitas, foto profil, dan keamanan akun SIBARA dalam satu tempat.</p></div><span class="profile-role"><?=e(strtoupper($d['role']))?></span></div>
<div class="section-grid profile-layout">
  <div class="panel">
    <h2>Informasi profil</h2>
    <?php if ($err): ?><div class="alert error"><?=e($err)?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
      <input type="hidden" name="aksi" value="profil">
      <div class="profile-photo-wrap">
        <?php if (!empty($d['foto'])): ?>
          <img class="profile-photo" src="<?=e(url('uploads/profil/'.$d['foto']))?>" alt="Foto profil">
        <?php else: ?>
          <div class="profile-photo profile-photo-fallback"><?=e(strtoupper(mb_substr($d['nama'],0,1)))?></div>
        <?php endif; ?>
        <div style="flex:1;min-width:0">
          <b><?=e($d['nama'])?></b><br>
          <small class="muted">@<?=e($d['username'])?> · <?=e(ucfirst($d['role']))?></small>
          <div class="field" style="margin:10px 0 0"><label for="foto_profil">Foto profil</label><input class="file-control" type="file" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/webp"><small class="muted">JPG, PNG, atau WEBP · maksimal 2 MB. Kosongkan jika tidak ingin mengganti foto.</small></div>
        </div>
      </div>
      <div class="field"><label>Nama lengkap</label><input name="nama" required maxlength="150" value="<?=e($d['nama'])?>"></div>
      <div class="field"><label>Email</label><input type="email" name="email" required value="<?=e($d['email'])?>"></div>
      <div class="field"><label>Kelas</label><input name="kelas" value="<?=e($d['kelas'] ?? '')?>"></div>
      <div class="field"><label>Nomor HP</label><input name="no_hp" value="<?=e($d['no_hp'] ?? '')?>" maxlength="20"></div>
      <button class="btn">Simpan perubahan</button>
    </form>
  </div>
  <div class="panel">
    <h2>Keamanan akun</h2>
    <p class="muted" style="font-size:12px">Gunakan password minimal 8 karakter dan jangan membagikannya kepada orang lain.</p>
    <form method="post">
      <input type="hidden" name="csrf" value="<?=e(csrfToken())?>"><input type="hidden" name="aksi" value="password">
      <div class="field"><label>Password lama</label><input type="password" name="password_lama" required></div>
      <div class="field"><label>Password baru</label><input type="password" name="password_baru" minlength="8" required></div>
      <div class="field"><label>Konfirmasi password baru</label><input type="password" name="konfirmasi" minlength="8" required></div>
      <button class="btn">Ubah password</button>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
