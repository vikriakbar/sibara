<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host='localhost'; $user='root'; $pass=''; $db='db_sibara';
try { $koneksi=new mysqli($host,$user,$pass,$db); $koneksi->set_charset('utf8mb4'); }
catch (mysqli_sql_exception $e) { http_response_code(500); exit('Koneksi database gagal. Periksa konfigurasi di koneksi.php dan pastikan database sudah diimpor.'); }
