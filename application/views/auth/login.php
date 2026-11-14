<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/auth_style') ?>
<main class="auth-shell">
	<div class="auth-card">
		<a href="/" class="auth-brand">
			<img src="<?= base_url() ?>assets/img/logo.svg" alt="">Project Tracker
		</a>
		<p class="auth-card__subtitle">Masuk untuk melanjutkan ke board.</p>

		<?php if ( ! empty($error)) { ?><div class="auth-alert auth-alert--error"><?= $error ?></div><?php } ?>
		<?php if ( ! empty($success)) { ?><div class="auth-alert auth-alert--success"><?= html_escape($success) ?></div><?php } ?>

		<?= form_open('auth/login') ?>
			<div class="form-group">
				<label for="identity">Email</label>
				<input id="identity" type="email" name="identity" class="form-control" required autofocus value="<?= html_escape(set_value('identity')) ?>" placeholder="nama@domain.com" maxlength="100">
			</div>
			<div class="form-group">
				<label for="password">Kata Sandi</label>
				<input id="password" type="password" name="password" class="form-control" required placeholder="Kata sandi Anda">
			</div>
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
				<label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Masuk</button>
		<?= form_close() ?>

		<div class="auth-card__footer">
			Lupa kata sandi? <a href="/auth/forgot_password">Reset di sini</a>
		</div>
	</div>
</main>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
