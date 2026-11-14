# 00 — Agent Brief: Project Tracker

> **Mulai dari dokumen ini.** Ini instruksi kerja untuk AI agent yang membangun aplikasi.
> Dokumen lain di `docs/` adalah spesifikasi detail yang dirujuk dari sini.

---

## 1. Misi

Bangun aplikasi web **Project Tracker** — kanban board 3 kolom (**Pending → WIP → Selesai**)
untuk melacak progres proyek pribadi, **tanpa login**, dengan stack:

**CodeIgniter 3.1.13 · PHP-FPM 7.4 · MySQL 8.0 · Nginx · Docker Compose**

Hierarki data: **Grup → Proyek → Progres**, plus **Arsip** untuk progres yang sudah selesai.

## 2. Kondisi repo saat ini

| Path | Status | Aksi |
|---|---|---|
| `system/`, `application/`, `composer.json` | Scaffold CodeIgniter **3.1.13** bawaan | Pertahankan; `application/` masih kosong dari kode bisnis, semua fitur ditulis baru |
| `index.php` (root) | Entrypoint CI3 bawaan | **Dipindahkan ke `public/index.php`** (lihat §4), file root dihapus |
| `DESIGN.md` | Design system (Mobbin-style: monokrom, pill, hairline, tanpa shadow) | **Wajib diikuti untuk tampilan** — ringkasan adaptasinya di `docs/04-UI-SPEC.md` |
| `prompt.md` | Kebutuhan user + **"Metode Penulisan"** (konvensi kode: nama model/controller/view, pola JSON, dsb.) | **Wajib diikuti untuk gaya kode.** Bagian bawah file berisi contoh halaman dari proyek lain — jadikan acuan *pola*, bukan *tema visual* (tema visual tetap `DESIGN.md`) |
| `docs/` | Spesifikasi lengkap | Baca berurutan (§3) |

## 3. Dokumen spesifikasi (baca berurutan)

1. `docs/01-PRD.md` — kebutuhan produk, user flow, aturan bisnis, acceptance criteria
2. `prompt.md` bagian **Metode Penulisan** — konvensi kode (authoritative)
3. `docs/02-DATABASE.md` — skema MySQL, DDL siap pakai, kontrak model
4. `docs/03-API-ROUTES.md` — routing, controller, kontrak JSON, AJAX
5. `docs/04-UI-SPEC.md` — halaman, komponen, interaksi, copywriting eksak
6. `docs/05-DEVOPS.md` — Docker/Nginx/PHP/MySQL, isi file infra lengkap

**Prioritas saat konflik:** perilaku produk → `01-PRD.md`; gaya/struktur kode → `prompt.md`;
lalu DATABASE/API > UI > DEVOPS. Jika masih ambigu: pilih opsi **paling sederhana** dan
catat keputusanmu di bagian "Keputusan & Asumsi" pada `docs/01-PRD.md`.

## 4. Restrukturisasi folder (Fase 0)

CI3 bawaan menaruh `index.php` di root. Untuk Nginx + keamanan, entrypoint dipindah:

```
tracker/
├── application/
│   ├── config/          (routes, database, config, autoload dimodifikasi)
│   ├── controllers/     Board.php, Group.php, Project.php, Progress.php, Archive.php
│   ├── models/          Model_group.php, Model_project.php, Model_progress.php
│   └── views/
│       ├── templates/   0_head.php, 1_topbar.php, 2_footer.php, 3_end.php
│       ├── board/       board_view.php
│       ├── group/       group_view.php, group_add.php, group_edit.php
│       ├── project/     project_view.php, project_add.php, project_edit.php
│       ├── progress/    progress_add.php
│       └── archive/     archive_view.php
├── system/              (jangan diubah)
├── public/
│   ├── index.php        ← pindahan dari root; $system_path='../system'; $application_folder='../application'
│   ├── assets/css/app.css   (tokens dari DESIGN.md, override Bootstrap)
│   └── cdn/lib/             jquery, bootstrap, bootbox, toastr, datatables, sortablejs (lihat docs/04 §4)
├── docker/
│   ├── php/Dockerfile
│   ├── php/php.ini
│   ├── nginx/default.conf
│   └── mysql/init/01-schema.sql
├── docker-compose.yml
├── DESIGN.md            (jangan diubah)
├── prompt.md            (jangan diubah)
└── docs/
```

- Root `index.php` lama **dihapus** setelah `public/index.php` siap.
- `application/cache/` & `application/logs/` harus writable oleh user PHP-FPM (`www-data`).

## 5. Urutan pengerjaan

