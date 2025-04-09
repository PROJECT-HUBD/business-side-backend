# HUBD 快時尚購物平台 — 企業端後端（Laravel API）

### 專案簡介
HUBD 快時尚購物平台 — 企業端後台 Laravel API 專案
提供 RESTful API 接口，供企業端前台呼叫。

### 技術架構
- PHP 8+ / Laravel 11
- RESTful API 設計
- MariaDB

### 初始化指令
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan install:api
php artisan storage:link
```

### 🌐 處理跨域 (CORS)
```bash
php artisan config:publish cors
```
確認在 `config/cors.php` 中做開發階段的設定：
```php
'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
```

### 圖片資源提供
- 圖片儲存於 `storage/app/public`
- 經由 `php artisan storage:link` 建立公開路徑
- 可透過 `/storage/xxx.jpg` 被前端使用
