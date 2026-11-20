
<?php
require_once __DIR__ . '/../includes/functions.php';
wajibRole('admin');

$pageTitle = 'Statistik';

// Mengambil data statistik
$total = getCount($koneksi, 'SELECT COUNT(*) FROM laporan');
$lost  = getCount($koneksi, "SELECT COUNT(*) FROM laporan WHERE jenis_laporan = 'hilang'");
$found = getCount($koneksi, "SELECT COUNT(*) FROM laporan WHERE jenis_laporan = 'ditemukan'");
$done  = getCount($koneksi, "SELECT COUNT(*) FROM laporan WHERE status = 'selesai'");
$users = getCount($koneksi, 'SELECT COUNT(*) FROM users');

// Menghitung persentase
$lostPercent = $total > 0 ? round(($lost / $total) * 100, 2) : 0;
$foundPercent = $total > 0 ? round(($found / $total) * 100, 2) : 0;

$cards = [
    [
        'label' => 'Total laporan',
        'value' => $total,
        'tint' => '#eeeaff',
        'accent' => '#7655f5',
        'icon' => '<svg viewBox="0 0 24 24"><path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/><path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7"/></svg>'
    ],
    [
        'label' => 'Barang hilang',
        'value' => $lost,
        'tint' => '#e8f2ff',
        'accent' => '#4388e8',
        'icon' => '<svg viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m15.5 15.5 4.2 4.2M8.5 10.8h4.6"/></svg>'
    ],
    [
        'label' => 'Barang ditemukan',
        'value' => $found,
        'tint' => '#e3faf4',
        'accent' => '#20a98e',
        'icon' => '<svg viewBox="0 0 24 24"><path d="m12 3 8 4.3v9.4L12 21l-8-4.3V7.3L12 3Z"/><path d="m4.4 7.5 7.6 4.2 7.6-4.2M12 11.7V21m-3.2-5.2 2.1 2.1 4.4-4.6"/></svg>'
    ],
    [
        'label' => 'Laporan selesai',
        'value' => $done,
        'tint' => '#fff1df',
        'accent' => '#e69a35',
        'icon' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="m8.2 12.1 2.5 2.5 5.2-5.3"/></svg>'
    ],
    [
        'label' => 'Pengguna terdaftar',
        'value' => $users,
        'tint' => '#ffeaf3',
        'accent' => '#d95798',
        'icon' => '<svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8M17 14a4.5 4.5 0 0 1 3.5 4.4"/></svg>'
    ],
];

require __DIR__ . '/../includes/header.php';
?>

<style>
/* ================================
   HALAMAN STATISTIK SIBARA
================================ */

.statistik-hero {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: clamp(24px, 4vw, 42px);
    margin-bottom: 24px;
    color: #fff;
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 26px;
    background: linear-gradient(115deg, #39217e, #5142c8 52%, #20a9c5);
    box-shadow: 0 18px 42px rgba(66,61,170,.2);
}

.statistik-hero::after {
    content: "";
    position: absolute;
    width: 290px;
    height: 290px;
    right: 7%;
    top: -190px;
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 50%;
    box-shadow: 0 0 0 25px rgba(255,255,255,.045),
                0 0 0 55px rgba(255,255,255,.035);
    pointer-events: none;
}

.statistik-hero h1 {
    margin: 0 0 8px;
    color: #fff;
    font-size: clamp(24px, 3vw, 34px);
    font-weight: 800;
    letter-spacing: -.04em;
}

.statistik-hero p {
    margin: 0;
    color: rgba(255,255,255,.84);
}

.statistik-hero .hero-tag {
    position: relative;
    z-index: 1;
    padding: 10px 15px;
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 14px;
    background: rgba(255,255,255,.13);
    color: #fff;
    font-size: 12px;
    white-space: nowrap;
}

/* KARTU STATISTIK */

.statistik-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 205px), 1fr));
    gap: 18px;
}

.statistik-card {
    position: relative;
    overflow: hidden;
    min-height: 148px;
    padding: 22px;
    border: 1px solid var(--line, rgba(120,130,160,.16));
    border-radius: 22px;
    background: var(--surface, #fff);
    box-shadow: 0 10px 28px rgba(31,43,82,.07);
    transition: transform .2s ease, box-shadow .2s ease;
}

.statistik-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 34px rgba(31,43,82,.12);
}

.statistik-card::after {
    content: "";
    position: absolute;
    width: 76px;
    height: 76px;
    right: -24px;
    top: -27px;
    border-radius: 50%;
    background: var(--tint);
    opacity: .95;
}

