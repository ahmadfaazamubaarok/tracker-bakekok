# 04 — UI Spec

**Sumber design language:** `DESIGN.md` (root repo) — gaya Mobbin: monokrom di atas kanvas putih,
struktur dari tint netral + hairline 1px, semua elemen interaktif berbentuk **pill**, kartu 24px,
**tanpa drop shadow**, tipografi grotesque.

**Sumber pola interaksi:** `prompt.md` (Metode Penulisan) — jQuery + Bootstrap 4 (modal/dropdown) +
DataTables + toastr + bootbox, modal CRUD via AJAX. Contoh halaman di `prompt.md` adalah acuan
*pola & struktur*; **tema visualnya (DashForge) tidak dipakai** — semua komponen di-*restyle*
via `app.css` agar mengikuti `DESIGN.md`.

Font: **Inter (variable)** via Google Fonts CDN sebagai substitusi Saans
(mapping bobot: 652→650/700, 456→450/500, 300→300 — lihat catatan font di `DESIGN.md`);
fallback stack tetap sistem sans.

## 1. Adaptasi design system untuk aplikasi ini

| Kebutuhan | Keputusan |
|---|---|
| Warna identitas proyek per kartu | `color` proyek = satu-satunya "warna konten" (analog screenshot di Mobbin). Chrome kartu monokrom; warna hanya sebagai **dot 8px** + strip tipis 3px tepi kiri kartu. |
| Distingsi prioritas | Chip kecil *low-saturation*: `low` = `--canvas-soft` + teks muted; `medium` = `rgba(245,166,35,.12)` + teks `#8A6100`; `high` = `rgba(229,72,77,.12)` + teks `#A83236`. |
| Sinyal mendesak (overdue) | Merah lembut pada teks deadline + label "Terlambat". Aksen biru `#0066ff` hanya untuk fokus input & item nav aktif. |
| Feedback sukses/gagal | toastr (hijau `#51a351` / merah `#bd362f`, override via `app.css` + `!important` — pola fix toastr dari contoh `prompt.md`). |

## 2. Tokens CSS (custom properties di `app.css`)

```css
:root {
  /* colors — dari DESIGN.md */
  --ink: #141414; --ink-soft: #262626;
  --text-muted: #707070; --text-faint: #adadad;
  --canvas: #ffffff; --canvas-soft: #f3f3f3; --field: #f0f0f0;
  --hairline-soft: #f0f0f0; --hairline: #e0e0e0;
  --accent: #0066ff;
  /* ekstensi semantic chip */
  --chip-medium-bg: rgba(245,166,35,.12);  --chip-medium-fg: #8A6100;
  --chip-high-bg: rgba(229,72,77,.12);     --chip-high-fg: #A83236;
  --chip-low-bg: var(--canvas-soft);       --chip-low-fg: var(--text-muted);
  /* radius — dari DESIGN.md */
  --r-sm: 16px; --r-md: 24px; --r-full: 9999px;
  /* spacing */
  --s-xs: 8px; --s-sm: 12px; --s-md: 16px; --s-lg: 24px; --s-xl: 32px;
  --font: "Inter", -apple-system, "Helvetica Neue", Arial, sans-serif;
}
```

Aturan wajib dari `DESIGN.md`: **tanpa drop shadow** (modal pun tanpa shadow — bedakan via
overlay gelap + border hairline); fokus input = ring 2px `--ink`; input = fill `--field`
tanpa border; semua tombol & kontrol pill; heading sentence-case; tabel: pemisah baris
1px `--hairline-soft`, header kolom caption w600 muted.

**Override Bootstrap di `app.css`** (agar komponen BS4 tetap dipakai tapi bernuansa DESIGN.md):
`.btn` → pill, tanpa gradient/shadow; `.btn-primary` → fill `--ink` + teks putih; `.form-control`
→ fill `--field`, border none, ring ink saat fokus, radius `--r-sm`; `.modal-content` → radius
`--r-md`, border hairline; `.dropdown-menu` → radius `--r-sm`, border hairline; tabel DataTables
→ monokrom (header tanpa fill gelap, pagination pill); toastr override warna (lihat §1).
Fix lebar tabel DataTables (`box-sizing: border-box; width:100% !important`) ikutkan dari contoh `prompt.md`.

