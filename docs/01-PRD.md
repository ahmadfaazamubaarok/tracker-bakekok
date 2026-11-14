# 01 — PRD (Product Requirements Document)

**Produk:** Project Tracker
**Versi:** 1.0 · **Tanggal:** 2026-10-31 · **Status:** Ready for development

---

## 1. Ringkasan

Aplikasi web single-user untuk melacak progres proyek pribadi berbentuk **kanban board**
dengan 3 kolom status: **Pending → WIP → Selesai**. Tanpa autentikasi, dijalankan lokal
via Docker. Data tersusun hierarki **Grup → Proyek → Progres**; progres yang selesai
dapat **diarsipkan** agar board tetap bersih.

## 2. Masalah & Tujuan

**Masalah:** progres proyek-proyek pribadi tersebar dan sulit dipantau statusnya dalam satu tampilan.

**Tujuan:**
1. User dapat melihat status seluruh progres dalam satu layar (board).
2. Update status sesederhana drag & drop kartu.
3. Progres selesai bisa diarsipkan tanpa menghilangkan riwayat.
4. Data terstruktur: progres → proyek → grup, sehingga bisa dikelompokkan.

## 3. Pengguna

| Peran | Deskripsi |
|---|---|
| Pemilik (single user) | Satu-satunya pengguna; tidak ada login, tidak ada multi-user. Aplikasi berjalan lokal. |

## 4. Ruang Lingkup

**In-scope (v1):**

| Kode | Fitur |
|---|---|
| F1 | Board kanban 3 kolom (Pending / WIP / Selesai) + kartu progres |
| F2 | CRUD Grup |
| F3 | CRUD Proyek |
| F4 | Tambah progres + hapus progres |
| F5 | Drag & drop kartu antar kolom (persist posisi) |
| F6 | Arsip (arsipkan dari Selesai, lihat daftar, pulihkan, hapus permanen) |
| F7 | Hapus data via tombol X pada grup/proyek/progres |

**Out-of-scope (v1):** login/auth & multi-user, edit progres (lihat §9 D7), subtask/checklist,
lampiran, komentar, notifikasi/reminder, import/export, pencarian & filter, dark mode,
i18n, real-time collaboration, API publik.

## 5. User Flow

### 5.1 First run (database kosong)

```
Buka / (Board)
  └─ 0 grup & 0 proyek → tampil empty-state + CTA "Buat Grup"
       └─ Halaman Grup → tambah minimal 1 grup
            └─ Buka /project → tambah minimal 1 proyek
                 └─ Buka / (Board) → empty-state board + CTA "+ Tambah Progres"
                      └─ Tambah progres → kartu otomatis masuk kolom Pending
```

### 5.2 Flow normal

```
Board → drag kartu antar kolom (persist) → kartu tiba di Selesai
      → klik "Arsipkan" → kartu keluar dari board, masuk halaman Arsip
      → di Arsip: "Pulihkan" (kembali ke Selesai) atau "Hapus" (permanen)
```

### 5.3 Guard berjenjang (patokan dependensi)

| Kondisi data | Halaman Board | Halaman Proyek |
|---|---|---|
| 0 grup, 0 proyek | CTA **"Buat Grup"** → `/group` | Banner guard → `/group` (form tambah disembunyikan) |
| ≥1 grup, 0 proyek | CTA **"Tambah Proyek"** → `/project` | CRUD normal |
| ≥1 grup, ≥1 proyek | Board + tombol **"+ Tambah Progres"** | CRUD normal |
| Form Tambah Progres diakses saat 0 proyek | — | Redirect ke halaman Proyek (server guard) |

Halaman Grup tidak pernah di-guard (selalu bisa diakses & CRUD).

## 6. Kebutuhan Data (ringkas)

Detail teknis di `docs/02-DATABASE.md`.

**Grup:** nama (wajib)
**Proyek:** nama (wajib), deskripsi (opsional), grup (wajib, pilih existing),
domain (wajib), direktori (wajib), warna (wajib, hex)
**Progres:** nama (wajib), proyek (wajib, pilih existing), deadline (wajib, tanggal),
deskripsi (opsional), prioritas (wajib: rendah/sedang/tinggi, default sedang),
status (sistem: pending/wip/done), arsip (sistem: flag)

## 7. Fitur & Acceptance Criteria

### F1 — Board Kanban
- Board adalah halaman **default** (`/`), tanpa login.
- 3 kolom tetap urut kiri→kanan: **Pending, WIP, Selesai**, masing-masing dengan counter kartu.
- Kartu menampilkan: dot warna + nama proyek, nama progres, chip prioritas, deadline,
  deskripsi (maks 2 baris, ada deskripsi saja), aksi hover (X hapus; "Arsipkan" jika di Selesai).
