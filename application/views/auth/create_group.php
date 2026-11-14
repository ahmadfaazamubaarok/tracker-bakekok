<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Tambah Grup</h3>
	<div>
		<a href="/auth" class="btn btn-outline-secondary btn-add">&larr; Daftar Pengguna</a>
	</div>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>

		<?= form_open('auth/create_group') ?>
			<div class="form-group">
				<label for="group_name">Nama Grup</label>
				<input id="group_name" type="text" name="group_name" class="form-control" required autofocus value="<?= html_escape(set_value('group_name')) ?>" placeholder="Misal: dev-team (tanpa spasi)" maxlength="100">
			</div>
			<div class="form-group">
				<label for="group_description">Deskripsi</label>
				<input id="group_description" type="text" name="group_description" class="form-control" value="<?= html_escape(set_value('group_description')) ?>" placeholder="Opsional" maxlength="100">
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Buat Grup</button>
		<?= form_close() ?>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