## 3. Template layout (pola `prompt.md`)

| File | Isi |
|---|---|
| `templates/0_head.php` | `<!DOCTYPE html>` s.d. `</head>`: meta, title "Project Tracker", Google Fonts Inter, css vendor (DataTables, toastr), `assets/css/app.css`, **tag `<body>` dibuka di sini** |
| `templates/1_topbar.php` | Navbar pill (sticky) + container induk dibuka |
| `templates/2_footer.php` | Container ditutup + semua `<script>` vendor |
| `templates/3_end.php` | `</body></html>` |

Setiap view memuat:

```php
<?php $this->load->view('templates/0_head') ?>
<?php $this->load->view('templates/1_topbar') ?>
<!-- konten halaman + <div id="modal_frame"> (untuk view bertabel/kanban) -->
<script> /* JS halaman, inline di sini (pola prompt.md) */ </script>
<?php $this->load->view('templates/2_footer') ?>
<?php $this->load->view('templates/3_end') ?>
```

Navbar (di `1_topbar`): track pill `--canvas-soft` sticky atas; brand "Project Tracker" (w650);
link: **Board** `/` · **Proyek** `/project` · **Grup** `/group` · **Arsip** `/archive`.
Item aktif = pill putih pada track (variable `$navbar` dari controller:
`'board'|'project'|'group'|'archive'`). Tanpa dropdown profil (tidak ada login).

## 4. Vendor library — simpan lokal di `public/cdn/lib/`

```bash
mkdir -p public/cdn/lib/{jquery,bootstrap,bootbox,toastr,datatables,sortablejs}
curl -L -o public/cdn/lib/jquery/jquery.min.js                 https://code.jquery.com/jquery-3.7.1.min.js
curl -L -o public/cdn/lib/bootstrap/bootstrap.bundle.min.js    https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js
curl -L -o public/cdn/lib/bootbox/bootbox.all.min.js           https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.3/bootbox.all.min.js
curl -L -o public/cdn/lib/toastr/toastr.min.js                 https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js
curl -L -o public/cdn/lib/toastr/toastr.min.css                https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css
curl -L -o public/cdn/lib/datatables/jquery.dataTables.min.js  https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js
curl -L -o public/cdn/lib/datatables/jquery.dataTables.min.css https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css
curl -L -o public/cdn/lib/sortablejs/Sortable.min.js           https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js
```

Urutan load di `2_footer`: jquery → bootstrap.bundle → bootbox → toastr → datatables →
Sortable → (tidak ada dashforge.js). CSS di `0_head`: toastr.min.css, jquery.dataTables.min.css, app.css.

## 5. Pola interaksi umum (identik dengan contoh `prompt.md`)

- **Modal frame** — setiap halaman bertabel/kanban punya satu `<div id="modal_frame">`
  (markup BS4 dari contoh; `modal-dialog modal-dialog-centered`, konten di-*restyle*).
- **Buka modal add/edit** — `$.get("/group/group_add", ...)` → isi `.modal-body`,
  judul via `.modal-title`. Pesan loading: `"<p class='text-center'><em>Memuat...</em></p>"`.
- **Submit** — `$("#form-group-add").on("submit")` → `e.preventDefault()` →
  `$.ajax` POST `$(this).serialize()` → `.done()`: `respon.status === "sukses"` ?
  `toastr.success(respon.pesan, "Sukses")` + reload tabel/tutup modal :
  `toastr.error(respon.pesan, "Gagal")`; `.fail()` → toast "Terjadi error di server".
  **Modal tidak ditutup saat gagal** → isi form tetap ada.
- **Delete** — delegated handler `.btn-group-delete` (dst) → `bootbox.confirm("Apakah Anda yakin ingin menghapus grup ini?")`
  → `$.post(url, { id: id }, function(res){ ... })` → toast sukses/gagal + reload tabel.
- **DataTables** — pola tetap: `dom: 'rt<"d-flex justify-content-end align-items-center flex-wrap mt-3"lp>'`,
  `language: { lengthMenu: "_MENU_" }`, filter per kolom via `<tfoot class="filter">` + keyup search.