- Kartu urut berdasarkan `position` ASC dalam kolomnya.
- Deadline yang sudah lewat (dan status ≠ Selesai) ditandai merah + label "Terlambat".
- **AC1:** semua kondisi guard §5.3 tampil benar. **AC2:** kartu hanya tampil di kolom sesuai statusnya; item terarsip tidak muncul di board.

### F2 — CRUD Grup
- Daftar grup: nama, jumlah proyek, aksi (Edit, X).
- Create & edit via form (nama). Delete via X + confirm.
- **AC:** hapus grup yang masih punya proyek **ditolak** dengan pesan alasan; nama grup wajib 3–150 karakter.

### F3 — CRUD Proyek
- Daftar proyek: dot warna, nama, grup, domain (tautan), direktori, jumlah progres, aksi (Edit, X).
- Form: nama, deskripsi (opsional), grup (dropdown existing), domain, direktori, warna (`<input type="color">`, default dari palet).
- **AC:** tanpa grup → banner guard; hapus proyek yang masih punya progres ditolak dengan pesan.

### F4 — Tambah / Hapus Progres
- Form modal (AJAX): nama, proyek (dropdown), deadline (date picker), deskripsi (opsional), prioritas (dropdown, default Sedang).
- Setelah simpan → kartu otomatis di kolom **Pending** (paling bawah) + board di-render ulang.
- Hapus via X di kartu/table + bootbox confirm, dari board maupun arsip.
- **AC:** progres baru selalu `pending`; hanya bisa dibuat jika ≥1 proyek ada.

### F5 — Drag & Drop
- Kartu dapat digeser antar kolom dan diurutkan ulang dalam kolom.
- Setelah drop, posisi & status **persist** (reload browser tidak mengubah hasil).
- **AC:** drop gagal disimpan → UI kembali ke kondisi server (reload) + toast gagal.

### F6 — Arsip
- Tombol "Arsipkan" **hanya** ada pada kartu di kolom Selesai.
- Halaman `/archive`: daftar item terarsip (nama, proyek, grup, prioritas, deadline, tanggal arsip) + aksi Pulihkan / Hapus permanen.
- **AC:** arsipkan → hilang dari board & muncul di Arsip; pulihkan → kembali ke kolom Selesai; status tidak berubah saat arsip/pulihkan.

### F7 — Hapus Data (X)
- X tersedia di: kartu progres (board & arsip), baris grup, baris proyek.
- Selalu ada konfirmasi via `bootbox.confirm`. Hapus progres bersifat permanen (progres tidak di-soft-delete).

## 8. Aturan Bisnis

| Kode | Aturan |
|---|---|
| BR-01 | Progres hanya bisa dibuat jika ada minimal 1 grup **dan** 1 proyek (dependensi berjenjang). |
| BR-02 | Setiap progres terikat tepat 1 proyek; setiap proyek terikat tepat 1 grup. |
| BR-03 | Progres baru selalu berstatus `pending`, posisi paling bawah kolomnya. |
| BR-04 | Arsip hanya boleh dari status `done`. Arsip tidak mengubah status. |
| BR-05 | Restore dari arsip mengembalikan kartu ke kolom Selesai (`archived=0`). |
| BR-06 | Hapus grup ditolak jika masih ada proyek; hapus proyek ditolak jika masih ada progres (RESTRICT + pesan). Hapus progres selalu boleh. |
| BR-07 | Nilai status: `pending` \| `wip` \| `done`. Nilai prioritas: `low` \| `medium` \| `high` (disimpan EN, tampil ID: Rendah/Sedang/Tinggi). |
| BR-08 | Warna proyek format `#RRGGBB`; dipakai sebagai dot identitas pada kartu & daftar. |
| BR-09 | Overdue = `deadline < hari ini` **dan** status ≠ `done` (kartu arsip tidak ditandai overdue). |
| BR-10 | Semua hapus (X) memerlukan konfirmasi via `bootbox.confirm`. |

## 9. Keputusan & Asumsi (diputuskan agar bisa langsung dibangun)