.statistik-card-top {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    color: var(--muted, #64748b);
    font-size: 13px;
    font-weight: 650;
}

.statistik-icon {
    display: grid;
    place-items: center;
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
    color: var(--accent);
    background: var(--tint);
    border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
    border-radius: 16px;
}

.statistik-icon svg {
    width: 25px;
    height: 25px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.statistik-value {
    display: block;
    margin-top: 14px;
    color: var(--text, #18233d);
    font-size: 34px;
    line-height: 1;
    font-weight: 800;
    letter-spacing: -.04em;
}

/* PANEL KOMPOSISI */

.statistik-panel {
    margin-top: 22px;
    padding: 24px;
    border: 1px solid var(--line, rgba(120,130,160,.16));
    border-radius: 22px;
    background: var(--surface, #fff);
    box-shadow: 0 10px 28px rgba(31,43,82,.06);
}

.statistik-panel h2 {
    margin: 0;
    color: var(--text, #18233d);
    font-size: 18px;
    font-weight: 750;
}

.statistik-bar {
    display: flex;
    height: 14px;
    overflow: hidden;
    margin: 20px 0 16px;
    border-radius: 30px;
    background: var(--tint, #eef1fa);
}

.statistik-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.statistik-legend span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 11px;
    border: 1px solid rgba(120,130,160,.12);
    border-radius: 999px;
    background: #f4f5fa;
    color: #26304a !important;
    font-size: 12px;
    font-weight: 600;
}

.statistik-dot {
    flex: 0 0 8px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

/* ================================
   DARK MODE
================================ */

/* Mendukung beberapa pola selector tema */
html[data-theme="dark"] .statistik-value,
body[data-theme="dark"] .statistik-value,
html.dark .statistik-value,
body.dark .statistik-value,
body.dark-mode .statistik-value,
.dark-mode .statistik-value,
.theme-dark .statistik-value {
    color: #f8fafc !important;
}

html[data-theme="dark"] .statistik-card-top,
body[data-theme="dark"] .statistik-card-top,
html.dark .statistik-card-top,
body.dark .statistik-card-top,
body.dark-mode .statistik-card-top,
.dark-mode .statistik-card-top,
.theme-dark .statistik-card-top {
    color: #cbd5e1;
}

html[data-theme="dark"] .statistik-panel h2,
body[data-theme="dark"] .statistik-panel h2,
html.dark .statistik-panel h2,
body.dark .statistik-panel h2,
body.dark-mode .statistik-panel h2,
.dark-mode .statistik-panel h2,
.theme-dark .statistik-panel h2 {
    color: #f8fafc;
}

/* RESPONSIVE */

@media (max-width: 600px) {
    .statistik-hero {
        align-items: flex-start;
        flex-direction: column;
    }

    .statistik-hero .hero-tag {
        white-space: normal;
    }

    .statistik-grid {
        grid-template-columns: 1fr;
    }

    .statistik-panel {
        padding: 18px;
    }
}
</style>

<section class="statistik-hero">
    <div>
        <h1>Ringkasan aktivitas SIBARA</h1>
        <p>Ikhtisar data laporan dan pengguna sekolah.</p>
    </div>
    <span class="hero-tag">Data keseluruhan</span>
</section>

<section class="statistik-grid" aria-label="Statistik SIBARA">

    <?php foreach ($cards as $card): ?>
        <article class="statistik-card"
            style="
                --tint: <?= htmlspecialchars($card['tint'], ENT_QUOTES, 'UTF-8') ?>;
                --accent: <?= htmlspecialchars($card['accent'], ENT_QUOTES, 'UTF-8') ?>;
            ">

            <div class="statistik-card-top">
                <span>
                    <?= htmlspecialchars($card['label'], ENT_QUOTES, 'UTF-8') ?>
                </span>

                <span class="statistik-icon">
                    <?= $card['icon'] ?>
                </span>
            </div>

            <strong class="statistik-value">
                <?= number_format((int)$card['value']) ?>
            </strong>

        </article>
    <?php endforeach; ?>

</section>

<section class="statistik-panel">

    <h2>Komposisi laporan</h2>

    <div class="statistik-bar"
         role="img"
         aria-label="Perbandingan laporan hilang dan ditemukan">

        <div style="
            width: <?= $lostPercent ?>%;
            background: #7655f5;
        "></div>

        <div style="
            width: <?= $foundPercent ?>%;
            background: #20b99f;
        "></div>

    </div>

    <div class="statistik-legend">

        <span>
            <i class="statistik-dot" style="background:#7655f5"></i>
            Hilang: <?= number_format($lost) ?>
        </span>

        <span>
            <i class="statistik-dot" style="background:#20b99f"></i>
            Ditemukan: <?= number_format($found) ?>
        </span>

        <span>
            <i class="statistik-dot" style="background:#e69a35"></i>
            Selesai: <?= number_format($done) ?>
        </span>

    </div>

</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
