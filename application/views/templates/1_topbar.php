<?php
// ion_auth di-autoload — dipakai untuk menu akun (ikon gear) di navigasi
$ci        = get_instance();
$auth_user = $ci->ion_auth->user()->row();
$is_admin  = $ci->ion_auth->is_admin();
$nav       = isset($navbar) ? $navbar : '';
?>
<header class="topnav">
	<nav class="navtrack" aria-label="Navigasi utama">
		<a href="/" class="navtrack__brand"><img src="<?= base_url() ?>assets/img/logo.svg" alt="" class="navtrack__logo">Project Tracker</a>
		<div class="navtrack__links">
			<a href="/" class="navtrack__link <?= ($nav == 'board') ? 'is-active' : '' ?>">Board</a>
			<a href="/project" class="navtrack__link <?= ($nav == 'project') ? 'is-active' : '' ?>">Proyek</a>
			<a href="/group" class="navtrack__link <?= ($nav == 'group') ? 'is-active' : '' ?>">Grup</a>
			<a href="/archive" class="navtrack__link <?= ($nav == 'archive') ? 'is-active' : '' ?>">Arsip</a>
			<div class="dropdown">
				<a href="javascript:;" id="navmenu-akun" class="navtrack__link navtrack__gear <?= ($nav == 'auth' || $nav == 'change_password') ? 'is-active' : '' ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Menu akun" title="Menu akun">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navmenu-akun">
					<h6 class="dropdown-header text-truncate" style="max-width: 220px;" title="<?= html_escape($auth_user ? $auth_user->email : 'Akun') ?>"><?= html_escape($auth_user ? $auth_user->email : 'Akun') ?></h6>
					<?php if ($is_admin) { ?>
					<a class="dropdown-item <?= ($nav == 'auth') ? 'active' : '' ?>" href="/auth">Pengguna</a>
					<?php } ?>
					<a class="dropdown-item <?= ($nav == 'change_password') ? 'active' : '' ?>" href="/auth/change_password">Ubah Kata Sandi</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/auth/logout">Keluar</a>
				</div>
			</div>
		</div>
	</nav>
</header>
<main class="app-shell">
	<div class="container app-container">
	<style>
		/* Ikon gear menu akun — ikut gaya navtrack__link, svg dipusatkan */
		.navtrack__gear {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			line-height: 1;
		}
		.navtrack__gear svg { display: block; }
	</style>

<!-- Navigasi Bawah Mobile (Hanya tampil pada mobile screen <= 768px) -->
<nav class="mobile-bottom-nav" aria-label="Navigasi bawah mobile">
	<a href="/" class="mobile-bottom-nav__item <?= ($nav == 'board') ? 'is-active' : '' ?>">
		<span class="mobile-bottom-nav__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="5" height="18" rx="1"/><rect x="10" y="3" width="5" height="12" rx="1"/><rect x="17" y="3" width="5" height="8" rx="1"/></svg>
		</span>
		<span class="mobile-bottom-nav__label">Board</span>
	</a>
	<a href="/project" class="mobile-bottom-nav__item <?= ($nav == 'project') ? 'is-active' : '' ?>">
		<span class="mobile-bottom-nav__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
		</span>
		<span class="mobile-bottom-nav__label">Proyek</span>
	</a>
	<a href="/group" class="mobile-bottom-nav__item <?= ($nav == 'group') ? 'is-active' : '' ?>">
		<span class="mobile-bottom-nav__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
		</span>
		<span class="mobile-bottom-nav__label">Grup</span>
	</a>
	<a href="/archive" class="mobile-bottom-nav__item <?= ($nav == 'archive') ? 'is-active' : '' ?>">
		<span class="mobile-bottom-nav__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
		</span>
		<span class="mobile-bottom-nav__label">Arsip</span>
	</a>
	<a href="javascript:;" class="mobile-bottom-nav__item <?= ($nav == 'auth' || $nav == 'change_password') ? 'is-active' : '' ?>" data-toggle="modal" data-target="#modal_setting">
		<span class="mobile-bottom-nav__icon">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
		</span>
		<span class="mobile-bottom-nav__label">Setting</span>
	</a>
</nav>

<!-- Modal Pengaturan Akun untuk Mobile -->
<div id="modal_setting" class="modal fade" tabindex="-1" role="dialog" aria-label="Pengaturan Akun" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Pengaturan Akun</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body p-0">
				<div class="p-3 border-bottom">
					<small class="text-muted d-block" style="font-size:11px;">Akun terhubung</small>
					<strong class="text-truncate d-block" style="font-size:14px;"><?= html_escape($auth_user ? $auth_user->email : 'Akun') ?></strong>
				</div>
				<div class="list-group list-group-flush">
					<?php if ($is_admin) { ?>
					<a href="/auth" class="list-group-item list-group-item-action <?= ($nav == 'auth') ? 'active' : '' ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
						<span>Pengguna</span>
					</a>
					<?php } ?>
					<a href="/auth/change_password" class="list-group-item list-group-item-action <?= ($nav == 'change_password') ? 'active' : '' ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
						<span>Ubah Kata Sandi</span>
					</a>
					<a href="/auth/logout" class="list-group-item list-group-item-action text-danger font-weight-bold">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
						<span>Keluar</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
