<?php
require_once __DIR__.'/../includes/functions.php'; wajibRole('guru'); $pageTitle='Dashboard'; require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/dashboard_view.php'; renderDashboard($koneksi,'guru'); require __DIR__.'/../includes/footer.php';
