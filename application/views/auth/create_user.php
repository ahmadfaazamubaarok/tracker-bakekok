<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Tambah Pengguna</h3>
	<div>
		<a href="/auth" class="btn btn-outline-secondary btn-add">&larr; Daftar Pengguna</a>
	</div>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>

		<?= form_open('auth/create_user') ?>
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
			<p class="text-muted" style="font-size: .85rem;">Pengguna baru masuk ke grup <em>members</em>. Grup bisa diubah lewat menu Edit pengguna.</p>
			<button type="submit" class="btn btn-primary w-100 mt-2">Buat Pengguna</button>
		<?= form_close() ?>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