| Kode | Keputusan | Alasan |
|---|---|---|
| D1 | Tanpa auth; port di-bind ke localhost saja | Sesuai flow user |
| D2 | Tabel grup bernama `project_groups` | `GROUPS` reserved word di MySQL 8 |
| D3 | Delete = hard delete; proteksi RESTRICT untuk grup/proyek yang punya anak | Sederhana & aman; tidak ada soft delete selain arsip |
| D4 | Arsip = flag `archived` + `archived_at` pada tabel progres, bukan tabel terpisah | Status tidak perlu diubah saat arsip; restore trivial |
| D5 | Urutan kartu: kolom `position` integer; drop mengirim daftar ID urut kolom tujuan; server tulis ulang dalam transaksi | Persist akurat & sederhana |
| D6 | CRUD via **modal + AJAX + JSON** mengikuti "Metode Penulisan" di `prompt.md` (controller `resource_daftar/add/addsave/edit/editsave/delete`, output `set_output`, DataTables, toastr, bootbox); halaman di-guard server-side | Pola wajib milik user; konsisten dengan contoh kode di `prompt.md` |
| D7 | Progres **tidak bisa diedit** di v1 (hanya tambah, geser, arsip, hapus) | Tidak ada di user flow; masuk backlog |
| D8 | UI Bahasa Indonesia; kode & DB Bahasa Inggris | Bahasa user |
| D9 | Frontend: jQuery + Bootstrap 4 (modal/dropdown) + DataTables + toastr + bootbox + SortableJS (vendor lokal di `public/cdn/lib/`), di-*restyle* via CSS mengikuti `DESIGN.md`; font Inter (substitusi Saans) | Mengikuti pola `prompt.md`; tema visual tetap DESIGN.md |
| D10 | Field domain & direktori proyek wajib (hanya deskripsi yang opsional) | Sesuai spesifikasi isian user |
| D11 | `DESIGN.md` di root repo = design system resmi aplikasi | File dibuat user di folder proyek ini |
| D12 | CSRF **nonaktif** (`csrf_protection = FALSE`) | Pola AJAX di Metode Penulisan tidak membawa token; app local-only, port bind `127.0.0.1` |
| D13 | **Tanpa auth/ion_auth** — potongan `ion_auth` di contoh controller `prompt.md` diabaikan | Tidak ada login di user flow |
| D14 | **PHP 7.4** — dilarang sintaks PHP 8+ (`match`, enum, named args, union types, `?->`, `str_contains()`) | Permintaan eksplisit user |

**Keputusan tambahan saat implementasi (agent):**

