# 🔍 Analisis Mendalam Proyek NetQuiz
**Tanggal Analisis:** 2026-08-23  
**Analis:** Antigravity (Senior Software Engineer Perspective)  
**Scope:** Folder `src/` saja

---

## 1. 🗺️ Gambaran Umum Proyek

**NetQuiz** adalah platform kuis berbasis web untuk materi jaringan MikroTik/RouterOS (mencakup topik Routing, Firewall & NAT, Wireless, dan Network Management). Proyek ini dibangun menggunakan **Custom PHP MVC Framework** tanpa ketergantungan framework pihak ketiga seperti Laravel atau Symfony.

| Atribut | Detail |
|---|---|
| **Bahasa** | PHP >= 8.1 |
| **Arsitektur** | Custom MVC (Model-View-Controller) |
| **Database** | MySQL (via PDO) |
| **Autoloading** | PSR-4 manual (tanpa Composer autoload aktif di runtime) |
| **Frontend** | Pure HTML/CSS/JS (tanpa framework JS) |
| **Deployment** | Docker (dev lokal) + InfinityFree Shared Hosting (production) |
| **PHP Version** | >= 8.1 (menggunakan Enums, Attributes, Named Arguments, dll) |

---

## 2. 🏗️ Struktur Direktori `src/`

```
src/
├── app/                        # Inti aplikasi (MVC)
│   ├── .htaccess               # Blokir akses langsung
│   ├── Controllers/            # Layer Controller
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── LeaderboardController.php
│   │   ├── LearnController.php
│   │   ├── QuizController.php
│   │   └── SettingsController.php
│   ├── Core/                   # Framework Core (infrastruktur)
│   │   ├── Authorize.php       # PHP Attribute untuk RBAC
│   │   ├── Container.php       # DI Container (PSR-11-like)
│   │   ├── Controller.php      # Abstract base controller
│   │   ├── Database.php        # PDO wrapper + transaksi
│   │   ├── ErrorHandler.php    # Global error/exception handler
│   │   ├── ImageHelper.php     # Upload & konversi WebP
│   │   ├── Model.php           # (Placeholder kosong)
│   │   ├── Request.php         # HTTP request abstraction
│   │   ├── Response.php        # HTTP response abstraction
│   │   ├── Role.php            # Role enum (ADMIN/USER/GUEST)
│   │   ├── Router.php          # URL Router + dispatch
│   │   └── Security.php        # CSRF, rate limiting, headers
│   ├── Repositories/           # Layer Data Access (Repository Pattern)
│   │   ├── AttemptRepository.php / Interface
│   │   ├── BadgeRepository.php / Interface
│   │   ├── MaterialRepository.php / Interface
│   │   ├── QuestionRepository.php / Interface
│   │   ├── QuizRepository.php / Interface
│   │   └── UserRepository.php / Interface
│   └── Views/                  # Layer View (PHP template)
│       ├── admin/              # Tampilan admin panel
│       ├── auth/               # Halaman login
│       ├── errors/             # 403, 404, 500
│       ├── home/               # Dashboard / beranda
│       ├── leaderboard/        # Papan peringkat
│       ├── learn/              # Materi belajar
│       ├── quiz/               # Halaman kuis (index, play, result, review)
│       ├── settings/           # Pengaturan akun
│       ├── templates/          # header.php & footer.php (layout)
│       └── index.php           # View dispatcher/layout wrapper
├── config/
│   ├── .htaccess               # Blokir akses langsung
│   └── config.php              # Konfigurasi app + database
├── logs/                       # Error log PHP runtime
└── public/                     # Document root web server
    ├── .htaccess               # URL rewriting & keamanan
    ├── index.php               # ENTRY POINT APLIKASI
    ├── css/                    # Stylesheet per halaman
    ├── js/                     # JavaScript per fitur
    └── uploads/                # File gambar yang diupload
```

---

## 3. 🔄 Alur Kerja Request (Request Lifecycle)

```
Browser Request
      │
      ▼
[Nginx / Apache]
      │  Semua URL di-rewrite ke public/index.php (.htaccess)
      ▼
[public/index.php]   ← Entry Point Tunggal
  ├── 1. Session Hardening & Start
  ├── 2. PSR-4 Autoloader (manual, tanpa Composer di runtime)
  ├── 3. Load config.php (auto-detect dev/prod berdasarkan HTTP_HOST)
  ├── 4. Define constants: BASE_URL, APP_NAME, APP_ROOT, PUBLIC_ROOT
  ├── 5. Register ErrorHandler (global error/exception handler)
  ├── 6. Set Security HTTP Headers (CSP, X-Frame-Options, dll)
  ├── 7. Setup DI Container + bind Repositories ke Interface
  └── 8. Definisi Routes + dispatch()
          │
          ▼
[Router::dispatch()]
  ├── Match HTTP method + URL pattern (regex dengan named groups)
  ├── Baca PHP Attribute #[Authorize] via Reflection API
  ├── Cek peran user (ADMIN/USER/GUEST)
  │    ├── Unauthorized → redirect login atau tampil 403
  │    └── Authorized → lanjut
  └── Resolve Controller via DI Container (auto-wiring)
          │
          ▼
[Controller Action]
  ├── Terima parameter route + Request object (auto-injected via DI)
  ├── Panggil Repository untuk operasi data
  └── Return Response (view, json, redirect)
          │
          ▼
[Response::send()]
  └── Kirim HTTP headers + body ke browser
```

