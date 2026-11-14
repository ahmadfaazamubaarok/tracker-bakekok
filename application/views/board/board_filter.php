<form id="form-board-filter" method="POST">
	<div class="form-group">
		<label for="filter-id-group">Grup</label>
		<select id="filter-id-group" name="id_group" class="form-control">
			<option value="0">Semua Grup</option>
			<?php foreach ($groups as $group) { ?>
				<option value="<?= intval($group->id) ?>"><?= html_escape($group->name) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<label for="filter-id-project">Proyek</label>
		<select id="filter-id-project" name="id_project" class="form-control">
			<option value="0">Semua Proyek</option>
			<?php foreach ($projects as $project) { ?>
				<option value="<?= intval($project->id) ?>"><?= html_escape($project->name) ?> — <?= html_escape($project->group_name) ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="d-flex justify-content-between" style="margin-top:16px">
		<button type="button" class="btn btn-outline-secondary btn-filter-reset">Reset</button>
		<button type="submit" class="btn btn-primary">Terapkan</button>
	</div>
</form>
