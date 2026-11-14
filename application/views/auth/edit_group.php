<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Edit Grup: <?= html_escape($group->name) ?></h3>
	<div>
		<a href="/auth" class="btn btn-outline-secondary btn-add">&larr; Daftar Pengguna</a>
	</div>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>

		<?= form_open('auth/edit_group/' . intval($group->id)) ?>
			<div class="form-group">
				<label for="group_name">Nama Grup</label>
				<input id="group_name" type="text" name="group_name" class="form-control" required value="<?= html_escape(set_value('group_name', $group->name)) ?>" maxlength="100" <?= ! empty($is_admin_group) ? 'readonly' : '' ?>>
				<?php if ( ! empty($is_admin_group)) { ?>
					<small class="form-text text-muted">Grup admin tidak boleh diubah namanya.</small>
				<?php } ?>
			</div>
			<div class="form-group">
				<label for="group_description">Deskripsi</label>
				<input id="group_description" type="text" name="group_description" class="form-control" value="<?= html_escape(set_value('group_description', $group->description)) ?>" maxlength="100">
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
		<?= form_close() ?>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
