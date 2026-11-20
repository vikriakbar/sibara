
<?php
require_once __DIR__ . '/functions.php';

/* Ikon SVG khusus SIBARA */
function dashboardViewIcon(string $name): string
{
    $icons = [
        'file-text' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',

        'search' => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/>',

        'package-check' => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.4 7.7 7.6 4.4 7.6-4.4M12 12v9M9 14.5l2 2 4-4"/>',

        'inbox' => '<path d="M4 4h16l1 11h-6l-3 3-3-3H3L4 4Z"/><path d="M3 15h6l3 3 3-3h6"/>',

        'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4.5V3h6v1.5M8 9h8M8 13h8M8 17h5"/>',

        'badge-check' => '<path d="M12 3 14.2 4l2.4-.1 1.1 2.1 2 1.3-.5 2.4.5 2.4-2 1.3-1.1 2.1-2.4-.1L12 16.5l-2.2-1.1-2.4.1-1.1-2.1-2-1.3.5-2.4-.5-2.4 2-1.3 1.1-2.1 2.4.1L12 3Z"/><path d="m8.5 10 2.2 2.2 4.8-4.8"/>',

        'bookmark' => '<path d="M6 4.5A1.5 1.5 0 0 1 7.5 3h9A1.5 1.5 0 0 1 18 4.5V21l-6-4-6 4V4.5Z"/>',

        'package-search' => '<path d="m11 3 8 4.5v7"/><path d="m11 3-8 4.5v9L11 21l4-2.2"/><path d="m3.4 7.7 7.6 4.4 7.6-4.4M11 12v9"/><circle cx="18" cy="17" r="2.5"/><path d="m20 19 2 2"/>',

        'megaphone' => '<path d="m3 11 17-6v14L3 13v-2Z"/><path d="M7 14.5 9 21h4l-2-5.1M20 10a3 3 0 0 1 0 4"/>',

        'bell' => '<path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4"/>',
    ];

    $paths = $icons[$name] ?? $icons['file-text'];

    return '<svg class="sibara-svg-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}

function renderDashboard(mysqli $db, string $role): void
{
    $u = currentUser();
    $id = (int)($u['id_user'] ?? 0);

    if ($role === 'admin') {
        $cards = [
            ['Total laporan', getCount($db, 'SELECT COUNT(*) FROM laporan'), 'file-text', 'purple'],
            ['Barang hilang', getCount($db, "SELECT COUNT(*) FROM laporan WHERE jenis_laporan='hilang'"), 'search', 'blue'],
            ['Barang ditemukan', getCount($db, "SELECT COUNT(*) FROM laporan WHERE jenis_laporan='ditemukan'"), 'package-check', 'teal'],
            ['Menunggu klaim', getCount($db, "SELECT COUNT(*) FROM klaim WHERE status_klaim='menunggu'"), 'inbox', 'orange'],
        ];

        $sql = "SELECT l.*,u.nama,k.nama_kategori,lo.nama_lokasi
                FROM laporan l
                JOIN users u ON u.id_user=l.id_user
                JOIN kategori k ON k.id_kategori=l.id_kategori
                JOIN lokasi lo ON lo.id_lokasi=l.id_lokasi
                ORDER BY l.created_at DESC LIMIT 7";

    } elseif ($role === 'guru') {
        $cards = [
            ['Laporan sekolah', getCount($db, 'SELECT COUNT(*) FROM laporan'), 'clipboard', 'purple'],
            ['Menunggu proses', getCount($db, "SELECT COUNT(*) FROM laporan WHERE status='menunggu'"), 'inbox', 'orange'],
            ['Sedang diproses', getCount($db, "SELECT COUNT(*) FROM laporan WHERE status='diproses'"), 'search', 'blue'],
            ['Selesai', getCount($db, "SELECT COUNT(*) FROM laporan WHERE status='selesai'"), 'badge-check', 'teal'],
        ];

        $sql = "SELECT l.*,u.nama,k.nama_kategori,lo.nama_lokasi
                FROM laporan l
                JOIN users u ON u.id_user=l.id_user
                JOIN kategori k ON k.id_kategori=l.id_kategori
                JOIN lokasi lo ON lo.id_lokasi=l.id_lokasi
                WHERE l.moderation_status='aktif'
                ORDER BY l.created_at DESC LIMIT 7";

    } else {
        $s = $db->prepare('SELECT COUNT(*) FROM laporan WHERE id_user=?');
        $s->bind_param('i', $id);
        $s->execute();
        $own = (int)$s->get_result()->fetch_row()[0];

        $s = $db->prepare('SELECT COUNT(*) FROM klaim WHERE id_user=?');
        $s->bind_param('i', $id);
        $s->execute();
        $claims = (int)$s->get_result()->fetch_row()[0];

        $s = $db->prepare('SELECT COUNT(*) FROM bookmark WHERE id_user=?');
        $s->bind_param('i', $id);
        $s->execute();
        $marks = (int)$s->get_result()->fetch_row()[0];

        $cards = [
            ['Laporan saya', $own, 'file-text', 'purple'],
            ['Klaim saya', $claims, 'badge-check', 'blue'],
            ['Barang tersimpan', $marks, 'bookmark', 'pink'],
            ['Barang ditemukan', getCount($db, "SELECT COUNT(*) FROM laporan WHERE jenis_laporan='ditemukan' AND moderation_status='aktif'"), 'package-search', 'teal'],
        ];

        $s = $db->prepare("SELECT l.*,u.nama,k.nama_kategori,lo.nama_lokasi
                           FROM laporan l
                           JOIN users u ON u.id_user=l.id_user
                           JOIN kategori k ON k.id_kategori=l.id_kategori
                           JOIN lokasi lo ON lo.id_lokasi=l.id_lokasi
                           WHERE l.id_user=?
                           ORDER BY l.created_at DESC LIMIT 7");

        $s->bind_param('i', $id);
        $s->execute();
        $res = $s->get_result();
    }

    if ($role !== 'siswa') {
        $res = $db->query($sql);
    }

    $greeting = $role === 'admin'
        ? 'Kelola seluruh aktivitas barang hilang dan temuan sekolah.'
        : ($role === 'guru'
            ? 'Pantau laporan dan bantu proses pengembalian barang.'
            : 'Pantau laporanmu dan temukan barang yang sedang dicari.');

    echo '<div class="welcome">
        <div>
            <span class="welcome-kicker">SISTEM INFORMASI BARANG HILANG & TEMUAN</span>
            <h1>Halo, ' . e($u['nama'] ?? 'Pengguna') . '!</h1>
            <p>' . $greeting . '</p>
        </div>
        <span class="tag">' . e(ucfirst($role)) . ' · SIBARA</span>
    </div>';

    echo '<div class="grid dashboard-stats">';

    foreach ($cards as $c) {
        echo '<div class="stat stat-' . e($c[3]) . '">
            <div class="stat-top">
                <span>' . e($c[0]) . '</span>
                <span class="stat-icon stat-icon-' . e($c[3]) . '">
                    ' . dashboardViewIcon($c[2]) . '
                </span>
            </div>
            <strong>' . (int)$c[1] . '</strong>
            <small>Data terhitung saat ini</small>
        </div>';
    }

    echo '</div>';

    $allLink = $role === 'siswa'
        ? 'siswa/laporan_saya.php'
        : ($role === 'admin' ? 'admin/laporan.php' : 'guru/laporan.php');

    echo '<div class="section-grid">
        <div class="panel">
            <div class="panel-head">
                <h2>' . ($role === 'siswa' ? 'Laporan terbaru saya' : 'Laporan terbaru') . '</h2>
                <a class="btn secondary sm" href="' . e(url($allLink)) . '">Lihat semua</a>
            </div>';

    if ($res->num_rows === 0) {
        echo '<div class="empty">
            <img class="empty-illustration"
                 src="' . e(url('assets/img/illustrations/empty-inbox.webp')) . '"
                 alt="Kotak laporan kosong">
            <p>Belum ada laporan yang tercatat.</p>
        </div>';
    }

    while ($r = $res->fetch_assoc()) {
        $kind = $r['jenis_laporan'] === 'hilang'
            ? 'search'
            : 'package-check';

        echo '<div class="item">
            <div class="item-thumb">' . dashboardViewIcon($kind) . '</div>
            <div class="item-info">
                <b>' . e($r['nama_barang']) . '</b>
                <small>' . e($r['nama_kategori']) . ' · ' . e($r['nama_lokasi']) . '</small>
            </div>
            <span class="badge ' . e($r['status']) . '">' . e(statusLabel($r['status'])) . '</span>
        </div>';
    }

    echo '</div>
        <div class="panel">
            <div class="panel-head">
                <h2>Akses cepat</h2>
                <span class="badge">SIBARA</span>
            </div>';

    $quick = [
        ['Buat laporan barang', 'Laporkan kehilangan atau temuan.', 'megaphone', 'siswa/lapor.php', 'Buka', ''],
        ['Cari barang temuan', 'Telusuri barang yang ditemukan.', 'package-search', 'siswa/barang.php', 'Cari', 'secondary'],
        ['Notifikasi', 'Lihat pembaruan status laporan.', 'bell', 'siswa/notifikasi.php', 'Lihat', 'secondary'],
    ];

    foreach ($quick as $q) {
        echo '<div class="item">
            <div class="item-thumb">' . dashboardViewIcon($q[2]) . '</div>
            <div class="item-info">
                <b>' . e($q[0]) . '</b>
                <small>' . e($q[1]) . '</small>
            </div>
            <a class="btn sm ' . e($q[5]) . '"
               href="' . e(url($q[3])) . '">' . e($q[4]) . '</a>
        </div>';
    }

    echo '</div></div>';
}
