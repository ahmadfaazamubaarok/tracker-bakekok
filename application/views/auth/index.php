<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Pengguna</h3>
	<div class="d-flex" style="gap:8px">
		<a href="/auth/create_group" class="btn btn-outline-secondary btn-add">Tambah Grup</a>
		<a href="/auth/create_user" class="btn btn-primary btn-add">Tambah Pengguna</a>
	</div>
</div>

<?php if ( ! empty($error)) { ?><div class="alert alert-danger"><?= $error ?></div><?php } ?>
<?php if ( ! empty($success)) { ?><div class="alert alert-success"><?= html_escape($success) ?></div><?php } ?>

<div class="table-card">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>Nama</th>
					<th>Email</th>
					<th>Grup</th>
					<th width="1%">Status</th>
					<th width="1%">Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $user) { ?>
				<tr>
					<td><?= html_escape($user->first_name . ' ' . $user->last_name) ?></td>
					<td><?= html_escape($user->email) ?></td>
					<td>
						<?php foreach ($user->groups as $group) { ?>
							<a href="/auth/edit_group/<?= intval($group->id) ?>"><?= html_escape($group->name) ?></a><br>
						<?php } ?>
					</td>
					<td class="text-center">
						<?php if ($user->active) { ?>
							<a href="/auth/deactivate/<?= intval($user->id) ?>" class="text-success" title="Klik untuk menonaktifkan">&#9679; Aktif</a>
						<?php } else { ?>
							<a href="/auth/activate/<?= intval($user->id) ?>" class="text-muted" title="Klik untuk mengaktifkan">&#9679; Nonaktif</a>
						<?php } ?>
					</td>
					<td class="text-right">
						<a href="/auth/edit_user/<?= intval($user->id) ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
