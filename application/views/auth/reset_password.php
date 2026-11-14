<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/auth_style') ?>
<main class="auth-shell">
	<div class="auth-card">
		<a href="/" class="auth-brand">
			<img src="<?= base_url() ?>assets/img/logo.svg" alt="">Project Tracker
		</a>
		<p class="auth-card__subtitle">Buat kata sandi baru untuk akun <strong><?= html_escape($user->email) ?></strong>.</p>

		<?php if ( ! empty($error)) { ?><div class="auth-alert auth-alert--error"><?= $error ?></div><?php } ?>

		<?= form_open('auth/reset_password/' . $code) ?>
			<div class="form-group">
				<label for="new">Kata Sandi Baru</label>
				<input id="new" type="password" name="new" class="form-control" required autofocus placeholder="Minimal <?= intval($min_password_length) ?> karakter" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>
			<div class="form-group">
				<label for="new_confirm">Konfirmasi Kata Sandi Baru</label>
				<input id="new_confirm" type="password" name="new_confirm" class="form-control" required placeholder="Ulangi kata sandi baru" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>
			<input type="hidden" name="user_id" value="<?= intval($user->id) ?>">
			<?php foreach ($csrf as $key => $value) { ?>
				<input type="hidden" name="<?= html_escape($key) ?>" value="<?= html_escape($value) ?>">
			<?php } ?>
			<button type="submit" class="btn btn-primary w-100 mt-2">Simpan Kata Sandi Baru</button>
		<?= form_close() ?>

		<div class="auth-card__footer">
			<a href="/auth/login">&larr; Kembali ke login</a>
		</div>
	</div>
</main>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
