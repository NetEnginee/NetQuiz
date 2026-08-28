// --- NETQUIZ ADMIN DASHBOARD CONTROLLER (EXTERNAL JS) ---

// Global function to trigger template.json download with rich demo and complete toolbar guide
function downloadJsonTemplate() {
  const templateData = {
    title: "Panduan Konfigurasi Dasar & Lanjutan MikroTik RouterOS",
    title: "",
    category: "Routing",
    difficulty: "Sedang",
    content: `<h2>1. Pengantar & Konsep Routing RouterOS</h2>
<p>Routing adalah mekanisme krusial pada MikroTik RouterOS untuk meneruskan paket data antar segmen network yang berbeda. Panduan ini mencakup konfigurasi IP Address, Default Gateway, Firewall NAT Masquerade, serta proteksi keamanan router.</p>

<div class="material-callout-quote callout-note">
  <div class="callout-header">💡 CATATAN & INFORMASI</div>
  <p>Sebelum memulai konfigurasi, pastikan router sudah terhubung ke jaringan internet pada port WAN (misal: <code>ether1</code>) dan kabel LAN terpasang pada port lokal (misal: <code>ether2</code>).</p>
</div>

<h3>Parameter Spesifikasi Jaringan Lab</h3>
<div class="network-spec-card">
  <div class="network-spec-title">🌐 SPESIFIKASI JARINGAN // TOPOLOGI</div>
  <div class="network-grid">
    <div class="network-item">
      <div class="network-label">IP Address LAN</div>
      <div class="network-value">192.168.88.1/24</div>
    </div>
    <div class="network-item">
      <div class="network-label">Gateway ISP / WAN</div>
      <div class="network-value">192.168.1.1</div>
    </div>
    <div class="network-item">
      <div class="network-label">DNS Resolvers</div>
      <div class="network-value">1.1.1.1, 8.8.8.8</div>
    </div>
    <div class="network-item">
      <div class="network-label">Interface Mapping</div>
      <div class="network-value">ether1 (WAN), ether2 (LAN)</div>
    </div>
  </div>
</div>

<h3>Langkah Konfigurasi Bertahap</h3>
<div class="step-guide-container">
  <div class="step-item">
    <div class="step-number">1</div>
    <div class="step-content">
      <div class="step-title">Pengaturan IP Address Interface</div>
      <p>Tambahkan alamat IP pada interface LAN agar perangkat klien dapat terhubung dan menggunakan gateway.</p>
    </div>
  </div>
  <div class="step-item">
    <div class="step-number">2</div>
    <div class="step-content">
      <div class="step-title">Konfigurasi Default Route (Gateway)</div>
      <p>Buat rute default dengan <code>dst-address=0.0.0.0/0</code> mengarah ke IP Gateway modem ISP.</p>
    </div>
  </div>
  <div class="step-item">
    <div class="step-number">3</div>
    <div class="step-content">
      <div class="step-title">Penerapan NAT Masquerade</div>
      <p>Aktifkan rule Source NAT agar semua IP privat lokal ditranslasikan ke IP publik saat menuju internet.</p>
    </div>
  </div>
</div>

<h3>Script Perintah CLI RouterOS</h3>
<div class="terminal-block-wrap">
  <div class="terminal-block-header">
    <div class="terminal-dots-group">
      <span class="terminal-dot dot-red"></span>
      <span class="terminal-dot dot-yellow"></span>
      <span class="terminal-dot dot-green"></span>
    </div>
    <span class="terminal-title-label">TERMINAL CLI // ROUTEROS</span>
    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">
      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>
      <span>Salin</span>
    </button>
  </div>
  <div class="terminal-block-body">
    <pre><code># 1. Konfigurasi IP Address
/ip address
add address=192.168.88.1/24 interface=ether2 comment="LAN Subnet"
add address=192.168.1.50/24 interface=ether1 comment="WAN ISP"

# 2. Tambah Default Route
/ip route
add dst-address=0.0.0.0/0 gateway=192.168.1.1 comment="Default Gateway"

# 3. Konfigurasi DNS Server
/ip dns
set servers=1.1.1.1,8.8.8.8 allow-remote-requests=yes

# 4. NAT Masquerade
/ip firewall nat
add chain=srcnat out-interface=ether1 action=masquerade comment="NAT Internet"</code></pre>
  </div>
</div>

<div class="material-callout-quote callout-tip">
  <div class="callout-header">✨ TIPS & TRIK</div>
  <p>Gunakan tombol <kbd>Tab</kbd> dua kali untuk auto-complete perintah pada terminal CLI RouterOS, atau gunakan tombol <kbd>Ctrl</kbd> + <kbd>C</kbd> untuk membatalkan perintah yang sedang berjalan.</p>
</div>

<div class="material-callout-quote callout-warning">
  <div class="callout-header">⚠️ PERHATIAN / PERINGATAN</div>
  <p>Pastikan opsi <code>allow-remote-requests=yes</code> pada DNS dilindungi oleh firewall rule input agar router tidak menjadi sasaran DNS Amplification Attack dari internet publik.</p>
</div>

<div class="material-callout-quote callout-danger">
  <div class="callout-header">🚨 PERINGATAN KRITIS</div>
  <p>Jangan pernah mengubah rule <code>/ip firewall filter</code> pada remote Winbox session tanpa menyalakan <strong>Safe Mode</strong> (Shortcut: <kbd>Ctrl</kbd> + <kbd>X</kbd>)!</p>
</div>

<h3>Tabel Perbandingan Opsi NAT</h3>
<div class="material-table-wrapper">
  <table class="material-data-table">
    <thead>
      <tr>
        <th>Action</th>
        <th>Chain</th>
        <th>Penggunaan Ideal</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>masquerade</code></td>
        <td><code>srcnat</code></td>
        <td>Koneksi ISP Dinamis (DHCP/PPPoE)</td>
        <td>Otomatis mendeteksi IP keluar</td>
      </tr>
      <tr>
        <td><code>src-nat</code></td>
        <td><code>srcnat</code></td>
        <td>Koneksi ISP Statis / IP Publik Tetap</td>
        <td>Lebih hemat resource CPU</td>
      </tr>
      <tr>
        <td><code>dst-nat</code></td>
        <td><code>dstnat</code></td>
        <td>Port Forwarding ke Web Server lokal</td>
        <td>Membuka akses dari internet ke server privat</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Checklist Verifikasi Konektivitas</h3>
<ul class="task-list">
  <li><input type="checkbox" checked disabled> Link Status ether1 dan ether2 menyala (R)</li>
  <li><input type="checkbox" checked disabled> Ping dari router ke IP Gateway ISP (192.168.1.1) Berhasil</li>
  <li><input type="checkbox" checked disabled> Ping dari router ke Domain internet (google.com) Berhasil</li>
  <li><input type="checkbox" disabled> Klien lokal mendapatkan IP via DHCP dan bisa browsing</li>
</ul>

<div class="material-callout-quote callout-success">
  <div class="callout-header">✅ BEST PRACTICE & HASIL</div>
  <p>Konfigurasi dasar routing MikroTik telah selesai dan siap digunakan untuk kebutuhan lab maupun jaringan skala kantor.</p>
</div>

<details class="material-details">
  <summary>🔍 Pertanyaan Umum (FAQ): Mengapa Klien Tidak Bisa Browsing Meskipun Ping IP Sukses?</summary>
  <div class="material-details-body">
    <p>Penyebab paling umum adalah setting DNS resolver klien belum aktif atau setting <code>allow-remote-requests=yes</code> pada router belum diaktifkan.</p>
  </div>
</details>`,
    difficulty: "Mudah",
    content: "",
    _panduan_dan_keterangan_toolbar: {
      deskripsi:
        "Panduan lengkap penggunaan fitur toolbar dan komponen HTML yang didukung di Editor Materi NetQuiz.",
      fitur_auto_format:
        "Anda dapat menulis dalam teks biasa atau Markdown (seperti # untuk judul, **teks** untuk tebal, > [!NOTE] untuk callout, baris /ip untuk terminal CLI), lalu klik tombol '⚡ Auto Format' pada toolbar editor untuk mengonversinya secara otomatis menjadi format HTML NetQuiz yang rapi.",
      daftar_toolbar_dan_fungsinya: {
        "⚡ Auto Format":
          "Mengonversi sintaks Markdown, baris CLI RouterOS, checklist, callout, dan tabel secara cerdas ke format HTML NetQuiz.",
        "🧹 Rapikan HTML":
          "Merapikan indentasi, spasi baris, dan penataan tag HTML editor.",
        "H2, H3, H4":
          "Heading terstruktur: H2 (Judul Bab), H3 (Sub-Bab pembahasan), H4 (Poin penting spesifik).",
        "¶ P (Paragraf)":
          "Membungkus paragraf teks penjelasan dengan tag <p>...</p>.",
        "B, I, U, S":
          "Format huruf: Bold <strong>, Italic <em>, Underline <u>, dan Strikethrough <s>.",
        Mark: "Memberikan efek highlight / stabilo kuning lembut <mark>...",
        Code: "Menandai nama perintah, parameter, atau kode inline <code>...",
        "[Kbd]":
          "Menampilkan lencana tombol keyboard retro <kbd>Ctrl</kbd> + <kbd>C</kbd>.",
        "── HR":
          'Garis pembatas bagian horizontal <hr class="section-divider">.',
        "• List": "Daftar poin bullet tidak berurutan <ul><li>...</li></ul>.",
        "1. List": "Daftar langkah atau nomor berurutan <ol><li>...</li></ol>.",
        "☑ Task":
          'Daftar checklist interaktif <ul class="task-list"><li><input type="checkbox">...</li></ul>.',
        "📋 Key-Val": "Daftar parameter spesifikasi konfigurasi jaringan.",
        "🖥️ RouterOS CLI":
          "Blok terminal MikroTik RouterOS lengkap dengan header dot berwarna dan tombol Salin aktif.",
        "🐧 Linux Bash":
          "Blok terminal shell Linux/Ubuntu dengan tombol Salin aktif.",
        "📦 JSON": "Blok format konfigurasi payload API atau file JSON.",
        "💡 Info":
          "Kotak sorotan informasi / catatan berwarna biru (callout-note).",
        "✨ Tips":
          "Kotak sorotan tips & trik efisiensi berwarna hijau (callout-tip).",
        "⚠️ Peringatan":
          "Kotak sorotan peringatan pencegahan kesalahan berwarna kuning (callout-warning).",
        "🚨 Bahaya":
          "Kotak peringatan risiko kritis berwarna merah (callout-danger).",
        "✅ Best Practice":
          "Kotak panduan praktik terbaik dan hasil verifikasi berwarna emerald (callout-success).",
        "💬 Kutipan": "Kutipan teori atau referensi teks <blockquote>...",
        "📊 Tabel":
          "Tabel data responsif modern dengan header gelap dan baris zebra.",
        "🔢 Step Guide":
          "Kartu alur kerja konfigurasi bernomor urut bertahap (Langkah 1, 2, 3).",
        "🌐 Network Card":
          "Kartu ringkasan spesifikasi parameter IP Address, Gateway, DNS, dan Interface.",
        "🔽 Accordion":
          "Kotak buka-tutup interaktif <details><summary> untuk FAQ atau spoiler solusi.",
        "🖼️ Gambar":
          "Elemen gambar <figure> dengan <img> dan keterangan <figcaption>.",
        "🔗 Link":
          "Tautan eksternal dengan target _blank dan proteksi noopener.",
        "📋 Salin HTML":
          "Menyalin seluruh kode markup HTML editor ke clipboard.",
        "🗑️ Bersihkan": "Mengosongkan teks editor dengan konfirmasi.",
        "📥 Upload JSON": "Memuat artikel materi langsung dari berkas JSON.",
        "📤 Template JSON":
          "Mengunduh file JSON template materi lengkap beserta panduan toolbar ini.",
      },
      kategori_tersedia: [
        "Routing",
        "Firewall & NAT",
        "Wireless",
        "Network Management",
      ],
      tingkat_kesulitan_tersedia: ["Mudah", "Sedang", "Sulit"],
    },
  };

  const jsonString = JSON.stringify(templateData, null, 2);
  const blob = new Blob([jsonString], { type: "application/json" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "template_materi_pembelajaran.json";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

// Global editor helper function to insert HTML tags at cursor position
function insertHtmlTag(tagOpen, tagClose = "") {
  const textarea = document.querySelector('textarea[name="content"]');
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const text = textarea.value;
  const selectedText = text.substring(start, end);
  const replacement = tagOpen + selectedText + tagClose;

  textarea.value = text.substring(0, start) + replacement + text.substring(end);

  const newCursorPos = start + replacement.length;
  textarea.focus();
  textarea.setSelectionRange(newCursorPos, newCursorPos);
}

// Global function to auto-format (beautify) HTML content in textarea
function formatHtmlContent() {
  const textarea = document.querySelector('textarea[name="content"]');
  if (!textarea) return;

  let html = textarea.value;
  let indent = 0;
  const tab = "    "; // 4 spaces indentation
  let formatted = "";

  // Normalize spacing and clean extra whitespace between elements
  html = html.replace(/\s+/g, " ").replace(/>\s+</g, "><");

  // Tokenize HTML tags and text contents
  const tokens = html.split(/(<\/?[^>]+>)/g);

  for (let i = 0; i < tokens.length; i++) {
    let token = tokens[i].trim();
    if (!token) continue;

    if (token.startsWith("</")) {
      // Closing tag: reduce indentation level
      indent = Math.max(0, indent - 1);
      formatted += "\n" + tab.repeat(indent) + token;
    } else if (
      token.startsWith("<") &&
      !token.startsWith("<!") &&
      !token.endsWith("/>")
    ) {
      // Opening tag: append and increase indentation (skip self-closing tags)
      const isSelfClosing = /<(img|br|hr|input|link|meta)/i.test(token);
      formatted += "\n" + tab.repeat(indent) + token;
      if (!isSelfClosing) {
        indent++;
      }
    } else {
      // Raw text node
      formatted += "\n" + tab.repeat(indent) + token;
    }
  }

  // Apply formatted value back to the editor field
  textarea.value = formatted.trim();
}

window.downloadJsonTemplate = downloadJsonTemplate;
window.insertHtmlTag = insertHtmlTag;
window.formatHtmlContent = formatHtmlContent;

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide SVG Icons
  if (window.lucide) {
    window.lucide.createIcons();
  }

  const config = window.NetQuizConfig || { baseUrl: "" };

  // --- TAB SWITCHER LOGIC (Left Vertical Sidebar & Floating Bottom Dock) ---
  const tabButtons = document.querySelectorAll(
    ".sidebar-cta-btn, .sidebar-menu-btn, .floating-bottom-btn",
  );
  const sections = document.querySelectorAll(".admin-section-content");

  function activateTab(targetId) {
    const matchingBtns = document.querySelectorAll(
      `.sidebar-cta-btn[data-target="${targetId}"], .sidebar-menu-btn[data-target="${targetId}"], .floating-bottom-btn[data-target="${targetId}"]`,
    );
    const targetSec = document.getElementById(targetId);

    if (matchingBtns.length > 0 && targetSec) {
      // Reset all tab buttons & ARIA attributes
      tabButtons.forEach((b) => {
        b.classList.remove("active");
        b.setAttribute("aria-selected", "false");
        b.setAttribute("tabindex", "-1");
      });
      sections.forEach((s) => s.classList.remove("active"));

      // Activate chosen tab button(s) & section
      matchingBtns.forEach((b) => {
        b.classList.add("active");
        b.setAttribute("aria-selected", "true");
        b.setAttribute("tabindex", "0");
      });
      targetSec.classList.add("active");

      // Update Dynamic Page Header Title, Desc, and Right Action Button (Strict Rules 2 & 3 in design/Layout.md)
      const headerTitle = document.getElementById("page-header-title");
      const headerDesc = document.getElementById("page-header-desc");
      const actionContainer = document.getElementById(
        "page-header-action-container",
      );
      const shell = document.getElementById("admin-canvas-shell");

      // Canvas shell max-width constraint
      if (shell) {
        shell.className = "canvas-shell-container max-w-6xl";
      }

      const headerMeta = {
        "quiz-section": {
          title: "Buat Kuis",
          desc: "Buat kuis baru dan kelola daftar pertanyaan ujian.",
          actionHtml: "",
        },
        "badge-section": {
          title: "Lencana",
          desc: "Manajemen lencana prestasi dan target pencapaian kuis siswa.",
          actionHtml: "",
        },
        "member-section": {
          title: "Daftarkan Member",
          desc: "Tambahkan anggota baru ke dalam sistem NetQuiz.",
          actionHtml: "",
        },
        "manage-section": {
          title: "Manajemen Member",
          desc: "Kelola data member terdaftar, status akun, dan hak akses.",
          actionHtml: "",
        },
        "materials-section": {
          title: "Materi Belajar",
          desc: "Kelola artikel materi pembelajaran MikroTik RouterOS.",
          actionHtml: "",
        },
      };

      const breadcrumbTitle = document.getElementById(
        "breadcrumb-active-title",
      );

      if (headerMeta[targetId]) {
        if (headerTitle) headerTitle.innerText = headerMeta[targetId].title;
        if (headerDesc) headerDesc.innerText = headerMeta[targetId].desc;
        if (breadcrumbTitle) {
          breadcrumbTitle.innerText = headerMeta[targetId].title;
        }
        if (actionContainer) {
          actionContainer.innerHTML = headerMeta[targetId].actionHtml || "";
          if (window.lucide) {
            window.lucide.createIcons({ root: actionContainer });
          }
        }
      }

      // Permanent Canonical Top Button in Sidebar: Always '+ Buat Kuis Baru' (Strict Rule 2)
      const ctaBtn = document.querySelector(".sidebar-cta-btn");
      if (ctaBtn) {
        ctaBtn.setAttribute("data-target", "quiz-section");
        if (targetId === "quiz-section") {
          ctaBtn.classList.add("active");
          ctaBtn.setAttribute("aria-selected", "true");
        } else {
          ctaBtn.classList.remove("active");
          ctaBtn.setAttribute("aria-selected", "false");
        }
      }

      // Update hash in URL quietly without page jump
      if (window.location.hash !== "#" + targetId) {
        history.replaceState(null, "", "#" + targetId);
      }

      // Smoothly reset scroll to top on tab switch
      const mainCanvasEl =
        document.querySelector(".admin-main-canvas") ||
        document.getElementById("admin-workspace");
      if (mainCanvasEl) {
        mainCanvasEl.scrollTo({ top: 0, behavior: "instant" });
        mainCanvasEl.scrollTop = 0;
      }
      window.scrollTo({ top: 0, behavior: "instant" });

      // Update active list button text dynamically
      const modalBtnText = document.getElementById("open-quiz-modal-text");
      if (modalBtnText) {
        if (targetId === "badge-section") {
          modalBtnText.innerText = "Lihat Lencana Aktif";
        } else if (targetId === "materials-section") {
          modalBtnText.innerText = "Lihat Materi Aktif";
        } else {
          modalBtnText.innerText = "Lihat Kuis Aktif";
        }
      }

      // Hide Floating Bulk Action Bar when navigating away from manage-section
      const floatingBulkBar = document.getElementById("floating-bulk-bar");
      if (floatingBulkBar && targetId !== "manage-section") {
        floatingBulkBar.classList.remove("active");
        floatingBulkBar.style.display = "none";
      }
    }
  }

  const tabListArray = Array.from(tabButtons);
  tabButtons.forEach((btn, index) => {
    btn.addEventListener("click", () => {
      const targetId = btn.getAttribute("data-target");
      activateTab(targetId);
      if (
        targetId === "quiz-section" &&
        (btn.classList.contains("sidebar-cta-btn") ||
          btn.classList.contains("floating-bottom-btn"))
      ) {
        if (window.openQuizStudio) window.openQuizStudio();
      }
    });

    // Arrow Key Keyboard Navigation (W3C Tablist Standard)
    btn.addEventListener("keydown", (e) => {
      let targetIndex = null;
      if (e.key === "ArrowRight") {
        targetIndex = (index + 1) % tabListArray.length;
      } else if (e.key === "ArrowLeft") {
        targetIndex = (index - 1 + tabListArray.length) % tabListArray.length;
      }

      if (targetIndex !== null) {
        e.preventDefault();
        const nextBtn = tabListArray[targetIndex];
        nextBtn.focus();
        const nextTargetId = nextBtn.getAttribute("data-target");
        activateTab(nextTargetId);
      }
    });
  });

  // Initialize active tab from URL hash
  function ensureCanvasScrollTop() {
    const mainCanvasEl =
      document.querySelector(".admin-main-canvas") ||
      document.getElementById("admin-workspace");
    if (mainCanvasEl) {
      mainCanvasEl.scrollTop = 0;
    }
    window.scrollTo(0, 0);
  }

  function initTabFromHash() {
    if ("scrollRestoration" in history) {
      history.scrollRestoration = "manual";
    }
    const currentHash = window.location.hash.substring(1);
    if (
      currentHash &&
      document.getElementById(currentHash) &&
      document.querySelector(
        `.sidebar-cta-btn[data-target="${currentHash}"], .sidebar-menu-btn[data-target="${currentHash}"], .floating-bottom-btn[data-target="${currentHash}"]`,
      )
    ) {
      activateTab(currentHash);
    } else {
      activateTab("quiz-section");
    }
    ensureCanvasScrollTop();
    requestAnimationFrame(ensureCanvasScrollTop);
    setTimeout(ensureCanvasScrollTop, 10);
    setTimeout(ensureCanvasScrollTop, 50);
    setTimeout(ensureCanvasScrollTop, 150);
  }

  // Run on load and on hash change
  initTabFromHash();
  window.addEventListener("hashchange", initTabFromHash);
  window.addEventListener("load", () => {
    ensureCanvasScrollTop();
    setTimeout(ensureCanvasScrollTop, 50);
  });

  // --- JSON MATERIAL IMPORT LOGIC ---
  const jsonFileInput = document.getElementById("import-material-json");
  if (jsonFileInput) {
    jsonFileInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (event) => {
        try {
          const data = JSON.parse(event.target.result);

          // Validate JSON format
          if (!data.title || !data.content || !data.category) {
            if (window.showGeistToast)
              window.showGeistToast(
                "error",
                "Format JSON Tidak Valid",
                'File JSON harus memiliki properti "title", "content", dan "category".',
              );
            else alert("Format JSON tidak valid!");
            return;
          }

          // Fill in the form fields
          const form = document.getElementById("create-material-form");
          if (form) {
            document.getElementById("form-material-title").value = data.title;
            document.getElementById("form-material-category").value =
              data.category;
            document.getElementById("form-material-difficulty").value =
              data.difficulty || "Mudah";
            document.getElementById("form-material-content").value =
              data.content;

            // Open visual builder and go directly to preview mode
            if (typeof openVisualBuilderFromForm === "function") {
              openVisualBuilderFromForm(true);
              setBuilderMode("preview");
            }

            if (window.showGeistToast)
              window.showGeistToast(
                "success",
                "Materi Diimpor",
                "Materi berhasil diimpor dari file JSON.",
              );
          }
        } catch (err) {
          if (window.showGeistToast)
            window.showGeistToast("error", "Gagal Membaca File", err.message);
          else alert("Gagal membaca file JSON: " + err.message);
        }
      };
      reader.readAsText(file);

      // Reset file input value so same file can be selected again
      jsonFileInput.value = "";
    });
  }

  // --- MEMBER REGISTRATION VALIDATION ---
  const registerForm = document.getElementById("register-member-form");
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      const passwordInput = registerForm.querySelector(
        'input[name="password"]',
      );
      if (passwordInput && passwordInput.value.length < 8) {
        e.preventDefault();
        if (window.showGeistToast)
          window.showGeistToast(
            "error",
            "Validasi Gagal",
            "Password harus minimal 8 karakter!",
          );
        else alert("Password sementara harus minimal 8 karakter!");
      }
    });
  }

  // Category Button Selectors
  const categoryInput = document.getElementById("selected-category-input");
  const categoryButtons = document.querySelectorAll(".category-select-btn");

  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      categoryButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      if (categoryInput) {
        categoryInput.value = btn.getAttribute("data-value");
      }
    });
  });

  // Active Quiz Modal Selectors
  const activeModal = document.getElementById("quiz-list-modal");
  const openActiveModalBtn = document.getElementById("open-quiz-modal-btn");
  const closeActiveModalBtn = document.getElementById("close-quiz-modal-btn");

  if (openActiveModalBtn && activeModal) {
    openActiveModalBtn.addEventListener("click", () => {
      const activeSection = document.querySelector(
        ".admin-section-content.active",
      );
      const activeId = activeSection ? activeSection.getAttribute("id") : "";

      const modalTitle = document.getElementById("active-list-modal-title");
      const quizzesContainer = document.getElementById("modal-list-quizzes");
      const badgesContainer = document.getElementById("modal-list-badges");
      const materialsContainer = document.getElementById(
        "modal-list-materials",
      );

      if (quizzesContainer) quizzesContainer.style.display = "none";
      if (badgesContainer) badgesContainer.style.display = "none";
      if (materialsContainer) materialsContainer.style.display = "none";

      if (activeId === "badge-section") {
        if (modalTitle)
          modalTitle.innerText = "Daftar Lencana yang Aktif Saat Ini";
        if (badgesContainer) badgesContainer.style.display = "block";
      } else if (activeId === "materials-section") {
        if (modalTitle)
          modalTitle.innerText = "Daftar Materi yang Aktif Saat Ini";
        if (materialsContainer) materialsContainer.style.display = "block";
      } else {
        if (modalTitle)
          modalTitle.innerText = "Daftar Kuis yang Aktif Saat Ini";
        if (quizzesContainer) quizzesContainer.style.display = "block";
      }

      activeModal.classList.add("show");
    });
  }

  if (closeActiveModalBtn && activeModal) {
    closeActiveModalBtn.addEventListener("click", () => {
      activeModal.classList.remove("show");
    });
  }

  if (activeModal) {
    activeModal.addEventListener("click", (e) => {
      if (e.target === activeModal) {
        activeModal.classList.remove("show");
      }
    });
  }

  // Saved Questions Modal Selectors
  const savedModal = document.getElementById("saved-questions-modal");
  const openSavedModalBtn = document.getElementById("open-saved-modal-btn");
  const closeSavedModalBtn = document.getElementById("close-saved-modal-btn");
  const modalSavedList = document.getElementById("saved-questions-list");

  if (openSavedModalBtn && savedModal) {
    openSavedModalBtn.addEventListener("click", () => {
      savedModal.classList.add("show");
    });
  }

  if (closeSavedModalBtn && savedModal) {
    closeSavedModalBtn.addEventListener("click", () => {
      savedModal.classList.remove("show");
    });
  }

  if (savedModal) {
    savedModal.addEventListener("click", (e) => {
      if (e.target === savedModal) {
        savedModal.classList.remove("show");
      }
    });
  }

  // Badge List Modal Selectors
  const badgeModal = document.getElementById("badge-list-modal");
  const openBadgeModalBtn = document.getElementById("open-badge-modal-btn");
  const closeBadgeModalBtn = document.getElementById("close-badge-modal-btn");

  if (openBadgeModalBtn && badgeModal) {
    openBadgeModalBtn.addEventListener("click", () => {
      badgeModal.classList.add("show");
    });
  }

  if (closeBadgeModalBtn && badgeModal) {
    closeBadgeModalBtn.addEventListener("click", () => {
      badgeModal.classList.remove("show");
    });
  }

  window.deleteBadgeSingle = function (id) {
    const tempForm = document.createElement("form");
    tempForm.method = "POST";
    tempForm.action = `${config.baseUrl}/admin/badges/delete/${id}`;

    if (window.CSRF_TOKEN) {
      const csrfInput = document.createElement("input");
      csrfInput.type = "hidden";
      csrfInput.name = "csrf_token";
      csrfInput.value = window.CSRF_TOKEN;
      tempForm.appendChild(csrfInput);
    }

    document.body.appendChild(tempForm);
    tempForm.submit();
  };

  if (badgeModal) {
    badgeModal.addEventListener("click", (e) => {
      if (e.target === badgeModal) {
        badgeModal.classList.remove("show");
      }
    });

    // Multi-select Badges logic
    const toggleSelectModeBtn = document.getElementById(
      "btn-toggle-select-mode",
    );
    const bulkDeleteBtn = document.getElementById("btn-bulk-delete-badges");
    const badgeCheckboxes = document.querySelectorAll(".badge-item-checkbox");

    let isSelectMode = false;

    function updateBulkDeleteButtonState() {
      const checkedCount = document.querySelectorAll(
        ".badge-item-checkbox:checked",
      ).length;
      if (bulkDeleteBtn) {
        if (checkedCount > 0 && isSelectMode) {
          bulkDeleteBtn.disabled = false;
          bulkDeleteBtn.style.opacity = "1";
          bulkDeleteBtn.style.cursor = "pointer";
        } else {
          bulkDeleteBtn.disabled = true;
          bulkDeleteBtn.style.opacity = "0.5";
          bulkDeleteBtn.style.cursor = "not-allowed";
        }
      }
    }

    if (toggleSelectModeBtn) {
      toggleSelectModeBtn.addEventListener("click", () => {
        isSelectMode = !isSelectMode;
        if (isSelectMode) {
          toggleSelectModeBtn.textContent = "Batal";
          badgeCheckboxes.forEach((cb) => {
            cb.style.display = "inline-block";
          });
        } else {
          toggleSelectModeBtn.textContent = "Pilih";
          badgeCheckboxes.forEach((cb) => {
            cb.checked = false;
            cb.style.display = "none";
          });
        }
        updateBulkDeleteButtonState();
      });
    }

    badgeCheckboxes.forEach((cb) => {
      cb.addEventListener("change", () => {
        updateBulkDeleteButtonState();
      });
    });
  }

  // Question Builder Selectors
  const hiddenContainer = document.getElementById("hidden-inputs-container");
  const savedCountEl = document.getElementById("saved-count");
  const addBtn = document.getElementById("add-question-btn");

  // Form fields
  const qTextInput = document.getElementById("q-text");
  const qOptAInput = document.getElementById("q-opt-a");
  const qOptBInput = document.getElementById("q-opt-b");
  const qOptCInput = document.getElementById("q-opt-c");
  const qOptDInput = document.getElementById("q-opt-d");
  const qCorrectSelect = document.getElementById("q-correct");
  const qExplanationInput = document.getElementById("q-explanation");

  let savedQuestions = [];

  const quizTitleInput = document.querySelector(
    '#create-quiz-form input[name="title"]',
  );
  const quizDurationInput = document.querySelector(
    '#create-quiz-form input[name="duration"]',
  );
  const quizDescInput = document.querySelector(
    '#create-quiz-form textarea[name="description"]',
  );

  function updateDOM() {
    if (!modalSavedList || !hiddenContainer || !savedCountEl) return;
    modalSavedList.innerHTML = "";
    hiddenContainer.innerHTML = "";

    const submitQuizBtn = document.getElementById("btn-submit-quiz");
    const isQuizInfoValid =
      quizTitleInput &&
      quizTitleInput.value.trim() !== "" &&
      quizDurationInput &&
      quizDurationInput.value.trim() !== "" &&
      quizDescInput &&
      quizDescInput.value.trim() !== "";

    if (submitQuizBtn) {
      if (savedQuestions.length >= 1 && isQuizInfoValid) {
        submitQuizBtn.disabled = false;
        submitQuizBtn.style.opacity = "1";
        submitQuizBtn.style.cursor = "pointer";
      } else {
        submitQuizBtn.disabled = true;
        submitQuizBtn.style.opacity = "0.5";
        submitQuizBtn.style.cursor = "not-allowed";
      }
    }

    if (savedQuestions.length === 0) {
      modalSavedList.innerHTML = `
                <div style="font-size: 0.85rem; color: #94a3b8; font-style: italic; padding: 1.5rem 0; text-align: center;">
                    Belum ada soal yang disimpan. Tambahkan soal menggunakan formulir di luar modal.
                </div>
            `;
      savedCountEl.textContent = "0";
      return;
    }

    savedCountEl.textContent = savedQuestions.length.toString();

    savedQuestions.forEach((q, index) => {
      const item = document.createElement("div");
      item.className = "quiz-row-item";
      item.style.backgroundColor = "#f8fafc";
      item.style.padding = "0.75rem 1rem";
      item.style.borderRadius = "8px";
      item.style.border = "1px solid #222222";
      item.innerHTML = `
                <div class="quiz-row-info">
                    <span style="font-weight: 700; font-size: 0.85rem; color: #ffffff;">#${index + 1}: ${q.question}</span>
                    <span style="font-size: 0.75rem; color: #a1a1aa;">Pilihan: [A: ${q.option_a}] [B: ${q.option_b}] [C: ${q.option_c}] [D: ${q.option_d}] &bull; Jawaban: <strong style="color: #50e3c2;">${q.correct}</strong></span>
                    ${q.explanation ? `<span style="font-size: 0.75rem; color: #a1a1aa; display: block; margin-top: 0.25rem;"><strong>Penjelasan:</strong> ${escapeHtml(q.explanation)}</span>` : ""}
                    ${q.image ? '<span style="font-size: 0.7rem; color: #0d9488;"><i data-lucide="image" style="width: 0.8rem; height: 0.8rem;"></i> Termasuk Gambar</span>' : ""}
                </div>
                <button type="button" class="btn-danger-sm" style="padding: 0.3rem 0.6rem;" onclick="removeQuestion(${index})">
                    <i data-lucide="trash-2" style="width: 0.85rem; height: 0.85rem;"></i>
                </button>
            `;
      modalSavedList.appendChild(item);

      hiddenContainer.innerHTML += `
                <input type="hidden" name="questions[${index}][question]" value="${escapeHtml(q.question)}">
                <input type="hidden" name="questions[${index}][option_a]" value="${escapeHtml(q.option_a)}">
                <input type="hidden" name="questions[${index}][option_b]" value="${escapeHtml(q.option_b)}">
                <input type="hidden" name="questions[${index}][option_c]" value="${escapeHtml(q.option_c)}">
                <input type="hidden" name="questions[${index}][option_d]" value="${escapeHtml(q.option_d)}">
                <input type="hidden" name="questions[${index}][correct]" value="${escapeHtml(q.correct)}">
                <input type="hidden" name="questions[${index}][explanation]" value="${escapeHtml(q.explanation || "")}">
                <input type="hidden" name="questions[${index}][image]" value="${escapeHtml(q.image || "")}">
            `;
    });

    if (window.lucide) window.lucide.createIcons();
  }

  window.removeQuestion = function (index) {
    savedQuestions.splice(index, 1);
    updateDOM();
  };

  function escapeHtml(text) {
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  let currentImageBase64 = "";
  const qImageInput = document.getElementById("q-image");
  const qImageFilename = document.getElementById("q-image-filename");
  const qImageLabelText = document.getElementById("q-image-label-text");

  if (qImageInput) {
    qImageInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 61440) {
          if (window.showGeistToast)
            window.showGeistToast(
              "error",
              "Ukuran Terlalu Besar",
              "Ukuran gambar maksimal adalah 60KB.",
            );
          else alert("Ukuran gambar maksimal adalah 60KB.");
          this.value = "";
          currentImageBase64 = "";
          if (qImageFilename)
            qImageFilename.textContent = "Belum ada gambar yang dipilih";
          if (qImageLabelText) qImageLabelText.textContent = "Tambahkan Gambar";
          return;
        }
        if (qImageFilename) qImageFilename.textContent = file.name;
        if (qImageLabelText) qImageLabelText.textContent = "Ganti Gambar";

        const reader = new FileReader();
        reader.onload = function (e) {
          currentImageBase64 = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        currentImageBase64 = "";
        if (qImageFilename)
          qImageFilename.textContent = "Belum ada gambar yang dipilih";
        if (qImageLabelText) qImageLabelText.textContent = "Tambahkan Gambar";
      }
    });
  }

  if (addBtn) {
    addBtn.addEventListener("click", () => {
      const text = qTextInput.value.trim();
      const optA = qOptAInput.value.trim();
      const optB = qOptBInput.value.trim();
      const optC = qOptCInput.value.trim();
      const optD = qOptDInput.value.trim();
      const correct = qCorrectSelect.value;
      const explanation = qExplanationInput
        ? qExplanationInput.value.trim()
        : "";

      if (!text || !optA || !optB || !optC || !optD) {
        if (window.showGeistToast)
          window.showGeistToast(
            "error",
            "Form Belum Lengkap",
            "Silakan isi seluruh teks soal dan semua pilihan jawaban terlebih dahulu.",
          );
        else
          alert(
            "Silakan isi seluruh teks soal dan semua pilihan jawaban terlebih dahulu.",
          );
        return;
      }

      savedQuestions.push({
        question: text,
        option_a: optA,
        option_b: optB,
        option_c: optC,
        option_d: optD,
        correct: correct,
        explanation: explanation,
        image: currentImageBase64,
      });

      qTextInput.value = "";
      qOptAInput.value = "";
      qOptBInput.value = "";
      qOptCInput.value = "";
      qOptDInput.value = "";
      if (qExplanationInput) qExplanationInput.value = "";
      qCorrectSelect.selectedIndex = 0;
      if (qImageInput) qImageInput.value = "";
      if (qImageFilename)
        qImageFilename.textContent = "Belum ada gambar yang dipilih";
      if (qImageLabelText) qImageLabelText.textContent = "Tambahkan Gambar";
      currentImageBase64 = "";

      updateDOM();
      qTextInput.focus();
      checkQuestionInputs();
    });
  }

  // Auto-Import Questions logic
  const importFileInput = document.getElementById("import-quiz-file");
  const importFileName = document.getElementById("import-file-name");
  const downloadJsonBtn = document.getElementById("download-template-json");
  const downloadCsvBtn = document.getElementById("download-template-csv");

  // CSV parsing helper
  function parseCSV(text) {
    let lines = [];
    let row = [""];
    let inQuotes = false;
    for (let i = 0; i < text.length; i++) {
      let c = text[i];
      let next = text[i + 1];
      if (c === '"') {
        if (inQuotes && next === '"') {
          row[row.length - 1] += '"';
          i++;
        } else {
          inQuotes = !inQuotes;
        }
      } else if (c === "," && !inQuotes) {
        row.push("");
      } else if ((c === "\r" || c === "\n") && !inQuotes) {
        if (c === "\r" && next === "\n") {
          i++;
        }
        lines.push(row);
        row = [""];
      } else {
        row[row.length - 1] += c;
      }
    }
    if (row.length > 1 || row[0] !== "") {
      lines.push(row);
    }
    return lines;
  }

  if (importFileInput) {
    importFileInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;

      if (importFileName) importFileName.textContent = file.name;
      const reader = new FileReader();

      reader.onload = function (evt) {
        try {
          const content = evt.target.result;
          let questionsImported = [];

          if (file.name.endsWith(".json")) {
            const parsed = JSON.parse(content);
            let questionsArray = [];

            if (
              !Array.isArray(parsed) &&
              parsed.questions &&
              Array.isArray(parsed.questions)
            ) {
              if (parsed.title && quizTitleInput)
                quizTitleInput.value = parsed.title;
              if (parsed.description && quizDescInput)
                quizDescInput.value = parsed.description;
              if (parsed.duration !== undefined && quizDurationInput)
                quizDurationInput.value = parsed.duration;
              if (parsed.difficulty) {
                const diffSelect = document.querySelector(
                  '#create-quiz-form select[name="difficulty"]',
                );
                if (diffSelect) diffSelect.value = parsed.difficulty;
              }
              if (parsed.category) {
                const catInput = document.getElementById(
                  "selected-category-input",
                );
                if (catInput) {
                  catInput.value = parsed.category;
                  const catBtns = document.querySelectorAll(
                    ".category-select-btn",
                  );
                  catBtns.forEach((btn) => {
                    if (btn.getAttribute("data-value") === parsed.category) {
                      btn.classList.add("active");
                    } else {
                      btn.classList.remove("active");
                    }
                  });
                }
              }
              questionsArray = parsed.questions;
            } else if (Array.isArray(parsed)) {
              questionsArray = parsed;
            } else {
              throw new Error(
                'Format JSON harus berupa array berisi list objek pertanyaan, atau objek kuis dengan properti "questions".',
              );
            }

            questionsArray.forEach((item, idx) => {
              const question = item.question || item.pertanyaan || "";
              const option_a = item.option_a || item.pilihan_a || item.a || "";
              const option_b = item.option_b || item.pilihan_b || item.b || "";
              const option_c = item.option_c || item.pilihan_c || item.c || "";
              const option_d = item.option_d || item.pilihan_d || item.d || "";
              const correct = (
                item.correct ||
                item.kunci ||
                item.jawaban ||
                "A"
              )
                .toUpperCase()
                .trim();
              const explanation = item.explanation || item.penjelasan || "";

              if (question && option_a && option_b && option_c && option_d) {
                questionsImported.push({
                  question,
                  option_a,
                  option_b,
                  option_c,
                  option_d,
                  correct: ["A", "B", "C", "D"].includes(correct)
                    ? correct
                    : "A",
                  explanation,
                  image: "",
                });
              } else {
                console.warn(
                  `Pertanyaan index ${idx} dilewati karena data kurang lengkap.`,
                );
              }
            });
          } else if (file.name.endsWith(".csv")) {
            const rows = parseCSV(content);
            if (rows.length < 2) {
              throw new Error(
                "File CSV kosong atau tidak memiliki baris data.",
              );
            }
            const headers = rows[0].map((h) => h.trim().toLowerCase());
            const map = {};
            headers.forEach((h, index) => {
              if (
                h.includes("question") ||
                h.includes("soal") ||
                h === "pertanyaan"
              )
                map.question = index;
              else if (
                h.includes("option_a") ||
                h === "a" ||
                h.includes("pilihan_a")
              )
                map.option_a = index;
              else if (
                h.includes("option_b") ||
                h === "b" ||
                h.includes("pilihan_b")
              )
                map.option_b = index;
              else if (
                h.includes("option_c") ||
                h === "c" ||
                h.includes("pilihan_c")
              )
                map.option_c = index;
              else if (
                h.includes("option_d") ||
                h === "d" ||
                h.includes("pilihan_d")
              )
                map.option_d = index;
              else if (
                h.includes("correct") ||
                h.includes("kunci") ||
                h.includes("jawaban")
              )
                map.correct = index;
              else if (h.includes("explanation") || h.includes("penjelasan"))
                map.explanation = index;
            });

            if (
              map.question === undefined ||
              map.option_a === undefined ||
              map.option_b === undefined ||
              map.option_c === undefined ||
              map.option_d === undefined
            ) {
              throw new Error(
                "Format kolom CSV tidak sesuai. Pastikan memiliki kolom: question, option_a, option_b, option_c, option_d, correct, explanation",
              );
            }

            for (let i = 1; i < rows.length; i++) {
              const row = rows[i];
              if (row.length <= 1 && row[0] === "") continue;

              const question = row[map.question]
                ? row[map.question].trim()
                : "";
              const option_a = row[map.option_a]
                ? row[map.option_a].trim()
                : "";
              const option_b = row[map.option_b]
                ? row[map.option_b].trim()
                : "";
              const option_c = row[map.option_c]
                ? row[map.option_c].trim()
                : "";
              const option_d = row[map.option_d]
                ? row[map.option_d].trim()
                : "";
              const correct = row[map.correct]
                ? row[map.correct].trim().toUpperCase()
                : "A";
              const explanation = row[map.explanation]
                ? row[map.explanation].trim()
                : "";

              if (question && option_a && option_b && option_c && option_d) {
                questionsImported.push({
                  question,
                  option_a,
                  option_b,
                  option_c,
                  option_d,
                  correct: ["A", "B", "C", "D"].includes(correct)
                    ? correct
                    : "A",
                  explanation,
                  image: "",
                });
              }
            }
          }

          if (questionsImported.length === 0) {
            if (window.showGeistToast)
              window.showGeistToast(
                "error",
                "Import Gagal",
                "Tidak ada soal valid yang berhasil di-import dari file.",
              );
            else
              alert("Tidak ada soal valid yang berhasil di-import dari file.");
          } else {
            if (quizTitleInput && quizTitleInput.value.trim() === "") {
              const today = new Date().toLocaleDateString("id-ID", {
                day: "numeric",
                month: "short",
                year: "numeric",
              });
              quizTitleInput.value = `Kuis Hasil Import - ${today}`;
            }
            if (quizDescInput && quizDescInput.value.trim() === "") {
              quizDescInput.value = `Kuis dinamis yang dibuat otomatis dari import berkas soal pada ${new Date().toLocaleString("id-ID")}.`;
            }
            if (
              quizDurationInput &&
              (quizDurationInput.value.trim() === "" ||
                quizDurationInput.value === "0")
            ) {
              quizDurationInput.value = "30";
            }

            savedQuestions = savedQuestions.concat(questionsImported);
            updateDOM();
            if (window.showGeistToast)
              window.showGeistToast(
                "success",
                "Import Berhasil",
                `Berhasil meng-import ${questionsImported.length} soal ke dalam daftar kuis.`,
              );
            else
              alert(`Berhasil meng-import ${questionsImported.length} soal.`);
          }
        } catch (err) {
          if (window.showGeistToast)
            window.showGeistToast("error", "Gagal Memproses File", err.message);
          else alert("Gagal memproses file: " + err.message);
        } finally {
          importFileInput.value = "";
        }
      };

      reader.readAsText(file);
    });
  }

  // Template downloads
  if (downloadJsonBtn) {
    downloadJsonBtn.addEventListener("click", function () {
      const template = [
        {
          question: "Contoh pertanyaan kuis MikroTik OSPF?",
          option_a: "Jawaban A",
          option_b: "Jawaban B",
          option_c: "Jawaban C",
          option_d: "Jawaban D",
          correct: "A",
          explanation:
            "Penjelasan mengapa jawaban A adalah kunci jawaban yang benar.",
        },
      ];
      const blob = new Blob([JSON.stringify(template, null, 2)], {
        type: "application/json",
      });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "template_kuis.json";
      a.click();
      URL.revokeObjectURL(url);
    });
  }

  if (downloadCsvBtn) {
    downloadCsvBtn.addEventListener("click", function () {
      const csvContent =
        "question,option_a,option_b,option_c,option_d,correct,explanation\n" +
        '"Contoh pertanyaan kuis MikroTik OSPF?","Jawaban A","Jawaban B","Jawaban C","Jawaban D","A","Penjelasan mengapa jawaban A adalah kunci jawaban yang benar."';
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "template_kuis.csv";
      a.click();
      URL.revokeObjectURL(url);
    });
  }

  if (quizTitleInput && quizDescInput && quizDurationInput) {
    quizTitleInput.addEventListener("input", updateDOM);
    quizDurationInput.addEventListener("input", updateDOM);
    quizDescInput.addEventListener("input", updateDOM);
  }

  function checkQuestionInputs() {
    if (
      addBtn &&
      qTextInput &&
      qOptAInput &&
      qOptBInput &&
      qOptCInput &&
      qOptDInput
    ) {
      const q = qTextInput.value.trim();
      const a = qOptAInput.value.trim();
      const b = qOptBInput.value.trim();
      const c = qOptCInput.value.trim();
      const d = qOptDInput.value.trim();
      if (q !== "" && a !== "" && b !== "" && c !== "" && d !== "") {
        addBtn.disabled = false;
        addBtn.style.opacity = "1";
        addBtn.style.cursor = "pointer";
      } else {
        addBtn.disabled = true;
        addBtn.style.opacity = "0.5";
        addBtn.style.cursor = "not-allowed";
      }
    }
  }

  if (qTextInput) {
    qTextInput.addEventListener("input", checkQuestionInputs);
    qOptAInput.addEventListener("input", checkQuestionInputs);
    qOptBInput.addEventListener("input", checkQuestionInputs);
    qOptCInput.addEventListener("input", checkQuestionInputs);
    qOptDInput.addEventListener("input", checkQuestionInputs);
    checkQuestionInputs();
  }

  // 3. Buat Kuis: Member Registration validation
  const regUsername = document.querySelector(
    '#register-member-form input[name="username"]',
  );
  const regEmail = document.querySelector(
    '#register-member-form input[name="email"]',
  );
  const regPassword = document.querySelector(
    '#register-member-form input[name="password"]',
  );
  const regBtn = document.querySelector(
    '#register-member-form button[type="submit"]',
  );

  if (regUsername && regEmail && regPassword && regBtn) {
    const checkRegInputs = () => {
      const u = regUsername.value.trim();
      const e = regEmail.value.trim();
      const p = regPassword.value;
      if (u !== "" && e !== "" && p.length >= 8) {
        regBtn.disabled = false;
        regBtn.style.opacity = "1";
        regBtn.style.cursor = "pointer";
      } else {
        regBtn.disabled = true;
        regBtn.style.opacity = "0.5";
        regBtn.style.cursor = "not-allowed";
      }
    };
    regUsername.addEventListener("input", checkRegInputs);
    regEmail.addEventListener("input", checkRegInputs);
    regPassword.addEventListener("input", checkRegInputs);
    checkRegInputs();
  }

  // 4. Lencana Form Validation
  const badgeTitle = document.querySelector(
    '#create-badge-form input[name="title"]',
  );
  const badgeDesc = document.querySelector(
    '#create-badge-form input[name="description"]',
  );
  const badgeTarget = document.querySelector(
    '#create-badge-form input[name="target_value"]',
  );
  const badgeBtn = document.querySelector(
    '#create-badge-form button[type="submit"]',
  );

  if (badgeTitle && badgeDesc && badgeTarget && badgeBtn) {
    const checkBadgeInputs = () => {
      const t = badgeTitle.value.trim();
      const d = badgeDesc.value.trim();
      const v = badgeTarget.value.trim();
      if (t !== "" && d !== "" && v !== "") {
        badgeBtn.disabled = false;
        badgeBtn.style.opacity = "1";
        badgeBtn.style.cursor = "pointer";
      } else {
        badgeBtn.disabled = true;
        badgeBtn.style.opacity = "0.5";
        badgeBtn.style.cursor = "not-allowed";
      }
    };
    badgeTitle.addEventListener("input", checkBadgeInputs);
    badgeDesc.addEventListener("input", checkBadgeInputs);
    badgeTarget.addEventListener("input", checkBadgeInputs);
    checkBadgeInputs();
  }

  // 5. Pengaturan Profil Form Validation
  const profForm = document.getElementById("update-profile-form");
  const profUsername = profForm
    ? profForm.querySelector('input[name="username"]')
    : null;
  const profEmail = profForm
    ? profForm.querySelector('input[name="email"]')
    : null;
  const profPassword = profForm
    ? profForm.querySelector('input[name="password"]')
    : null;
  // --- TOPBAR & SIDEBAR SCROLL SYNCHRONIZATION ---
  const topNav = document.querySelector(".admin-top-nav");
  const scrollableCanvas =
    document.querySelector(".admin-main-canvas") ||
    document.getElementById("admin-workspace");
  function handleAppScroll() {
    if (!topNav) return;
    const currentScroll =
      (scrollableCanvas ? scrollableCanvas.scrollTop : 0) ||
      window.scrollY ||
      0;
    if (currentScroll > 8) {
      topNav.classList.add("scrolled");
    } else {
      topNav.classList.remove("scrolled");
    }
  }

  if (scrollableCanvas) {
    scrollableCanvas.addEventListener("scroll", handleAppScroll, {
      passive: true,
    });
  }
  window.addEventListener("scroll", handleAppScroll, { passive: true });
  handleAppScroll();

  // --- ALERT AUTO-DISMISS LOGIC ---
  const adminAlerts = document.querySelectorAll(".admin-alert");
  adminAlerts.forEach((alertEl) => {
    setTimeout(() => {
      alertEl.style.opacity = "0";
      setTimeout(() => {
        alertEl.remove();
      }, 500);
    }, 2000);
  });
});
