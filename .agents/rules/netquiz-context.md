# NetQuiz Project Context

## Tech Stack

- **Backend:** PHP >= 8.1, Custom MVC Framework (tanpa Laravel/Symfony/framework eksternal)
- **Database:** MySQL via PDO (prepared statements wajib di semua query)
- **Frontend:** Pure HTML/CSS/JS (tanpa framework JS eksternal)
- **Deployment:** Docker + Nginx (dev lokal) + InfinityFree Shared Hosting (production)
- **Dependency Manager:** Composer (hanya untuk autoload metadata, tidak ada vendor package)

## Arsitektur & Struktur

- **Namespace root:** `App\` di-map ke `src/app/`
- **Autoloading:** PSR-4 manual via `spl_autoload_register` di `src/public/index.php`
- **Entry point tunggal:** `src/public/index.php` (semua request di-rewrite ke sini)
- **Layer MVC:**
  - `src/app/Controllers/` — Layer Controller
  - `src/app/Repositories/` — Layer Data Access (Repository Pattern + Interface)
  - `src/app/Views/` — Layer View (PHP template, output buffering)
  - `src/app/Core/` — Framework Core (infrastruktur: Router, Container, DB, Security, dll)
- **Folder analisis/dokumentasi:** Hasil analisis disimpan ke `analysis/` di root proyek

## Komponen Core Penting

- **Container.php** — DI Container dengan auto-wiring via Reflection
- **Router.php** — URL router dengan dukungan #[Authorize] PHP Attribute
- **Database.php** — PDO wrapper dengan atomic transaction support
- **Security.php** — CSRF, rate limiting, URL encryption, HTTP security headers
- **Role.php** — Enum dengan 3 nilai: `Role::ADMIN`, `Role::USER`, `Role::GUEST`
- **Authorize.php** — PHP Attribute untuk deklaratif RBAC pada controller/method

## Role & Autentikasi

- Admin dideteksi via email hardcoded: `admin@routerosquiz.academy`
- RBAC menggunakan PHP 8.x Attribute `#[Authorize(Role::ADMIN)]` dibaca Router via Reflection
- Session disimpan di `$_SESSION['user']` dengan key: `id`, `name`, `email`

## Kategori Konten (Hardcoded)

- `Routing`, `Firewall & NAT`, `Wireless`, `Network Management`

## Aturan Saat Bekerja di Proyek Ini

- **Scope analisis:** Fokus pada folder `src/` saja — abaikan folder di luar `src/`
- **Non-destruktif:** Jangan mengubah atau menghapus file apapun kecuali diminta secara eksplisit oleh pengguna
- **Simpan analisis:** Hasil analisis dan dokumentasi disimpan ke folder `analysis/` di root proyek
- **Query SQL:** Selalu gunakan prepared statements — tidak boleh ada string interpolasi langsung di query
- **Data access:** Ikuti pola Repository Pattern yang sudah ada untuk semua operasi data access baru
- **Response:** Semua controller action harus mengembalikan objek `Response` (bukan echo langsung)
