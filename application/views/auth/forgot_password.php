<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/auth_style') ?>
<main class="auth-shell">
	<div class="auth-card">
		<a href="/" class="auth-brand">
			<img src="<?= base_url() ?>assets/img/logo.svg" alt="">Project Tracker
		</a>
		<p class="auth-card__subtitle">Masukkan email akun Anda untuk reset kata sandi.</p>

		<?php if ( ! empty($error)) { ?><div class="auth-alert auth-alert--error"><?= $error ?></div><?php } ?>
		<?php if ( ! empty($success)) { ?><div class="auth-alert auth-alert--success"><?= html_escape($success) ?></div><?php } ?>

		<?= form_open('auth/forgot_password') ?>
			<div class="form-group">
				<label for="identity">Email</label>
				<input id="identity" type="email" name="identity" class="form-control" required autofocus value="<?= html_escape(set_value('identity')) ?>" placeholder="nama@domain.com" maxlength="100">
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Kirim Permintaan Reset</button>
		<?= form_close() ?>

		<div class="auth-card__footer">
			<a href="/auth/login">&larr; Kembali ke login</a>
		</div>
	</div>
</main>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
