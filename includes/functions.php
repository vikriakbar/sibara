<?php
require_once __DIR__.'/auth.php';
function e($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function csrfToken(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrfCheck(): void { if($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){ http_response_code(419); exit('Sesi formulir tidak valid. Muat ulang halaman.'); } }
function flash($msg,$type='success'): void { $_SESSION['flash']=['msg'=>$msg,'type'=>$type]; }
function notifyUser(mysqli $db,int $uid,string $title,string $message,string $link=''): void { $s=$db->prepare('INSERT INTO notifikasi(id_user,judul,pesan,tautan) VALUES(?,?,?,?)'); $s->bind_param('isss',$uid,$title,$message,$link); $s->execute(); }
function logActivity(mysqli $db,int $uid,string $activity,string $detail=''): void { $s=$db->prepare('INSERT INTO aktivitas_user(id_user,aktivitas,detail) VALUES(?,?,?)'); $s->bind_param('iss',$uid,$activity,$detail); $s->execute(); }
function currentUser(): array { return $_SESSION['user']??[]; }
function url($path=''): string { return basePath().ltrim($path,'/'); }
function redirect($path): never { header('Location: '.url($path)); exit; }
function statusLabel($s): string { return ucfirst(str_replace('_',' ',$s)); }
function getCount(mysqli $db,string $sql): int { return (int)($db->query($sql)->fetch_row()[0]??0); }


function sibaraIcon(string $name, string $class=''): string {
    $paths = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4.5h6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1Z"/><path d="M8 10h8M8 14h8M8 18h5"/>',
        'hand' => '<path d="M8 11V5a1.5 1.5 0 0 1 3 0v5-7a1.5 1.5 0 0 1 3 0v7-5a1.5 1.5 0 0 1 3 0v7-3a1.5 1.5 0 0 1 3 0v6c0 4-2.5 7-7 7h-1c-2 0-3.5-1-4.5-2.5L4 14a1.7 1.7 0 0 1 2.8-2L8 13"/>',
        'package' => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.7 7.5 4.2 7.5-4.2M12 12v9M8 5.2l8 4.5"/>',
        'compare' => '<path d="M7 7h13m-4-4 4 4-4 4M17 17H4m4-4-4 4 4 4"/>',
        'shield' => '<path d="M12 3 20 6v5c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/>',
        'users' => '<path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20"/><circle cx="9.5" cy="7" r="3.5"/><path d="M17 11a3.5 3.5 0 1 0-1-6.8M21 20v-1.5a4 4 0 0 0-2.8-3.8"/>',
        'database' => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'chart' => '<path d="M4 20V10m5 10V4m5 16v-7m5 7V7"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'plus' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
        'search' => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/>',
        'files' => '<path d="M8 7V4h12v14h-3M4 8h12v13H4z"/><path d="M7 12h6M7 16h6"/>',
        'bookmark' => '<path d="M6 4.5A1.5 1.5 0 0 1 7.5 3h9A1.5 1.5 0 0 1 18 4.5V21l-6-4-6 4V4.5Z"/>',
        'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15m6-12v15"/>',
        'package-check' => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.7 7.5 4.2 7.5-4.2M12 12v9m-3-6 2 2 4-4"/>',
        'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8"/>',
        'badge-check' => '<path d="m12 3 2.2 1.3 2.6-.1 1.2 2.3 2.2 1.4-.5 2.6.5 2.5-2.2 1.5-1.2 2.3-2.6-.1L12 18l-2.2-1.3-2.6.1L6 14.5l-2.2-1.4.5-2.6-.5-2.5L6 6.5l1.2-2.3 2.6.1L12 3Z"/><path d="m8.5 10.5 2.2 2.2 4.8-4.8"/>',
        'package-search' => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.7 7.5 4.2 7.5-4.2M12 12v9"/><circle cx="17.5" cy="16.5" r="2.5"/><path d="m19.4 18.4 2 2"/>',
        'clipboard-check' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4.5h6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1Z"/><path d="m8.5 13 2 2 5-5"/>',
        'inbox' => '<path d="M4 4h16l2 11v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4L4 4Z"/><path d="M2 15h6l2 3h4l2-3h6"/>',
        'megaphone' => '<path d="m3 11 18-6v14L3 13v-2Z"/><path d="M11 15 13 21H8l-2-7"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
    ];
    $body = $paths[$name] ?? $paths['grid'];
    $cls = $class !== '' ? ' class="'.e($class).'"' : '';
    return '<svg'.$cls.' viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$body.'</svg>';
}
