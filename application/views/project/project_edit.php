<form id="form-project-edit" method="POST">
	<input type="hidden" name="id" value="<?= intval($project->id) ?>">
	<div class="form-group">
		<label for="name">Nama Proyek</label>
		<input id="name" type="text" name="name" class="form-control" required maxlength="150" value="<?= html_escape($project->name) ?>">
	</div>
	<div class="form-group">
		<label for="description">Deskripsi</label>
		<textarea id="description" name="description" class="form-control" maxlength="1000"><?= html_escape($project->description) ?></textarea>
	</div>
	<div class="form-group">
		<label for="id_group">Grup</label>
		<select id="id_group" name="id_group" class="form-control" required>
			<?php foreach ($groups as $group) { ?>
				<option value="<?= intval($group->id) ?>" <?= (intval($group->id) == intval($project->group_id)) ? 'selected' : '' ?>><?= html_escape($group->name) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<label for="domain">Domain</label>
		<input id="domain" type="text" name="domain" class="form-control" required maxlength="255" value="<?= html_escape($project->domain) ?>">
	</div>
	<div class="form-group">
		<label for="directory_path">Direktori</label>
		<input id="directory_path" type="text" name="directory_path" class="form-control" required maxlength="255" value="<?= html_escape($project->directory_path) ?>">
	</div>
	<div class="form-group">
		<label for="color">Warna</label>
		<div class="d-flex align-items-center" style="gap:12px">
			<input id="color" type="color" name="color" class="form-control form-control-color" value="<?= html_escape($project->color) ?>">
			<div class="color-caption"><?= html_escape($project->color) ?></div>
		</div>
	</div>
	<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
</form>