- **Quotes & URL** — JS pakai petik 2 `"`; URL AJAX statis `"/group/group_daftar"` dst.
- **Naming** — tombol `btn-group-add|btn-group-edit|btn-group-delete`, `btn-project-*`,
  `btn-progress-*`, `btn-card-*` (board), `btn-archive-restore`; form `form-group-add`,
  `form-group-edit`, `form-project-*`, `form-progress-add`.

## 6. Halaman: Board `/` (default) — `board/board_view.php`

`$mode` dari controller menentukan isi:

| Mode | Isi |
|---|---|
| `empty_group` | Kartu guard besar (r-md, `--canvas-soft`): "Belum ada grup." / "Sebelum menambah progres, buat dulu minimal 1 grup." + CTA pill ink "Buat Grup" → `/group` |
| `empty_project` | "Belum ada proyek." / "Tambahkan minimal 1 proyek untuk mulai melacak progres." + CTA "Tambah Proyek" → `/project` |
| `board` | Header "Board" + tombol pill "+ Tambah Progres" (`btn-progress-add`) + 3 kolom kanban |

Kanban (mode board):

```
┌─ Pending (3) ─────┐ ┌─ WIP (1) ──────┐ ┌─ Selesai (2) ──┐
│  [kartu...]       │ │  [kartu...]    │ │  [kartu...]    │
│  [drop area]      │ │                │ │  [Arsipkan]    │
└───────────────────┘ └──────────────┘ └────────────────┘
```

- Grid `1fr 1fr 1fr`, gap 24px; header kolom: label + counter pill (dihitung dari data).
- Kolom kosong: placeholder dashed hairline "Belum ada kartu".
- **Kartu dirender JS** dari JSON `progress_daftar` (lihat `docs/03` §7) saat
  `$(document).ready` dan **setelah setiap aksi** (add/delete/archive/restore → panggil ulang).
- Kartu kosong di semua kolom + data lengkap: empty state inline di kolom Pending:
  "Belum ada progres." / "Kartu progres baru akan muncul di kolom Pending."

### 6.1 Anatomi kartu (r-md 24px, hairline-soft 1px, padding 16px)

```
┌────────────────────────────────┐
│ ● Nama Proyek        (caption  │  ← dot 8px warna proyek + nama proyek muted;
│                                │     strip 3px warna proyek di tepi kiri kartu
│ Nama Progres            (title │  ← 16px w500, wrap maks 2 baris
│                         [×])   │  ← btn-card-delete, muncul saat hover
│ [Sedang]  Deadline 22 Nov 2026 │  ← chip prioritas + deadline caption
│ Deskripsi singkat maksimal dua │  ← hanya jika ada; clamp 2 baris, muted
│ baris...                       │
│ [Arsipkan]                     │  ← HANYA kartu di Selesai; pill-soft kecil (btn-card-archive)
└────────────────────────────────┘
```

- `terlambat == true` → deadline merah + " · Terlambat".
- Drag: `opacity .5` + outline hairline (tanpa shadow). Kursor `grab`/`grabbing`.

## 7. Halaman: Grup `/group` — `group/group_view.php`

- Header: judul "Grup" + tombol pill `btn-group-add` "+ Tambah Grup".
- DataTables `#tabel-group`: kolom **Nama · Jumlah Proyek · Aksi** (dropdown), `tfoot.filter`.
- Modal add: form `#form-group-add` — label "Nama Grup", input `name` (required, maxlength 150),
  tombol "Simpan" (pill ink) — fragmen `group/group_add.php`.
- Modal edit: `#form-group-edit` + hidden `id` — fragmen `group/group_edit.php`.
- Guard hapus dari server: `$.post` → toast gagal "Grup tidak bisa dihapus karena masih memiliki proyek."

## 8. Halaman: Proyek `/project` — `project/project_view.php`

- `$mode == 'empty_group'` → banner guard: "Belum ada grup." / "Proyek wajib terhubung ke grup.
  Buat grup terlebih dahulu." + CTA "Buat Grup →" (tombol add disembunyikan).
- Header: judul "Proyek" + tombol `btn-project-add` "+ Tambah Proyek".
- DataTables `#tabel-project`: **Proyek (dot warna + nama) · Grup · Domain (link) · Direktori
  (`<code>`) · Jumlah Progres · Aksi**. Domain tanpa scheme di-render `https://` + teks.
