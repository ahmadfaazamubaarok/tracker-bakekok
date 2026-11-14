# 02 — Database Spec (MySQL 8.0)

Charset & collation wajib: `utf8mb4` / `utf8mb4_unicode_ci`. Engine: InnoDB.

> **Catatan penting:** nama tabel grup adalah **`project_groups`**, bukan `groups`,
> karena `GROUPS` adalah reserved word MySQL 8. Tetap gunakan Query Builder CI3
> (identifiers otomatis di-backtick) dan hindari raw query.

---

## 1. ERD

```
project_groups 1 ──── n projects 1 ──── n progresses
     │                     │                    │
  id (PK)              id (PK)              id (PK)
  name                 group_id (FK)        project_id (FK)
                       name                 name
                       description          description
                       domain               deadline
                       directory_path       priority (low|medium|high)
                       color                status (pending|wip|done)
                       created_at           position
                       updated_at           archived (0|1), archived_at
                                            created_at, updated_at
```

Hapus selalu RESTRICT dari sisi anak (lihat §4).

## 2. DDL siap pakai

File ini dipasang sebagai `docker/mysql/init/01-schema.sql`
(dieksekusi MySQL otomatis saat **pertama kali** volume dibuat).

```sql
SET NAMES utf8mb4;

CREATE TABLE `project_groups` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_groups_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `projects` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id`       INT UNSIGNED NOT NULL,
  `name`           VARCHAR(150) NOT NULL,
  `description`    TEXT NULL,
  `domain`         VARCHAR(255) NOT NULL,
  `directory_path` VARCHAR(255) NOT NULL,
  `color`          CHAR(7) NOT NULL DEFAULT '#3B82F6',
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projects_group` (`group_id`),
  CONSTRAINT `fk_projects_group`
    FOREIGN KEY (`group_id`) REFERENCES `project_groups` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `progresses` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`  INT UNSIGNED NOT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `notes`       TEXT NULL,
  `deadline`    DATE NOT NULL,
  `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `status`      ENUM('pending','wip','done') NOT NULL DEFAULT 'pending',
  `position`    INT UNSIGNED NOT NULL DEFAULT 0,
  `archived`    TINYINT(1) NOT NULL DEFAULT 0,
  `archived_at` DATETIME NULL DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_progresses_project` (`project_id`),
  KEY `idx_progresses_board` (`archived`, `status`, `position`),
  CONSTRAINT `fk_progresses_project`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sub_progresses` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `progress_id` INT UNSIGNED NOT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `status`      ENUM('pending','wip','done') NOT NULL DEFAULT 'pending',
  `position`    INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_progresses_progress` (`progress_id`),
  CONSTRAINT `fk_sub_progresses_progress`
    FOREIGN KEY (`progress_id`) REFERENCES `progresses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> **Catatan (v1.1):** tabel `sub_progresses` ditambahkan belakangan (fitur detail sub tugas).
> Pada volume yang sudah berjalan, terapkan manual: `CREATE TABLE IF NOT EXISTS ...` —
> lihat `docker/mysql/init/01-schema.sql` sebagai sumber kebenaran.

---

## 2b. Model `Model_sub_progress` (v1.1)

```php
list_sub_progress($id_progress, $status = '')   // result(): per progres, opsional filter status, ORDER position ASC
single_sub_progress($id)                        // row()
add_sub_progress($data)                         // insert
update_sub_progress($id, $data)                 // update + reset_query()
delete_sub_progress($id = '', $id_progress = '') // hapus per id ATAU seluruh sub tugas per progres + reset_query()
count_sub_progress($id_progress, $status = '')  // agregat (badge kartu, opsional filter 'done')
max_position($id_progress)                      // agregat: posisi terakhir dalam satu progres
```

Aturan hapus: **menghapus progres menghapus seluruh sub tugasnya** (transaksi di controller, lihat docs/03 §7).

## 3. Penjelasan kolom kunci

| Tabel.Kolom | Aturan |
|---|---|
| `project_groups.name` | Unique (case-insensitive via collation). Wajib 3–150 char. |
| `projects.color` | `#RRGGBB` (7 char). Default dari palet UI saat form dibuka. |
| `projects.domain` | Teks bebas maks 255; di-view otomatis di-prepend `https://` untuk link jika tanpa scheme. |
| `progresses.status` | Nilai tunggal `pending`/`wip`/`done`. Tidak ada status arsip — arsip diwakili flag. |
| `progresses.position` | Urutan kartu **dalam kolomnya** (ASC). Saat kartu baru: `MAX(position)+1` per kolom `pending`. |
| `progresses.archived` | `1` = tidak tampil di board, tampil di halaman Arsip. `0` = tampil di board. |
| `progresses.archived_at` | Diisi `NOW()` saat arsip; `NULL` saat pulihkan. |

