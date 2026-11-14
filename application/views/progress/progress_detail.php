<div class="task-detail" data-progress="<?= intval($progress->id) ?>">
	<!-- TAMPILAN KHUSUS DESKTOP (d-none d-md-block) -->
	<div class="d-none d-md-block">
		<div class="page-head">
			<div class="d-flex align-items-center" style="gap:12px">
				<h3 class="page-head__title mb-0"><?= html_escape($progress->name) ?></h3>
			</div>
			<div>
				<a href="javascript:;" class="btn btn-outline-secondary btn-detail-back">&larr; Kembali</a>
				<a href="javascript:;" class="btn btn-primary btn-progress-edit" data-id="<?= intval($progress->id) ?>">Edit Tugas</a>
			</div>
		</div>

		<div class="task-detail__meta">
			<span class="dot-label"><span class="dot" style="background-color:<?= html_escape($progress->project_color) ?>"></span><?= html_escape($progress->project_name) ?> · <?= html_escape($progress->group_name) ?></span>
			<span class="chip chip--<?= $progress->priority ?>"><?= priority_label($progress->priority) ?></span>
			<span class="task-detail__deadline <?= is_overdue($progress->deadline, $progress->status) ? 'task-detail__deadline--overdue' : '' ?>">Deadline <?= format_tanggal($progress->deadline) ?><?= is_overdue($progress->deadline, $progress->status) ? ' · Terlambat' : '' ?></span>
		</div>
		<?php if (trim((string) $progress->description) != '') { ?>
		<p class="task-detail__desc"><?= html_escape($progress->description) ?></p>
		<?php } ?>

		<div class="task-notes">
			<label for="task-notes-input">Catatan</label>
			<textarea id="task-notes-input" name="notes" class="form-control" maxlength="2000" placeholder="Tulis catatan untuk tugas ini..."><?= html_escape($progress->notes) ?></textarea>
			<div class="d-flex justify-content-start" style="margin-top:8px">
				<button type="button" class="btn btn-outline-secondary btn-xs btn-notes-save">Simpan Catatan</button>
			</div>
		</div>

		<h5 class="section-title">Sub Tugas</h5>
		<form id="form-subtask-add" class="subtask-add">
			<input type="hidden" name="id_progress" value="<?= intval($progress->id) ?>">
			<input type="text" name="name" class="form-control" maxlength="150" placeholder="Tambah sub tugas baru..." required>
			<button type="submit" class="btn btn-outline-secondary btn-subtask-add">Tambah</button>
		</form>

		<div class="kanban subtask-board">
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Inbox</h5>
					<span class="kanban__count" data-sub-count="pending">0</span>
				</div>
				<div class="kanban__cards" data-sub-column="pending"></div>
			</div>
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Progress</h5>
					<span class="kanban__count" data-sub-count="wip">0</span>
				</div>
				<div class="kanban__cards" data-sub-column="wip"></div>
			</div>
			<div class="kanban__col">
				<div class="kanban__head">
					<h5 class="kanban__label">Done</h5>
					<span class="kanban__count" data-sub-count="done">0</span>
				</div>
				<div class="kanban__cards" data-sub-column="done"></div>
			</div>
		</div>
	</div>

	<!-- TAMPILAN KHUSUS MOBILE VIEW (d-md-none): TRUE FULLPAGE DETAIL & SUBTASK KANBAN -->
	<div class="d-md-none mobile-detail-layout">
		<!-- Top Bar Header Mobile -->
		<div class="mobile-detail__topbar">
			<button type="button" class="btn btn-detail-back mobile-detail__back-btn" aria-label="Kembali ke Board">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
				<span>Kembali</span>
			</button>
			<button type="button" class="btn btn-progress-edit mobile-detail__edit-btn" data-id="<?= intval($progress->id) ?>" aria-label="Edit Tugas">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
				<span>Edit</span>
			</button>
		</div>

		<!-- Ringkasan Info Tugas Induk (Compact + Collapsible Details) -->
		<div class="mobile-detail__card">
			<h3 class="task-detail__mobile-title"><?= html_escape($progress->name) ?></h3>
			<div class="task-detail__meta mb-0">
				<span class="dot-label"><span class="dot" style="background-color:<?= html_escape($progress->project_color) ?>"></span><?= html_escape($progress->project_name) ?> · <?= html_escape($progress->group_name) ?></span>
				<span class="chip chip--<?= $progress->priority ?>"><?= priority_label($progress->priority) ?></span>
				<span class="task-detail__deadline <?= is_overdue($progress->deadline, $progress->status) ? 'task-detail__deadline--overdue' : '' ?>">Deadline <?= format_tanggal($progress->deadline) ?><?= is_overdue($progress->deadline, $progress->status) ? ' · Terlambat' : '' ?></span>
			</div>

			<?php if (trim((string) $progress->description) != '' || trim((string) $progress->notes) != '') { ?>
			<div class="collapse mt-2" id="collapse-mobile-extra">
				<?php if (trim((string) $progress->description) != '') { ?>
				<div class="task-detail__mobile-desc mb-2">
					<div class="task-detail__mobile-desc-label">Deskripsi</div>
					<div class="task-detail__mobile-desc-text"><?= nl2br(html_escape($progress->description)) ?></div>
				</div>
				<?php } ?>

				<div class="task-notes-collapsible">
					<div class="task-detail__mobile-desc-label">Catatan Tugas</div>
					<textarea id="task-notes-input-mobile" name="notes_mobile" class="form-control" rows="2" maxlength="2000" placeholder="Tulis catatan untuk tugas ini..."><?= html_escape($progress->notes) ?></textarea>
					<div class="d-flex justify-content-end mt-2">
						<button type="button" class="btn btn-xs btn-outline-secondary btn-notes-save btn-notes-save-mobile" style="border-radius:9999px; font-weight:600; padding:4px 14px;">Simpan Catatan</button>
					</div>
				</div>
			</div>

			<a href="javascript:;" class="mobile-detail__toggle-btn" data-toggle="collapse" data-target="#collapse-mobile-extra" aria-expanded="false">
				<span class="toggle-show-text">Lihat Detail &amp; Catatan</span>
				<span class="toggle-hide-text">Sembunyikan Detail</span>
				<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
			</a>
			<?php } ?>
		</div>

		<!-- Kanban Sub Tugas Khusus Mobile: PERSIS SEPERTI KANBAN INDUK -->
		<div class="mobile-subtask-board">
			<!-- 3 Status Card Utama: Inbox, Progress, Done -->
			<div class="mstatus-cards">
				<div class="mstatus-card" data-sub-status="pending" id="msubstatus-pending">
					<span class="mstatus-card__icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
					</span>
					<span class="mstatus-card__label">Inbox</span>
				</div>
				<div class="mstatus-card is-active" data-sub-status="wip" id="msubstatus-wip">
					<span class="mstatus-card__icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					</span>
					<span class="mstatus-card__label">Progress</span>
				</div>
				<div class="mstatus-card" data-sub-status="done" id="msubstatus-done">
					<span class="mstatus-card__icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
					</span>
					<span class="mstatus-card__label">Done</span>
				</div>
			</div>

			<!-- Panel Sub Tugas di Bawah 3 Status Card: default Progress -->
			<div class="mpanel">
				<div class="mpanel__head">
					<div class="d-flex align-items-center" style="gap:10px;">
						<h4 class="mpanel__title" id="msubpanel-title">Progress</h4>
						<span class="mpanel__count" id="msubpanel-count">0</span>
					</div>
					<button type="button" class="btn btn-sm btn-outline-secondary btn-subtask-toggle" style="border-radius:9999px; font-weight:600; padding:4px 14px; font-size:12.5px;">+ Sub Tugas</button>
				</div>

				<!-- Form Tambah Sub Tugas Mobile (Toggle / Inline) -->
				<form id="form-subtask-add-mobile" class="subtask-add px-3 pb-2" style="display:none;">
					<input type="hidden" name="id_progress" value="<?= intval($progress->id) ?>">
					<div class="input-group">
						<input type="text" name="name" class="form-control" maxlength="150" placeholder="Tambah sub tugas baru..." required style="border-radius: 9999px 0 0 9999px; font-size: 13.5px;">
						<div class="input-group-append">
							<button type="submit" class="btn btn-primary" style="border-radius: 0 9999px 9999px 0; padding: 0 16px; font-size: 13px; font-weight: 600;">Tambah</button>
						</div>
					</div>
				</form>

				<!-- Sliding Viewport: Animasi geser pergantian status -->
				<div class="mpanel__viewport">
					<div class="mpanel__track" id="msubpanel-track">
						<div class="mpanel__col" data-msubcol="pending">
							<div class="mpanel__list" data-mobile-sub-column="pending"></div>
						</div>
						<div class="mpanel__col" data-msubcol="wip">
							<div class="mpanel__list" data-mobile-sub-column="wip"></div>
						</div>
						<div class="mpanel__col" data-msubcol="done">
							<div class="mpanel__list" data-mobile-sub-column="done"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