---

## 4. ⚙️ Komponen Core Framework

### 4.1. Container.php — Dependency Injection Container
Implementasi DI Container ringan yang menyerupai PSR-11. Fitur:
- **Singleton binding**: `->singleton(Interface::class, Concrete::class)`
- **Transient binding**: `->bind()`
- **Auto-wiring**: Resolusi dependensi otomatis via PHP Reflection
- **Method injection**: `->call($instance, 'method', $routeParams)` untuk controller action

Semua binding didaftarkan di `index.php` sebelum routing dimulai.

### 4.2. Router.php — URL Router
Router berbasis array dengan dukungan:
- HTTP method: GET, POST, PUT, DELETE
- Dynamic route parameters: `/quiz/play/{id}` → regex `(?P<id>[^/]+)`
- Integrasi DI Container untuk resolve controller
- Pembacaan PHP 8.x `#[Authorize]` Attribute via Reflection API
- 404 dan 403 handler terpusat

### 4.3. Database.php — PDO Wrapper
- Koneksi PDO dengan strict mode (`ERRMODE_EXCEPTION`, `EMULATE_PREPARES=false`)
- Support transaksi atomik via `->transaction(callable $callback)`
- `PDO::ATTR_PERSISTENT = true` untuk koneksi persisten
- Memiliki pola Singleton tapi juga bisa di-inject via DI Container

### 4.4. Security.php — Security Center
Mengakomodasi semua kebutuhan keamanan:
- **CSRF Token**: generate (`bin2hex(random_bytes(32))`), validasi via `hash_equals()`
- **Rate Limiting**: DB-based login attempt tracking (IP + email)
- **URL Encryption**: AES-256-CBC untuk menyembunyikan integer ID di URL
- **HTTP Headers**: CSP, X-Frame-Options, Referrer-Policy, dll
- **Brute Force Protection**: 5 attempts per 5 menit (configurable)

> CATATAN KRITIS: Encryption key di `Security.php` di-hardcode sebagai string literal.
> Ini adalah risiko keamanan jika source code bocor.

### 4.5. Authorize.php + Role.php — RBAC via PHP Attributes
```php
// Contoh penggunaan pada controller
#[Authorize(Role::ADMIN)]
class AdminController extends Controller { ... }

#[Authorize(Role::USER, Role::ADMIN)]
class QuizController extends Controller { ... }
```
Router membaca attribute ini via Reflection dan melakukan pengecekan role sebelum controller diinstansiasi.

### 4.6. Request.php — HTTP Request Abstraction
Encapsulasi `$_GET`, `$_POST`, `$_FILES`, `$_SERVER`, `$_COOKIE`, dan raw body. Mendukung:
- JSON payload auto-parsing
- Bearer token extraction
- Client IP detection (dengan dukungan Cloudflare, X-Forwarded-For)
- AJAX detection

### 4.7. Response.php — HTTP Response Abstraction
Factory methods:
- `Response::view($view, $data)` — render PHP template via output buffering
- `Response::json($data, $statusCode)` — JSON response
- `Response::redirect($url)` — HTTP redirect

### 4.8. ErrorHandler.php — Global Error Handler
- Konversi PHP error ke `ErrorException`
- Logging ke file dengan sanitasi (menyensor password/token di log)
- Respons berbeda untuk JSON request vs HTML request
- Tangani fatal error via `register_shutdown_function`

### 4.9. ImageHelper.php — Secure Image Upload
- Validasi MIME type via magic bytes (bukan ekstensi file)
- Konversi semua upload ke format WebP (efisiensi bandwidth)
- Nama file randomized: `img_{32-char-hex}.webp`
- Support upload dari file fisik maupun base64 data URI

---

## 5. 📦 Repository Layer (Data Access Pattern)

Seluruh akses database dienkapsulasi dalam Repository. Setiap Repository memiliki Interface-nya sendiri.

### Mapping Repository ke Fitur:

