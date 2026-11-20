<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__.'/../koneksi.php';
function wajibLogin(): void { if (empty($_SESSION['user']['id_user'])) { header('Location: '.basePath().'login.php'); exit; } }
function wajibRole($roles): void { wajibLogin(); $roles=(array)$roles; if (!in_array($_SESSION['user']['role']??'', $roles, true)) { http_response_code(403); exit('Akses ditolak.'); } }
function basePath(): string { $script=$_SERVER['SCRIPT_NAME']??''; $depth=substr_count(trim(dirname($script),'/'),'/' ); return str_repeat('../',$depth); }
