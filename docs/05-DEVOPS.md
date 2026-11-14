# 05 — DevOps Spec (Docker · PHP-FPM · Nginx · MySQL)

Semua file di bawah dibuat **apa adanya** (copy-paste) ke struktur di
`docs/00-AGENT-BRIEF.md` §4. Target: `docker compose up --build` → app jalan di
`http://localhost:8080` (hanya bind localhost, karena tanpa auth).

## 1. `docker-compose.yml` (root repo)

```yaml
services:
  db:
    image: mysql:8.0
    container_name: tracker_db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root_secret
      MYSQL_DATABASE: tracker
      MYSQL_USER: tracker
      MYSQL_PASSWORD: tracker_secret
      TZ: Asia/Jakarta
    command: --default-authentication-plugin=mysql_native_password
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-uroot", "-proot_secret"]
      interval: 5s
      timeout: 3s
      retries: 20
    # ports:
    #   - "127.0.0.1:3306:3306"   # buka hanya jika butuh akses DB dari host

  app:
    build: ./docker/php
    container_name: tracker_app
    restart: unless-stopped
    depends_on:
      db:
        condition: service_healthy
    environment:
      CI_ENV: development
      DB_HOST: db
      DB_NAME: tracker
      DB_USER: tracker
      DB_PASS: tracker_secret
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-app.ini:ro

  web:
    image: nginx:1.27-alpine
    container_name: tracker_web
    restart: unless-stopped
    depends_on:
      - app
    ports:
      - "127.0.0.1:8080:80"
    volumes:
      - ./:/var/www/html:ro
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro

volumes:
  db_data:
```

## 2. `docker/php/Dockerfile`

```dockerfile
FROM php:7.4-fpm-alpine
# Jika tag alpine gagal di-pull (tag lama/arsip), fallback: FROM php:7.4-fpm (Debian)

RUN docker-php-ext-install pdo_mysql mysqli opcache

# Pastikan env var dari compose sampai ke worker PHP-FPM (getenv)
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Cache & log CI harus writable oleh www-data
RUN chown -R www-data:www-data /var/www/html || true
```

> **PHP 7.4** — semua kode PHP wajib sintaks ≤7.4: tanpa `match`, enum, named arguments,
> union types, constructor promotion, nullsafe `?->`, `str_contains()`, dll.
> `--default-authentication-plugin=mysql_native_password` di compose memastikan autentikasi
> MySQL 8 kompatibel dengan klien PHP 7.4.

## 3. `docker/php/php.ini`

```ini
date.timezone = Asia/Jakarta
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 12M
display_errors = 1
log_errors = 1
; opcache untuk performa (validasi timestamp agar edit kode langsung efektif)
opcache.enable = 1
opcache.validate_timestamps = 1
opcache.revalidate_freq = 0
```

> `date.timezone` penting: logika **overdue** membandingkan `deadline` (DATE) dengan
> "hari ini" yang dihitung di PHP (`date('Y-m-d')`). Bandingkan sebagai string `Y-m-d` — aman & tanpa isu TZ.

## 4. `docker/nginx/default.conf`

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;          # entrypoint = public/index.php
    index index.php;

    # Sembunyikan file tersembunyi & folder sistem
    location ~ /\.(?!well-known) { deny all; }
    location ~* ^/(application|system|docker)/ { deny all; }

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~* \.(css|js|svg|png|jpg|jpeg|gif|ico|woff2?)$ {
        expires 7d;
        access_log off;
        try_files $uri =404;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
    }
}
```

## 5. `public/index.php` (perubahan dari index.php bawaan CI3)

Copy `index.php` bawaan ke `public/index.php`, lalu ubah 2 baris:

```php
$system_path = '../system';
$application_folder = '../application';
```

Pastikan juga baris ENVIRONMENT membaca env (fallback dev):

```php
define('ENVIRONMENT', getenv('CI_ENV') ?: 'development');
```

Hapus `index.php` lama di root repo.

## 6. `application/config/database.php` (potongan)

```php
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => getenv('DB_HOST') ?: '127.0.0.1',
    'username'  => getenv('DB_USER') ?: 'tracker',
    'password'  => getenv('DB_PASS') ?: 'tracker_secret',
    'database'  => getenv('DB_NAME') ?: 'tracker',
    'dbdriver'  => 'mysqli',
    'dbprefix'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'db_debug'  => (ENVIRONMENT !== 'production'),
    'cache_on'  => FALSE,
    'stricton'  => TRUE,
);
```

## 7. Perintah harian

```bash
docker compose up --build -d      # build & jalankan
docker compose logs -f app        # log PHP/CI
docker compose logs -f web        # log nginx
docker compose exec db mysql -uroot -proot_secret tracker   # klien SQL
docker compose down               # stop (data bertahan di volume db_data)
docker compose down -v            # HAPUS data (schema+seed init jalan lagi saat up)
```

## 8. Urutan inisialisasi pertama

1. `docker compose up --build` → MySQL membuat DB `tracker` & mengeksekusi
   `docker/mysql/init/01-schema.sql` (opsional `02-seed.sql`) **hanya saat volume baru dibuat**.
2. Buka `http://localhost:8080` → Board dengan guard "Buat Grup".
3. Edit kode `application/` langsung berefek (volume bind mount, opcache revalidate 0).

## 9. Troubleshooting cepat

| Gejala | Penyebab umum | Perbaikan |
|---|---|---|
| 502 Bad Gateway | php-fpm belum siap / host salah | pastikan `fastcgi_pass app:9000` & container `app` jalan |
| "Unable to connect to database" | DB belum healthy / env salah | cek `docker compose logs db`; healthcheck menahan `app` |
| Perubahan DDL tidak muncul | init SQL hanya jalan saat volume baru | `docker compose down -v && docker compose up` (data hilang!) |
| Toast tidak muncul setelah aksi | error JS / respon bukan JSON | cek console browser & `docker compose logs -f app` |
| CSS/JS 404 | root nginx bukan `public/` | cek `root /var/www/html/public;` |
| Permission denied saat simpan | cache/log tidak writable | `docker compose exec app chown -R www-data:www-data application/cache application/logs` |
| Tag `php:7.4-fpm-alpine` gagal di-pull | tag arsip | ganti `FROM php:7.4-fpm` (Debian) di Dockerfile |

## 10. Catatan keamanan

- Port hanya di-bind `127.0.0.1` (tanpa auth, jangan expose ke jaringan).
- `db_debug` hanya TRUE di development; production = FALSE (meski target utama lokal).
- Nginx memblokir `/application`, `/system`, `/docker`, dotfiles.
