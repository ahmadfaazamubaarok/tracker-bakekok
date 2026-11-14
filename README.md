<p align="center">
	<img src="public/assets/img/logo.svg" width="96" alt="Logo Project Tracker">
</p>

<h1 align="center">Project Tracker</h1>

<p align="center">
	Kanban board ringan untuk melacak progres proyek pribadi — tanpa login, jalan lokal via Docker.<br>
	<code>Pending → WIP → Selesai</code>
</p>

---

## Fitur

- **Board kanban 3 kolom** dengan drag & drop kartu (posisi tersimpan permanen)
- **Hierarki data**: Grup → Proyek → Progres, dengan guard berjenjang (tidak bisa menambah progres sebelum ada proyek & grup)
- **CRUD lengkap** via modal + AJAX: grup, proyek (domain, direktori, warna identitas), dan progres (deadline, prioritas)
- **Arsip**: progres di kolom Selesai bisa diarsipkan, dipulihkan, atau dihapus permanen — riwayat tetap ada
- **Badge terlambat**: deadline yang terlewat ditandai merah + label "Terlambat"
- **PWA**: installable di desktop/home screen, halaman offline, aset di-cache
- Tanpa autentikasi & tanpa CSRF — memang **dirancang untuk dijalankan lokal** (port hanya di-bind ke `127.0.0.1`)

## Tech Stack

CodeIgniter 3.1.13 · PHP-FPM 7.4 · MySQL 8.0 · Nginx · Docker Compose · jQuery 3 + Bootstrap 4 + DataTables + toastr + bootbox + SortableJS · PWA (Manifest + Service Worker)

## Menjalankan

```bash
git clone git@github.com:ahmadfaazamubaarok/tracker.git
cd tracker

# (opsional) sesuaikan kredensial/port
cp .env.example .env

docker compose up --build -d
```

Buka **http://localhost:3000**.

Alur pertama kali: buat minimal **1 Grup** → tambah **1 Proyek** → mulai tambah **Progres** ke board.

Perintah berguna:

```bash
docker compose logs -f app     # log PHP/CI
docker compose down            # stop; data tetap ada di volume
docker compose down -v         # reset total data
```

> Port default adalah `3000` (bukan `8080`) karena di mesin pengembang port `8080` sudah terpakai. Ubah via `APP_PORT` di `.env` bila perlu.

## Struktur Proyek

```
├── application/          # kode CodeIgniter 3
│   ├── controllers/      # Board, Group, Project, Progress, Archive
│   ├── models/           # Model_group, Model_project, Model_progress
│   ├── views/            # templates/, board/, group/, project/, progress/, archive/
│   ├── helpers/          # app_helper (label prioritas, tanggal ID, overdue)
│   └── config/           # routes, database (env-based), autoload
├── public/               # entrypoint + asset
│   ├── index.php
│   ├── assets/           # app.css (design system), img (logo & ikon PWA)
│   ├── cdn/lib/          # vendor lokal (jQuery, Bootstrap, DataTables, dst.)
│   ├── manifest.json     # PWA manifest
│   ├── sw.js             # service worker (cache aset, fallback offline)
│   └── offline.html
├── docker/
│   ├── php/              # Dockerfile + php.ini
│   ├── nginx/            # server block (root ke public/, blokir application/system)
│   └── mysql/init/       # DDL schema (auto-eksekusi saat volume pertama dibuat)
├── docker-compose.yml
└── DESIGN.md             # design system (monokrom, pill, hairline, tanpa shadow)
```

## Catatan Keamanan

Aplikasi ini **sengaja tanpa login/CSRF** untuk penggunaan personal di mesin sendiri. Nginx hanya mengekspos ke `127.0.0.1` dan memblokir folder `application/`, `system/`, `docker/`. Jangan expose ke jaringan publik tanpa menambahkan auth terlebih dahulu. Kredensial MySQL di compose hanyalah default dev — timpa lewat file `.env` (lihat `.env.example`).

## Lisensi

[MIT](LICENSE) © Ahmad Faaza Mubaarok
# tracker-bakekok
