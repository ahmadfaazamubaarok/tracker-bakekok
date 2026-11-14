<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<?php if ($mode == 'empty_group') { ?>
<div class="page-head">
	<h3 class="page-head__title">Proyek</h3>
</div>
<div class="guard-banner" role="alert">
	<div>
		<p class="guard-banner__title">Belum ada grup.</p>
		<p class="guard-banner__desc">Proyek wajib terhubung ke grup. Buat grup terlebih dahulu.</p>
	</div>
	<div>
		<a href="/group" class="btn btn-primary">Buat Grup &rarr;</a>
	</div>
</div>
<?php } else { ?>
<div class="page-head">
	<h3 class="page-head__title">Proyek</h3>
	<div>
		<a href="javascript:;" class="btn btn-primary btn-add btn-project-add">Tambah Proyek</a>
	</div>
</div>
<div class="table-card">
	<div class="table-responsive">
		<table class="table" id="tabel-project">
			<thead>
				<tr>
					<th>Proyek</th>
					<th>Grup</th>
					<th>Domain</th>
					<th>Direktori</th>
					<th>Jumlah Progres</th>
					<th width="1%">Aksi</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot class="filter">
				<tr>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php } ?>
<div id="modal_frame" class="modal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal_dinamis">
				<div class="modal-body">
					<p>Modal body text goes here.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<script>
$(document).ready(function(){
	let pesan_loading = "<p class='text-center'><em>Memuat...</em></p>";
	let frame = $("#modal_frame");

	var tabel_project = $("#tabel-project").DataTable({
		ajax: "/project/project_daftar",
		dom: 'rt<"d-flex justify-content-end align-items-center flex-wrap mt-3"lp>',
		language: {
			lengthMenu: "_MENU_",
			emptyTable: "Belum ada proyek."
		},
		orderClasses: false
	});

	$(".filter input").on("keyup", function(){
		tabel_project
			.column($(this).closest("th").index())
			.search(this.value)
			.draw();
	});

	// Kode hex caption mengikuti input warna (modal add & edit)
	$(document).on("input", "input[type=color].form-control-color", function(){
		$(this).closest("form").find(".color-caption").text(this.value);
	});

	$(document).on("click",".btn-project-add",function(){
		frame.find(".modal-title").html("Tambah Proyek");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");
		$.get("/project/project_add", function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("click",".btn-project-edit",function(){
		frame.find(".modal-title").html("Edit Proyek");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");

		let id_project = $(this).data("id");
		$.get("/project/project_edit/" + id_project, function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("submit","#form-project-add",function(e){
		e.preventDefault();
		$.ajax({
			url: "/project/project_addsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_project.ajax.reload();
				frame.modal("hide");
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	$(document).on("submit","#form-project-edit",function(e){
		e.preventDefault();
		$.ajax({
			url: "/project/project_editsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_project.ajax.reload();
				frame.modal("hide");
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	$(document).on("click",".btn-project-delete",function(){
		let id_project = $(this).data("id");
		let url = "/project/project_delete";

		bootbox.confirm("Apakah Anda yakin ingin menghapus proyek ini?", function(result){
			if (result) {
				$.post(url, {
					id: id_project
				}, function(res){
					if (res.status == "sukses") {
						toastr.success(res.pesan,"Sukses");
						tabel_project.ajax.reload();
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
