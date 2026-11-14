<form id="form-group-edit" method="POST">
	<input type="hidden" name="id" value="<?= intval($group->id) ?>">
	<div class="form-group">
		<label for="name">Nama Grup</label>
		<input id="name" type="text" name="name" class="form-control" required maxlength="150" value="<?= html_escape($group->name) ?>">
	</div>
	<button type="submit" class="btn btn-primary w-100 mt-2">Simpan</button>
</form>
