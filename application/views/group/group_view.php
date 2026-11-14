<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<div class="page-head">
	<h3 class="page-head__title">Grup</h3>
	<div>
		<a href="javascript:;" class="btn btn-primary btn-add btn-group-add">Tambah Grup</a>
	</div>
</div>
<div class="table-card">
	<div class="table-responsive">
		<table class="table" id="tabel-group">
			<thead>
				<tr>
					<th>Nama</th>
					<th>Jumlah Proyek</th>
					<th width="1%">Aksi</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot class="filter">
				<tr>
					<th><input type="text" name="" class="form-control w-100"></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
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

	var tabel_group = $("#tabel-group").DataTable({
		ajax: "/group/group_daftar",
		dom: 'rt<"d-flex justify-content-end align-items-center flex-wrap mt-3"lp>',
		language: {
			lengthMenu: "_MENU_",
			emptyTable: "Belum ada grup."
		},
		orderClasses: false
	});

	$(".filter input").on("keyup", function(){
		tabel_group
			.column($(this).closest("th").index())
			.search(this.value)
			.draw();
	});

	$(document).on("click",".btn-group-add",function(){
		frame.find(".modal-title").html("Tambah Grup");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");
		$.get("/group/group_add", function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("click",".btn-group-edit",function(){
		frame.find(".modal-title").html("Edit Grup");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");

		let id_group = $(this).data("id");
		$.get("/group/group_edit/" + id_group, function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("submit","#form-group-add",function(e){
		e.preventDefault();
		$.ajax({
			url: "/group/group_addsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_group.ajax.reload();
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

	$(document).on("submit","#form-group-edit",function(e){
		e.preventDefault();
		$.ajax({
			url: "/group/group_editsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_group.ajax.reload();
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

	$(document).on("click",".btn-group-delete",function(){
		let id_group = $(this).data("id");
		let url = "/group/group_delete";

		bootbox.confirm("Apakah Anda yakin ingin menghapus grup ini?", function(result){
			if (result) {
				$.post(url, {
					id: id_group
				}, function(res){
					if (res.status == "sukses") {
						toastr.success(res.pesan,"Sukses");
						tabel_group.ajax.reload();
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
