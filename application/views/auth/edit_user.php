<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Edit Pengguna: <?= html_escape($user->first_name . ' ' . $user->last_name) ?></h3>
	<div>
		<a href="/auth" class="btn btn-outline-secondary btn-add">&larr; Daftar Pengguna</a>
	</div>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>

		<?= form_open('auth/edit_user/' . intval($user->id)) ?>
			<input type="hidden" name="id" value="<?= intval($user->id) ?>">
			<?php foreach ($csrf as $key => $value) { ?>
				<input type="hidden" name="<?= html_escape($key) ?>" value="<?= html_escape($value) ?>">
			<?php } ?>

			<div class="form-group">
				<label for="first_name">Nama Depan</label>
				<input id="first_name" type="text" name="first_name" class="form-control" required autofocus value="<?= html_escape(set_value('first_name', $user->first_name)) ?>" maxlength="50">
			</div>
			<div class="form-group">
				<label for="last_name">Nama Belakang</label>
				<input id="last_name" type="text" name="last_name" class="form-control" required value="<?= html_escape(set_value('last_name', $user->last_name)) ?>" maxlength="50">
			</div>
			<div class="form-group">
				<label>Email</label>
				<input type="text" class="form-control" value="<?= html_escape($user->email) ?>" disabled>
				<small class="form-text text-muted">Email tidak dapat diubah.</small>
			</div>

			<hr>
			<div class="form-group">
				<label for="password">Kata Sandi Baru</label>
				<input id="password" type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>
			<div class="form-group">
				<label for="password_confirm">Konfirmasi Kata Sandi Baru</label>
				<input id="password_confirm" type="password" name="password_confirm" class="form-control" placeholder="Ulangi kata sandi baru" pattern=".{<?= intval($min_password_length) ?>,}.*">
			</div>

			<?php if ($this->ion_auth->is_admin()) { ?>
			<hr>
			<div class="form-group">
				<label>Grup</label>
				<?php foreach ($groups as $group) { ?>
				<?php
					$is_checked = FALSE;
					foreach ($currentGroups as $cg) {
						if ($cg['id'] == $group['id']) { $is_checked = TRUE; break; }
					}
				?>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="groups[]" value="<?= intval($group['id']) ?>" id="group-<?= intval($group['id']) ?>" <?= $is_checked ? 'checked' : '' ?>>
					<label class="form-check-label" for="group-<?= intval($group['id']) ?>"><?= html_escape($group['name']) ?></label>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
		<?= form_close() ?>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
