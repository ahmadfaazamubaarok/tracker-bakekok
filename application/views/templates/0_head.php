<!DOCTYPE html>
<html lang="id">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Project Tracker</title>
	<!-- PWA -->
	<link rel="manifest" href="<?= base_url() ?>manifest.json">
	<meta name="theme-color" content="#ffffff">
	<meta name="description" content="Kanban board untuk melacak progres proyek pribadi: Pending, WIP, Selesai.">
	<link rel="icon" type="image/svg+xml" href="<?= base_url() ?>assets/img/logo.svg">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url() ?>assets/img/favicon-32.png">
	<link rel="apple-touch-icon" href="<?= base_url() ?>assets/img/apple-touch-icon.png">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="apple-mobile-web-app-title" content="Tracker">
	<!-- Font Inter (substitusi Saans, lihat DESIGN.md) -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..700&display=swap" rel="stylesheet">
	<!-- vendor css -->
	<link href="<?= base_url() ?>cdn/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/datatables/jquery.dataTables.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/toastr/toastr.min.css" rel="stylesheet">
	<!-- app css (design system DESIGN.md) -->
	<link href="<?= base_url() ?>assets/css/app.css?v=<?= file_exists(FCPATH . 'assets/css/app.css') ? filemtime(FCPATH . 'assets/css/app.css') : '2' ?>" rel="stylesheet">
</head>
<body>