| Repository | Tabel DB | Fungsi Utama |
|---|---|---|
| `UserRepository` | `users` | CRUD user, cek duplikasi email/username |
| `QuizRepository` | `quizzes`, `questions` | CRUD kuis + soal (atomic transaction) |
| `QuestionRepository` | `questions` | Operasi soal individual |
| `AttemptRepository` | `quiz_attempts` | Rekam pengerjaan, pause/resume, leaderboard |
| `BadgeRepository` | `badges` | CRUD badge, kalkulasi achievement dinamis |
| `MaterialRepository` | `materials` | CRUD artikel materi belajar |

### Pola Penting di Repository:
- **Atomic Transaction**: `createWithQuestions()` dan `recordFinishedAttempt()` menggunakan `Database::transaction()` untuk menjamin konsistensi data
- **Prepared Statements**: Seluruh query menggunakan prepared statement (anti SQL Injection)
- **Soft Enum untuk kategori**: Hardcoded array `['Routing', 'Firewall & NAT', 'Wireless', 'Network Management']`

---

## 6. 🎮 Fitur Bisnis Utama

### 6.1. Sistem Kuis (Quiz Flow)
```
GET  /quiz               → Daftar kuis (filter by difficulty, status: selesai/paused/belum)
GET  /quiz/play/{id}     → Mulai / lanjutkan kuis (pause state restoration dari DB)
POST /quiz/pause/{id}    → Simpan state (answers + time_left) ke DB sebagai JSON
POST /quiz/submit/{id}   → Kalkulasi skor + simpan attempt (atomic transaction)
GET  /quiz/result/{id}   → Halaman hasil pengerjaan
GET  /quiz/review/{id}   → Review jawaban + penjelasan (hanya setelah selesai)
```

Logika skor: `(correct / total) * 100`, dibulatkan ke integer.  
Pause/Resume: State tersimpan di kolom `user_answers` (JSON) dengan format `{"answers": {...}, "time_left": 1800}`.

### 6.2. Sistem Autentikasi
- **Login**: AJAX POST ke `/api/login`, validasi CSRF, rate limiting, `password_verify()`
- **Session**: Hardened session (strict mode, httponly, samesite=Strict, 30-menit inactivity timeout)
- **Logout**: `session_destroy()` + redirect ke /login
- **Admin detection**: Email hardcoded `admin@routerosquiz.academy` → Role::ADMIN

> CATATAN: Role admin ditentukan murni berdasarkan email string. Tidak ada kolom `role` di tabel `users`.
> Ini membatasi skalabilitas sistem role di masa depan.

### 6.3. Sistem Badge/Achievement
Badge didefinisikan di database (`badges` tabel) dengan field `metric` dan `target_value`. 
`BadgeRepository::calculateUserBadges()` menghitung progress secara dinamis berdasarkan metric:
- `completed_quizzes`: Jumlah kuis selesai
- `total_score`: Total skor kumulatif
- `perfect_scores`: Jumlah nilai 100
- `category_routing/firewall/wireless/network`: Jumlah kuis per kategori

### 6.4. Leaderboard
Query aggregasi SQL: `SUM(score)` dan `COUNT(attempts)` per user, diurutkan descending. 
Admin dikecualikan via `WHERE LOWER(TRIM(u.email)) != 'admin@routerosquiz.academy'`.

### 6.5. Materi Belajar (Learn)
Artikel konten teks yang dikelola admin. Diakses pengguna via `/learn` dan `/learn/{id}`.

---

## 7. 🔒 Analisis Keamanan

### Praktik Baik yang Sudah Diterapkan:
| Keamanan | Implementasi |
|---|---|
| SQL Injection | Prepared statements di seluruh query |
| XSS | `htmlspecialchars()` via `Security::escape()` |
| CSRF | Token via `Security::validateCsrfToken()` di semua POST |
| Brute Force | DB-based rate limiting (5 attempts / 5 menit) |
| Session Fixation | `session_regenerate_id(true)` setelah login sukses |
| Clickjacking | `X-Frame-Options: DENY` |
| MIME Sniffing | `X-Content-Type-Options: nosniff` |
| CSP | Content-Security-Policy header aktif |
| File Upload | Magic byte validation + rename + WebP convert |
| Password Hashing | `password_hash($pass, PASSWORD_ARGON2ID)` |
| Session Timeout | 30 menit inactivity timeout |
| BFCache Control | Headers diatur per halaman (sensitif: no-store) |

