# 03 — Routes & API Spec (CodeIgniter 3 · pola Metode Penulisan)

Semua interaksi mengikuti pola di `prompt.md`: halaman dimuat server-side (`index()`),
data list via **AJAX JSON** (`resource_daftar` untuk DataTables), CRUD via **modal + AJAX**
(`resource_add/addsave/edit/editsave/delete`), notifikasi via JSON `{status, pesan}`.
Satu-satunya endpoint AJAX tambahan: `progress_reorder` (drag & drop).

> **Gaya kode wajib:** `set_output()` persis seperti contoh `prompt.md` (private method di
> tiap controller, `exit` di dalamnya → **jangan tulis blok else setelah panggilan set_output**),
> array PHP `array()`, petik 1 `'` untuk PHP, petik 2 `"` untuk JS, URL AJAX statis.

## 1. Konfigurasi

```php
// application/config/config.php (potongan yang diubah)
$config['base_url'] = '';            // auto-detect CI 3.1.x
$config['index_page'] = '';          // URL bersih via nginx
$config['uri_protocol'] = 'REQUEST_URI';
$config['csrf_protection'] = FALSE;  // lihat catatan keamanan §9
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
```

```php
// application/config/autoload.php
$autoload['packages'] = array();
$autoload['libraries'] = array('database');
$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'app');   // app_helper: badge prioritas, tanggal ID, overdue
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array('Model_group', 'Model_project', 'Model_progress');
```

> Session & form_validation **tidak dipakai** (tidak ada login, tidak ada flashdata —
> umpan balik lewat toast). Contoh `ion_auth` di `prompt.md` berasal dari proyek lain: **abaikan**.

```php
// application/config/routes.php
$route['default_controller'] = 'board';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Routing lain mengandalkan default CI3: /{controller}/{method}
// AJAX selalu memanggil URL statis, contoh: "/group/group_daftar"
```

## 2. Kontrak JSON

**Respons aksi** (addsave/editsave/delete/archive/restore/reorder):

```php
// sukses   → HTTP 200
$this->set_output(array('status' => 'sukses', 'pesan' => 'Grup berhasil ditambahkan.'));
// validasi / guard gagal → HTTP 400
$this->set_output(array('status' => 'gagal', 'pesan' => 'Nama grup wajib diisi.'), 400);
// kegagalan DB → HTTP 500
$this->set_output(array('status' => 'gagal', 'pesan' => 'Gagal menambahkan grup'), 500);
```

**Respons daftar DataTables** (`*_daftar`): `array('data' => array(row1, row2, ...))`
— tiap row adalah `array()` kolom sesuai urutan `<thead>`; kolom aksi berisi string HTML
dropdown (pola persis contoh `kategori_daftar` di `prompt.md`, dengan class
`btn-group-edit`/`btn-group-delete` dst). Semua nilai dari DB wajib dibungkus `html_escape()`.

**Sisi klien (pola tetap):** `.done()` cek `respon.status === "sukses"` →
`toastr.success(respon.pesan, "Sukses")` + reload DataTables/tutup modal;
selain itu `toastr.error(respon.pesan, "Gagal")`; `.fail()` → `toastr.error("Terjadi error di server", "Gagal")`.

## 3. Validasi (manual, pola `prompt.md`)

Validasi dilakukan manual di `*_addsave`/`*_editsave` — periksa berurutan, tiap kegagalan
langsung `set_output(..., 400)`. Validasi edit **wajib** juga menerima `id` dan mengabaikan
record itu sendiri saat cek unik.

**Group** (`group_addsave` / `group_editsave`):

| Cek | Pesan gagal |
|---|---|
| `name` terisi setelah `trim()` | "Nama grup wajib diisi." |
| `strlen(trim($name)) >= 3` dan `<= 150` | "Nama grup minimal 3 karakter." |
| Nama belum dipakai grup lain (loop `list_group()`, perbandingan case-insensitive) | "Nama grup sudah dipakai." |

**Project** (`project_addsave` / `project_editsave`):

| Cek | Pesan gagal |
|---|---|
| `name` terisi, 3–150 char | "Nama proyek wajib diisi." / "Nama proyek minimal 3 karakter." |
| `id_group` valid: `single_group($id_group) !== null` | "Grup tidak valid." |
| `domain` terisi, `strlen <= 255` | "Domain wajib diisi." |
| `directory_path` terisi, `strlen <= 255` | "Direktori wajib diisi." |
| `color` cocok `preg_match('/^#[0-9a-fA-F]{6}$/', $color)` | "Warna tidak valid." |
| `description` opsional, `strlen <= 1000` | "Deskripsi maksimal 1000 karakter." |

