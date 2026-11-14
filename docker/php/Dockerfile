FROM php:7.4-fpm-alpine
# Jika tag alpine gagal di-pull (tag lama/arsip), fallback: FROM php:7.4-fpm (Debian)

RUN docker-php-ext-install pdo_mysql mysqli opcache

# Pastikan env var dari compose sampai ke worker PHP-FPM (getenv)
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Cache & log CI harus writable oleh www-data
RUN chown -R www-data:www-data /var/www/html || true
