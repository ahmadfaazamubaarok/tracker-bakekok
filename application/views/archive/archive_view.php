<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<div>
		<h3 class="page-head__title">Arsip</h3>
		<p class="page-head__desc">Progres selesai yang sudah diarsipkan.</p>
	</div>
</div>
<div class="table-card">
	<div class="table-responsive">
		<table class="table" id="tabel-archive">
			<thead>
				<tr>
					<th>Nama</th>
					<th>Proyek</th>
					<th>Grup</th>
					<th>Prioritas</th>
					<th>Deadline</th>
					<th>Diarsipkan</th>
					<th width="1%">Aksi</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot class="filter">
				<tr>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<script>
$(document).ready(function(){
	var tabel_archive = $("#tabel-archive").DataTable({
		ajax: "/archive/archive_daftar",
		dom: 'rt<"d-flex justify-content-end align-items-center flex-wrap mt-3"lp>',
		language: {
			lengthMenu: "_MENU_",
			emptyTable: "Belum ada arsip."
		},
		orderClasses: false
	});

	$(".filter input").on("keyup", function(){
		tabel_archive
			.column($(this).closest("th").index())
			.search(this.value)
			.draw();
	});

	$(document).on("click",".btn-archive-restore",function(){
		let id_progress = $(this).data("id");
		let url = "/progress/progress_restore";

		$.post(url, {
			id: id_progress
		}, function(res){
			if (res.status == "sukses") {
				toastr.success(res.pesan,"Sukses");
				tabel_archive.ajax.reload();
			} else {
				toastr.error(res.pesan,"Gagal");
			}
		}, "json").fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	$(document).on("click",".btn-progress-delete",function(){
		let id_progress = $(this).data("id");
		let url = "/progress/progress_delete";

		bootbox.confirm("Apakah Anda yakin ingin menghapus progres ini secara permanen?", function(result){
			if (result) {
				$.post(url, {
					id: id_progress
				}, function(res){
					if (res.status == "sukses") {
						toastr.success(res.pesan,"Sukses");
						tabel_archive.ajax.reload();
					} else {
						toastr.error(res.pesan,"Gagal");
					}
				}, "json").fail(function(xhr){
					let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
					toastr.error(pesan,"Gagal");
				});
			}
		});
	});
});
</script>
<?php $this->load->view('templates/3_end') ?>