| Kode | Keputusan | Alasan |
|---|---|---|
| D15 | Port host di-bind `127.0.0.1:3000` (bukan 8080) | Port 8080 host dipakai container lain (`crud_app`); keputusan user saat sesi build. App: `http://localhost:3000` |
| D16 | Bootstrap 4 **CSS** (`bootstrap.min.css`) ditambahkan sebagai vendor lokal | `docs/04 §4` hanya mendaftar JS bundle; modal/dropdown BS4 butuh CSS-nya, lalu di-*restyle* penuh oleh `app.css` agar tetap mengikuti `DESIGN.md` |
| D17 | Autoload model memakai alias lowercase: `array('Model_group' => 'model_group', …)` | Autoload CI3 dengan key angka menghasilkan property `Model_group` (kapital); alias menyamakan pemakaian `$this->model_group` sesuai kontrak `docs/03` |
| D18 | Method `delete_*` model mengembalikan hasil `$this->db->delete()` | Contoh `prompt.md` tidak me-return sehingga cek sukses/gagal di controller (kontrak 200/500 `docs/03 §2`) tidak berfungsi; pola `update_*` diikuti |
| D19 | Validasi batas atas ditambahkan: "Nama … maksimal 150 karakter." dst. | Tabel validasi `docs/03 §3` eksplisit hanya batas bawah; guard server diperlukan agar strict mode tidak melempar HTTP 500 saat input > panjang kolom |
| D20 | `$config['base_url']` diisi dinamis dari `HTTP_HOST` (fallback `localhost:3000`) | CI3 dengan `base_url` kosong memakai `SERVER_ADDR` (IP container) sehingga `redirect()` menghasilkan `Location` salah (tidak bisa diakses host) |
| D21 | Handler `.fail()` jQuery membaca `xhr.responseJSON.pesan` untuk toast | Respons validasi gagal ber-HTTP 400 jatuh ke `.fail()`; tanpa ini QA #6 gagal menampilkan pesan eksak. Pola `.done()/.fail()` `docs/03 §2` tetap dipertahankan |
| D22 | `docker/mysql/init/02-seed.sql` **tidak dibuat** | Seed bersifat opsional (`docs/02 §7`); reset data via `docker compose down -v && docker compose up` |
| D23 | SortableJS config ditambah `draggable: ".kcard"` | Placeholder "Belum ada kartu" tidak ikut ter-drag; perilaku board lain sesuai `docs/04 §11` |
| D24 | **PWA + logo** (permintaan user, pasca-delivery): `manifest.json`, service worker `sw.js`, `offline.html`, ikon PNG + SVG di `public/assets/img/` | Akses lebih mudah: installable (desktop/home screen), aset statis cache-first, navigasi network-first dengan fallback offline; AJAX/JSON **tidak** pernah di-cache agar data board selalu segar. Logo SVG = 3 pill kanban monokrom (ladder `--text-faint`/`--canvas-soft`/`--canvas` di atas ink) mengikuti DESIGN.md |
| D25 | **Edit progres diaktifkan** (permintaan user, supersedes D7 & backlog): `progress_edit`/`progress_editsave` mengikuti pola Metode Penulisan; hanya **5 field konten** (nama, proyek, deadline, deskripsi, prioritas) | `status` & `position` tetap eksklusif dikelola drag & drop & arsip (BR-03..BR-05) agar tidak bentrok; tombol edit (✎) muncul saat hover di kartu board; item arsip tidak diberi edit (cukup Pulihkan dulu jika perlu) |
| D26 | **Detail sub tugas** (permintaan user): tabel `sub_progresses` (anak `progresses`, status pending/wip/done), klik kartu → modal Detail Tugas berisi 3 grup berlabel **Inbox / Progress / Done** (istilah view user; DB tetap EN). Sub tugas baru selalu masuk Inbox; chip status diklik untuk memutar status; hapus via bootbox confirm; badge `done/total` di kartu; hapus tugas = sub tugas ikut terhapus (transaksi) | Sub tugas tanpa deadline/prioritas — unit kerja cukup nama + status. Aksi tambah & ganti status sukses **tanpa toast** (masuk cepat banyak item sekaligus; feedback = UI ter-update); kegagalan tetap toast. DB tumbuh additive (CREATE TABLE), data user tidak tersentuh |
| D27 | **Detail tugas jadi halaman (page-swap), bukan modal** (permintaan user): klik kartu mengganti html utama board via AJAX; tambahan **kolom Catatan** pada tugas induk (`progresses.notes` TEXT, disimpan via `progress_catatan`, maks 2000 char) dan **board sub tugas** bergaya sama dengan drag & drop antar Inbox/Progress/Done (`subtask_reorder`, kontrak identik `progress_reorder`) | Halaman memberi ruang penuh untuk catatan + board sub tugas (modal terlalu sempit); drag & drop menggantikan chip klik-status karena konsisten dengan interaksi board induk; ESC/← Kembali kembali ke board; kolom `notes` tumbuh additive (ALTER TABLE), catatan sub tugas tidak dibuat (permintaan user: khusus induk) |
| D28 | **Filter board** (permintaan user): tombol ikon funnel (outline secondary) di samping tombol Tambah Tugas → modal filter Grup & Proyek; filter diterapkan di server (`progress_daftar?id_group=&id_project=` → `list_progress` param tambahan) dan **persist di localStorage** klien — tetap aktif lintas refresh & navigasi sampai diubah/direset; tombol diberi tanda aktif; kosong saat terfilter → pesan khusus "Tidak ada tugas yang cocok dengan filter." | Tanpa login/session → localStorage adalah tempat persist yang alami; filter di SQL (bukan client-side) menjaga query tetap ringan; id tak valid → hasil kosong tanpa error (guard ringan); Toast "Filter diterapkan./Filter direset." sebagai feedback |

## 10. Backlog (masa depan, jangan dikerjakan)

Edit progres · pencarian/filter board · dark mode · export CSV · statistik ringkas
(jumlah selesai per minggu) · soft delete · lampiran/tautan eksternal.

## 11. Kebutuhan Non-Fungsional

- **Runtime:** `docker compose up` satu perintah; data persist di named volume.
- **Performa:** semua halaman < 200 ms pada data lokal (ratusan kartu pun wajar).
- **Browser:** Chrome/Firefox/Edge/Safari modern. Responsif dasar: kanban horizontal-scroll di ≤768px.
- **Keamanan dasar:** `html_escape()` di semua output, `intval()` id, Query Builder, port hanya di-bind `127.0.0.1` (tanpa auth & CSRF — jangan expose ke jaringan).
- **Bahasa tampilan:** Indonesia; format tanggal "31 Des 2026".

## 12. Glosarium

| Istilah | Arti |
|---|---|
| Grup | Wadah pengelompok proyek; hanya punya nama |
| Proyek | Item kerja dengan domain & direktori; anak dari grup |
| Progres | Kartu pekerjaan pada board; anak dari proyek |
| WIP | Work In Progress — sedang dikerjakan |
| Arsip | Progres selesai yang dikeluarkan dari board, riwayatnya tetap ada |
| Overdue | Deadline terlewat dan progres belum selesai |
