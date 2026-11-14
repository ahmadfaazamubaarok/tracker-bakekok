<form id="form-progress-edit" method="POST">
	<input type="hidden" name="id" value="<?= intval($progress->id) ?>">
	<div class="form-group">
		<label for="name">Nama Tugas</label>
		<input id="name" type="text" name="name" class="form-control" required maxlength="150" value="<?= html_escape($progress->name) ?>">
	</div>
	<div class="form-group">
		<label for="id_project">Proyek</label>
		<select id="id_project" name="id_project" class="form-control" required>
			<?php foreach ($projects as $project) { ?>
				<option value="<?= intval($project->id) ?>" <?= (intval($project->id) == intval($progress->project_id)) ? 'selected' : '' ?>><?= html_escape($project->name) ?> — <?= html_escape($project->group_name) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<label for="deadline">Deadline</label>
		<input id="deadline" type="date" name="deadline" class="form-control" required value="<?= html_escape($progress->deadline) ?>">
	</div>
	<div class="form-group">
		<label for="description">Deskripsi</label>
		<textarea id="description" name="description" class="form-control" maxlength="1000"><?= html_escape($progress->description) ?></textarea>
	</div>
	<div class="form-group">
		<label for="priority">Prioritas</label>
		<select id="priority" name="priority" class="form-control" required>
			<option value="low" <?= ($progress->priority == 'low') ? 'selected' : '' ?>>Rendah</option>
			<option value="medium" <?= ($progress->priority == 'medium') ? 'selected' : '' ?>>Sedang</option>
			<option value="high" <?= ($progress->priority == 'high') ? 'selected' : '' ?>>Tinggi</option>
		</select>
	</div>
	<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
</form>
