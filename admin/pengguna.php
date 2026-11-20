<?php
require_once __DIR__.'/../includes/functions.php'; wajibRole('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrfCheck(); $id=(int)($_POST['id']??0); $status=$_POST['status']??'';
    if(in_array($status,['aktif','nonaktif','menunggu'],true)){
        $s=$koneksi->prepare("UPDATE users SET status_akun=? WHERE id_user=? AND role<>'admin'");
        $s->bind_param('si',$status,$id); $s->execute();
        if($s->affected_rows>=0) notifyUser($koneksi,$id,'Status akun diperbarui','Status akun kamu sekarang: '.$status);
        flash('Status akun diperbarui.');
    }
    redirect('admin/pengguna.php');
}
$pageTitle='Pengguna';
$totalUsers=(int)$koneksi->query('SELECT COUNT(*) FROM users')->fetch_row()[0];
$totalStudents=(int)$koneksi->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetch_row()[0];
$totalTeachers=(int)$koneksi->query("SELECT COUNT(*) FROM users WHERE role='guru'")->fetch_row()[0];
$totalAdmins=(int)$koneksi->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
$rows=$koneksi->query('SELECT id_user,nama,username,email,role,kelas,status_akun,created_at FROM users ORDER BY FIELD(role,\'admin\',\'guru\',\'siswa\'), created_at DESC');
require __DIR__.'/../includes/header.php';
?>
<div class="welcome admin-users-hero"><div><span class="welcome-kicker">ADMIN CONTROL CENTER · MANAJEMEN AKUN</span><h1>Pengguna SIBARA</h1><p>Kelola identitas, peran, dan status seluruh pengguna dalam satu tempat.</p></div><a class="btn secondary" href="<?=e(url('register.php'))?>">+ Tambah pengguna</a></div>
<div class="grid admin-users-stats">
<div class="stat stat-purple"><div class="stat-top"><span>Total pengguna</span><span class="stat-icon"><?=sibaraIcon('users')?></span></div><strong><?=$totalUsers?></strong><small>Seluruh akun terdaftar</small></div>
<div class="stat stat-blue"><div class="stat-top"><span>Siswa</span><span class="stat-icon"><?=sibaraIcon('user')?></span></div><strong><?=$totalStudents?></strong><small>Akun siswa</small></div>
<div class="stat stat-pink"><div class="stat-top"><span>Guru</span><span class="stat-icon"><?=sibaraIcon('clipboard-check')?></span></div><strong><?=$totalTeachers?></strong><small>Akun guru</small></div>
<div class="stat stat-teal"><div class="stat-top"><span>Admin</span><span class="stat-icon"><?=sibaraIcon('shield')?></span></div><strong><?=$totalAdmins?> / 3</strong><small><?=max(0,3-$totalAdmins)?> slot admin tersedia</small></div>
</div>
<div class="panel"><div class="panel-head"><div><h2>Daftar seluruh pengguna</h2><small class="muted">Admin dapat melihat akun dan mengatur status siswa/guru.</small></div><span class="badge"><?=$rows->num_rows?> akun</span></div>
<div class="admin-users-toolbar"><input class="search" id="userSearch" type="search" placeholder="Cari nama, username, atau email..."><select class="search" id="roleFilter"><option value="">Semua role</option><option value="admin">Admin</option><option value="guru">Guru</option><option value="siswa">Siswa</option></select></div>
<div class="table-wrap admin-users-table"><table id="usersTable"><thead><tr><th>Nama / Username</th><th>Email</th><th>Role</th><th>Kelas</th><th>Terdaftar</th><th>Status</th><th>Tindakan</th></tr></thead><tbody>
<?php while($r=$rows->fetch_assoc()):?><tr data-role="<?=e($r['role'])?>"><td><div class="user-cell"><span class="user-avatar"><?=e(strtoupper(substr($r['nama'],0,1)))?></span><div><div class="user-name"><?=e($r['nama'])?></div><div class="user-handle">@<?=e($r['username'])?></div></div></div></td><td><?=e($r['email'])?></td><td><span class="role-pill role-<?=e($r['role'])?>"><?=sibaraIcon($r['role']==='admin'?'shield':($r['role']==='guru'?'clipboard-check':'user'))?><?=e(ucfirst($r['role']))?></span></td><td><?=e($r['kelas']?:'-')?></td><td><?=e(date('d/m/Y',strtotime($r['created_at'])))?></td><td><span class="badge <?=e($r['status_akun'])?>"><?=e($r['status_akun'])?></span></td><td><?php if($r['role']!=='admin'):?><form method="post" class="actions"><input type="hidden" name="csrf" value="<?=e(csrfToken())?>"><input type="hidden" name="id" value="<?=$r['id_user']?>"><select name="status" class="search" style="width:105px;padding:7px"><option value="aktif" <?=$r['status_akun']==='aktif'?'selected':''?>>Aktif</option><option value="menunggu" <?=$r['status_akun']==='menunggu'?'selected':''?>>Menunggu</option><option value="nonaktif" <?=$r['status_akun']==='nonaktif'?'selected':''?>>Nonaktif</option></select><button class="btn sm">Ubah</button></form><?php else:?><span class="muted">Admin</span><?php endif;?></td></tr><?php endwhile;?>
</tbody></table></div><div class="empty" id="noUsers" hidden>Tidak ada pengguna yang cocok dengan pencarian.</div></div>
<script>(function(){const q=document.getElementById('userSearch'),f=document.getElementById('roleFilter'),rows=[...document.querySelectorAll('#usersTable tbody tr')],empty=document.getElementById('noUsers');function filter(){let n=0;rows.forEach(r=>{const matchText=r.innerText.toLowerCase().includes(q.value.toLowerCase()),matchRole=!f.value||r.dataset.role===f.value;r.hidden=!(matchText&&matchRole);if(!r.hidden)n++});empty.hidden=n!==0}q.addEventListener('input',filter);f.addEventListener('change',filter)})();</script>
<?php require __DIR__.'/../includes/footer.php';?>
