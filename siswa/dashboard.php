<?php
require_once __DIR__.'/../includes/functions.php'; wajibRole('siswa'); $pageTitle='Dashboard'; require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/dashboard_view.php'; renderDashboard($koneksi,'siswa'); require __DIR__.'/../includes/footer.php';