**Progress** (`progress_addsave`):

| Cek | Pesan gagal |
|---|---|
| `name` terisi, 3–150 char | "Nama progres wajib diisi." / "Nama progres minimal 3 karakter." |
| `id_project` valid: `single_project($id_project) !== null` | "Proyek tidak valid." |
| `deadline` cocok `/^\d{4}-\d{2}-\d{2}$/` dan `checkdate()` | "Deadline wajib diisi." / "Deadline tidak valid." |
| `priority` in `array('low','medium','high')` | "Prioritas tidak valid." |
| `description` opsional, `strlen <= 1000` | "Deskripsi maksimal 1000 karakter." |

## 4. Controller `Board.php`

| Method | Perilaku |
|---|---|
| `index()` | Hitung guard: `count($this->model_group->list_group())` dan `count($this->model_project->list_project())`. Kirim `$data['mode']`: `empty_group` \| `empty_project` \| `board` + `$data['navbar'] = 'board'` → view `board/board_view.php`. Saat `board`: tombol "+ Tambah Progres" aktif; kartu diambil via AJAX §7. |

## 5. Controller `Group.php` (resource: group)

| Method | Perilaku |
|---|---|
| `index()` | `$data['navbar']='group'` → view `group/group_view.php` (DataTables `#tabel-group` + `modal_frame`). |
| `group_daftar()` | `list_group()` → rows: `array(nama, count_project($id), $aksi_html)`; `$aksi_html` dropdown Edit/Delete dengan `btn-group-edit`/`btn-group-delete` + `data-id`. |
| `group_add()` | View fragmen `group/group_add.php` (form `#form-group-add`: input nama). |
| `group_addsave()` | Validasi §3 → `add_group(array('name' => trim($name)))` → sukses: "Grup berhasil ditambahkan." |
| `group_edit($id = '')` | `single_group($id)`; null → redirect `group`; else view `group/group_edit.php`. |
| `group_editsave()` | POST `id`, `name`; validasi §3 (skip id sendiri) → `update_group($id, array('name' => trim($name)))` → "Grup berhasil diperbarui." |
| `group_delete()` | POST `id`. `count_project($id) > 0` → **400** "Grup tidak bisa dihapus karena masih memiliki proyek." Else `delete_group($id)` → "Grup berhasil dihapus." |

## 6. Controller `Project.php` (resource: project)

| Method | Perilaku |
|---|---|
| `index()` | Guard: `list_group()` kosong → `$data['mode']='empty_group'` (banner + tombol tambah disembunyikan); else `mode='list'`. → `project/project_view.php`. |
| `project_daftar()` | `list_project()` → rows: `array(dot_warna+nama_html, nama_grup, domain_html(link), direktori_html(<code>), count_progress($id), $aksi_html)`. |
| `project_add()` | Guard 0 grup → redirect `group`. View `project/project_add.php` dengan `$groups = list_group()` + warna default palet (rotasi: `#3B82F6, #10B981, #F59E0B, #EF4444, #8B5CF6, #EC4899`). |
| `project_addsave()` | POST `name, id_group, description, domain, directory_path, color` → validasi §3 → `add_project(array(...))` (id dari auto-increment, **jangan** `uniqid()`) → "Proyek berhasil ditambahkan." |
| `project_edit($id = '')` | `single_project($id)`; null → redirect `project`; else view `project/project_edit.php` (+ `$groups`). |
| `project_editsave()` | POST sama + `id` → validasi → `update_project($id, array(...))` → "Proyek berhasil diperbarui." |
| `project_delete()` | POST `id`. `count_progress($id) > 0` → **400** "Proyek tidak bisa dihapus karena masih memiliki progres." Else `delete_project($id)` → "Proyek berhasil dihapus." |

Field DB project: `group_id, name, description, domain, directory_path, color`.

## 7. Controller `Progress.php` (resource: progress)