Kerjakan **berurutan**; tiap fase harus bisa diuji sebelum lanjut.

| Fase | Isi | Selesai jika |
|---|---|---|
| 0 | Infra & scaffold: semua file dari `docs/05-DEVOPS.md`, restrukturisasi §4 | `docker compose up --build` → `http://localhost:8080` menampilkan CI3 tanpa error, koneksi MySQL OK |
| 1 | Database: DDL jalan via init MySQL, 3 model dibuat sesuai `docs/02-DATABASE.md` + pola `prompt.md` | Semua method model teruji |
| 2 | Kerangka UI: templates `0_head`–`3_end`, vendor libs ke `public/cdn/lib/`, navbar, `app.css` (tokens `DESIGN.md`) | Halaman kosong dengan navbar tampil konsisten |
| 3 | CRUD Grup: `group_view` (DataTables) + modal add/edit + delete bootbox, guard hapus | `docs/01-PRD.md` F2 lulus |
| 4 | CRUD Proyek: banner guard "belum ada grup", CRUD modal lengkap, input warna | F3 lulus |
| 5 | Progres: modal tambah (guard ≥1 proyek), auto masuk Pending | F4 lulus |
| 6 | Board kanban: `board_view` + 3 kolom, render kartu dari JSON `progress_daftar`, drag & drop SortableJS, persist via `progress_reorder` | F1 + F5 lulus, posisi persist setelah reload |
| 7 | Arsip: tombol Arsipkan (hanya di Selesai), halaman Arsip (DataTables, Pulihkan / Hapus permanen) | F6 lulus |
| 8 | Polish & QA: empty states, validasi & toast, badge overdue, checklist §7 | Semua QA §8 lulus |

## 6. Konvensi wajib

**Gaya kode (sumber: `prompt.md` — Metode Penulisan):**
- **Model** — file `Model_resource.php`, class `Model_resource`. Method:
  `list_resource()` (result), `single_resource($id)` (row), `add_resource($data)`,
  `update_resource($id, $data)`, `delete_resource($id)`.
  Kondisi tambahan = **parameter opsional** `if($param != '')`, BUKAN method baru
  (dilarang `list_x_by_status()`). Method update/delete diakhiri `$this->db->reset_query();`.
  Method agregat boleh (`count_…`, `max_position`). Semua via Query Builder.
- **Controller** — file `Resource.php` (Kapital di depan). Method:
  `index()`, `resource_daftar()`, `resource_add()`, `resource_edit($id)`,
  `resource_addsave()`, `resource_editsave()`, `resource_delete()`.
  Output JSON via `private function set_output($data, $status=200)` (persis seperti
  contoh di `prompt.md`), **tanpa blok else setelah panggilan set_output** (sudah `exit`).
- **Array PHP** — selalu `array('k' => v)`, bukan `[]`. Data berdependensi ditulis
  `$data['k1'] = ...;` per baris, bukan inline di literal array.
- **View** — `resource_view.php` (layout), `resource_add.php`/`resource_edit.php`
  (fragmen form untuk konten modal). Layout: `templates/0_head.php`, `1_topbar.php`,
  `2_footer.php`, `3_end.php` (load: `$this->load->view('templates/0_head')` dst).
  Setiap controller kirim `$data['navbar'] = 'board'|'project'|'group'|'archive'`.
- **Quotes** — petik 1 `'` untuk PHP, petik 2 `"` untuk JavaScript.
- **URL AJAX statis** — `"/group/group_daftar"`, bukan `site_url()`.
- **Naming class/id** — tombol `btn-resource-add|-edit|-delete`, form `form-resource-add|-edit`.
- **PHP 7.4 saja** — dilarang sintaks PHP 8+: `match`, enum, named arguments,
  union types, constructor promotion, nullsafe `?->`, `str_contains()`, dll.

**Interaksi & frontend:**
- CRUD via **modal + AJAX** (pola persis contoh `kategori` di `prompt.md`):
  list = DataTables (`ajax: "/resource/resource_daftar"`), modal add/edit memuat fragmen
  form via `$.get`, submit via `$.ajax` JSON, delete via `bootbox.confirm` + `$.post`,
  notifikasi via `toastr`.
- Vendor: jQuery 3, Bootstrap 4 bundle (untuk modal/dropdown), bootbox 5, toastr,
  DataTables 1.13, SortableJS 1.15 — simpan lokal di `public/cdn/lib/` (daftar di `docs/04 §4`).
- **Tampilan** mengikuti `DESIGN.md` (monokrom, pill, hairline, tanpa drop shadow) —
  komponen Bootstrap di-*restyle* via `app.css`. Ringkasan di `docs/04-UI-SPEC.md`.