### Potensi Masalah / Technical Debt:
1. **Hardcoded encryption key** di `Security.php` — sebaiknya dipindah ke environment variable
2. **Hardcoded admin email** — deteksi admin via email string, bukan kolom role di DB
3. **Hardcoded DB password** di `config.php` (prod) — sebaiknya via environment variable
4. **Model.php kosong** — placeholder yang belum diimplementasi
5. **Route duplikasi** di `index.php` (contoh: `/admin/quizzes` dan `/admin/quiz/create` keduanya mengarah ke `createQuiz`) — tidak konsisten URL design
6. **`Security::checkRateLimit()`** menggunakan `Database::getInstance()` (singleton statis) bukan DI injection — mempersulit testing
7. **Tidak ada middleware pipeline** — logika autentikasi tersebar antara Attribute + `checkAdmin()` manual di setiap method AdminController

---

## 8. 🧩 Pola Desain yang Digunakan

| Design Pattern | Digunakan di |
|---|---|
| **MVC** | Arsitektur keseluruhan |
| **Repository Pattern** | Layer data access (semua Repository) |
| **Dependency Injection** | `Container.php` + constructor injection |
| **Singleton** | `Container`, `Database` |
| **Factory Method** | `Response::view()`, `Response::json()`, `Response::redirect()` |
| **Template Method** | `abstract Controller` dengan helper `view()`, `redirect()`, `jsonResponse()` |
| **Strategy** (via Attribute) | `#[Authorize]` sebagai declarative authorization |
| **Front Controller** | `public/index.php` sebagai single entry point |

---

## 9. ⚡ Analisis Kualitas Kode

### Kekuatan:
- `declare(strict_types=1)` di semua file → type safety PHP
- PHP 8.1+ features: Enums, Named Arguments, Match expression, `str_starts_with/contains`
- Docblock lengkap di semua metode Core
- Interface-based Repository → inversion of dependency
- Atomic transaction untuk operasi multi-tabel kritis
- Output buffering di `Response::view()` → rendering bersih tanpa output noise

### Area untuk Perbaikan:
- Tidak ada unit test (folder `tests/` ada di root tapi di luar scope analisis ini)
- View memanggil helper global (`assetUrl()`, konstanta) yang membuat view kurang portable
- Session langsung di controller (`$_SESSION['user']`) daripada abstraksi service layer
- `QuizRepository::getCategorizedQuizzesWithUserStatus()` terlalu besar dan bertanggung jawab banyak

---

## 10. 📊 Ringkasan Statistik Kode

| Kategori | Jumlah File | Estimasi Baris |
|---|---|---|
| Core Framework | 12 file | ~1,400 baris |
| Controllers | 7 file | ~900 baris |
| Repositories (impl) | 6 file | ~800 baris |
| Repository Interfaces | 6 file | ~200 baris |
| Views | ~15+ file | ~3,000+ baris |
| Config | 1 file | 37 baris |
| Entry Point | 1 file | 174 baris |
| **Total** | **~48+ file** | **~6,500+ baris** |

---

## 11. 🗄️ Skema Database (Diinferensikan dari Query)

| Tabel | Kolom Utama |
|---|---|
| `users` | `id`, `username`, `email`, `password`, `status`, `created_at` |
| `quizzes` | `id`, `title`, `description`, `category`, `duration`, `difficulty`, `image_path` |
| `questions` | `id`, `quiz_id`, `question`, `option_a/b/c/d`, `correct`, `explanation`, `image_path` |
| `quiz_attempts` | `id`, `user_id`, `quiz_id`, `category`, `score`, `status`, `user_answers`, `created_at` |
| `badges` | `id`, `title`, `description`, `icon`, `metric`, `target_value` |
| `materials` | `id`, `title`, `content`, `category`, `difficulty` |
| `login_attempts` | `id`, `ip_address`, `email`, `attempted_at` |

---

## 12. 💡 Rekomendasi Senior Engineer

### Prioritas Tinggi:
1. **Pindahkan secret/config sensitif ke `.env`** — gunakan `getenv()` atau library `vlucas/phpdotenv`
2. **Pisahkan role admin ke database** — tambah kolom `role` di tabel `users` (scalable RBAC)
3. **Tambah unit test** — terutama untuk Repository dan Core classes
4. **Middleware pipeline** — ganti scattered `checkAdmin()` dengan true middleware layer

### Prioritas Menengah:
5. **Perbaiki duplikasi route** — standardisasi URL pattern (pilih satu: plural atau singular)
6. **Extract session access ke service** — `SessionService` daripada `$_SESSION` langsung di controller
7. **Input validation layer** — buat DTO/Request validation yang bisa di-reuse
8. **Implementasikan atau hapus `Model.php`** — jangan biarkan file kosong

### Prioritas Rendah:
9. **Cache layer** — untuk leaderboard query yang berat (aggregasi SUM + COUNT)
10. **Audit log** — rekam perubahan penting (delete user, delete quiz) untuk traceability

---

*Analisis ini dibuat berdasarkan pembacaan seluruh source code di folder `src/` tanpa melakukan perubahan apapun pada kode.*
