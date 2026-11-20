<?php
require_once __DIR__.'/includes/functions.php';
$user = currentUser();
if (!empty($user['id_user']) && !empty($user['role'])) {
    redirect($user['role'].'/dashboard.php');
}
// Pengunjung yang membuka alamat utama langsung diarahkan ke halaman login.
redirect('login.php');