## 4. Aturan integritas (implementasi wajib di model)

| Operasi | Perilaku |
|---|---|
| Hapus grup | Cek `COUNT(projects WHERE group_id = ?)`; jika > 0 → gagal + pesan. Jika 0 → DELETE. |
| Hapus proyek | Cek `COUNT(progresses WHERE project_id = ?)`; jika > 0 → gagal + pesan. Jika 0 → DELETE. |
| Hapus progres | Selalu boleh (hard delete). |
| FK di DB | `ON DELETE RESTRICT` sebagai jaring pengaman kedua. |

## 5. Kontrak Model (CI3 — pola "Metode Penulisan" di `prompt.md`)

File: `application/models/Model_group.php`, `Model_project.php`, `Model_progress.php`.
Class: `Model_group`, `Model_project`, `Model_progress` (extends `CI_Model`).

```php
// Model_group
list_group()                       // result(): semua grup, ORDER BY name ASC
single_group($id_group)            // row()
add_group($data)                   // insert; TRUE/FALSE
update_group($id_group, $data)     // update + reset_query() di baris terakhir
delete_group($id_group)            // delete + reset_query() di baris terakhir
count_project($id_group)           // count_all_results('projects') — pivot (guard hapus)

// Model_project
list_project($id_group = '')       // result(): JOIN project_groups (nama grup);
                                   // if($id_group != '') → where group_id
single_project($id_project)        // row(): JOIN nama grup
add_project($data)
update_project($id_project, $data) // + reset_query()
delete_project($id_project)        // + reset_query()
count_progress($id_project)        // count_all_results('progresses') — pivot (guard hapus)

// Model_progress
list_progress($status = '', $archived = '')
                                   // result(): JOIN projects + project_groups;
                                   // ORDER BY FIELD(status,'pending','wip','done'), position ASC;
                                   // if($status != '') → where; if($archived != '') → where
single_progress($id_progress)      // row(): JOIN (dipakai validasi & guard)
add_progress($data)                // insert; status & position dihitung di controller
update_progress($id_progress, $data)  // + reset_query(); juga untuk move/archive/restore
delete_progress($id_progress)      // + reset_query()
max_position($status)              // row(): SELECT_MAX position WHERE status
                                   // (agregat, pola seperti count_ — method khusus diperbolehkan)
```

Aturan dari `prompt.md` yang wajib diikuti di semua model:

- Kondisi tambahan = **parameter opsional** dengan `if($param != '')` — dilarang membuat
  method baru semacam `list_progress_by_status()`.
- Method `update_*` dan `delete_*` diakhiri `$this->db->reset_query();`.
- Pemanggilan dengan ≥2 parameter wajib mengirim semua argumen, urutan tepat.
- Semua query via Query Builder (tanpa raw query). `created_at`/`updated_at` dikelola
  default DDL — model tidak perlu set manual.

## 6. Query contoh (validasi perilaku)

```sql
-- Isi board (kartu aktif) + konteks proyek/grup
SELECT p.*, pr.name AS project_name, pr.color AS project_color, g.name AS group_name
FROM progresses p
JOIN projects pr ON pr.id = p.project_id
JOIN project_groups g ON g.id = pr.group_id
WHERE p.archived = 0
ORDER BY FIELD(p.status,'pending','wip','done'), p.position ASC;

-- Reorder kolom 'wip' dengan urutan ID [12, 7, 3] (di dalam transaksi)
UPDATE progresses SET position = 2, status = 'wip' WHERE id = 12; -- dst per index
```

## 7. Seed (opsional, untuk dev)

Buat file `docker/mysql/init/02-seed.sql` **opsional**. Jika dibuat, isi:
1 grup ("Pengembangan Web"), 2 proyek (warna berbeda, domain & direktori dummy),
3 progres (1 per kolom, deadline bervariasi, satu di antaranya overdue).
Seed **hanya** dieksekusi saat volume pertama dibuat — dokumentasikan:
reset data = `docker compose down -v && docker compose up`.
