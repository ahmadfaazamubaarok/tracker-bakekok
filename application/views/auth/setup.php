<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/auth_style') ?>
<main class="auth-shell">
	<div class="auth-card">
		<a href="/" class="auth-brand">
			<img src="<?= base_url() ?>assets/img/logo.svg" alt="">Project Tracker
		</a>
		<p class="auth-card__subtitle">Belum ada pengguna terdaftar. Buat akun admin pertama.</p>

		<?php if ( ! empty($error)) { ?><div class="auth-alert auth-alert--error"><?= $error ?></div><?php } ?>

		<?= form_open('auth/setup') ?>
			<div class="form-group">
				<label for="first_name">Nama Depan</label>
				<input id="first_name" type="text" name="first_name" class="form-control" required autofocus value="<?= html_escape(set_value('first_name')) ?>" placeholder="Misal: Budi" maxlength="50">
			</div>
			<div class="form-group">
				<label for="last_name">Nama Belakang</label>
				<input id="last_name" type="text" name="last_name" class="form-control" required value="<?= html_escape(set_value('last_name')) ?>" placeholder="Misal: Santoso" maxlength="50">
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input id="email" type="email" name="email" class="form-control" required value="<?= html_escape(set_value('email')) ?>" placeholder="nama@domain.com" maxlength="100">
			</div>
			<div class="form-group">
				<label for="password">Kata Sandi</label>
				<input id="password" type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
			</div>
			<div class="form-group">
				<label for="password_confirm">Konfirmasi Kata Sandi</label>
				<input id="password_confirm" type="password" name="password_confirm" class="form-control" required placeholder="Ulangi kata sandi">
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Buat Akun Admin</button>
		<?= form_close() ?>

		<div class="auth-card__footer">
			Sudah punya akun? <a href="/auth/login">Masuk</a>
		</div>
	</div>
</main>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
