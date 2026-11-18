# Panduan Deployment & Backup (Deployment Guide)

## 1. Kebutuhan Server
- PHP 8.2 atau lebih tinggi dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `gd`.
- Web Server: Nginx atau Apache dengan mod_rewrite aktif.
- Database: MySQL 8.0+ atau MariaDB 10.4+.
- Memory minimal: 1 GB RAM (direkomendasikan 2 GB+ untuk rendering PDF laporan).

## 2. Checklist Production
1. Set environment production di `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://erp.domainanda.com
   ```
2. Jalankan optimasi cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. Symlink direktori storage:
   ```bash
   php artisan storage:link
   ```
4. Pastikan file permissions aman:
   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

## 3. Strategi Backup & Restore
- **Backup Harian Database**:
  ```bash
  mysqldump -u root -p bakery_erp --single-transaction --quick > /backups/bakery_erp_$(date +\%Y\%m\%d).sql
  ```
- **Restore Database**:
  ```bash
  mysql -u root -p bakery_erp < /backups/bakery_erp_backup.sql
  ```
