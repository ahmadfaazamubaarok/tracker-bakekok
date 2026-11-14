<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<?php if ($mode == 'empty_group') { ?>
<div class="guard-card">
	<h2 class="guard-card__title">Belum ada grup.</h2>
	<p class="guard-card__desc">Sebelum menambah progres, buat dulu minimal 1 grup.</p>
	<a href="/group" class="btn btn-primary">Buat Grup</a>
</div>
<?php } else if ($mode == 'empty_project') { ?>
<div class="guard-card">
	<h2 class="guard-card__title">Belum ada proyek.</h2>
	<p class="guard-card__desc">Tambahkan minimal 1 proyek untuk mulai melacak progres.</p>
	<a href="/project" class="btn btn-primary">Tambah Proyek</a>
</div>
<?php } else { ?>
<div id="board-view">
	<!-- DESKTOP BOARD: Hanya tampil di desktop (min-width: 769px) -->
	<div class="desktop-board">
		<div class="page-head">
			<h3 class="page-head__title">Board</h3>
			<div class="d-flex" style="gap:8px">
				<a href="javascript:;" class="btn btn-outline-secondary btn-board-filter" aria-label="Filter tugas" title="Filter">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
				</a>
				<a href="javascript:;" class="btn btn-primary btn-add btn-progress-add">Tambah Tugas</a>
			</div>
		</div>
		<div class="kanban">
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Inbox</h5>
					<span class="kanban__count" data-count="pending">0</span>
				</div>
				<div class="kanban__cards" data-column="pending"></div>
			</div>
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Progress</h5>
					<span class="kanban__count" data-count="wip">0</span>
				</div>
				<div class="kanban__cards" data-column="wip"></div>
			</div>
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Done</h5>
					<span class="kanban__count" data-count="done">0</span>
				</div>
				<div class="kanban__cards" data-column="done"></div>
			</div>
		</div>
	</div>

	<!-- MOBILE BOARD: Tampilan khusus mobile view (max-width: 768px) sesuai desain halaman-board.png -->
	<div class="mobile-board">
		<!-- 3 Card Status Utama di Atas: Inbox, Progress, Done (Ikon tanpa lingkaran) -->
		<div class="mstatus-cards">
			<div class="mstatus-card" data-status="pending" id="mstatus-pending">
				<span class="mstatus-card__icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
				</span>
				<span class="mstatus-card__label">Inbox</span>
			</div>
			<div class="mstatus-card is-active" data-status="wip" id="mstatus-wip">
				<span class="mstatus-card__icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</span>
				<span class="mstatus-card__label">Progress</span>
			</div>
			<div class="mstatus-card" data-status="done" id="mstatus-done">
				<span class="mstatus-card__icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
				</span>
				<span class="mstatus-card__label">Done</span>
			</div>
		</div>

		<!-- Panel di Bawah 3 Status Card: default menampilkan status Progress -->
		<div class="mpanel">
			<div class="mpanel__head">
				<div class="d-flex align-items-center" style="gap:10px;">
					<h4 class="mpanel__title" id="mpanel-title">Progress</h4>
					<span class="mpanel__count" id="mpanel-count">0</span>
				</div>
				<a href="javascript:;" class="btn-board-filter mpanel__filter-btn" aria-label="Filter tugas" title="Filter">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
				</a>
			</div>
			<!-- Sliding Viewport: Animasi geser pergantian status -->
			<div class="mpanel__viewport">
				<div class="mpanel__track" id="mpanel-track">
					<div class="mpanel__col" data-mcol="pending">
						<div class="mpanel__list" data-mobile-column="pending"></div>
					</div>
					<div class="mpanel__col" data-mcol="wip">
						<div class="mpanel__list" data-mobile-column="wip"></div>
					</div>
					<div class="mpanel__col" data-mcol="done">
						<div class="mpanel__list" data-mobile-column="done"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- FAB Plus di pojok kanan bawah di atas navbar bottom -->
		<button type="button" class="mobile-fab btn-progress-add" aria-label="Tambah Tugas" title="Tambah Tugas">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
		</button>
	</div>
</div>
<div id="detail-view" class="d-none"></div>
<div id="preview-pop" class="preview-pop d-none" aria-hidden="true"></div>
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
<?php if ($mode == 'board') { ?>
<script>
$(document).ready(function(){
	let pesan_loading = "<p class='text-center'><em>Memuat...</em></p>";
	let frame = $("#modal_frame");
	let bulan_id = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
	let dataByCard = {};
	let previewTimer = null;

	// ====== Filter board — persist di localStorage (tetap aktif lintas refresh & navigasi) ======
	function getBoardFilter() {
		try {
			let f = JSON.parse(localStorage.getItem("tracker_board_filter") || "{}");
			return { id_group: parseInt(f.id_group || 0, 10) || 0, id_project: parseInt(f.id_project || 0, 10) || 0 };
		} catch (e) {
			return { id_group: 0, id_project: 0 };
		}
	}

	function setBoardFilter(f) {
		localStorage.setItem("tracker_board_filter", JSON.stringify(f));
	}

	function boardFilterQuery() {
		let f = getBoardFilter();
		let q = [];
		if (f.id_group > 0) {
			q.push("id_group=" + f.id_group);
		}
		if (f.id_project > 0) {
			q.push("id_project=" + f.id_project);
		}
		return q.length > 0 ? "?" + q.join("&") : "";
	}

	function updateFilterButton() {
		let f = getBoardFilter();
		let aktif = f.id_group > 0 || f.id_project > 0;
		$(".btn-board-filter").toggleClass("btn-filter-active", aktif);
	}

	function escapeHtml(text) {
		return $("<div>").text(text == null ? "" : String(text)).html();
	}

	function formatTanggal(tanggal) {
		let pecah = String(tanggal).split("-");
		if (pecah.length !== 3) {
			return tanggal;
		}
		return parseInt(pecah[2], 10) + " " + bulan_id[parseInt(pecah[1], 10) - 1] + " " + pecah[0];
	}

	function kartuHtml(item, status) {
		let deadlineTeks = escapeHtml(formatTanggal(item.deadline));
		if (item.terlambat) {
			deadlineTeks += " &middot; Terlambat";
		}

		let descHtml = "";
		if (item.description) {
			descHtml = "<div class='kcard__desc'>" + escapeHtml(item.description) + "</div>";
		}

		let subBadgeHtml = "";
		if (item.subtask_total > 0) {
			subBadgeHtml = "<span class='kcard__subtask'>" + item.subtask_done + "/" + item.subtask_total + " sub</span>";
		}

		let arsipHtml = "";
		if (status === "done") {
			arsipHtml = "<div class='kcard__archive'><button type='button' class='btn btn-soft btn-xs btn-card-archive' data-id='" + item.id + "'>Arsipkan</button></div>";
		}

		return "" +
			"<div class='kcard' data-id='" + item.id + "' style='--kcolor:" + item.project_color + "'>" +
				"<div class='kcard__project'><span class='dot' style='background-color:" + item.project_color + "'></span>" + escapeHtml(item.project_name) + "</div>" +
				"<div class='kcard__title'>" + escapeHtml(item.name) + "</div>" +
				"<button type='button' class='kcard__edit btn-progress-edit' data-id='" + item.id + "' aria-label='Edit progres'>&#9998;</button>" +
				"<button type='button' class='kcard__delete btn-card-delete' data-id='" + item.id + "' aria-label='Hapus progres'>&times;</button>" +
				"<div class='kcard__meta'>" +
					"<span class='chip chip--" + item.priority + "'>" + escapeHtml(item.priority_label) + "</span>" +
					"<span class='kcard__deadline" + (item.terlambat ? " kcard__deadline--overdue" : "") + "'>" + deadlineTeks + "</span>" +
					subBadgeHtml +
				"</div>" +
				descHtml +
				arsipHtml +
			"</div>";
	}

	// ====== LOGIKA KHUSUS MOBILE VIEW ======
	let currentMobileStatus = "wip"; // default menampilkan status Progress
	let dataByStatus = { pending: [], wip: [], done: [] };

	const statusLabelMap = {
		pending: "Inbox",
		wip: "Progress",
		done: "Done"
	};

	const statusTranslateMap = {
		pending: "0%",
		wip: "-33.333333%",
		done: "-66.666666%"
	};

	function switchMobileStatus(status, animate) {
		if (!statusLabelMap[status]) return;
		currentMobileStatus = status;

		$(".mstatus-card").removeClass("is-active");
		$("#mstatus-" + status).addClass("is-active");

		$("#mpanel-title").text(statusLabelMap[status]);
		let count = (dataByStatus[status] || []).length;
		$("#mpanel-count").text(count);

		let track = $("#mpanel-track");
		if (animate === false) {
			track.css({ transition: "none", transform: "translateX(" + statusTranslateMap[status] + ")" });
			if (track[0]) track[0].offsetHeight;
			track.css({ transition: "transform 0.35s cubic-bezier(0.25, 1, 0.5, 1)" });
		} else {
			track.css({ transition: "transform 0.35s cubic-bezier(0.25, 1, 0.5, 1)", transform: "translateX(" + statusTranslateMap[status] + ")" });
		}
	}

	function mobileKartuHtml(item, status) {
		let deadlineTeks = escapeHtml(formatTanggal(item.deadline));
		if (item.terlambat) {
			deadlineTeks += " &middot; Terlambat";
		}

		let projectHtml = "";
		if (item.project_name) {
			projectHtml = "<div class='mcard__project'><span class='dot' style='background-color:" + (item.project_color || "#141414") + "'></span>" + escapeHtml(item.project_name) + "</div>";
		}

		let subBadgeHtml = "";
		if (item.subtask_total > 0) {
			subBadgeHtml = "<span class='kcard__subtask'>" + item.subtask_done + "/" + item.subtask_total + " sub</span>";
		}

		let descHtml = "";
		if (item.description) {
			descHtml = "<div class='mcard__desc'>" + escapeHtml(item.description) + "</div>";
		}

		// Khusus status 'done': opsi Arsipkan muncul di kiri saat swipe ke kanan
		let leftActionsHtml = "";
		if (status === "done") {
			leftActionsHtml = "" +
				"<div class='mcard__actions mcard__actions--left'>" +
					"<button type='button' class='mcard__action-btn mcard__btn-archive' data-id='" + item.id + "' aria-label='Arsipkan tugas' title='Arsipkan'>" +
						"<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><polyline points='21 8 21 21 3 21 3 8'></polyline><rect x='1' y='3' width='22' height='5'></rect><line x1='10' y1='12' x2='14' y2='12'></line></svg>" +
						"<span>Arsip</span>" +
					"</button>" +
				"</div>";
		}

		return "" +
			"<div class='mcard-wrapper' data-id='" + item.id + "' data-status='" + status + "'>" +
				leftActionsHtml +
				"<div class='mcard__actions mcard__actions--right'>" +
					"<button type='button' class='mcard__action-btn mcard__btn-edit' data-id='" + item.id + "' aria-label='Edit tugas' title='Edit'>" +
						"<svg width='17' height='17' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'></path><path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'></path></svg>" +
					"</button>" +
					"<button type='button' class='mcard__action-btn mcard__btn-delete' data-id='" + item.id + "' aria-label='Hapus tugas' title='Hapus'>" +
						"<svg width='17' height='17' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><polyline points='3 6 5 6 21 6'></polyline><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path><line x1='10' y1='11' x2='10' y2='17'></line><line x1='14' y1='11' x2='14' y2='17'></line></svg>" +
					"</button>" +
				"</div>" +
				"<div class='mcard' data-id='" + item.id + "' data-status='" + status + "' style='--kcolor:" + (item.project_color || "#141414") + "'>" +
					projectHtml +
					"<div class='mcard__title'>" + escapeHtml(item.name) + "</div>" +
					"<div class='mcard__meta'>" +
						"<span class='chip chip--" + item.priority + "'>" + escapeHtml(item.priority_label) + "</span>" +
						"<span class='mcard__deadline" + (item.terlambat ? " mcard__deadline--overdue" : "") + "'>" + deadlineTeks + "</span>" +
						subBadgeHtml +
					"</div>" +
					descHtml +
				"</div>" +
			"</div>";
	}

	function renderBoard() {
		hidePreview();
		$("#mcard-drop-placeholder").remove();
		$.getJSON("/progress/progress_daftar" + boardFilterQuery(), function(res){
			if (res.status !== "sukses") {
				return;
			}

			let data = res.data;
			dataByStatus = data;
			let total = 0;
			["pending","wip","done"].forEach(function(status){
				total += (data[status] || []).length;
			});

			dataByCard = {};
			["pending","wip","done"].forEach(function(status){
				let daftar = data[status] || [];

				// Kolom Desktop
				let kolom = $(".kanban__cards[data-column='" + status + "']");
				kolom.empty();

				// List Mobile
				let kolomMobile = $(".mpanel__list[data-mobile-column='" + status + "']");
				kolomMobile.empty();

				daftar.forEach(function(item){
					item.status = status;
					dataByCard[item.id] = item;
					kolom.append(kartuHtml(item, status));
					kolomMobile.append(mobileKartuHtml(item, status));
				});

				if (daftar.length === 0) {
					let f = getBoardFilter();
					let filterAktif = f.id_group > 0 || f.id_project > 0;
					if (status === "pending" && total === 0) {
						kolom.html("<div class='kanban__placeholder'>" + (filterAktif ? "Tidak ada tugas yang cocok dengan filter." : "Belum ada progres.<br>Kartu progres baru akan muncul di kolom Pending.") + "</div>");
					} else {
						kolom.html("<div class='kanban__placeholder'>Belum ada kartu</div>");
					}
					kolomMobile.html("<div class='kanban__placeholder'>Belum ada tugas di status ini.</div>");
				}

				$(".kanban__count[data-count='" + status + "']").text(daftar.length);
			});

			switchMobileStatus(currentMobileStatus, false);
		});
	}

	// Drag & drop antar kolom (F5) — persist via progress_reorder
	document.querySelectorAll("[data-column]").forEach(function (el) {
		new Sortable(el, {
			group: "kanban",
			animation: 150,
			draggable: ".kcard",
			ghostClass: "kcard--ghost",
			onEnd: function (evt) {
				var col = evt.to;
				var status = col.getAttribute("data-column");
				var order = [];
				col.querySelectorAll("[data-id]").forEach(function (c) {
					order.push(c.getAttribute("data-id"));
				});
				$.post("/progress/progress_reorder", { status: status, order: order }, function (res) {
					if (res.status !== "sukses") {
						toastr.error(res.pesan || "Gagal menyimpan posisi. Coba lagi.", "Gagal");
						location.reload();
					} else {
						renderBoard();
					}
				}, "json").fail(function () {
					toastr.error("Gagal menyimpan posisi. Coba lagi.", "Gagal");
					location.reload();
				});
			}
		});
	});

	renderBoard();
	updateFilterButton();

	// ====== INTERAKSI KHUSUS MOBILE VIEW ======

	// 1. Tap pada 3 Card Status Utama di atas untuk berganti panel dengan animasi geser
	$(document).on("click", "#board-view .mstatus-card[data-status]", function(){
		let st = $(this).data("status");
		switchMobileStatus(st, true);
	});

	let activeSwipedWrapper = null;
	let suppressCardClick = false;

	function closeSwipedCards() {
		if (activeSwipedWrapper) {
			activeSwipedWrapper.find(".mcard").css({ transform: "translateX(0px)" });
			activeSwipedWrapper.removeClass("is-swiped-open is-swiped-open-left");
			activeSwipedWrapper = null;
		}
	}

	// Klik di luar kartu menutup menu swipe yang sedang terbuka
	$(document).on("click touchstart", function(e){
		if (activeSwipedWrapper && !$(e.target).closest(".mcard-wrapper").length) {
			closeSwipedCards();
		}
	});

	// Klik tombol Edit pada menu swipe
	$(document).on("click", ".mcard__btn-edit", function(e){
		e.stopPropagation();
		let id = $(this).data("id");
		closeSwipedCards();
		frame.find(".modal-title").html("Edit Tugas");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");
		$.get("/progress/progress_edit/" + id, function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	// Klik tombol Hapus pada menu swipe
	$(document).on("click", ".mcard__btn-delete", function(e){
		e.stopPropagation();
		let id = $(this).data("id");
		closeSwipedCards();
		bootbox.confirm("Apakah Anda yakin ingin menghapus tugas ini secara permanen?", function(result){
			if (result) {
				$.post("/progress/progress_delete", { id: id }, function(res){
					if (res.status === "sukses") {
						toastr.success(res.pesan, "Sukses");
						renderBoard();
					} else {
						toastr.error(res.pesan, "Gagal");
					}
				}, "json").fail(function(xhr){
					let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
					toastr.error(pesan, "Gagal");
				});
			}
		});
	});

	// Klik tombol Arsipkan pada status Done (saat swipe ke kanan)
	$(document).on("click", ".mcard__btn-archive", function(e){
		e.stopPropagation();
		let id = $(this).data("id");
		closeSwipedCards();
		$.post("/progress/progress_archive", { id: id }, function(res){
			if (res.status === "sukses") {
				toastr.success(res.pesan, "Sukses");
				renderBoard();
			} else {
				toastr.error(res.pesan, "Gagal");
			}
		}, "json").fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan, "Gagal");
		});
	});

	// Klik pada card tugas mobile: Buka Detail Tugas (atau tutup menu swipe jika sedang terbuka)
	$(document).on("click", ".mcard:not(.msubcard)", function(e){
		if (suppressCardClick) return;
		let card = $(this);
		let wrapper = card.closest(".mcard-wrapper");

		if (wrapper.hasClass("is-swiped-open") || wrapper.hasClass("is-swiped-open-left")) {
			closeSwipedCards();
			return;
		}

		hidePreview();
		loadDetail(card.data("id"));
	});

	// ====== LONG-PRESS (1 DETIK) & DRAG-AND-DROP UBAH STATUS DAN URUTAN ======
	let longPressTimer = null;
	let isDraggingCard = false;
	let dragProxy = null;
	let dragData = null; // { id, originalStatus, currentTargetStatus, targetIndex, cardElement }
	let dragOffset = { x: 0, y: 0 };
	let touchCoords = { startX: 0, startY: 0 };
	let isSwipingCard = false;
	let swipeDirection = null; // "left" atau "right"
	let activeTouchCard = null;

	function cancelLongPress() {
		if (longPressTimer) {
			clearTimeout(longPressTimer);
			longPressTimer = null;
		}
	}

	function startDragOperation(card, clientX, clientY) {
		isDraggingCard = true;
		closeSwipedCards();
		// Tambahkan class agar SEMUA tombol aksi (edit/hapus/arsip) tidak terlihat saat drag
		$(".mobile-board").addClass("is-dragging-active");

		if (navigator.vibrate) {
			navigator.vibrate(50);
		}

		let rect = card[0].getBoundingClientRect();
		dragOffset = {
			x: clientX - rect.left,
			y: clientY - rect.top
		};

		dragData = {
			id: card.data("id"),
			originalStatus: card.data("status"),
			currentTargetStatus: card.data("status"),
			targetIndex: undefined,
			cardElement: card
		};

		dragProxy = card.clone();
		dragProxy.addClass("mcard--dragging-proxy");
		dragProxy.css({
			position: "fixed",
			left: rect.left + "px",
			top: rect.top + "px",
			width: rect.width + "px",
			zIndex: 9999,
			pointerEvents: "none",
			transform: "scale(1.03)",
			boxShadow: "0 12px 30px rgba(0,0,0,0.28)"
		});
		$("body").append(dragProxy);
		card.addClass("is-drag-source");
	}

	function handleMovePointer(clientX, clientY) {
		if (isDraggingCard && dragProxy) {
			dragProxy.css({
				left: (clientX - dragOffset.x) + "px",
				top: (clientY - dragOffset.y) + "px"
			});

			// 1. Cek apakah posisi seretan berada di atas salah satu dari 3 card status utama di atas
			let statuses = ["pending", "wip", "done"];
			let foundTarget = null;

			statuses.forEach(function(st){
				let el = document.getElementById("mstatus-" + st);
				if (el) {
					let r = el.getBoundingClientRect();
					if (clientX >= r.left && clientX <= r.right && clientY >= r.top && clientY <= r.bottom) {
						foundTarget = st;
					}
				}
			});

			$(".mstatus-card").removeClass("is-drop-target");
			if (foundTarget) {
				$("#mstatus-" + foundTarget).addClass("is-drop-target");
				// Saat diarahkan ke status tujuan, seketika ubah isi panel dengan animasi geser
				if (dragData.currentTargetStatus !== foundTarget) {
					dragData.currentTargetStatus = foundTarget;
					switchMobileStatus(foundTarget, true);
				}
			}

			// 2. Tentukan posisi urutan tugas di dalam panel target
			let activeList = $(".mpanel__list[data-mobile-column='" + dragData.currentTargetStatus + "']");
			let wrappers = activeList.children(".mcard-wrapper").not("[data-id='" + dragData.id + "']");
			let insertBeforeEl = null;
			let insertIndex = wrappers.length;

			wrappers.each(function(idx){
				let r = this.getBoundingClientRect();
				let midY = r.top + r.height / 2;
				if (clientY < midY) {
					insertBeforeEl = $(this);
					insertIndex = idx;
					return false; // break
				}
			});

			dragData.targetIndex = insertIndex;

			// Tampilkan garis indikator penempatan (placeholder)
			let placeholder = $("#mcard-drop-placeholder");
			if (!placeholder.length) {
				placeholder = $("<div id='mcard-drop-placeholder' class='mcard-drop-placeholder'></div>");
			}

			if (insertBeforeEl && insertBeforeEl.length) {
				placeholder.insertBefore(insertBeforeEl);
			} else {
				activeList.append(placeholder);
			}
		}
	}

	function handleEndPointer() {
		cancelLongPress();

		if (isDraggingCard) {
			isDraggingCard = false;
			$("#mcard-drop-placeholder").remove();
			$(".mobile-board").removeClass("is-dragging-active");
			$(".mstatus-card").removeClass("is-drop-target");

			if (dragData && dragData.cardElement) {
				dragData.cardElement.removeClass("is-drag-source");
			}

			if (dragProxy) {
				dragProxy.remove();
				dragProxy = null;
			}

			// Susun urutan baru (reorder) di kolom target sesuai posisi drop
			if (dragData) {
				let targetStatus = dragData.currentTargetStatus;
				let cardId = dragData.id;

				let order = [];
				let activeList = $(".mpanel__list[data-mobile-column='" + targetStatus + "']");
				activeList.children(".mcard-wrapper").not("[data-id='" + cardId + "']").each(function(){
					order.push($(this).data("id"));
				});

				let insertAt = (dragData.targetIndex !== undefined) ? dragData.targetIndex : order.length;
				if (insertAt > order.length) insertAt = order.length;
				order.splice(insertAt, 0, cardId);

				// Kirim pembaruan status dan urutan ke backend
				$.post("/progress/progress_reorder", { status: targetStatus, order: order }, function(res){
					if (res.status === "sukses") {
						// Tanpa toastr saat memindahkan status / urutan sesuai instruksi user
						renderBoard();
					} else {
						toastr.error(res.pesan || "Gagal menyimpan posisi", "Gagal");
						renderBoard();
					}
				}, "json").fail(function(){
					toastr.error("Gagal menyimpan posisi", "Gagal");
					renderBoard();
				});
			}

			dragData = null;
			suppressCardClick = true;
			setTimeout(function(){ suppressCardClick = false; }, 300);
		}
	}

	// Touch Events pada .mcard
	$(document).on("touchstart", ".mcard:not(.msubcard)", function(e){
		let card = $(this);
		let touch = e.originalEvent.touches[0];
		activeTouchCard = card;
		touchCoords.startX = touch.clientX;
		touchCoords.startY = touch.clientY;
		isSwipingCard = false;
		swipeDirection = null;

		let wrapper = card.closest(".mcard-wrapper");
		if (activeSwipedWrapper && !activeSwipedWrapper.is(wrapper)) {
			closeSwipedCards();
		}

		cancelLongPress();
		// Tekan tahan ~800ms (sekitar 1 detik) untuk memulai drag drop
		longPressTimer = setTimeout(function(){
			startDragOperation(card, touch.clientX, touch.clientY);
		}, 800);
	});

	$(document).on("touchmove", function(e){
		if (!activeTouchCard) return;
		let touch = e.originalEvent.touches[0];
		let deltaX = touch.clientX - touchCoords.startX;
		let deltaY = touch.clientY - touchCoords.startY;

		if (isDraggingCard) {
			e.preventDefault();
			handleMovePointer(touch.clientX, touch.clientY);
			return;
		}

		// Jika ada pergerakan sebelum long-press: batalkan timer drag
		if (Math.hypot(deltaX, deltaY) > 8) {
			cancelLongPress();
		}

		let wrapper = activeTouchCard.closest(".mcard-wrapper");
		let isDoneStatus = (currentMobileStatus === "done") || (activeTouchCard.data("status") === "done") || (activeTouchCard.attr("data-status") === "done") || (wrapper.data("status") === "done");
		let wasOpenRight = wrapper.hasClass("is-swiped-open");
		let wasOpenLeft = wrapper.hasClass("is-swiped-open-left");

		// Deteksi swipe horizontal
		if (!isSwipingCard) {
			if (deltaX < -6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingCard = true;
				swipeDirection = "left";
			} else if (isDoneStatus && deltaX > 6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingCard = true;
				swipeDirection = "right";
			} else if (wasOpenLeft && deltaX < -6) {
				isSwipingCard = true;
				swipeDirection = "right";
			} else if (wasOpenRight && deltaX > 6) {
				isSwipingCard = true;
				swipeDirection = "left";
			}
		}

		if (isSwipingCard) {
			e.preventDefault();
			if (swipeDirection === "left") {
				let baseOffset = wasOpenRight ? -108 : 0;
				let posX = baseOffset + deltaX;
				if (posX > 0) posX = 0;
				if (posX < -130) posX = -130;
				activeTouchCard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
			} else if (swipeDirection === "right" && isDoneStatus) {
				let baseOffset = wasOpenLeft ? 115 : 0;
				let posX = baseOffset + deltaX;
				if (posX < 0) posX = 0;
				if (posX > 135) posX = 135;
				activeTouchCard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
			}
		}
	});

	$(document).on("touchend touchcancel", function(e){
		cancelLongPress();

		if (isDraggingCard) {
			handleEndPointer();
			activeTouchCard = null;
			return;
		}

		if (isSwipingCard && activeTouchCard) {
			let touch = (e.originalEvent.changedTouches && e.originalEvent.changedTouches[0]) || {};
			let endX = touch.clientX || touchCoords.startX;
			let deltaX = endX - touchCoords.startX;
			let wrapper = activeTouchCard.closest(".mcard-wrapper");
			let isDoneStatus = (currentMobileStatus === "done") || (activeTouchCard.data("status") === "done") || (activeTouchCard.attr("data-status") === "done") || (wrapper.data("status") === "done");
			let wasOpenRight = wrapper.hasClass("is-swiped-open");
			let wasOpenLeft = wrapper.hasClass("is-swiped-open-left");

			activeTouchCard.css("transition", "transform 0.22s cubic-bezier(0.2, 0.9, 0.3, 1)");

			if (swipeDirection === "left") {
				if (!wasOpenRight && deltaX < -30) {
					// Swipe ke kiri: Tampilkan pilihan Edit dan Hapus (ikon)
					activeTouchCard.css("transform", "translateX(-108px)");
					wrapper.addClass("is-swiped-open");
					activeSwipedWrapper = wrapper;
				} else if (wasOpenRight && deltaX > 25) {
					activeTouchCard.css("transform", "translateX(0px)");
					wrapper.removeClass("is-swiped-open");
					activeSwipedWrapper = null;
				} else if (wasOpenRight) {
					activeTouchCard.css("transform", "translateX(-108px)");
				} else {
					activeTouchCard.css("transform", "translateX(0px)");
				}
			} else if (swipeDirection === "right" && isDoneStatus) {
				if (!wasOpenLeft && deltaX > 30) {
					// Swipe ke kanan pada status Done: Tampilkan pilihan Arsipkan
					activeTouchCard.css("transform", "translateX(115px)");
					wrapper.addClass("is-swiped-open-left");
					activeSwipedWrapper = wrapper;
				} else if (wasOpenLeft && deltaX < -25) {
					activeTouchCard.css("transform", "translateX(0px)");
					wrapper.removeClass("is-swiped-open-left");
					activeSwipedWrapper = null;
				} else if (wasOpenLeft) {
					activeTouchCard.css("transform", "translateX(115px)");
				} else {
					activeTouchCard.css("transform", "translateX(0px)");
				}
			}

			isSwipingCard = false;
			swipeDirection = null;
			suppressCardClick = true;
			setTimeout(function(){ suppressCardClick = false; }, 300);
		}

		activeTouchCard = null;
	});

	// Dukungan mouse drag saat pengujian pada mobile viewport emulator
	$(document).on("mousedown", ".mcard:not(.msubcard)", function(e){
		if ($(window).width() > 768) return;
		if (e.which !== 1) return;
		let card = $(this);
		let clientX = e.clientX;
		let clientY = e.clientY;
		activeTouchCard = card;
		touchCoords.startX = clientX;
		touchCoords.startY = clientY;
		isSwipingCard = false;
		swipeDirection = null;

		let wrapper = card.closest(".mcard-wrapper");
		if (activeSwipedWrapper && !activeSwipedWrapper.is(wrapper)) {
			closeSwipedCards();
		}

		cancelLongPress();
		longPressTimer = setTimeout(function(){
			startDragOperation(card, clientX, clientY);
		}, 800);
	});

	$(document).on("mousemove", function(e){
		if ($(window).width() > 768 || !activeTouchCard) return;
		let deltaX = e.clientX - touchCoords.startX;
		let deltaY = e.clientY - touchCoords.startY;

		if (isDraggingCard) {
			e.preventDefault();
			handleMovePointer(e.clientX, e.clientY);
			return;
		}

		if (Math.hypot(deltaX, deltaY) > 8) {
			cancelLongPress();
		}

		let wrapper = activeTouchCard.closest(".mcard-wrapper");
		let isDoneStatus = (currentMobileStatus === "done") || (activeTouchCard.data("status") === "done") || (activeTouchCard.attr("data-status") === "done") || (wrapper.data("status") === "done");
		let wasOpenRight = wrapper.hasClass("is-swiped-open");
		let wasOpenLeft = wrapper.hasClass("is-swiped-open-left");

		if (!isSwipingCard) {
			if (deltaX < -6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingCard = true;
				swipeDirection = "left";
			} else if (isDoneStatus && deltaX > 6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingCard = true;
				swipeDirection = "right";
			} else if (wasOpenLeft && deltaX < -6) {
				isSwipingCard = true;
				swipeDirection = "right";
			} else if (wasOpenRight && deltaX > 6) {
				isSwipingCard = true;
				swipeDirection = "left";
			}
		}

		if (isSwipingCard) {
			if (swipeDirection === "left") {
				let baseOffset = wasOpenRight ? -108 : 0;
				let posX = baseOffset + deltaX;
				if (posX > 0) posX = 0;
				if (posX < -130) posX = -130;
				activeTouchCard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
			} else if (swipeDirection === "right" && isDoneStatus) {
				let baseOffset = wasOpenLeft ? 115 : 0;
				let posX = baseOffset + deltaX;
				if (posX < 0) posX = 0;
				if (posX > 135) posX = 135;
				activeTouchCard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
			}
		}
	});

	$(document).on("mouseup", function(e){
		if ($(window).width() > 768) return;
		cancelLongPress();

		if (isDraggingCard) {
			handleEndPointer();
			activeTouchCard = null;
			return;
		}

		if (isSwipingCard && activeTouchCard) {
			let deltaX = e.clientX - touchCoords.startX;
			let wrapper = activeTouchCard.closest(".mcard-wrapper");
			let isDoneStatus = (currentMobileStatus === "done") || (activeTouchCard.data("status") === "done") || (activeTouchCard.attr("data-status") === "done") || (wrapper.data("status") === "done");
			let wasOpenRight = wrapper.hasClass("is-swiped-open");
			let wasOpenLeft = wrapper.hasClass("is-swiped-open-left");

			activeTouchCard.css("transition", "transform 0.22s cubic-bezier(0.2, 0.9, 0.3, 1)");

			if (swipeDirection === "left") {
				if (!wasOpenRight && deltaX < -30) {
					activeTouchCard.css("transform", "translateX(-108px)");
					wrapper.addClass("is-swiped-open");
					activeSwipedWrapper = wrapper;
				} else if (wasOpenRight && deltaX > 25) {
					activeTouchCard.css("transform", "translateX(0px)");
					wrapper.removeClass("is-swiped-open");
					activeSwipedWrapper = null;
				} else if (wasOpenRight) {
					activeTouchCard.css("transform", "translateX(-108px)");
				} else {
					activeTouchCard.css("transform", "translateX(0px)");
				}
			} else if (swipeDirection === "right" && isDoneStatus) {
				if (!wasOpenLeft && deltaX > 30) {
					activeTouchCard.css("transform", "translateX(115px)");
					wrapper.addClass("is-swiped-open-left");
					activeSwipedWrapper = wrapper;
				} else if (wasOpenLeft && deltaX < -25) {
					activeTouchCard.css("transform", "translateX(0px)");
					wrapper.removeClass("is-swiped-open-left");
					activeSwipedWrapper = null;
				} else if (wasOpenLeft) {
					activeTouchCard.css("transform", "translateX(115px)");
				} else {
					activeTouchCard.css("transform", "translateX(0px)");
				}
			}

			isSwipingCard = false;
			swipeDirection = null;
			suppressCardClick = true;
			setTimeout(function(){ suppressCardClick = false; }, 300);
		}

		activeTouchCard = null;
	});


	$(document).on("click",".btn-progress-add",function(){
		frame.find(".modal-title").html("Tambah Tugas");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");
		$.get("/progress/progress_add", function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	// ====== Modal filter board ======
	$(document).on("click",".btn-board-filter",function(){
		frame.find(".modal-title").html("Filter Tugas");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");

		let f = getBoardFilter();
		$.get("/board/board_filter", function(res) {
			frame.find(".modal-body").html(res);
			// preselect nilai filter tersimpan (jika opsinya masih ada)
			let selG = $("#filter-id-group");
			if (selG.find("option[value='" + f.id_group + "']").length) {
				selG.val(String(f.id_group));
			}
			let selP = $("#filter-id-project");
			if (selP.find("option[value='" + f.id_project + "']").length) {
				selP.val(String(f.id_project));
			}
		});
	});

	$(document).on("submit","#form-board-filter",function(e){
		e.preventDefault();
		setBoardFilter({
			id_group: parseInt($("#filter-id-group").val(), 10) || 0,
			id_project: parseInt($("#filter-id-project").val(), 10) || 0
		});
		updateFilterButton();
		frame.modal("hide");
		toastr.success("Filter diterapkan.","Sukses");
		renderBoard();
	});

	$(document).on("click",".btn-filter-reset",function(){
		setBoardFilter({ id_group: 0, id_project: 0 });
		updateFilterButton();
		frame.modal("hide");
		toastr.success("Filter direset.","Sukses");
		renderBoard();
	});

	$(document).on("submit","#form-progress-add",function(e){
		e.preventDefault();
		$.ajax({
			url: "/progress/progress_addsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				frame.modal("hide");
				renderBoard();
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	$(document).on("click",".btn-progress-edit",function(){
		frame.find(".modal-title").html("Edit Tugas");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");

		let id_progress = $(this).data("id");
		$.get("/progress/progress_edit/" + id_progress, function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("submit","#form-progress-edit",function(e){
		e.preventDefault();
		$.ajax({
			url: "/progress/progress_editsave",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				frame.modal("hide");
				renderBoard();
				if (detailId) {
					loadDetail(detailId);
				}
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	// ====== Detail Tugas: mengganti html utama halaman board via AJAX ======
	let detailId = null;

	function loadDetail(id) {
		detailId = id;
		hidePreview();
		$("body").addClass("has-detail-open");
		$("#board-view").addClass("d-none");
		let detailView = $("#detail-view");
		detailView.removeClass("d-none");
		detailView.html("<div class='page-head'><h3 class='page-head__title'>Memuat...</h3></div>");
		detailView.scrollTop(0);
		$(document).scrollTop(0);

		$.get("/progress/progress_detail/" + id, function(res) {
			detailView.html(res);
			initSubtaskSortable();
			renderSubtasks(id);
		}).fail(function(){
			toastr.error("Terjadi error di server","Gagal");
			closeDetail();
		});
	}

	function closeDetail() {
		detailId = null;
		hidePreview();
		closeSwipedSubCards();
		$("body").removeClass("has-detail-open");
		$("#detail-view").addClass("d-none").empty();
		$("#board-view").removeClass("d-none");
		renderBoard();
	}

	function initSubtaskSortable() {
		document.querySelectorAll("[data-sub-column]").forEach(function (el) {
			new Sortable(el, {
				group: "subtask",
				animation: 150,
				draggable: ".kcard",
				ghostClass: "kcard--ghost",
				onEnd: function (evt) {
					let col = evt.to;
					let status = col.getAttribute("data-sub-column");
					let order = [];
					col.querySelectorAll("[data-id]").forEach(function (c) {
						order.push(c.getAttribute("data-id"));
					});
					$.post("/subtask/subtask_reorder", { status: status, order: order }, function (res) {
						if (res.status !== "sukses") {
							toastr.error(res.pesan || "Gagal menyimpan posisi. Coba lagi.", "Gagal");
							renderSubtasks(detailId);
						} else {
							renderSubtasks(detailId);
							renderBoard();
						}
					}, "json").fail(function () {
						toastr.error("Gagal menyimpan posisi. Coba lagi.", "Gagal");
						renderSubtasks(detailId);
					});
				}
			});
		});
	}

	// klik kartu = ganti html utama board dengan Detail Tugas (tombol di kartu tetap berperilaku sendiri)
	$(document).on("click",".kcard:not(.kcard--sub)",function(e){
		if ($(e.target).closest(".btn-card-delete, .btn-card-archive, .btn-progress-edit, .kcard__edit, .kcard__delete").length) {
			return;
		}

		hidePreview();
		loadDetail($(this).data("id"));
	});

	// ====== Hover preview kartu tugas (delay 1 detik, muncul di bawah kursor) ======
	let mousePos = { x: 0, y: 0 };

	function subStatusLabel(status) {
		if (status === "pending") return "Inbox";
		if (status === "wip") return "Progress";
		return "Done";
	}

	function hidePreview() {
		if (previewTimer !== null) {
			clearTimeout(previewTimer);
			previewTimer = null;
		}
		$("#preview-pop").addClass("d-none");
	}

	function previewHtml(item, status) {
		let rows = "";
		rows += "<div class='preview-pop__row'><span class='preview-pop__label'>Proyek</span>" + escapeHtml(item.project_name) + " · " + escapeHtml(item.group_name) + "</div>";
		rows += "<div class='preview-pop__row'><span class='preview-pop__label'>Status</span><span class='chip chip--" + status + "'>" + subStatusLabel(status) + "</span></div>";
		rows += "<div class='preview-pop__row'><span class='preview-pop__label'>Deadline</span>" + escapeHtml(formatTanggal(item.deadline)) + (item.terlambat ? " · <span class='kcard__deadline--overdue'>Terlambat</span>" : "") + "</div>";
		if (item.description) {
			rows += "<div class='preview-pop__row'><span class='preview-pop__label'>Deskripsi</span>" + escapeHtml(item.description) + "</div>";
		}
		if (item.notes_preview) {
			rows += "<div class='preview-pop__row'><span class='preview-pop__label'>Catatan</span>" + item.notes_preview + (item.notes_truncated ? "..." : "") + "</div>";
		}

		// mini kanban sub tugas — tiap status maks 3 terlihat + keterangan tersembunyi
		let labelMap = { pending: "Inbox", wip: "Progress", done: "Done" };
		let colsHtml = "";
		["pending","wip","done"].forEach(function(st){
			let daftarSub = (item.subtasks && item.subtasks[st]) || [];
			let tersembunyi = (item.subtask_hidden && item.subtask_hidden[st]) || 0;
			let isi = daftarSub.length > 0
				? daftarSub.map(function(n){ return "<div class='preview-pop__subitem'>" + n + "</div>"; }).join("")
				: "<div class='preview-pop__empty'>Kosong</div>";
			colsHtml += "<div class='preview-pop__col'>" +
				"<div class='preview-pop__colhead'><span>" + labelMap[st] + "</span><span>" + daftarSub.length + "</span></div>" +
				isi +
				(tersembunyi > 0 ? "<div class='preview-pop__more'>+" + tersembunyi + " lagi</div>" : "") +
			"</div>";
		});
		let subHtml = "<div class='preview-pop__label' style='margin-top:8px'>Sub Tugas (" + item.subtask_total + ")</div>" +
			"<div class='preview-pop__cols'>" + colsHtml + "</div>";

		return "<div class='preview-pop__title'>" + escapeHtml(item.name) + "</div>" + rows + subHtml;
	}

	// popover tepat di bawah kursor; flip/clamp agar tetap di dalam layar
	function positionPreview(card) {
		let pop = $("#preview-pop");
		pop.removeClass("d-none");
		let pw = pop.outerWidth();
		let ph = pop.outerHeight();
		let left = mousePos.x + 14;
		if (left + pw > window.innerWidth - 8) {
			left = mousePos.x - pw - 14;
		}
		if (left < 8) {
			left = 8;
		}
		let top = mousePos.y + 16;
		if (top + ph > window.innerHeight - 8) {
			top = mousePos.y - ph - 16;
		}
		if (top < 8) {
			top = 8;
		}
		pop.css({ left: left, top: top });
	}

	$(document).on("mouseenter",".kcard:not(.kcard--sub)",function(e){
		mousePos.x = e.clientX;
		mousePos.y = e.clientY;
		let card = $(this);
		hidePreview();
		previewTimer = setTimeout(function(){
			let item = dataByCard[card.data("id")];
			if (!item) {
				return;
			}
			$("#preview-pop").html(previewHtml(item, card.closest(".kanban__cards").data("column")));
			positionPreview(card);
		}, 1000);
	});

	$(document).on("mousemove",".kcard:not(.kcard--sub)",function(e){
		mousePos.x = e.clientX;
		mousePos.y = e.clientY;
	});

	$(document).on("mouseleave",".kcard:not(.kcard--sub)",function(){
		hidePreview();
	});

	$(document).on("mousedown",".kcard:not(.kcard--sub)",function(){
		hidePreview();
	});

	$(document).on("click",".btn-detail-back",function(){
		closeDetail();
	});

	// ESC: kembali ke board (jika modal tidak sedang terbuka)
	$(document).on("keydown",function(e){
		if (e.key === "Escape" && detailId && !$("#modal_frame").hasClass("show")) {
			closeDetail();
		}
	});

	// Simpan catatan tugas induk
	$(document).on("click",".btn-notes-save, .btn-notes-save-mobile",function(){
		let progressId = $("#detail-view .task-detail").data("progress") || detailId;
		let notesVal = $(this).hasClass("btn-notes-save-mobile")
			? $("#task-notes-input-mobile").val()
			: $("#task-notes-input").val();
		$.post("/progress/progress_catatan", {
			id: progressId,
			notes: notesVal
		}, function(res){
			if (res.status == "sukses") {
				toastr.success(res.pesan,"Sukses");
			} else {
				toastr.error(res.pesan,"Gagal");
			}
		}, "json").fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	function subtaskCardHtml(item) {
		return "" +
			"<div class='kcard kcard--sub' data-id='" + item.id + "'>" +
				"<div class='kcard__title kcard__title--sub'>" + escapeHtml(item.name) + "</div>" +
				"<button type='button' class='kcard__delete btn-subtask-delete' data-id='" + item.id + "' aria-label='Hapus sub tugas'>&times;</button>" +
			"</div>";
	}

	// ====== LOGIKA KHUSUS SUBTASK DI MOBILE VIEW ======
	let currentMobileSubStatus = "wip";
	let subtaskDataByStatus = { pending: [], wip: [], done: [] };

	function switchMobileSubStatus(status, animate) {
		if (!statusLabelMap[status]) return;
		currentMobileSubStatus = status;

		$(".mobile-subtask-board .mstatus-card").removeClass("is-active");
		$("#msubstatus-" + status).addClass("is-active");

		$("#msubpanel-title").text(statusLabelMap[status]);
		let count = (subtaskDataByStatus[status] || []).length;
		$("#msubpanel-count").text(count);

		let track = $("#msubpanel-track");
		if (animate === false) {
			track.css({ transition: "none", transform: "translateX(" + statusTranslateMap[status] + ")" });
			if (track[0]) track[0].offsetHeight;
			track.css({ transition: "transform 0.35s cubic-bezier(0.25, 1, 0.5, 1)" });
		} else {
			track.css({ transition: "transform 0.35s cubic-bezier(0.25, 1, 0.5, 1)", transform: "translateX(" + statusTranslateMap[status] + ")" });
		}
	}

	function mobileSubtaskCardHtml(item, status) {
		return "" +
			"<div class='mcard-wrapper msubcard-wrapper' data-id='" + item.id + "' data-status='" + status + "'>" +
				"<div class='mcard__actions mcard__actions--right'>" +
					"<button type='button' class='mcard__action-btn mcard__btn-delete btn-subtask-delete' data-id='" + item.id + "' aria-label='Hapus sub tugas' title='Hapus'>" +
						"<svg width='17' height='17' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'><polyline points='3 6 5 6 21 6'></polyline><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path><line x1='10' y1='11' x2='10' y2='17'></line><line x1='14' y1='11' x2='14' y2='17'></line></svg>" +
					"</button>" +
				"</div>" +
				"<div class='mcard msubcard' data-id='" + item.id + "' data-status='" + status + "'>" +
					"<div class='mcard__title mb-0' style='font-size:14px;'>" + escapeHtml(item.name) + "</div>" +
				"</div>" +
			"</div>";
	}

	function renderSubtasks(progressId) {
		$("#msubcard-drop-placeholder").remove();
		$.getJSON("/subtask/subtask_daftar/" + progressId, function(res){
			if (res.status !== "sukses") {
				return;
			}

			let data = res.data;
			subtaskDataByStatus = data;
			let total = 0;
			["pending","wip","done"].forEach(function(st){
				total += (data[st] || []).length;
			});

			["pending","wip","done"].forEach(function(st){
				let daftar = data[st] || [];
				
				// Desktop
				let kolom = $(".kanban__cards[data-sub-column='" + st + "']");
				kolom.empty();

				// Mobile
				let kolomMobile = $("[data-mobile-sub-column='" + st + "']");
				kolomMobile.empty();

				daftar.forEach(function(item){
					kolom.append(subtaskCardHtml(item));
					kolomMobile.append(mobileSubtaskCardHtml(item, st));
				});

				if (daftar.length === 0) {
					if (st === "pending" && total === 0) {
						kolom.html("<div class='kanban__placeholder'>Belum ada sub tugas.<br>Sub tugas baru akan muncul di Inbox.</div>");
					} else {
						kolom.html("<div class='kanban__placeholder'>Belum ada sub tugas</div>");
					}
					kolomMobile.html("<div class='kanban__placeholder'>Belum ada sub tugas di status ini.</div>");
				}

				$("[data-sub-count='" + st + "']").text(daftar.length);
			});

			switchMobileSubStatus(currentMobileSubStatus, false);
		});
	}

	let activeSwipedSubWrapper = null;
	let suppressSubCardClick = false;

	function closeSwipedSubCards() {
		if (activeSwipedSubWrapper) {
			activeSwipedSubWrapper.find(".msubcard").css({ transform: "translateX(0px)" });
			activeSwipedSubWrapper.removeClass("is-swiped-open");
			activeSwipedSubWrapper = null;
		}
	}

	// Klik di luar subcard menutup menu swipe sub tugas
	$(document).on("click touchstart", function(e){
		if (activeSwipedSubWrapper && !$(e.target).closest(".msubcard-wrapper").length) {
			closeSwipedSubCards();
		}
	});

	// Tap pada 3 status card sub tugas di atas
	$(document).on("click", ".mobile-subtask-board .mstatus-card", function(){
		let st = $(this).data("sub-status");
		switchMobileSubStatus(st, true);
	});

	// Tap pada subcard
	$(document).on("click", ".msubcard", function(e){
		if (suppressSubCardClick) return;
		let wrapper = $(this).closest(".msubcard-wrapper");
		if (wrapper.hasClass("is-swiped-open")) {
			closeSwipedSubCards();
		}
	});

	// Drag & drop dan swipe gesture untuk Sub tugas mobile
	let subLongPressTimer = null;
	let isDraggingSubcard = false;
	let subDragProxy = null;
	let subDragData = null;
	let subDragOffset = { x: 0, y: 0 };
	let subTouchCoords = { startX: 0, startY: 0 };
	let isSwipingSubcard = false;
	let activeTouchSubcard = null;

	function cancelSubLongPress() {
		if (subLongPressTimer) {
			clearTimeout(subLongPressTimer);
			subLongPressTimer = null;
		}
	}

	function startSubDragOperation(card, clientX, clientY) {
		isDraggingSubcard = true;
		closeSwipedSubCards();
		$(".mobile-subtask-board").addClass("is-dragging-active");

		if (navigator.vibrate) {
			navigator.vibrate(50);
		}

		let rect = card[0].getBoundingClientRect();
		subDragOffset = {
			x: clientX - rect.left,
			y: clientY - rect.top
		};

		subDragData = {
			id: card.data("id"),
			originalStatus: card.data("status"),
			currentTargetStatus: card.data("status"),
			targetIndex: undefined,
			cardElement: card
		};

		subDragProxy = card.clone();
		subDragProxy.addClass("mcard--dragging-proxy");
		subDragProxy.css({
			position: "fixed",
			left: rect.left + "px",
			top: rect.top + "px",
			width: rect.width + "px",
			zIndex: 9999,
			pointerEvents: "none",
			transform: "scale(1.03)",
			boxShadow: "0 12px 30px rgba(0,0,0,0.28)"
		});
		$("body").append(subDragProxy);
		card.addClass("is-drag-source");
	}

	function handleSubMovePointer(clientX, clientY) {
		if (isDraggingSubcard && subDragProxy) {
			subDragProxy.css({
				left: (clientX - subDragOffset.x) + "px",
				top: (clientY - subDragOffset.y) + "px"
			});

			let statuses = ["pending", "wip", "done"];
			let foundTarget = null;
			statuses.forEach(function(st){
				let el = document.getElementById("msubstatus-" + st);
				if (el) {
					let r = el.getBoundingClientRect();
					if (clientX >= r.left && clientX <= r.right && clientY >= r.top && clientY <= r.bottom) {
						foundTarget = st;
					}
				}
			});

			$(".mobile-subtask-board .mstatus-card").removeClass("is-drop-target");
			if (foundTarget) {
				$("#msubstatus-" + foundTarget).addClass("is-drop-target");
				if (subDragData.currentTargetStatus !== foundTarget) {
					subDragData.currentTargetStatus = foundTarget;
					switchMobileSubStatus(foundTarget, true);
				}
			}

			let activeList = $("[data-mobile-sub-column='" + subDragData.currentTargetStatus + "']");
			let wrappers = activeList.children(".msubcard-wrapper").not("[data-id='" + subDragData.id + "']");
			let insertBeforeEl = null;
			let insertIndex = wrappers.length;

			wrappers.each(function(idx){
				let r = this.getBoundingClientRect();
				let midY = r.top + r.height / 2;
				if (clientY < midY) {
					insertBeforeEl = $(this);
					insertIndex = idx;
					return false;
				}
			});

			subDragData.targetIndex = insertIndex;

			let placeholder = $("#msubcard-drop-placeholder");
			if (!placeholder.length) {
				placeholder = $("<div id='msubcard-drop-placeholder' class='mcard-drop-placeholder'></div>");
			}

			if (insertBeforeEl && insertBeforeEl.length) {
				placeholder.insertBefore(insertBeforeEl);
			} else {
				activeList.append(placeholder);
			}
		}
	}

	function handleSubEndPointer() {
		cancelSubLongPress();

		if (isDraggingSubcard) {
			isDraggingSubcard = false;
			$("#msubcard-drop-placeholder").remove();
			$(".mobile-subtask-board").removeClass("is-dragging-active");
			$(".mobile-subtask-board .mstatus-card").removeClass("is-drop-target");

			if (subDragData && subDragData.cardElement) {
				subDragData.cardElement.removeClass("is-drag-source");
			}

			if (subDragProxy) {
				subDragProxy.remove();
				subDragProxy = null;
			}

			if (subDragData) {
				let targetStatus = subDragData.currentTargetStatus;
				let cardId = subDragData.id;

				let order = [];
				let activeList = $("[data-mobile-sub-column='" + targetStatus + "']");
				activeList.children(".msubcard-wrapper").not("[data-id='" + cardId + "']").each(function(){
					order.push($(this).data("id"));
				});

				let insertAt = (subDragData.targetIndex !== undefined) ? subDragData.targetIndex : order.length;
				if (insertAt > order.length) insertAt = order.length;
				order.splice(insertAt, 0, cardId);

				$.post("/subtask/subtask_reorder", { status: targetStatus, order: order }, function(res){
					if (res.status === "sukses") {
						renderSubtasks(detailId);
						renderBoard();
					} else {
						toastr.error(res.pesan || "Gagal menyimpan posisi", "Gagal");
						renderSubtasks(detailId);
					}
				}, "json").fail(function(){
					toastr.error("Gagal menyimpan posisi", "Gagal");
					renderSubtasks(detailId);
				});
			}

			subDragData = null;
			suppressSubCardClick = true;
			setTimeout(function(){ suppressSubCardClick = false; }, 300);
		}
	}

	// Touch events untuk subcard
	$(document).on("touchstart", ".msubcard", function(e){
		let card = $(this);
		let touch = e.originalEvent.touches[0];
		activeTouchSubcard = card;
		subTouchCoords.startX = touch.clientX;
		subTouchCoords.startY = touch.clientY;
		isSwipingSubcard = false;

		let wrapper = card.closest(".msubcard-wrapper");
		if (activeSwipedSubWrapper && !activeSwipedSubWrapper.is(wrapper)) {
			closeSwipedSubCards();
		}

		cancelSubLongPress();
		subLongPressTimer = setTimeout(function(){
			startSubDragOperation(card, touch.clientX, touch.clientY);
		}, 800);
	});

	$(document).on("touchmove", function(e){
		if (!activeTouchSubcard) return;
		let touch = e.originalEvent.touches[0];
		let deltaX = touch.clientX - subTouchCoords.startX;
		let deltaY = touch.clientY - subTouchCoords.startY;

		if (isDraggingSubcard) {
			e.preventDefault();
			handleSubMovePointer(touch.clientX, touch.clientY);
			return;
		}

		if (Math.hypot(deltaX, deltaY) > 8) {
			cancelSubLongPress();
		}

		let wrapper = activeTouchSubcard.closest(".msubcard-wrapper");
		let wasOpen = wrapper.hasClass("is-swiped-open");

		if (!isSwipingSubcard) {
			if (deltaX < -6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingSubcard = true;
			} else if (wasOpen && deltaX > 6) {
				isSwipingSubcard = true;
			}
		}

		if (isSwipingSubcard) {
			e.preventDefault();
			let baseOffset = wasOpen ? -60 : 0;
			let posX = baseOffset + deltaX;
			if (posX > 0) posX = 0;
			if (posX < -80) posX = -80;
			activeTouchSubcard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
		}
	});

	$(document).on("touchend touchcancel", function(e){
		cancelSubLongPress();

		if (isDraggingSubcard) {
			handleSubEndPointer();
			activeTouchSubcard = null;
			return;
		}

		if (isSwipingSubcard && activeTouchSubcard) {
			let touch = (e.originalEvent.changedTouches && e.originalEvent.changedTouches[0]) || {};
			let endX = touch.clientX || subTouchCoords.startX;
			let deltaX = endX - subTouchCoords.startX;
			let wrapper = activeTouchSubcard.closest(".msubcard-wrapper");
			let wasOpen = wrapper.hasClass("is-swiped-open");

			activeTouchSubcard.css("transition", "transform 0.22s cubic-bezier(0.2, 0.9, 0.3, 1)");

			if (!wasOpen && deltaX < -25) {
				activeTouchSubcard.css("transform", "translateX(-60px)");
				wrapper.addClass("is-swiped-open");
				activeSwipedSubWrapper = wrapper;
			} else if (wasOpen && deltaX > 20) {
				activeTouchSubcard.css("transform", "translateX(0px)");
				wrapper.removeClass("is-swiped-open");
				activeSwipedSubWrapper = null;
			} else if (wasOpen) {
				activeTouchSubcard.css("transform", "translateX(-60px)");
			} else {
				activeTouchSubcard.css("transform", "translateX(0px)");
			}

			isSwipingSubcard = false;
			suppressSubCardClick = true;
			setTimeout(function(){ suppressSubCardClick = false; }, 300);
		}

		activeTouchSubcard = null;
	});

	// Mouse events untuk subcard (dukungan emulator)
	$(document).on("mousedown", ".msubcard", function(e){
		if ($(window).width() > 768) return;
		if (e.which !== 1) return;
		let card = $(this);
		let clientX = e.clientX;
		let clientY = e.clientY;
		activeTouchSubcard = card;
		subTouchCoords.startX = clientX;
		subTouchCoords.startY = clientY;
		isSwipingSubcard = false;

		let wrapper = card.closest(".msubcard-wrapper");
		if (activeSwipedSubWrapper && !activeSwipedSubWrapper.is(wrapper)) {
			closeSwipedSubCards();
		}

		cancelSubLongPress();
		subLongPressTimer = setTimeout(function(){
			startSubDragOperation(card, clientX, clientY);
		}, 800);
	});

	$(document).on("mousemove", function(e){
		if ($(window).width() > 768 || !activeTouchSubcard) return;
		let deltaX = e.clientX - subTouchCoords.startX;
		let deltaY = e.clientY - subTouchCoords.startY;

		if (isDraggingSubcard) {
			e.preventDefault();
			handleSubMovePointer(e.clientX, e.clientY);
			return;
		}

		if (Math.hypot(deltaX, deltaY) > 8) {
			cancelSubLongPress();
		}

		let wrapper = activeTouchSubcard.closest(".msubcard-wrapper");
		let wasOpen = wrapper.hasClass("is-swiped-open");

		if (!isSwipingSubcard) {
			if (deltaX < -6 && Math.abs(deltaX) > Math.abs(deltaY)) {
				isSwipingSubcard = true;
			} else if (wasOpen && deltaX > 6) {
				isSwipingSubcard = true;
			}
		}

		if (isSwipingSubcard) {
			let baseOffset = wasOpen ? -60 : 0;
			let posX = baseOffset + deltaX;
			if (posX > 0) posX = 0;
			if (posX < -80) posX = -80;
			activeTouchSubcard.css({ transition: "none", transform: "translateX(" + posX + "px)" });
		}
	});

	$(document).on("mouseup", function(e){
		if ($(window).width() > 768) return;
		cancelSubLongPress();

		if (isDraggingSubcard) {
			handleSubEndPointer();
			activeTouchSubcard = null;
			return;
		}

		if (isSwipingSubcard && activeTouchSubcard) {
			let deltaX = e.clientX - touchCoords.startX;
			let wrapper = activeTouchSubcard.closest(".msubcard-wrapper");
			let wasOpen = wrapper.hasClass("is-swiped-open");

			activeTouchSubcard.css("transition", "transform 0.22s cubic-bezier(0.2, 0.9, 0.3, 1)");

			if (!wasOpen && deltaX < -25) {
				activeTouchSubcard.css("transform", "translateX(-60px)");
				wrapper.addClass("is-swiped-open");
				activeSwipedSubWrapper = wrapper;
			} else if (wasOpen && deltaX > 20) {
				activeTouchSubcard.css("transform", "translateX(0px)");
				wrapper.removeClass("is-swiped-open");
				activeSwipedSubWrapper = null;
			} else if (wasOpen) {
				activeTouchSubcard.css("transform", "translateX(-60px)");
			} else {
				activeTouchSubcard.css("transform", "translateX(0px)");
			}

			isSwipingSubcard = false;
			suppressSubCardClick = true;
			setTimeout(function(){ suppressSubCardClick = false; }, 300);
		}

		activeTouchSubcard = null;
	});

	$(document).on("click", ".btn-subtask-toggle", function(){
		let form = $("#form-subtask-add-mobile");
		form.slideToggle(180, function(){
			if (form.is(":visible")) {
				form.find("input[name=name]").focus();
			}
		});
	});

	$(document).on("submit","#form-subtask-add, #form-subtask-add-mobile",function(e){
		e.preventDefault();
		let form = $(this);
		let progressId = form.find("input[name=id_progress]").val() || detailId;

		$.ajax({
			url: "/subtask/subtask_addsave",
			type: "POST",
			data: form.serialize(),
			dataType: "json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				form.find("input[name=name]").val("").focus();
				renderSubtasks(progressId);
				renderBoard();
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});

	$(document).on("click",".btn-subtask-delete",function(){
		let id_sub = $(this).data("id");
		let progressId = $("#detail-view .task-detail").data("progress");

		bootbox.confirm("Apakah Anda yakin ingin menghapus sub tugas ini?", function(result){
			if (result) {
				$.post("/subtask/subtask_delete", { id: id_sub }, function(res){
					if (res.status == "sukses") {
						toastr.success(res.pesan,"Sukses");
						renderSubtasks(progressId);
						renderBoard();
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

	$(document).on("click",".btn-card-delete",function(){
		let id_progress = $(this).data("id");
		let url = "/progress/progress_delete";

		bootbox.confirm("Apakah Anda yakin ingin menghapus tugas ini secara permanen?", function(result){
			if (result) {
				$.post(url, {
					id: id_progress
				}, function(res){
					if (res.status == "sukses") {
						toastr.success(res.pesan,"Sukses");
						renderBoard();
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

	$(document).on("click",".btn-card-archive",function(){
		let id_progress = $(this).data("id");
		let url = "/progress/progress_archive";

		$.post(url, {
			id: id_progress
		}, function(res){
			if (res.status == "sukses") {
				toastr.success(res.pesan,"Sukses");
				renderBoard();
			} else {
				toastr.error(res.pesan,"Gagal");
			}
		}, "json").fail(function(xhr){
			let pesan = (xhr && xhr.responseJSON && xhr.responseJSON.pesan) ? xhr.responseJSON.pesan : "Terjadi error di server";
			toastr.error(pesan,"Gagal");
		});
	});
});
</script>
<?php } ?>
<?php $this->load->view('templates/3_end') ?>
