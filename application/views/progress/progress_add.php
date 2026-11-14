<form id="form-progress-add" method="POST">
	<div class="form-group">
		<label for="name">Nama Tugas</label>
		<input id="name" type="text" name="name" class="form-control" required maxlength="150" placeholder="Misal: Redesign halaman utama">
	</div>
	<div class="form-group">
		<label for="id_project">Proyek</label>
		<select id="id_project" name="id_project" class="form-control" required>
			<?php foreach ($projects as $project) { ?>
				<option value="<?= intval($project->id) ?>"><?= html_escape($project->group_name) ?> — <?= html_escape($project->name) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<label for="deadline">Deadline</label>
		<input id="deadline" type="date" name="deadline" class="form-control" required value="<?= html_escape($deadline_default) ?>">
	</div>
	<div class="form-group">
		<label for="description">Deskripsi</label>
		<textarea id="description" name="description" class="form-control" maxlength="1000"></textarea>
	</div>
	<div class="form-group">
		<label for="priority">Prioritas</label>
		<select id="priority" name="priority" class="form-control" required>
			<option value="low">Rendah</option>
			<option value="medium" selected>Sedang</option>
			<option value="high">Tinggi</option>
		</select>
	</div>
	<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
</form>
