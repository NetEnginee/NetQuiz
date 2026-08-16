# 🛡️ NetQuiz - Official Masterpiece Design System & Anti-Slop Specification

Dokumen ini berisi standar **Design System Resmi NetQuiz** (berbasis *Vercel Geist UI Light Theme*) serta **Daftar Hitam Mutlak (Anti-Slop Blacklist)**. Seluruh pengembangan halaman, komponen, modul kuis, materi belajar, dan panel admin wajib mematuhi aturan di dalam dokumen ini secara konsisten.

---

## 🏛️ 1. STANDAR DESAIN RESMI (NETQUIZ GEIST MASTERPIECE)

### A. Palet Warna (Color System)
- **Canvas Background**: `#FAFAFA` (Geist Soft Canvas).
- **Surface / Container Card**: `#FFFFFF` (Pure White Card Container).
- **Input / Field Background**: `#FFFFFF` (Pure White).
- **Hairline Border (1px)**: `#EAEAEA` (Perbatasan presisi ultra-halus).
- **Border Hover**: `#AAAAAA`.
- **Border Focus**: `#000000` (Pure Black Precision Focus).
- **Primary Text**: `#000000` (High Precision Pure Black).
- **Secondary Text**: `#666666` (Geist Muted Secondary).
- **Muted / Details Text**: `#999999`.
- **Primary Action Button**: `#000000` (Vercel Signature Black Button) dengan teks putih `#FFFFFF` dan efek hover inverse (`#FFFFFF` background & `#000000` border).
- **Alert Errors**: `#FFF0F0` (Background Red Light), `#FFC0C0` (Border), `#EE0000` (Text/Icon Red).
- **Alert Success**: `#F0FDF4` (Background Green Light), `#BBF7D0` (Border), `#008800` (Text/Icon Green).

### B. Tipografi & Identitas Merek
- **Headings & Title**: `Plus Jakarta Sans`, sans-serif (ExtraBold `800` / Bold `700`, `letter-spacing: -0.03em` hingga `-0.04em`).
- **Body & Controls**: `Inter`, sans-serif (`letter-spacing: -0.01em`).
- **Monospace Tags & CLI**: `JetBrains Mono`, monospace (Wajib untuk IP Address, Script CLI RouterOS, Command, dan Field Tag `.field-tag`).
- **Identitas Brand Logo**:
  - Logo Box: Serba hitam `#000000` (38px x 38px, radius `9px`) dengan ikon Lucide `<i data-lucide="terminal"></i>` warna putih `#FFFFFF`.
  - Indikator Status: `live-dot` hijau `#10B981` (titik status server online).
  - Tipografi Merek: `Net` (`#000000` ExtraBold) + `Quiz` (`#666666` SemiBold) + `_` (`.brand-cursor` monospaced Terminal Underscore Pulse).

### C. Kanvas Latar Belakang (Masterpiece Background Ornaments)
1. **Dual-Layer CAD Blueprint Grid**:
   - Micro Grid: 28px (`rgba(0,0,0,0.04)`).
   - Major Blueprint Grid: 112px (`rgba(0,0,0,0.065)`).
   - Vignette Masking: `mask-image: radial-gradient(ellipse 70% 70% at 50% 45%, #000 35%, transparent 95%)`.
2. **Top Ambient Soft Reflection**: `radial-gradient(ellipse 60% 40% at 50% 0%, rgba(0, 0, 0, 0.035) 0%, transparent 80%)`.
3. **Structural Viewport Framing Lines**: Garis batas vertikal 1px di 12% kiri dan kanan viewport layar (`.left-line` dan `.right-line`).

### D. Kartu & Komponen UI
- **Precision Corner Crosshairs**: Tanda silang monospaced `+` di keempat sudut luar kartu (`.corner-tl`, `.corner-tr`, `.corner-bl`, `.corner-br`).
- **Hairline Inset Glow**: `box-shadow: 0 1px 0 rgba(255, 255, 255, 0.9) inset, 0 12px 32px -4px rgba(0,0,0,0.05)`.
- **Button Micro-Interactions**: Ikon panah `.button-arrow` yang bergeser ke kanan saat hover (`transform: translateX(3px)`) dan efek tekan (`transform: scale(0.985)`).

---

## 🚫 2. DAFTAR BLACKLIST ANTI-SLOP (DILARANG KERAS)

| No | Elemen / Praktik | Status | Alasan Blacklist |
|----|------------------|--------|------------------|
| 1 | Split-Screen Marketing Template Cliché | ❌ **BLACKLIST** | Pola 2-kolom klise pasaran buatan AI. |
| 2 | Widget Status & Metric Palsu (*Artificial Busyness*) | ❌ **BLACKLIST** | Menambah kebingungan visual tanpa fungsi asli. |
| 3 | Input Icon Left Padding Cliché | ❌ **BLACKLIST** | Menaruh ikon surat/kunci di dalam kotak input sebelah kiri. |
| 4 | Gradien Melayang (Radial Glow Blobs Ungu/Sian) | ❌ **BLACKLIST** | Tren visual AI pasaran yang merusak keterbacaan. |
| 5 | Overuse Glassmorphism (`backdrop-blur` buram) | ❌ **BLACKLIST** | Menyulitkan keterbacaan (WCAG Fail). |
| 6 | Copywriting Klise ("*Supercharge your workflow*") | ❌ **BLACKLIST** | Bahasa tidak profesional & terlalu umum. |
| 7 | Div Soup (Bungkus `<div>` Tanpa Semantik) | ❌ **BLACKLIST** | Membengkakkan DOM & merusak SEO/Aksesibilitas. |
| 8 | Clickable Divs & Aksesibilitas Buruk | ❌ **BLACKLIST** | Mengabaikan navigasi keyboard & screen reader. |
| 9 | Happy-Path Only (Abaikan State Error/Loading) | ❌ **BLACKLIST** | Pengalaman pengguna rusak saat error. |

---

Dokumen ini adalah standar acuan permanen untuk seluruh halaman aplikasi NetQuiz.