**Lainnya:**
- **Bahasa** — UI & pesan: Indonesia (teks eksak di `docs/04-UI-SPEC.md`).
  Kode, identifier, tabel/kolom: Inggris.
- **Tanpa auth** — JANGAN pakai ion_auth/session guard (potongan `ion_auth` di contoh
  controller `prompt.md` berasal dari proyek lain; tidak berlaku di sini).
- **CSRF nonaktif** (`csrf_protection = FALSE`) — pola AJAX di Metode Penulisan tidak
  membawa token; aplikasi local-only, port di-bind `127.0.0.1`. Tetap wajib:
  `html_escape()` di semua output & `intval()` semua id.
- **Timestamps** — dikelola default DDL (`DEFAULT CURRENT_TIMESTAMP`); model tidak perlu set manual.
- **Larangan** — jangan menambah: login/auth, dependency composer, migration library,
  REST server, subtask, lampiran. Skema DB tidak boleh diubah tanpa alasan kuat (catat jika terpaksa).

## 7. Definition of Done

- [ ] `docker compose up --build` dari kondisi bersih → app jalan di `http://localhost:8080`
- [ ] `docker compose down && docker compose up` (tanpa hapus volume) → data tetap ada
- [ ] Semua acceptance criteria di `docs/01-PRD.md` §7 terpenuhi
- [ ] Semua CRUD mengikuti pola `prompt.md`: modal + AJAX + JSON `set_output` + toastr/bootbox
- [ ] Drag & drop kartu antar kolom **persist setelah reload**
- [ ] Alur guard lengkap: 0 grup → 0 proyek → 0 progres, masing-masing CTA benar
- [ ] Arsip hanya dari kolom Selesai; restore kembali ke Selesai; hapus permanen dari Arsip
- [ ] Delete grup/proyek yang punya data anak **ditolak** dengan toast pesan jelas
- [ ] Gagal validasi → toast gagal (HTTP 400), modal tetap terbuka, isi form tidak hilang
- [ ] Tampilan konsisten dengan `DESIGN.md` (monokrom, pill, hairline, tanpa drop shadow)
- [ ] Checklist QA §8 lulus semua

## 8. QA manual (jalankan sendiri sebelum menyatakan selesai)

| # | Skenario | Hasil diharapkan |
|---|---|---|
| 1 | First run: DB kosong, buka `/` | Board menampilkan CTA "Buat Grup" (bukan kolom kanban) |
| 2 | Klik CTA → halaman Grup → tambah grup "Marketing" | Toast sukses, grup tampil di DataTables |
| 3 | Kembali ke `/` | CTA berubah: "Tambah Proyek" |
| 4 | Buka `/project` tanpa grup (DB grup dikosongkan) | Banner guard ke halaman Grup, tombol tambah disembunyikan |
| 5 | Buat proyek valid (warna, domain, direktori terisi) | Toast sukses; tampil di daftar dengan dot warna |
| 6 | Submit modal proyek dengan nama kosong | Toast gagal (HTTP 400), modal tetap terbuka, field lain terisi |
| 7 | `/` dengan grup+proyek tapi 0 progres | Board 3 kolom + empty state "+ Tambah Progres" |
| 8 | Tambah progres valid via modal | Kartu muncul di kolom **Pending**, toast sukses |
| 9 | Progres tanpa deadline | Toast gagal; tidak tersimpan |
| 10 | Drag kartu Pending → WIP → reload | Kartu tetap di WIP, posisi tersimpan |
| 11 | Drag ke Selesai → tombol "Arsipkan" | Kartu hilang dari board, muncul di `/archive` |
| 12 | Di Arsip: "Pulihkan" | Kartu kembali ke kolom Selesai |
| 13 | Di Arsip: Hapus (bootbox confirm) | Data hilang permanen |
| 14 | Hapus grup yang masih punya proyek | **Ditolak**, toast pesan alasan, data utuh |
| 15 | Hapus proyek yang masih punya progres | **Ditolak**, toast pesan alasan, data utuh |
| 16 | Progres deadline kemarin & status Pending/WIP | Teks deadline merah + label "Terlambat" |
| 17 | Nama grup duplikat | Toast gagal "Nama grup sudah dipakai." |
| 18 | Akses `/progress/progress_add` saat 0 proyek | Redirect ke `/project` |
| 19 | Ukuran layar sempit (≤768px) | Kolom kanban horizontal-scroll, tidak rusak |
| 20 | `docker compose down && up` | Semua data dari skenario 1–19 masih ada |