| Method | Perilaku |
|---|---|
| `progress_daftar()` | **AJAX board.** GET opsional `?id_group=&id_project=` (filter board dari modal filter, persist di localStorage klien; id tak valid → hasil kosong, tetap 200). `list_progress()` (archived=0, +filter) → kelompokkan per status → respons: `array('status'=>'sukses', 'data'=>array('pending'=>array(card,...), 'wip'=>..., 'done'=>...))`. Tiap card: `array('id', 'name', 'description', 'deadline' ('Y-m-d'), 'terlambat' (bool: `date('Y-m-d') > deadline`, status ≠ done), 'priority' ('low'|'medium'|'high'), 'priority_label' ('Rendah'|'Sedang'|'Tinggi'), 'project_name', 'project_color', 'group_name')`. **(v1.2)** + `subtask_total`, `subtask_done` (badge), `notes_preview` (catatan dipotong maks 20 char, `mb_substr`), `notes_truncated` (bool), `subtasks` (per status, maks 3 nama), `subtask_hidden` (per status, jumlah sisa) — bahan hover preview kartu; sub tugas diambil **satu query batch** `where_in` semua id kartu (ringan). |
| `progress_add()` | Guard `count(list_project()) == 0` → redirect `project`. View `progress/progress_add.php`: `$projects = list_project()` (label option: "nama — grup"), deadline default `date('Y-m-d', strtotime('+7 days'))`, prioritas default `medium`. |
| `progress_addsave()` | POST `name, id_project, deadline, description, priority` → validasi §3 → posisi: `$max = max_position('pending'); $position = $max ? (int)$max->position + 1 : 1;` → `add_progress(array('project_id' => ..., 'name' => ..., 'description' => ..., 'deadline' => ..., 'priority' => ..., 'status' => 'pending', 'position' => $position))` → "Progres berhasil ditambahkan." |
| `progress_edit($id = '')` | `single_progress($id)`; null → redirect `board`. View `progress/progress_edit.php`: form `#form-progress-edit` (hidden `id` + nama/proyek terpilih/deadline/deskripsi/prioritas terisi). |
| `progress_editsave()` | POST `id, name, id_project, deadline, description, priority` → `$id` ada (null → 400 "Progres tidak ditemukan.") → validasi §3 sama dengan `progress_addsave` → `update_progress($id, array('project_id' => ..., 'name' => ..., 'description' => ..., 'deadline' => ..., 'priority' => ...))` — **hanya 5 field konten; `status` & `position` tidak disentuh** → "Progres berhasil diperbarui." |
| `progress_delete()` | POST `id` → `delete_progress($id)` → "Progres berhasil dihapus." (boleh dari board & arsip). |
| `progress_archive()` | POST `id`. `$item = single_progress($id)`; null → 400 "Progres tidak ditemukan."; `$item->status != 'done'` → 400 "Hanya progres di kolom Selesai yang bisa diarsipkan."; else `update_progress($id, array('archived' => 1, 'archived_at' => date('Y-m-d H:i:s')))` → "Progres berhasil diarsipkan." |
| `progress_restore()` | POST `id`. `$item = single_progress($id)`; null → 400; `!$item->archived` → 400 "Progres ini tidak sedang diarsipkan."; else `update_progress($id, array('archived' => 0, 'archived_at' => null))` → "Progres berhasil dipulihkan." |
| `progress_reorder()` | **AJAX drag & drop** — lihat §8. |

## 8. Kontrak AJAX `POST /progress/progress_reorder`

Dipanggil sekali per kejadian drop (handler `onEnd` SortableJS), jQuery `$.post`
menyerialisasi array otomatis (`order[]`):

```
status  = pending | wip | done      // kolom TUJUAN
order   = [12, 7, 3]                // SELURUH id kartu kolom tujuan, urut DOM setelah drop
```

Server:
1. `status` harus in `array('pending','wip','done')`; tiap id numerik & `single_progress($id)` ada dan `archived == 0` → jika tidak: **400** "Gagal menyimpan posisi. Coba lagi."
2. Transaksi: `$this->db->trans_start();` → foreach `order` sebagai `$i => $id`: `update_progress($id, array('status' => $status, 'position' => $i))` → `$this->db->trans_complete();`
3. `$this->db->trans_status() == TRUE` → `array('status' => 'sukses', 'pesan' => '')`; else **500** "Gagal menyimpan posisi. Coba lagi."

Klien: gagal (HTTP ≠ 200 / `status !== "sukses"` / `.fail()`) → `toastr.error(...)` + `location.reload()`
(kartu kembali ke kondisi server). Kartu yang keluar dari kolom asal tidak dikirim ulang;
posisi kartu sisa kolom asal tidak berubah (gap diperbolehkan).

## 9. Controller `Archive.php`

| Method | Perilaku |
|---|---|
| `index()` | `$data['navbar']='archive'` → view `archive/archive_view.php` (DataTables `#tabel-archive` + `modal_frame` tidak perlu). |
| `archive_daftar()` | `list_progress('', '1')` → rows: `array(nama, dot_warna+nama_proyek, nama_grup, label_prioritas, deadline, tanggal_arsip ('d M Y'), $aksi_html: btn-progress-restore + btn-progress-delete)`. Aksi pulihkan/hapus memanggil `progress_restore`/`progress_delete` di controller Progress. |

