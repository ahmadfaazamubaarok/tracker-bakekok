<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Ubah Kata Sandi</h3>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>
		<?php if ( ! empty($success)) { ?><div class="alert alert-success"><?= html_escape($success) ?></div><?php } ?>

		<?= form_open('auth/change_password') ?>
			<div class="form-group">
				<label for="old">Kata Sandi Lama</label>
				<input id="old" type="password" name="old" class="form-control" required autofocus>
			</div>
			<div class="form-group">
				<label for="new">Kata Sandi Baru</label>
				<input id="new" type="password" name="new" class="form-control" required placeholder="Minimal <?= intval($min_password_length) ?> karakter" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>
			<div class="form-group">
				<label for="new_confirm">Konfirmasi Kata Sandi Baru</label>
				<input id="new_confirm" type="password" name="new_confirm" class="form-control" required placeholder="Ulangi kata sandi baru" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
		<?= form_close() ?>

		<p class="text-muted mt-3 mb-0" style="font-size: .85rem;">Setelah kata sandi diubah, Anda akan diminta login ulang.</p>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