- Modal add/edit (`project/project_add.php`, `project/project_edit.php`): Nama · Deskripsi
  (textarea) · Grup (select dari `$groups`) · Domain · Direktori · Warna (`<input type="color">`
  + kode hex caption; default palet `#3B82F6, #10B981, #F59E0B, #EF4444, #8B5CF6, #EC4899`).

## 9. Modal: Tambah Progres — `progress/progress_add.php`

- Form `#form-progress-add`: Nama · Proyek (select, label option "Nama Proyek — Grup") ·
  Deadline (`<input type="date">`, default +7 hari) · Deskripsi (textarea, opsional) ·
  Prioritas (select `Rendah/Sedang/Tinggi`, value `low/medium/high`, default `medium`).
- Submit → `progress_addsave` → sukses: tutup modal + toast + **render ulang board**.
- Tombol `btn-progress-add` hanya dirender saat mode `board`.

## 10. Halaman: Arsip `/archive` — `archive/archive_view.php`

- Deskripsi kecil: "Progres selesai yang sudah diarsipkan."
- DataTables `#tabel-archive`: **Nama · Proyek (dot warna) · Grup · Prioritas (chip) ·
  Deadline · Diarsipkan · Aksi (Pulihkan + X)**.
- `btn-archive-restore` → `$.post("/progress/progress_restore", { id })` → toast + reload tabel.
- `btn-archive-delete` (X) → bootbox "Apakah Anda yakin ingin menghapus progres ini secara permanen?"
  → `$.post("/progress/progress_delete", { id })`.
- Empty (DataTables 0 row): "Belum ada arsip." / "Progres di kolom Selesai bisa diarsipkan dari Board."

## 11. Interaksi drag & drop (SortableJS, di `board_view.php`)

```js
document.querySelectorAll("[data-column]").forEach(function (el) {
	new Sortable(el, {
		group: "kanban", animation: 150, ghostClass: "card--ghost",
		onEnd: function (evt) {
			var col = evt.to;
			var status = col.getAttribute("data-column");          // pending | wip | done
			var order = [];
			col.querySelectorAll("[data-id]").forEach(function (c) {
				order.push(c.getAttribute("data-id"));
			});
			$.post("/progress/progress_reorder", { status: status, order: order }, function (res) {
				if (res.status !== "sukses") {
					toastr.error(res.pesan || "Gagal menyimpan posisi. Coba lagi.", "Gagal");
					location.reload();
				}
			}).fail(function () {
				toastr.error("Gagal menyimpan posisi. Coba lagi.", "Gagal");
				location.reload();
			});
		}
	});
});
```

- Setelah drop sukses: counter kolom diperbarui; kartu di Selesai menampilkan tombol "Arsipkan".
- Kartu arsip/delete di board: `$.post` sukses → toast + render ulang board (panggil ulang `progress_daftar`).

## 12. Copywriting eksak (Bahasa Indonesia)

