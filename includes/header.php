<?php
require_once __DIR__.'/functions.php';
$user=currentUser(); $role=$user['role']??'guest'; $title=$pageTitle??'Dashboard';
$nav=[
'admin'=>[['Dashboard','admin/dashboard.php','grid-1x2'],['Laporan Barang','admin/laporan.php','clipboard-list'],['Klaim Barang','admin/klaim.php','hand'],['Pengambilan','admin/pengambilan.php','package-check'],['Pencocokan','admin/matching.php','git-compare'],['Moderasi','admin/moderasi.php','shield-check'],['Pengguna','admin/pengguna.php','users'],['Data Master','admin/master_data.php','database'],['Statistik','admin/statistik.php','chart-no-axes-combined'],['Notifikasi','siswa/notifikasi.php','bell']],
'guru'=>[['Dashboard','guru/dashboard.php','grid-1x2'],['Laporan','guru/laporan.php','clipboard-list'],['Klaim Barang','guru/klaim.php','hand'],['Notifikasi','siswa/notifikasi.php','bell']],
'siswa'=>[['Dashboard','siswa/dashboard.php','grid-1x2'],['Buat Laporan','siswa/lapor.php','plus-circle'],['Barang Ditemukan','siswa/barang.php','search'],['Laporan Saya','siswa/laporan_saya.php','files'],['Pelacakan','siswa/tracking.php','search'],['Klaim Saya','siswa/klaim.php','package-check'],['Bukti Klaim','siswa/qr.php','shield-check'],['Bookmark','siswa/bookmark.php','bookmark'],['Notifikasi','siswa/notifikasi.php','bell']]
];
$active=$_SERVER['SCRIPT_NAME']??''; $unread=0;
if(!empty($user['id_user'])) { $q=$koneksi->prepare('SELECT COUNT(*) FROM notifikasi WHERE id_user=? AND dibaca=0'); $q->bind_param('i',$user['id_user']); $q->execute(); $unread=(int)$q->get_result()->fetch_row()[0]; }
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=e($title)?> | SIBARA</title><style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap');
:root{--ink:#172554;--muted:#64748b;--line:#e8edfa;--bg:#f4f6ff;--purple:#6847f5;--blue:#2589f5;--pink:#f45ba5;--teal:#21b8a6;--orange:#ffad48;--shadow:0 14px 38px rgba(44,55,120,.09)}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font-family:'DM Sans',sans-serif}a{color:inherit;text-decoration:none}button,input,select,textarea{font:inherit}.app{display:flex;min-height:100vh}.sidebar{width:258px;background:linear-gradient(165deg,#172b66,#263e89 58%,#4435a7);color:#fff;padding:24px 17px;position:fixed;inset:0 auto 0 0;z-index:10;display:flex;flex-direction:column}.brand{display:flex;align-items:center;gap:11px;padding:0 8px 26px;border-bottom:1px solid #ffffff24}.brand img{width:54px;height:54px;object-fit:contain;border-radius:14px;background:#fff}.brand strong{display:block;font:800 20px 'Plus Jakarta Sans';letter-spacing:1px}.brand small{color:#c8d7ff;font-size:10px;line-height:1.35;display:block}.nav-label{font-size:10px;letter-spacing:1.5px;color:#9fb5ed;margin:24px 12px 10px;font-weight:700}.nav a{display:flex;align-items:center;gap:12px;padding:11px 13px;border-radius:12px;color:#dce6ff;font-size:13px;margin:3px 0;transition:.2s}.nav a:hover,.nav a.active{background:linear-gradient(100deg,#765bff,#438df5);color:#fff;box-shadow:0 8px 20px #121e5838}.nav .ico{width:20px;height:20px;flex:0 0 20px;border:1.5px solid currentColor;border-radius:6px;opacity:.9;position:relative}.nav .ico:after{content:"";position:absolute;left:4px;right:4px;top:6px;height:5px;border-top:1.5px solid currentColor;border-bottom:1.5px solid currentColor}.nav .ico-line{display:inline-block}.side-bottom{margin-top:auto;padding:16px 9px 0;border-top:1px solid #ffffff24;color:#c8d7ff;font-size:11px;line-height:1.6}.main{margin-left:258px;width:calc(100% - 258px);min-height:100vh}.main.guest{margin-left:0;width:100%;min-height:100vh}.topbar{height:78px;background:#ffffffd9;backdrop-filter:blur(12px);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 34px;position:sticky;top:0;z-index:5}.crumb{font-size:12px;color:var(--muted)}.top-title{font:700 18px 'Plus Jakarta Sans';margin-top:3px}.top-actions{display:flex;align-items:center;gap:14px}.icon-btn{width:40px;height:40px;border:1px solid var(--line);border-radius:13px;background:#fff;display:grid;place-items:center;position:relative}.icon-btn svg{display:block;color:#6654d8}.notification-btn:hover{background:#f1efff;border-color:#dcd5ff}.dot{position:absolute;width:8px;height:8px;border-radius:50%;background:#ff5591;right:7px;top:7px}.avatar{width:38px;height:38px;border-radius:13px;background:linear-gradient(135deg,#b5c8ff,#f7c3e1);display:grid;place-items:center;color:#293d8d;font-weight:800}.user-mini{display:flex;align-items:center;gap:10px}.user-mini b{display:block;font-size:12px}.user-mini small{color:var(--muted);font-size:10px}.content{padding:30px 34px 45px;max-width:1600px;margin:auto}.welcome{background:linear-gradient(110deg,#6746f5 0%,#397fe9 60%,#26b6d4 100%);color:white;border-radius:23px;padding:28px 32px;display:flex;justify-content:space-between;align-items:center;overflow:hidden;position:relative;box-shadow:0 16px 32px #5d69df32;margin-bottom:25px}.welcome:after{content:'';font-size:180px;line-height:1;position:absolute;right:35px;top:-35px;color:#ffffff20}.welcome h1{font:800 24px 'Plus Jakarta Sans';margin:0 0 8px}.welcome p{margin:0;color:#e4eaff;font-size:13px}.welcome .tag{background:#ffffff24;padding:9px 13px;border-radius:12px;font-size:11px;z-index:1}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px}.stat{background:#fff;border:1px solid var(--line);border-radius:18px;padding:19px;box-shadow:0 7px 20px #243b7710;position:relative;overflow:hidden}.stat:after{content:'';position:absolute;width:80px;height:80px;border-radius:50%;right:-26px;top:-25px;background:var(--tint)}.stat-top{display:flex;align-items:center;justify-content:space-between;color:var(--muted);font-size:11px;font-weight:600}.stat-icon{width:38px;height:38px;border-radius:12px;background:var(--tint);display:grid;place-items:center;font-size:18px}.stat strong{display:block;font:800 27px 'Plus Jakarta Sans';margin-top:12px}.stat small{color:var(--muted);font-size:10px}.panel{background:#fff;border:1px solid var(--line);border-radius:19px;padding:21px;box-shadow:0 7px 20px #243b770b}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:17px}.panel h2{font:700 15px 'Plus Jakarta Sans';margin:0}.muted{color:var(--muted)}.section-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:18px;margin-top:19px}.btn{border:0;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 15px;border-radius:11px;background:linear-gradient(100deg,var(--purple),var(--blue));color:#fff;font-weight:700;font-size:12px;cursor:pointer;box-shadow:0 6px 14px #5b66d52a}.btn:hover{filter:brightness(1.05);transform:translateY(-1px)}.btn.secondary{background:#eef2ff;color:#4c48bf;box-shadow:none}.btn.danger{background:#fff0f3;color:#d83262;box-shadow:none}.btn.success{background:#e7fbf5;color:#07876f;box-shadow:none}.btn.warning{background:#fff5e8;color:#b76b08;box-shadow:none}.btn.sm{padding:7px 10px;font-size:11px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.field{margin-bottom:15px}.field label{display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:7px}.field input,.field select,.field textarea,.search{width:100%;border:1px solid #e0e6f2;border-radius:11px;padding:11px 13px;background:#fbfcff;outline:none;font-size:12px;color:var(--ink)}.field textarea{min-height:110px;resize:vertical}.field input:focus,.field select:focus,.field textarea:focus,.search:focus{border-color:#8d83fa;box-shadow:0 0 0 3px #8275f51c}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th{text-align:left;color:#8994ae;font-size:10px;letter-spacing:.5px;text-transform:uppercase;padding:12px;border-bottom:1px solid var(--line)}td{padding:13px 12px;border-bottom:1px solid #f0f2f8;vertical-align:middle}tr:last-child td{border-bottom:0}.badge{display:inline-flex;padding:5px 9px;border-radius:30px;font-size:10px;font-weight:700;background:#edf1ff;color:#5552b8}.badge.menunggu{background:#fff4df;color:#b36d05}.badge.diproses{background:#e8f2ff;color:#2473c8}.badge.selesai,.badge.aktif,.badge.disetujui{background:#e4faf2;color:#078365}.badge.ditolak,.badge.disembunyikan{background:#fff0f2;color:#d04467}.alert{padding:12px 15px;border-radius:12px;margin-bottom:17px;font-size:12px;background:#e8f8f2;color:#08785f}.alert.error{background:#fff0f2;color:#bd3458}.empty{padding:24px;text-align:center;color:var(--muted);font-size:12px}.empty-illustration{display:block;width:min(190px,65%);height:145px;object-fit:contain;margin:0 auto 10px}.item-thumb img{width:100%;height:100%;object-fit:contain;padding:2px}.stat-art{width:42px;height:42px;object-fit:contain;border-radius:10px;background:#f5f2ff;padding:2px}.stat-icon{font-size:12px;font-weight:800;color:#5146b8}.empty .big{font-size:35px;margin-bottom:8px}.item{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid #f0f2f8}.item:last-child{border:0}.item-thumb{width:47px;height:47px;border-radius:13px;background:linear-gradient(135deg,#e9eaff,#ffe5f1);display:grid;place-items:center;font-size:20px;overflow:hidden}.item-thumb img{width:100%;height:100%;object-fit:cover}.item-info{flex:1;min-width:0}.item-info b{font-size:12px;display:block}.item-info small{font-size:10px;color:var(--muted)}.actions{display:flex;gap:6px;flex-wrap:wrap}.searchbar{display:flex;gap:10px;align-items:center;margin-bottom:15px}.searchbar .search{max-width:320px}.auth-page{min-height:100vh;background:radial-gradient(circle at 15% 15%,#e7dfff,transparent 28%),radial-gradient(circle at 90% 85%,#c8f5f1,transparent 30%),#f4f6ff;display:grid;place-items:center;padding:25px}.auth-card{width:min(100%,460px);background:#fff;border:1px solid #fff;border-radius:25px;padding:30px;box-shadow:0 25px 65px #35447d20}.auth-logo{display:block;width:105px;height:105px;object-fit:contain;margin:0 auto 12px;border-radius:20px}.auth-card h1{text-align:center;font:800 22px 'Plus Jakarta Sans';margin:5px 0}.auth-card .sub{text-align:center;color:var(--muted);font-size:12px;margin:0 0 24px}.auth-foot{text-align:center;font-size:11px;color:var(--muted);margin-top:18px}.auth-foot a{color:var(--purple);font-weight:700}.footer{text-align:center;color:#9aa5bd;font-size:10px;margin-top:24px}.hamb{display:none}.hero-links{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}@media(max-width:1100px){.grid{grid-template-columns:repeat(2,1fr)}.section-grid{grid-template-columns:1fr}}@media(max-width:760px){.sidebar{transform:translateX(-100%);transition:.25s}.sidebar.open{transform:translateX(0)}.main{margin:0;width:100%}.topbar{padding:0 17px;height:68px}.content{padding:20px 16px 35px}.hamb{display:grid}.grid{gap:10px}.stat{padding:14px}.stat strong{font-size:23px}.welcome{padding:23px 20px}.welcome h1{font-size:20px}.welcome .tag{display:none}.form-grid{grid-template-columns:1fr;gap:0}.user-mini .meta{display:none}}@media(max-width:420px){.grid{grid-template-columns:1fr 1fr}.stat strong{font-size:21px}.panel{padding:15px}}

/* SIBARA UI refresh + theme system */
:root{color-scheme:light;--ink:#172554;--muted:#64748b;--line:#e5eaf5;--bg:#f5f7ff;--surface:#fff;--surface-2:#f8faff;--field:#fbfcff;--shadow:0 16px 42px rgba(37,52,111,.09);--purple:#6847f5;--blue:#2589f5;--pink:#f45ba5;--teal:#21b8a6;--orange:#ffad48}
body{background:radial-gradient(ellipse at 12% 0%,rgba(111,91,255,.08),transparent 30%),radial-gradient(ellipse at 100% 20%,rgba(38,182,212,.07),transparent 28%),var(--bg);color:var(--ink);transition:background .25s,color .25s}
.sidebar{box-shadow:8px 0 35px rgba(25,36,91,.12)}
.topbar{background:color-mix(in srgb,var(--surface) 88%,transparent);border-color:var(--line)}
.content{max-width:1680px;padding:34px clamp(18px,3vw,46px) 52px}
.panel,.stat,.auth-card{background:var(--surface);border-color:var(--line);box-shadow:var(--shadow);transition:background .25s,border-color .25s,box-shadow .25s,transform .2s}
.panel{border-radius:22px;padding:24px}.panel:hover{box-shadow:0 18px 45px rgba(37,52,111,.11)}
.stat{border-radius:20px;padding:22px}.stat:hover{transform:translateY(-3px)}
.stat strong{letter-spacing:-.8px}.stat-top{font-size:12px}
.welcome{min-height:168px;padding:34px 38px;border-radius:26px;background:linear-gradient(112deg,#5c45ed 0%,#4779ee 53%,#22b7ca 100%);box-shadow:0 18px 40px rgba(80,91,220,.2)}
.welcome:after{content:'';width:220px;height:220px;border:1px solid #ffffff25;border-radius:50%;right:5%;top:-90px;box-shadow:0 0 0 28px #ffffff0c,0 0 0 58px #ffffff09}
.welcome h1{font-size:clamp(22px,2vw,29px);letter-spacing:-.7px}.welcome p{font-size:13px;line-height:1.7}
.btn{padding:11px 17px;border-radius:12px;transition:transform .18s,filter .18s,box-shadow .18s}.btn:hover{transform:translateY(-2px);box-shadow:0 9px 20px rgba(83,86,215,.2)}
.btn.secondary{background:var(--surface-2);color:#6154d9;border:1px solid var(--line)}
.field input,.field select,.field textarea,.search{background:var(--field);border-color:var(--line);color:var(--ink);padding:12px 14px;border-radius:12px}
.field label{color:var(--muted);font-size:12px}.field input:focus,.field select:focus,.field textarea:focus,.search:focus{border-color:#8275f5;box-shadow:0 0 0 4px rgba(104,71,245,.12)}
th{color:var(--muted);border-color:var(--line)}td{border-color:var(--line)}
.item{border-color:var(--line)}.item-thumb{background:linear-gradient(135deg,#eeeaff,#e6f7ff)}
.auth-page{background:radial-gradient(circle at 12% 12%,rgba(124,93,255,.18),transparent 30%),radial-gradient(circle at 90% 85%,rgba(34,190,203,.16),transparent 30%),var(--bg)!important}
.auth-card{border-radius:28px;padding:clamp(24px,4vw,38px)}.auth-logo{width:92px;height:92px}.auth-card h1{letter-spacing:-.6px}
.icon-btn{background:var(--surface);color:var(--ink);border-color:var(--line);cursor:pointer}
.theme-toggle{width:42px;height:42px;border:1px solid var(--line);border-radius:14px;background:var(--surface);color:var(--ink);display:grid;place-items:center;cursor:pointer;transition:.2s}
.theme-toggle:hover{transform:translateY(-2px);border-color:#9b8dfb;box-shadow:0 8px 18px rgba(85,76,190,.12)}.theme-toggle svg{width:19px;height:19px}
.footer{color:var(--muted);padding:8px 16px 22px}
/* Dark appearance */
html[data-theme="dark"]{color-scheme:dark;--ink:#edf1ff;--muted:#a5b1cc;--line:#293653;--bg:#0d1426;--surface:#151f35;--surface-2:#1c2943;--field:#101a2e;--shadow:0 16px 42px rgba(0,0,0,.22)}
html[data-theme="dark"] body{background:radial-gradient(ellipse at 12% 0%,rgba(104,71,245,.13),transparent 30%),radial-gradient(ellipse at 100% 20%,rgba(38,182,212,.08),transparent 28%),var(--bg);color:var(--ink)}
html[data-theme="dark"] .topbar{background:rgba(21,31,53,.9)}
html[data-theme="dark"] .panel,html[data-theme="dark"] .stat,html[data-theme="dark"] .auth-card{background:var(--surface);border-color:var(--line)}
html[data-theme="dark"] .field input,html[data-theme="dark"] .field select,html[data-theme="dark"] .field textarea,html[data-theme="dark"] .search{background:var(--field);border-color:var(--line);color:var(--ink)}
html[data-theme="dark"] .btn.secondary{background:#263454;color:#c7c5ff;border-color:#344263}
html[data-theme="dark"] .item-thumb{background:linear-gradient(135deg,#29234f,#173c52)}
html[data-theme="dark"] .badge{background:#29365a;color:#c8d0ff}html[data-theme="dark"] .badge.menunggu{background:#49391f;color:#ffd58c}html[data-theme="dark"] .badge.diproses{background:#1d3c61;color:#a9d4ff}html[data-theme="dark"] .badge.selesai,html[data-theme="dark"] .badge.aktif,html[data-theme="dark"] .badge.disetujui{background:#16483e;color:#8ce6c9}html[data-theme="dark"] .badge.ditolak{background:#51283b;color:#ffabc4}
html[data-theme="dark"] .alert{background:#16483e;color:#9cebd1}html[data-theme="dark"] .alert.error{background:#51283b;color:#ffb3c8}
html[data-theme="dark"] .auth-page{background:radial-gradient(circle at 12% 12%,rgba(124,93,255,.18),transparent 30%),radial-gradient(circle at 90% 85%,rgba(34,190,203,.12),transparent 30%),var(--bg)!important}
html[data-theme="dark"] .auth-card .sub,html[data-theme="dark"] .auth-foot{color:var(--muted)}
@media(max-width:760px){.content{padding:20px 15px 34px}.panel{padding:18px}.welcome{padding:25px 22px;min-height:145px}.top-actions{gap:8px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;transition:none!important;animation:none!important}}

/* ===== SIBARA COLORFUL DESIGN SYSTEM v5 ===== */
:root{
 --ink:#19264b;--muted:#71809f;--line:#e8e9fb;--bg:#f4f5ff;--surface:#fff;--surface-2:#f8f7ff;--field:#fbfaff;
 --purple:#6d4aff;--blue:#3988ff;--pink:#f05aa8;--teal:#19b9ad;--orange:#ffad45;--lime:#a7d94d;
 --shadow:0 18px 48px rgba(65,58,145,.10);--radius:22px
}
html{scroll-behavior:smooth}body{background:radial-gradient(ellipse at 8% 3%,rgba(126,93,255,.14),transparent 26%),radial-gradient(ellipse at 95% 8%,rgba(40,195,213,.12),transparent 25%),radial-gradient(ellipse at 80% 100%,rgba(244,91,168,.08),transparent 24%),var(--bg)}
.sidebar{background:linear-gradient(155deg,#27205f 0%,#4433a4 48%,#245a9d 100%);box-shadow:12px 0 40px rgba(36,36,105,.16)}
.brand img{box-shadow:0 8px 22px rgba(0,0,0,.13)}.nav a{border:1px solid transparent;border-radius:14px;padding:12px 13px}.nav a:hover{background:rgba(255,255,255,.13);border-color:rgba(255,255,255,.14);transform:translateX(3px)}.nav a.active{background:linear-gradient(105deg,#a58bff,#5e9cff);box-shadow:0 9px 24px rgba(9,14,60,.25);border-color:#ffffff30}
.main{background:transparent}.topbar{height:82px;padding-inline:clamp(18px,3vw,42px);box-shadow:0 5px 22px rgba(49,54,125,.035)}.content{padding-top:32px}.top-title{letter-spacing:-.35px}.crumb{font-weight:600;letter-spacing:.2px}
.welcome{min-height:190px;padding:34px clamp(22px,3vw,42px);border-radius:28px;background:linear-gradient(112deg,#6846f5 0%,#5268f1 36%,#328ee9 68%,#22b9c9 100%);box-shadow:0 20px 42px rgba(82,83,218,.23)}.welcome:before{content:"";position:absolute;width:280px;height:280px;border-radius:50%;right:11%;top:-155px;background:rgba(255,255,255,.12);box-shadow:0 0 0 35px rgba(255,255,255,.055),0 0 0 75px rgba(255,255,255,.035)}.welcome:after{content:"";position:absolute;width:145px;height:145px;border-radius:36px;right:4%;bottom:-90px;transform:rotate(35deg);background:rgba(255,255,255,.10)}.welcome h1{font-size:clamp(23px,2.4vw,32px);line-height:1.2}.welcome p{max-width:620px;font-size:14px;line-height:1.8}
.grid{gap:19px}.stat{border:1px solid rgba(255,255,255,.9);border-radius:22px;padding:22px 21px;box-shadow:0 12px 30px rgba(52,62,137,.07)}.stat:before{content:"";position:absolute;inset:auto 0 0;height:4px;background:linear-gradient(90deg,var(--accent,#7559f5),transparent 90%)}.stat:nth-child(4n+1){--accent:#7656f5;--tint:#eee9ff}.stat:nth-child(4n+2){--accent:#24a7ed;--tint:#e5f5ff}.stat:nth-child(4n+3){--accent:#ef65a8;--tint:#ffedf5}.stat:nth-child(4n+4){--accent:#19b9a7;--tint:#e4faf5}.stat-icon{width:46px;height:46px;border-radius:15px;background:var(--tint);color:var(--accent);box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--accent) 12%,transparent)}.stat strong{font-size:31px;color:var(--ink);margin-top:14px}.stat small{font-size:11px}
.panel{border-radius:23px;padding:24px;box-shadow:0 12px 34px rgba(52,62,137,.065)}.panel-head{margin-bottom:20px}.panel h2{font-size:16px;letter-spacing:-.2px}.section-grid{gap:20px}.panel:hover{transform:translateY(-1px)}
.btn{background:linear-gradient(110deg,#704cf5,#398cff);border-radius:13px;padding:11px 17px;box-shadow:0 8px 18px rgba(91,87,220,.20)}.btn:hover{box-shadow:0 12px 24px rgba(91,87,220,.27)}.btn.secondary{background:linear-gradient(110deg,#f0edff,#eaf3ff);color:#5948c7;border:1px solid #e4e2ff}.btn.success{background:linear-gradient(110deg,#e1faf1,#e6fbff);color:#07846e}.btn.warning{background:linear-gradient(110deg,#fff2df,#fff8e9);color:#a96505}.btn.danger{background:linear-gradient(110deg,#ffedf2,#fff0f6);color:#c33b68}
.field input,.field select,.field textarea,.search{border-radius:13px;padding:13px 14px;background:var(--field);border:1px solid var(--line);transition:.2s}.field input:focus,.field select:focus,.field textarea:focus,.search:focus{transform:translateY(-1px);box-shadow:0 0 0 4px rgba(109,74,255,.12)}
th{font-size:10px;letter-spacing:.8px;padding:14px 13px;background:var(--surface-2)}td{padding:15px 13px}tbody tr{transition:background .18s}tbody tr:hover{background:rgba(112,91,245,.045)}
.badge{padding:6px 10px;border-radius:9px;letter-spacing:.1px}.item{padding:13px 0}.item-thumb{width:52px;height:52px;border-radius:16px}.empty{padding:30px 20px}.empty-illustration{width:min(210px,70%);height:160px}
.auth-page{background:radial-gradient(circle at 12% 12%,rgba(123,89,255,.20),transparent 29%),radial-gradient(circle at 88% 84%,rgba(26,190,202,.18),transparent 30%),radial-gradient(circle at 75% 8%,rgba(244,91,168,.11),transparent 22%),var(--bg)!important}.auth-card{border-radius:30px;box-shadow:0 28px 75px rgba(48,49,126,.16);border:1px solid rgba(255,255,255,.85)}.auth-logo{filter:drop-shadow(0 8px 12px rgba(45,52,120,.10))}.auth-card h1{font-size:25px}.auth-card .btn{min-height:48px;font-size:13px}
.theme-toggle{border-radius:14px;background:linear-gradient(145deg,var(--surface),var(--surface-2));box-shadow:0 5px 15px rgba(46,53,120,.06)}.theme-toggle:hover{background:linear-gradient(120deg,#eeeaff,#e5f5ff)}
/* Dark theme: retain vivid accent colors with comfortable contrast */
html[data-theme="dark"]{--ink:#f0f2ff;--muted:#aab6d1;--line:#2b3858;--bg:#0c1326;--surface:#151f38;--surface-2:#1b2947;--field:#111b32;--shadow:0 18px 48px rgba(0,0,0,.25)}
html[data-theme="dark"] body{background:radial-gradient(ellipse at 8% 3%,rgba(126,93,255,.16),transparent 26%),radial-gradient(ellipse at 95% 8%,rgba(40,195,213,.10),transparent 25%),var(--bg)}
html[data-theme="dark"] .topbar{background:rgba(21,31,56,.92);border-color:var(--line)}html[data-theme="dark"] .panel,html[data-theme="dark"] .stat,html[data-theme="dark"] .auth-card{background:var(--surface);border-color:var(--line)}html[data-theme="dark"] .stat strong{color:var(--ink)}html[data-theme="dark"] th{background:#1b2947;color:#aab6d1}html[data-theme="dark"] td{border-color:var(--line)}html[data-theme="dark"] tbody tr:hover{background:rgba(144,126,255,.08)}html[data-theme="dark"] .btn.secondary{background:#29365a;color:#d5d0ff;border-color:#39476c}html[data-theme="dark"] .notification-btn{background:#1b2947;color:#edf1ff;border-color:#354365}html[data-theme="dark"] .notification-btn svg{color:#c8bdff}html[data-theme="dark"] .theme-toggle{background:#1b2947;color:#edf1ff;border-color:#354365}html[data-theme="dark"] .theme-toggle:hover{background:#29365a}html[data-theme="dark"] .field label{color:#bdc7df}html[data-theme="dark"] .item{border-color:var(--line)}
@media(max-width:1100px){.content{padding-inline:22px}.grid{gap:14px}}
@media(max-width:760px){.topbar{height:70px}.content{padding:20px 15px 35px}.welcome{min-height:155px;padding:25px 22px;border-radius:22px}.welcome h1{font-size:22px}.welcome p{font-size:12px}.grid{gap:11px}.stat{padding:16px;border-radius:18px}.stat strong{font-size:25px}.panel{padding:18px;border-radius:19px}}
@media(max-width:420px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.stat{padding:13px}.stat strong{font-size:22px}.stat-top{font-size:10px}.stat-icon{width:39px;height:39px}}


/* ICON SYSTEM REFINEMENT */
.nav .ico{width:34px;height:34px;flex:0 0 34px;border:0;border-radius:11px;background:rgba(255,255,255,.10);display:grid;place-items:center;opacity:1;position:static;color:#dce5ff;transition:background .2s,color .2s,transform .2s}
.nav .ico:after{display:none}
.nav .nav-svg{width:19px;height:19px;display:block}
.nav a{gap:12px;min-height:47px;font-weight:600;letter-spacing:.05px}
.nav a:hover .ico{background:rgba(255,255,255,.19);transform:scale(1.06)}
.nav a.active .ico{background:rgba(255,255,255,.22);color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.18)}
.stat-icon{font-size:0!important}
.stat-icon svg{width:24px;height:24px}
.stat-art{display:none!important}
.stat-top{position:relative;z-index:1}
.stat .stat-top:after{content:"";position:absolute;right:-2px;top:-3px;width:46px;height:46px;border-radius:15px;background:var(--tint);z-index:-1}
.item-thumb{display:grid;place-items:center;color:#6554d8;background:linear-gradient(145deg,#eeeaff,#e4f5ff);border:1px solid rgba(110,90,220,.08)}
.item-thumb svg{width:24px;height:24px;stroke-width:1.9}
.item-thumb img{display:none!important}
.top-actions .icon-btn svg{width:19px;height:19px}
html[data-theme="dark"] .nav .ico{background:rgba(255,255,255,.12);color:#e2e8ff}
html[data-theme="dark"] .item-thumb{background:linear-gradient(145deg,#29264e,#183b51);color:#c6bdff;border-color:#39446b}
@media(max-width:760px){.nav .ico{width:32px;height:32px;flex-basis:32px}.nav a{min-height:45px}}


/* Detailed iconography and dashboard polish v7 */
.welcome-kicker{display:inline-flex;align-items:center;margin-bottom:10px;padding:6px 10px;border:1px solid rgba(255,255,255,.24);border-radius:999px;background:rgba(255,255,255,.13);font-size:9px;font-weight:800;letter-spacing:1.1px;color:#f1f3ff}
.stat-icon{width:48px;height:48px;border-radius:16px;display:grid;place-items:center;position:relative;z-index:1;box-shadow:inset 0 0 0 1px rgba(255,255,255,.6),0 7px 16px rgba(42,48,105,.07)}
.stat-icon svg{width:24px;height:24px;stroke-width:1.9}
.stat-purple .stat-icon{background:linear-gradient(145deg,#eee8ff,#dcd2ff);color:#6846e8}
.stat-blue .stat-icon{background:linear-gradient(145deg,#e5f4ff,#cbe7ff);color:#237fdb}
.stat-pink .stat-icon{background:linear-gradient(145deg,#fff0f7,#ffd9eb);color:#d94e98}
.stat-teal .stat-icon{background:linear-gradient(145deg,#e3fbf6,#c8f2e9);color:#109b87}
.stat-orange .stat-icon{background:linear-gradient(145deg,#fff5e6,#ffe3bd);color:#cb7b17}
.stat .stat-top{min-height:48px;align-items:center}
.stat .stat-top:after{display:none}
.stat strong{position:relative;z-index:1}
.stat small{position:relative;z-index:1}
.item-thumb{flex:0 0 48px}
.item-thumb svg{width:23px;height:23px}
html[data-theme="dark"] .stat-icon{box-shadow:inset 0 0 0 1px rgba(255,255,255,.08),0 7px 16px rgba(0,0,0,.15)}
html[data-theme="dark"] .stat-purple .stat-icon{background:linear-gradient(145deg,#40356e,#302951);color:#c8baff}
html[data-theme="dark"] .stat-blue .stat-icon{background:linear-gradient(145deg,#24476a,#1b3654);color:#a9d8ff}
html[data-theme="dark"] .stat-pink .stat-icon{background:linear-gradient(145deg,#58304c,#40243b);color:#ffb4d6}
html[data-theme="dark"] .stat-teal .stat-icon{background:linear-gradient(145deg,#1c514a,#173c3a);color:#9cebdc}
html[data-theme="dark"] .stat-orange .stat-icon{background:linear-gradient(145deg,#59432b,#3e3023);color:#ffd49b}


/* ===== SIBARA ADMIN SIGNATURE: premium control center ===== */
.admin-shell .topbar{
  background:linear-gradient(105deg,rgba(255,255,255,.96),rgba(246,244,255,.94));
  border-bottom:1px solid #e4defb;
  box-shadow:0 8px 28px rgba(57,43,126,.07)
}
.admin-shell .top-title{color:#30215f;font-size:19px;letter-spacing:-.55px}
.admin-shell .crumb{color:#796da6}
.admin-shell .content{max-width:1740px}
.admin-shell .welcome{
  background:linear-gradient(112deg,#24185f 0%,#4932a7 43%,#397ce4 78%,#23aebd 100%);
  border:1px solid rgba(255,255,255,.24);
  box-shadow:0 20px 48px rgba(57,42,145,.23);
  min-height:202px;position:relative
}
.admin-shell .welcome:before{
  content:"";position:absolute;width:270px;height:270px;right:8%;top:-145px;
  border:1px solid rgba(255,255,255,.22);border-radius:50%;
  box-shadow:0 0 0 24px rgba(255,255,255,.055),0 0 0 52px rgba(255,255,255,.04),0 0 0 82px rgba(255,255,255,.025)
}
.admin-shell .welcome:after{
  content:"";position:absolute;width:112px;height:112px;right:19%;bottom:-66px;
  border:1px solid rgba(255,255,255,.25);border-radius:32px;transform:rotate(35deg);
  background:linear-gradient(135deg,rgba(255,255,255,.13),rgba(255,255,255,.015))
}
.admin-shell .welcome>*{position:relative;z-index:1}
.admin-shell .welcome h1{font-size:clamp(25px,2.3vw,32px);letter-spacing:-1px}
.admin-shell .welcome .tag{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(8px)}
.admin-shell .panel,.admin-shell .stat{border-radius:23px;box-shadow:0 12px 32px rgba(46,38,111,.075)}
.admin-shell .panel{border-color:#e6e2f8}
.admin-shell .stat{border:1px solid rgba(222,218,247,.95);min-height:150px;padding:21px 22px}
.admin-shell .stat:before{content:"";position:absolute;left:0;right:0;bottom:0;height:4px;background:linear-gradient(90deg,var(--admin-accent,#7658e9),transparent 88%)}
.admin-shell .stat-purple{--admin-accent:#7658e9}
.admin-shell .stat-blue{--admin-accent:#3988ed}
.admin-shell .stat-pink{--admin-accent:#df64a5}
.admin-shell .stat-teal{--admin-accent:#22aa99}
.admin-shell .stat-orange{--admin-accent:#e89b3f}
.admin-shell .stat-icon{width:52px;height:52px;border-radius:17px;box-shadow:0 8px 18px rgba(48,43,108,.09),inset 0 0 0 1px rgba(255,255,255,.72)}
.admin-shell .stat strong{font-size:30px}
.admin-shell .sidebar{background:linear-gradient(165deg,#191447 0%,#30216f 48%,#244b8b 100%);box-shadow:12px 0 38px rgba(23,20,73,.2)}
.admin-shell .brand{padding-bottom:24px}
.admin-shell .nav-label{color:#b5aaf1}
.admin-shell .nav a{min-height:49px;border-radius:15px;font-weight:650}
.admin-shell .nav a.active{background:linear-gradient(105deg,#9878ff,#548ff5);box-shadow:0 10px 25px rgba(8,10,45,.3),inset 0 1px 0 rgba(255,255,255,.25)}
.admin-shell .nav .ico{width:34px;height:34px;flex:0 0 34px;border:1px solid rgba(255,255,255,.25);border-radius:11px;background:rgba(255,255,255,.075);display:grid;place-items:center}
.admin-shell .nav .ico:after{display:none}
.admin-shell .nav .nav-svg{width:18px;height:18px}
.admin-shell .nav a.active .ico{background:rgba(255,255,255,.19);border-color:rgba(255,255,255,.36)}
.admin-shell .btn{box-shadow:0 8px 19px rgba(89,67,190,.2)}
.admin-shell .panel-head h2{font-size:16px;letter-spacing:-.25px}
.admin-shell table{font-size:12px}
.admin-shell th{background:#f7f5ff;color:#776c9e}
.admin-shell td{padding:15px 12px}
.admin-shell .search{min-height:43px}
.admin-shell .search:focus{box-shadow:0 0 0 4px rgba(111,82,225,.13)}
/* Admin user-management refinements */
.admin-users-hero{margin-bottom:22px}
.admin-users-hero .welcome-kicker{background:rgba(255,255,255,.15)}
.admin-users-stats{margin-bottom:22px}
.admin-users-stats .stat{min-height:148px}
.admin-users-stats .stat-top{gap:10px}
.admin-users-stats .stat-icon{width:48px;height:48px}
.admin-users-stats .stat-icon svg{width:25px;height:25px}
.admin-users-stats .stat-purple .stat-icon{background:linear-gradient(145deg,#eee8ff,#dcd1ff);color:#6745df}
.admin-users-stats .stat-blue .stat-icon{background:linear-gradient(145deg,#e4f3ff,#c9e6ff);color:#287fce}
.admin-users-stats .stat-pink .stat-icon{background:linear-gradient(145deg,#fff0f7,#ffd8e9);color:#d34c91}
.admin-users-stats .stat-teal .stat-icon{background:linear-gradient(145deg,#e0faf3,#c4eee4);color:#118d79}
.admin-users-table .user-cell{display:flex;align-items:center;gap:11px;min-width:190px}
.admin-users-table .user-avatar{width:39px;height:39px;border-radius:13px;display:grid;place-items:center;flex:0 0 39px;background:linear-gradient(140deg,#e7e0ff,#d9edff);color:#5842ba;font-weight:800;font-size:13px;border:1px solid #fff;box-shadow:0 4px 12px rgba(67,57,139,.1)}
.admin-users-table .role-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:800}
.admin-users-table .role-admin{background:#eee9ff;color:#6747ce}
.admin-users-table .role-guru{background:#e5f3ff;color:#2879bd}
.admin-users-table .role-siswa{background:#e4f8f2;color:#13856f}
.admin-users-table .user-name{font-weight:800;color:var(--ink)}
.admin-users-table .user-handle{font-size:10px;color:var(--muted);margin-top:3px}
.admin-users-toolbar{display:flex;gap:11px;align-items:center;flex-wrap:wrap;margin:17px 0}
.admin-users-toolbar .search{max-width:390px;flex:1}
.admin-users-toolbar select{max-width:180px}
.admin-shell .table-wrap{border:1px solid var(--line);border-radius:16px}
.admin-shell table thead th:first-child{border-radius:14px 0 0 0}
.admin-shell table thead th:last-child{border-radius:0 14px 0 0}
html[data-theme="dark"] .admin-shell .topbar{background:rgba(21,25,54,.94);border-color:#34345e}
html[data-theme="dark"] .admin-shell .top-title{color:#f1edff}
html[data-theme="dark"] .admin-shell .crumb{color:#b7addb}
html[data-theme="dark"] .admin-shell .panel,html[data-theme="dark"] .admin-shell .stat{border-color:#343453}
html[data-theme="dark"] .admin-shell th{background:#20213b;color:#bcb4dc}
html[data-theme="dark"] .admin-users-table .user-name{color:#f0edff}
html[data-theme="dark"] .admin-users-table .role-admin{background:#3b3065;color:#d2c4ff}
html[data-theme="dark"] .admin-users-table .role-guru{background:#203e5d;color:#b9ddff}
html[data-theme="dark"] .admin-users-table .role-siswa{background:#1d493f;color:#a7ead8}
html[data-theme="dark"] .admin-users-table .user-avatar{background:linear-gradient(140deg,#40366d,#254765);color:#e0d8ff;border-color:#4b4b70}
@media(max-width:760px){
 .admin-shell .welcome{min-height:165px;padding:25px 21px}
 .admin-shell .stat{min-height:130px;padding:15px}
 .admin-users-toolbar{align-items:stretch}
 .admin-users-toolbar .search,.admin-users-toolbar select{max-width:none;width:100%}
}


.admin-users-table .role-pill svg{width:13px;height:13px;stroke-width:2}
.admin-users-table tbody tr{transition:background .16s}
.admin-users-table tbody tr:hover{background:rgba(112,91,220,.045)}
.admin-users-table .actions .btn{min-width:62px}
.admin-users-table .badge{white-space:nowrap}
.admin-users-hero .btn.secondary{background:rgba(255,255,255,.94);color:#4b3aa4;border:1px solid rgba(255,255,255,.65)}
html[data-theme="dark"] .admin-users-table tbody tr:hover{background:rgba(164,145,255,.07)}
.avatar-photo{object-fit:cover;border:2px solid #fff;box-shadow:0 4px 12px rgba(50,60,120,.15)}

/* Profile navigation polish: shared by admin, teacher and student dashboards */
.sidebar{overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.24) transparent}
.sidebar .nav{padding-bottom:12px}
.side-account{margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.17)}
.side-profile-link{display:flex;align-items:center;gap:11px;padding:11px 12px;border:1px solid rgba(255,255,255,.17);border-radius:16px;background:linear-gradient(115deg,rgba(255,255,255,.13),rgba(255,255,255,.055));color:#fff;transition:all .2s ease;min-width:0}
.side-profile-link:hover,.side-profile-link.is-active{background:linear-gradient(110deg,rgba(151,123,255,.42),rgba(66,151,244,.3));border-color:rgba(255,255,255,.36);transform:translateY(-1px);box-shadow:0 8px 22px rgba(7,13,50,.18)}
.side-profile-avatar{width:40px;height:40px;flex:0 0 40px;border-radius:13px;display:grid;place-items:center;overflow:hidden;background:linear-gradient(135deg,#f5d0fe,#bfdbfe);color:#43328d;font-size:15px;font-weight:800;border:1px solid rgba(255,255,255,.55)}
.side-profile-avatar img{width:100%;height:100%;object-fit:cover}
.side-profile-meta{display:flex;flex-direction:column;gap:3px;min-width:0;flex:1}
.side-profile-meta b{font-size:12px;font-weight:800}
.side-profile-meta small{font-size:10px;color:#d3ddff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.side-profile-arrow{font-size:17px;color:#d9d4ff}
.side-account .side-bottom{margin-top:12px;padding:11px 5px 0;font-size:9px;opacity:.78}
.user-mini-link{padding:5px 9px 5px 5px;border-radius:14px;transition:background .18s;min-width:0}
.user-mini-link:hover{background:rgba(111,91,220,.09)}
.user-mini-link .meta{min-width:0}
.user-mini-link .meta b,.user-mini-link .meta small{max-width:145px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-mini-link .avatar{flex:0 0 38px}
html[data-theme="dark"] .side-profile-link{background:linear-gradient(115deg,rgba(255,255,255,.1),rgba(255,255,255,.035))}
html[data-theme="dark"] .user-mini-link:hover{background:rgba(180,165,255,.12)}
@media(max-width:760px){.side-account{padding-top:12px}.side-profile-link{padding:10px}.user-mini-link .meta{display:none}.top-actions{gap:7px}.topbar{padding:0 13px}}

</style></head><body><div class="app">
<?php if($role!=='guest'): ?><aside class="sidebar" id="sidebar"><div class="brand"><img src="<?=e(url('assets/img/logo-sibara.png'))?>" alt="Logo SIBARA"><div><strong>SIBARA</strong><small>Barang Hilang & Temuan<br>SMK Negeri 2 Bangkalan</small></div></div><div class="nav-label">MENU UTAMA</div><nav class="nav"><?php foreach(($nav[$role]??[]) as $n): $is=str_ends_with($active,'/'.basename($n[1])); ?><a class="<?=$is?'active':''?>" href="<?=e(url($n[1]))?>"><span class="ico"><?=sibaraIcon((['grid-1x2'=>'grid','clipboard-list'=>'clipboard','hand'=>'hand','git-compare'=>'compare','shield-check'=>'shield','users'=>'users','database'=>'database','chart-no-axes-combined'=>'chart','bell'=>'bell','user-round'=>'user','plus-circle'=>'plus','search'=>'search','files'=>'files','package-check'=>'package-check','bookmark'=>'bookmark'][$n[2]]??'grid'),'nav-svg')?></span><?=e($n[0])?></a><?php endforeach; ?></nav><div class="side-account"><a class="side-profile-link <?=str_ends_with($active,'/profil.php')?'is-active':''?>" href="<?=e(url('profil.php'))?>"><span class="side-profile-avatar"><?php if(!empty($user['foto'])): ?><img src="<?=e(url('uploads/profil/'.$user['foto']))?>" alt=""><?php else: ?><?=e(strtoupper(mb_substr($user['nama']??'U',0,1)))?><?php endif; ?></span><span class="side-profile-meta"><b>Profil Saya</b><small><?=e($user['nama']??'Pengguna')?> · <?=e(ucfirst($role))?></small></span><span class="side-profile-arrow">↗</span></a><div class="side-bottom">SIBARA · Sistem Informasi Barang Hilang dan Temuan<br>SMK Negeri 2 Bangkalan</div></div></aside><?php endif; ?>
<main class="main <?= $role==='guest' ? 'guest' : '' ?> <?=e($role)?>-shell"><header class="topbar"><?php if($role!=='guest'): ?><div style="display:flex;align-items:center;gap:12px"><button class="icon-btn hamb" onclick="document.getElementById('sidebar').classList.toggle('open')"></button><div><div class="crumb">SIBARA / <?=e($role)?></div><div class="top-title"><?=e($title)?></div></div></div><div class="top-actions"><button class="theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema" title="Ganti mode terang/gelap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.6 8.6 0 1 0 20.5 15.2Z"/></svg></button><a class="icon-btn notification-btn" href="<?=e(url('siswa/notifikasi.php'))?>" title="Notifikasi" aria-label="Notifikasi"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg><?php if($unread): ?><i class="dot"></i><?php endif; ?></a><a class="user-mini user-mini-link" href="<?=e(url('profil.php'))?>" title="Buka profil saya"><?php if(!empty($user['foto'])): ?><img class="avatar avatar-photo" src="<?=e(url('uploads/profil/'.$user['foto']))?>" alt="Foto profil"><?php else: ?><div class="avatar"><?=e(strtoupper(mb_substr($user['nama']??'U',0,1)))?></div><?php endif; ?><div class="meta"><b><?=e($user['nama']??'Pengguna')?></b><small><?=e(ucfirst($role))?> · Lihat profil</small></div></a><a class="btn secondary sm" href="<?=e(url('logout.php'))?>">Keluar</a></div><?php else: ?><div class="top-actions"><button class="theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema" title="Ganti mode terang/gelap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.6 8.6 0 1 0 20.5 15.2Z"/></svg></button><a href="<?=e(url('index.php'))?>" class="brand" style="border:0;color:var(--ink)"><img src="<?=e(url('assets/img/logo-sibara.png'))?>" style="width:44px;height:44px"><strong>SIBARA</strong></a><a class="btn" href="<?=e(url('login.php'))?>">Masuk</a></div><?php endif; ?></header><section class="content">
<?php if(isset($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?><div class="alert <?=$f['type']==='error'?'error':''?>"><?=e($f['msg'])?></div><?php endif; ?>
