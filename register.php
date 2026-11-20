<?php
require_once __DIR__.'/includes/functions.php';
$loggedUser=currentUser();
$isAdminCreator=($loggedUser['role']??'')==='admin';
if(!empty($loggedUser['id_user']) && !$isAdminCreator) redirect(($loggedUser['role']??'siswa').'/dashboard.php');
$error='';
$adminCount=(int)$koneksi->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrfCheck();
    $nama=trim($_POST['nama']??''); $username=trim($_POST['username']??''); $email=trim($_POST['email']??'');
    $kelas=trim($_POST['kelas']??''); $hp=trim($_POST['no_hp']??''); $role=$_POST['role']??'siswa';
    $pw=$_POST['password']??''; $kon=$_POST['konfirmasi']??'';
    try{
        if(!$nama||!$username||!$email||!$pw) throw new Exception('Lengkapi seluruh kolom wajib.');
        if(mb_strlen($nama)>150 || !preg_match('/^[a-zA-Z0-9_]{4,50}$/',$username)) throw new Exception('Nama terlalu panjang atau username tidak valid.');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new Exception('Format email tidak valid.');
        if(!in_array($role,['siswa','guru','admin'],true)) throw new Exception('Jenis akun tidak valid.');
        if($role==='admin' && !$isAdminCreator) throw new Exception('Hanya admin yang sudah login yang boleh membuat akun admin.');
        if($role==='siswa' && !$kelas) throw new Exception('Kelas wajib diisi untuk siswa.');
        if(strlen($pw)<8 || $pw!==$kon) throw new Exception('Password minimal 8 karakter dan harus sama dengan konfirmasi.');
        $check=$koneksi->prepare('SELECT id_user FROM users WHERE username=? OR email=? LIMIT 1');
        $check->bind_param('ss',$username,$email); $check->execute();
        if($check->get_result()->num_rows) throw new Exception('Username atau email sudah digunakan.');
        $hash=password_hash($pw,PASSWORD_DEFAULT);
        if($role==='admin'){
            // Lock the existing admin rows so simultaneous requests cannot exceed the 3-admin limit.
            $koneksi->begin_transaction();
            $lock=$koneksi->query("SELECT id_user FROM users WHERE role='admin' FOR UPDATE");
            $adminCount=$lock->num_rows;
            if($adminCount>=3) throw new Exception('Batas maksimal 3 akun admin sudah tercapai.');
            $status='aktif';
            $insert=$koneksi->prepare('INSERT INTO users(nama,username,email,password,role,kelas,no_hp,status_akun) VALUES(?,?,?,?,?,?,?,?)');
            $insert->bind_param('ssssssss',$nama,$username,$email,$hash,$role,$kelas,$hp,$status); $insert->execute();
            $koneksi->commit();
            logActivity($koneksi,(int)$loggedUser['id_user'],'Membuat akun admin','Admin baru: '.$username);
            flash('Akun admin berhasil dibuat. Total admin sekarang '.($adminCount+1).' dari maksimal 3.');
            redirect('admin/pengguna.php');
        }
        $status=$role==='guru'?'menunggu':'aktif';
        $insert=$koneksi->prepare('INSERT INTO users(nama,username,email,password,role,kelas,no_hp,status_akun) VALUES(?,?,?,?,?,?,?,?)');
        $insert->bind_param('ssssssss',$nama,$username,$email,$hash,$role,$kelas,$hp,$status); $insert->execute();
        flash($role==='guru'?'Pendaftaran guru berhasil. Akun menunggu aktivasi admin.':'Pendaftaran berhasil. Silakan login.');
        redirect('login.php');
    }catch(Throwable $e){
        if($koneksi->errno || $koneksi->in_transaction) { try{$koneksi->rollback();}catch(Throwable $ignored){} }
        $error=$e->getMessage();
    }
}
$pageTitle=$isAdminCreator?'Tambah Pengguna':'Daftar Akun';
require __DIR__.'/includes/header.php';
?>
<style>.auth-illustration{display:block;width:min(100%,210px);height:135px;object-fit:contain;margin:0 auto 10px}.admin-limit{padding:11px 13px;border-radius:12px;background:linear-gradient(100deg,#eeeaff,#e8f7ff);color:#5144b8;font-size:12px;font-weight:700;margin-bottom:16px}html[data-theme="dark"] .admin-limit{background:#2b3157;color:#d5ceff}</style>
<div class="auth-page" style="margin:-30px -34px -45px"><div class="auth-card" style="width:min(100%,580px)">
<img class="auth-logo" src="<?=e(url('assets/img/logo-sibara.png'))?>" alt="Logo SIBARA"><img class="auth-illustration" src="<?=e(url('assets/img/illustrations/report-student.webp'))?>" alt="Ilustrasi pendaftaran">
<h1><?=$isAdminCreator?'Tambah pengguna':'Buat akun SIBARA'?></h1><p class="sub"><?=$isAdminCreator?'Buat akun siswa, guru, atau admin baru.':'Daftar untuk menggunakan layanan barang hilang dan temuan.'?></p>
<?php if($isAdminCreator):?><div class="admin-limit">Akun admin: <?=$adminCount?> / 3 · <?=max(0,3-$adminCount)?> slot tersedia</div><?php endif;?>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
<div class="field"><label>Nama lengkap *</label><input name="nama" maxlength="150" required value="<?=e($_POST['nama']??'')?>"></div>
<div class="form-grid"><div class="field"><label>Username *</label><input name="username" maxlength="50" required value="<?=e($_POST['username']??'')?>"></div><div class="field"><label>Email *</label><input type="email" name="email" maxlength="100" required value="<?=e($_POST['email']??'')?>"></div></div>
<div class="form-grid"><div class="field"><label>Jenis akun *</label><select name="role" id="role"><option value="siswa" <?=($_POST['role']??'siswa')==='siswa'?'selected':''?>>Siswa</option><option value="guru" <?=($_POST['role']??'')==='guru'?'selected':''?>>Guru</option><?php if($isAdminCreator && $adminCount<3):?><option value="admin" <?=($_POST['role']??'')==='admin'?'selected':''?>>Admin</option><?php endif;?></select></div><div class="field" id="kelasBox"><label>Kelas *</label><input name="kelas" placeholder="Contoh: XI RPL" value="<?=e($_POST['kelas']??'')?>"></div></div>
<div class="field"><label>Nomor HP</label><input name="no_hp" maxlength="20" value="<?=e($_POST['no_hp']??'')?>"></div>
<div class="form-grid"><div class="field"><label>Password *</label><input type="password" name="password" minlength="8" required></div><div class="field"><label>Konfirmasi password *</label><input type="password" name="konfirmasi" minlength="8" required></div></div>
<button class="btn" style="width:100%;padding:13px"><?=$isAdminCreator?'Simpan akun':'Buat akun'?></button></form>
<div class="auth-foot"><?php if($isAdminCreator):?><a href="<?=e(url('admin/pengguna.php'))?>">Kembali ke manajemen pengguna</a><?php else:?>Sudah punya akun? <a href="<?=e(url('login.php'))?>">Masuk</a><?php endif;?></div>
</div></div>
<script>const r=document.getElementById('role'),k=document.getElementById('kelasBox');function toggleKelas(){const show=r.value==='siswa';k.style.display=show?'block':'none';k.querySelector('input').required=show}r.addEventListener('change',toggleKelas);toggleKelas();</script>
<?php require __DIR__.'/includes/footer.php';?>