| Konteks | Teks |
|---|---|
| Brand & nav | **Project Tracker** — Board, Proyek, Grup, Arsip |
| Judul halaman | Board / Grup / Proyek / Arsip |
| Judul modal | "Tambah Grup" / "Edit Grup" / "Tambah Proyek" / "Edit Proyek" / "Tambah Progres" / "Edit Progres" |
| Tombol | + Tambah Progres · + Tambah Grup · + Tambah Proyek · Simpan · Batal · Edit · Delete · Arsipkan · Pulihkan |
| Label form | Nama Grup, Nama Proyek, Deskripsi, Grup, Domain, Direktori, Warna, Nama Progres, Proyek, Deadline, Prioritas |
| Opsi prioritas | Rendah / Sedang / Tinggi |
| Kolom board | Pending / WIP / Selesai |
| Placeholder | "Misal: Redesign halaman utama", "https://contoh.com", "/var/www/proyek-a", "Memuat..." |
| Empty board (guard) | "Belum ada grup." · "Belum ada proyek." (+ deskripsi §6) |
| Empty kolom | "Belum ada kartu" |
| Empty daftar | "Belum ada grup." / "Belum ada proyek." / "Belum ada arsip." |
| Toast sukses | "Grup berhasil ditambahkan." · "Grup berhasil diperbarui." · "Grup berhasil dihapus." · "Proyek berhasil ditambahkan." · "Proyek berhasil diperbarui." · "Proyek berhasil dihapus." · "Progres berhasil ditambahkan." · "Progres berhasil diperbarui." · "Progres berhasil diarsipkan." · "Progres berhasil dipulihkan." · "Progres berhasil dihapus." |
| Toast gagal validasi | "Nama grup wajib diisi." · "Nama grup minimal 3 karakter." · "Nama grup sudah dipakai." · "Nama proyek wajib diisi." · "Grup tidak valid." · "Domain wajib diisi." · "Direktori wajib diisi." · "Warna tidak valid." · "Nama progres wajib diisi." · "Proyek tidak valid." · "Deadline wajib diisi." · "Deadline tidak valid." · "Prioritas tidak valid." |
| Toast guard hapus | "Grup tidak bisa dihapus karena masih memiliki proyek." · "Proyek tidak bisa dihapus karena masih memiliki progres." |
| Toast guard arsip | "Hanya progres di kolom Selesai yang bisa diarsipkan." · "Progres ini tidak sedang diarsipkan." |
| Toast sistem | "Terjadi error di server" · "Gagal menyimpan posisi. Coba lagi." · "Progres tidak ditemukan." |
| Bootbox confirm | "Apakah Anda yakin ingin menghapus grup ini?" · "Apakah Anda yakin ingin menghapus proyek ini?" · "Apakah Anda yakin ingin menghapus progres ini secara permanen?" · "Apakah Anda yakin ingin menghapus sub tugas ini?" |
| Overdue | "Terlambat" (ditempel setelah tanggal) |
| Detail tugas (v1.2) | Klik kartu → **html utama board diganti** halaman detail via AJAX (`#board-view` ↔ `#detail-view`, tombol **← Kembali** / ESC untuk mundur). Header: ← Kembali + judul tugas + tombol "Edit Tugas". Isi: meta (proyek·grup, prioritas, deadline), deskripsi, **Catatan** (textarea + "Simpan Catatan", khusus tugas induk), lalu **board Sub Tugas** bergaya identik dengan board induk: 3 kolom **Inbox / Progress / Done**, drag & drop antar kolom + urutan persist (`subtask_reorder`), tambah via form, × hapus (bootbox confirm). Kartu memuat badge `x/y sub` (done/total). |
| Hover preview kartu (v1.2) | Hover kartu induk selama **1 detik** → popover fixed **tepat di bawah kursor** (flip ke atas/clamp bila tak muat layar, `pointer-events:none`, tanpa shadow — border hairline, radius 16): nama, Proyek·Grup, chip Status, Deadline (+Terlambat), Deskripsi, **Catatan maks 20 karakter (+...)**, lalu **mini kanban Sub Tugas** 3 kolom (Inbox/Progress/Done) — tiap kolom **maks 3 nama** + "+N lagi". Hilang saat kursor keluar kartu / mousedown (drag) / render ulang. Data diEmbed di `progress_daftar` (tanpa request per-hover). |
| Filter board (v1.2) | Tombol ikon funnel (`.btn-board-filter`, outline secondary) **persis di samping** tombol Tambah Tugas → modal "Filter Tugas": dropdown **Grup** & **Proyek** ("Semua ..." = 0), tombol **Reset** / **Terapkan**. Filter tersimpan di localStorage (`tracker_board_filter`) → tetap aktif lintas refresh & navigasi; tombol filter diberi gaya aktif (`.btn-filter-active`) saat filter tersimpan. Penerapan memfilter kartu di sisi server (`progress_daftar?id_group=&id_project=`); hasil kosong saat filter aktif → "Tidak ada tugas yang cocok dengan filter." |

## 13. Responsif & aksesibilitas

- Breakpoint tunggal ≤768px: kolom kanban horizontal-scroll (`overflow-x:auto`, kolom min-width 280px);
  navbar pill menyempit; tabel pakai `.table-responsive`.
- Semua tombol min-height 44px; tombol X min 32px area klik (padding).
- Kontras: teks sekunder minimal `--text-muted`; `--text-faint` hanya untuk fine-print.
- `aria-label` pada tombol ikon (×), `aria-live="polite"` opsional untuk area toast.
- Kartu bisa difokus keyboard; drag butuh pointer (tombol pindah kolom manual = backlog).
