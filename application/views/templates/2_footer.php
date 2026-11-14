	</div><!-- /.app-container -->
</main>
<footer class="app-footer">
	<div class="app-container">
		<span class="app-footer__text">Project Tracker V1</span>
	</div>
</footer>
<script src="<?= base_url() ?>cdn/lib/jquery/jquery.min.js"></script>
<script src="<?= base_url() ?>cdn/lib/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>cdn/lib/bootbox/bootbox.all.min.js"></script>
<script src="<?= base_url() ?>cdn/lib/toastr/toastr.min.js"></script>
<script src="<?= base_url() ?>cdn/lib/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>cdn/lib/sortablejs/Sortable.min.js"></script>
<script>
	// Konfigurasi global notifikasi & dialog
	toastr.options = {
		closeButton: true,
		progressBar: false,
		positionClass: "toast-top-right",
		timeOut: 3500,
		extendedTimeOut: 1500
	};
	bootbox.setDefaults({
		buttons: {
			confirm: { label: "Hapus", className: "btn-primary" },
			cancel: { label: "Batal", className: "btn-outline-secondary" }
		}
	});

	// Registrasi service worker (PWA) — offline-ready & installable
	if ("serviceWorker" in navigator) {
		window.addEventListener("load", function () {
			navigator.serviceWorker.register("/sw.js").catch(function () {
				// PWA opsional: kegagalan registrasi tidak mengganggu aplikasi
			});
		});
	}
	toastr.options = {
	  "positionClass": "toast-bottom-left"
	};
</script>
