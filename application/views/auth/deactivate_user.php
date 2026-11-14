<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Nonaktifkan Pengguna</h3>
	<div>
		<a href="/auth" class="btn btn-outline-secondary btn-add">&larr; Daftar Pengguna</a>
	</div>
</div>
<div class="card" style="max-width: 560px;">
	<div class="card-body">
		<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>

		<p>Anda yakin akan menonaktifkan akun <strong><?= html_escape($user->first_name . ' ' . $user->last_name) ?></strong> (<?= html_escape($user->email) ?>)?</p>
		<p class="text-muted" style="font-size: .85rem;">Pengguna yang dinonaktifkan tidak bisa login sampai diaktifkan kembali.</p>

		<?= form_open('auth/deactivate/' . intval($user->id)) ?>
			<input type="hidden" name="id" value="<?= html_escape(intval($user->id)) ?>">
			<?php foreach ($csrf as $key => $value) { ?>
				<input type="hidden" name="<?= html_escape($key) ?>" value="<?= html_escape($value) ?>">
			<?php } ?>

			<div class="form-group form-check">
				<input class="form-check-input" type="radio" name="confirm" value="yes" id="confirm-yes" required>
				<label class="form-check-label" for="confirm-yes">Ya, nonaktifkan</label>
			</div>
			<div class="form-group form-check">
				<input class="form-check-input" type="radio" name="confirm" value="no" id="confirm-no" checked>
				<label class="form-check-label" for="confirm-no">Tidak</label>
			</div>
			<button type="submit" class="btn btn-primary w-100 mt-2">Kirim</button>
		<?= form_close() ?>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