## 10. Controller `Subtask.php` (v1.1 — detail sub tugas)

| Method | Perilaku |
|---|---|
| `subtask_daftar($id_progress = '')` | Progres tak ada → **400** "Progres tidak ditemukan." Respons: `array('status'=>'sukses','data'=>array('pending'=>..., 'wip'=>..., 'done'=>...))` — tiap item `array('id','name','status')`, ORDER `position` ASC. |
| `subtask_addsave()` | POST `id_progress, name` → progres valid, nama 3–150 char (pesan: "Nama sub tugas wajib diisi." dst.) → selalu `status='pending'` (grup Inbox), `position` = MAX+1 dalam progres → "Sub tugas berhasil ditambahkan." |
| `subtask_status()` | POST `id, status` → sub tugas ada (null → 400 "Sub tugas tidak ditemukan."), status in list (else 400 "Status sub tugas tidak valid.") → update. Sukses `pesan=''` (UI update diam-diam, tanpa toast). |
| `subtask_delete()` | POST `id` → hapus → "Sub tugas berhasil dihapus." |

Perubahan di `Progress.php` (v1.1):
- `progress_detail($id = '')` — `single_progress($id)`; null → redirect `board`; view `progress/progress_detail.php` — **fragmen halaman penuh detail tugas** (bukan modal): klik kartu mengganti html utama board via AJAX (`#board-view` disembunyikan, `#detail-view` diisi). Isi: header (← Kembali + judul + tombol Edit Tugas), meta, **textarea Catatan**, dan board sub tugas bergaya sama dengan board induk.
- `progress_catatan()` (v1.2) — POST `id, notes` → progres ada (null → 400 "Progres tidak ditemukan."), `strlen(notes) <= 2000` (else 400 "Catatan maksimal 2000 karakter.") → `update_progress($id, array('notes' => trim($notes)))` → "Catatan berhasil disimpan." Catatan hanya untuk tugas induk.
- `progress_daftar()` — tiap kartu memuat `subtask_total` & `subtask_done` (badge `1/3 sub` di kartu).
- `progress_delete()` — dalam transaksi: hapus seluruh sub tugas progres (`delete_sub_progress('', $id)`) lalu progresnya (sub tugas = anak; ikut terhapus).

Perubahan di `Subtask.php` (v1.2):
- `subtask_reorder()` — POST `status, order[]` (kontrak identik `progress_reorder`): status valid, tiap id ada, transaksi `update_sub_progress($id, array('status' => ..., 'position' => i))` → sukses `pesan=''`; gagal → 400/500 "Gagal menyimpan posisi. Coba lagi." Dipanggil SortableJS `onEnd` pada board sub tugas di halaman detail (drag antar Inbox/Progress/Done + urutan dalam grup persist).

Perubahan di `Board.php` (v1.2 — filter board):
- `board_filter()` — view fragmen `board/board_filter.php`: dropdown **Grup** & **Proyek** ("Semua ..." = value 0) + tombol **Reset** / **Terapkan**. Dimuat ke modal via `$.get` oleh tombol ikon filter (`.btn-board-filter`, outline secondary + ikon funnel) persis di samping tombol Tambah Tugas.
- Nilai filter **persist di localStorage** klien (`tracker_board_filter`) → bertahan lintas refresh & navigasi sampai diubah/direset; tombol filter diberi tanda aktif (`.btn-filter-active`).
- `progress_daftar` menerima GET `id_group`/`id_project` → filter di SQL via `list_progress($status, $archived, $id_project, $id_group)` (param opsional; `Archive` memanggil dengan `''` untuk filter). Filter aktif + hasil kosong → board menampilkan "Tidak ada tugas yang cocok dengan filter."

## 11. Keamanan & ketelitian

- Semua string dari DB yang masuk HTML (termasuk HTML string di `*_daftar` dan JSON kartu)
  wajib `html_escape()`; `color` sudah tervalidasi regex sebelum dipakai di attribute `style`.
- Semua `id` dari input: `(int)` cast / `intval()` sebelum dipakai ke model.
- CSRF nonaktif secara sadar: tanpa auth, tanpa multi-user, dan port hanya di-bind
  `127.0.0.1:8080` (lihat `docs/05-DEVOPS.md`) — **jangan** expose ke jaringan publik.
- `db_debug` mengikuti ENVIRONMENT; jangan menampilkan detail DB di produksi.
