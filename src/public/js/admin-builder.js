/**
 * NetQuiz Admin Console - Unified Single Panel & Grid 12 Builder (Upgraded UX/UI)
 * Standards: Vercel Geist Light Theme, Dual-Layer CAD Blueprint, Zero AI Slop
 */

// ==========================================================================
// 1. GLOBAL TOAST & CONFIRM MODAL SYSTEM (GEIST STANDARDS)
// ==========================================================================

/** Global non-blocking floating toast notification */
window.showGeistToast = function (type, title, message, duration = 3500) {
  const toaster = document.getElementById("geist-toaster");
  if (!toaster) return;

  const toast = document.createElement("div");
  toast.className = `geist-toast toast-${type}`;

  let iconName = "check-circle";
  if (type === "error") iconName = "alert-circle";
  else if (type === "info") iconName = "info";

  toast.innerHTML = `
        <div class="toast-icon-wrapper">
            <i data-lucide="${iconName}" style="width: 15px; height: 15px;"></i>
        </div>
        <div class="toast-body">
            <div class="toast-title">${escapeHtml(title)}</div>
            ${message ? `<div class="toast-message">${escapeHtml(message)}</div>` : ""}
        </div>
        <button type="button" class="toast-close-btn" title="Tutup">
            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
        </button>
        <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
    `;

  toaster.appendChild(toast);
  if (window.lucide) window.lucide.createIcons();

  let dismissTimeout;
  const dismiss = () => {
    if (toast.classList.contains("hiding")) return;
    toast.classList.add("hiding");
    setTimeout(() => toast.remove(), 220);
  };

  dismissTimeout = setTimeout(dismiss, duration);

  const closeBtn = toast.querySelector(".toast-close-btn");
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      clearTimeout(dismissTimeout);
      dismiss();
    });
  }
};

/** Global custom confirmation modal dialog with focus trap & scroll lock */
window.showGeistConfirm = function (
  title,
  message,
  confirmText,
  onConfirm,
  isDanger = true,
) {
  const overlay = document.getElementById("geist-confirm-overlay");
  const titleEl = document.getElementById("confirm-modal-title");
  const msgEl = document.getElementById("confirm-modal-message");
  const cancelBtn = document.getElementById("btn-confirm-cancel");
  const submitBtn = document.getElementById("btn-confirm-submit");
  const iconContainer = document.getElementById("confirm-icon-container");

  if (!overlay || !submitBtn) {
    if (confirm(message)) onConfirm();
    return;
  }

  if (titleEl) titleEl.textContent = title;
  if (msgEl) msgEl.textContent = message;
  if (submitBtn) {
    submitBtn.textContent = confirmText || "Lanjutkan";
    submitBtn.className = isDanger ? "btn-confirm-danger" : "btn-primary-black";
  }
  if (iconContainer) {
    iconContainer.className = isDanger
      ? "confirm-icon-box confirm-icon-danger"
      : "confirm-icon-box confirm-icon-info";
  }

  // Lock body scroll
  document.body.style.overflow = "hidden";
  overlay.classList.add("active");

  if (cancelBtn) cancelBtn.focus();

  const handleConfirm = () => {
    cleanup();
    if (typeof onConfirm === "function") onConfirm();
  };

  const handleCancel = () => {
    cleanup();
  };

  const handleKeyDown = (e) => {
    if (e.key === "Escape") {
      cleanup();
    } else if (e.key === "Tab") {
      // Focus trap between Cancel and Submit
      const focusables = [cancelBtn, submitBtn];
      const first = focusables[0];
      const last = focusables[1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  };

  function cleanup() {
    overlay.classList.remove("active");
    document.body.style.overflow = "";
    submitBtn.removeEventListener("click", handleConfirm);
    cancelBtn.removeEventListener("click", handleCancel);
    document.removeEventListener("keydown", handleKeyDown);
  }

  submitBtn.addEventListener("click", handleConfirm);
  cancelBtn.addEventListener("click", handleCancel);
  document.addEventListener("keydown", handleKeyDown);
};

/** Update sidebar & floating nav live counter badges */
window.updateSidebarCounters = function () {
  const quizzesCount = Array.isArray(window.NETQUIZ_QUIZZES)
    ? window.NETQUIZ_QUIZZES.length
    : 0;
  const membersCount = Array.isArray(window.NETQUIZ_MEMBERS)
    ? window.NETQUIZ_MEMBERS.length
    : 0;
  const materialsCount = Array.isArray(window.NETQUIZ_MATERIALS)
    ? window.NETQUIZ_MATERIALS.length
    : 0;
  const badgesCount = Array.isArray(window.NETQUIZ_BADGES)
    ? window.NETQUIZ_BADGES.length
    : 0;

  const elQ = document.querySelectorAll(
    "#sidebar-count-quizzes, #floating-count-quizzes",
  );
  const elM = document.querySelectorAll(
    "#sidebar-count-members, #floating-count-members",
  );
  const elMat = document.querySelectorAll(
    "#sidebar-count-materials, #floating-count-materials",
  );
  const elB = document.querySelectorAll(
    "#sidebar-count-badges, #floating-count-badges",
  );

  elQ.forEach((el) => (el.textContent = quizzesCount));
  elM.forEach((el) => (el.textContent = membersCount));
  elMat.forEach((el) => (el.textContent = materialsCount));
  elB.forEach((el) => (el.textContent = badgesCount));
};

/** Helper to bind show/hide password eye buttons */
window.bindPasswordToggles = function () {
  const toggleBtns = document.querySelectorAll(".btn-toggle-password");
  toggleBtns.forEach((btn) => {
    if (btn.getAttribute("data-bound")) return;
    btn.setAttribute("data-bound", "true");

    btn.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const input = document.getElementById(targetId);
      if (!input) return;

      if (input.type === "password") {
        input.type = "text";
        this.innerHTML =
          '<i data-lucide="eye-off" style="width: 15px; height: 15px;"></i>';
        this.title = "Sembunyikan Kata Sandi";
      } else {
        input.type = "password";
        this.innerHTML =
          '<i data-lucide="eye" style="width: 15px; height: 15px;"></i>';
        this.title = "Lihat Kata Sandi";
      }
      if (window.lucide) window.lucide.createIcons();
    });
  });
};

//** Global Helper to Open and Close Quiz Studio */
window.openQuizStudio = function () {
  if (window.switchQuizView) {
    window.switchQuizView("create");
  }
  const titleInput = document.getElementById("quiz-input-title");
  if (titleInput) {
    titleInput.focus();
    titleInput.scrollIntoView({ behavior: "smooth", block: "center" });
  }
};

window.closeQuizStudio = function () {
  if (window.switchQuizView) {
    window.switchQuizView("list");
  }
};

/** Global Helper to Open and Close Material Form */
window.openMaterialForm = function () {
  const container = document.getElementById("material-form-container");
  if (container) {
    container.style.display = "block";
    const titleInput = document.getElementById("material-input-title");
    if (titleInput) titleInput.focus();
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};

window.closeMaterialForm = function () {
  const container = document.getElementById("material-form-container");
  if (container) {
    container.style.display = "none";
  }
};

/** Global Helper to Open and Close Badge Form */
window.openBadgeForm = function () {
  const container = document.getElementById("badge-form-container");
  if (container) {
    container.style.display = "block";
    const titleInput = container.querySelector('input[name="title"]');
    if (titleInput) titleInput.focus();
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};

window.closeBadgeForm = function () {
  const container = document.getElementById("badge-form-container");
  if (container) {
    container.style.display = "none";
  }
};

/** Global Helper to Open and Close Edit Member Modal */
window.openEditMemberModal = function (id, username, email) {
  const modal = document.getElementById("edit-member-modal");
  const form = document.getElementById("edit-member-form");
  if (!modal || !form) return;

  form.action = `${window.BASE_URL}/admin/users/update/${id}`;
  const unInput = document.getElementById("edit-member-username");
  const emInput = document.getElementById("edit-member-email");
  const pwInput = document.getElementById("edit-member-password");
  if (unInput) unInput.value = username || "";
  if (emInput) emInput.value = email || "";
  if (pwInput) pwInput.value = "";

  modal.classList.add("active");
  modal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";

  if (unInput) setTimeout(() => unInput.focus(), 50);
  if (window.bindPasswordToggles) window.bindPasswordToggles();
  if (window.lucide) window.lucide.createIcons();
};

window.closeEditMemberModal = function () {
  const modal = document.getElementById("edit-member-modal");
  if (!modal) return;
  modal.classList.remove("active");
  modal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
};

// Global backdrop click & escape key listener for edit modal
document.addEventListener("DOMContentLoaded", () => {
  const editModal = document.getElementById("edit-member-modal");
  if (editModal) {
    editModal.addEventListener("click", (e) => {
      if (e.target === editModal) closeEditMemberModal();
    });
  }
  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const activeModal = document.querySelector(".admin-modal-overlay.active");
      if (activeModal && activeModal.id === "edit-member-modal") {
        closeEditMemberModal();
      }
    }
  });
});

// ==========================================================================
// 2. DOCUMENT INITIALIZATION
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  // Render all 5 workspace module sections
  renderQuizSection();
  renderMemberSection();
  renderManageMemberSection();
  renderMaterialsSection();
  renderBadgeSection();

  // Update Sidebar counters
  updateSidebarCounters();

  // Bind eye icons
  bindPasswordToggles();

  // Global keyboard shortcut '/' for search
  document.addEventListener("keydown", (e) => {
    if (
      e.key === "/" &&
      !["INPUT", "TEXTAREA"].includes(document.activeElement.tagName)
    ) {
      e.preventDefault();
      const activeSection = document.querySelector(
        ".admin-section-content.active",
      );
      if (activeSection) {
        const searchInput = activeSection.querySelector(".panel-search-input");
        if (searchInput) {
          searchInput.focus();
          searchInput.select();
        }
      }
    }
  });

  // Re-initialize Lucide Icons
  if (window.lucide) {
    window.lucide.createIcons();
  }
});

// ==========================================================================
// 3. MODUL 1: BUAT KUIS (Dedicated Studio View & Quizzes Table)
// ==========================================================================
function renderQuizSection() {
  const sec = document.getElementById("quiz-section");
  if (!sec) return;

  const rawQuizzes = Array.isArray(window.NETQUIZ_QUIZZES)
    ? window.NETQUIZ_QUIZZES
    : [];

  sec.innerHTML = `
        <!-- Top Segmented View Switcher -->
        <div class="quiz-segmented-switcher-bar">
            <div class="quiz-segmented-control">
                <button type="button" class="quiz-segment-tab active" id="tab-btn-quiz-create" onclick="window.switchQuizView('create')">
                    <i data-lucide="plus-circle" style="width: 15px; height: 15px;"></i>
                    <span>Buat Kuis Baru</span>
                </button>
                <button type="button" class="quiz-segment-tab" id="tab-btn-quiz-list" onclick="window.switchQuizView('list')">
                    <i data-lucide="list" style="width: 15px; height: 15px;"></i>
                    <span>Daftar Kuis (${rawQuizzes.length})</span>
                </button>
            </div>
        </div>

        <!-- VIEW 1: DEDICATED QUIZ CREATION STUDIO FORM -->
        <div id="quiz-view-create" class="quiz-subview-panel active">
            <div class="supabase-panel-card">
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <form id="form-create-quiz" action="${window.BASE_URL}/admin/quizzes" method="POST" style="padding: 1.75rem;">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">

                    
                    <!-- 1. Metadata Kuis -->
                    <div class="form-grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Judul Kuis</label>
                            <input type="text" class="form-field-input" name="title" id="quiz-input-title" placeholder="Misal: OSPF Routing Dasar" required>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Kategori</label>
                            <select class="panel-select" name="category" id="quiz-input-category" style="width: 100%;" required>
                                <option value="Routing">Routing</option>
                                <option value="Security">Firewall & Security</option>
                                <option value="Wireless">Wireless</option>
                                <option value="Network Management">Network Management</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Durasi (Menit)</label>
                            <input type="number" class="form-field-input" name="duration" id="quiz-input-duration" value="15" min="1" max="180" required>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Tingkat Kesulitan</label>
                            <select class="panel-select" name="difficulty" id="quiz-input-difficulty" style="width: 100%;" required>
                                <option value="Mudah">Mudah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Sulit">Sulit</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field-group" style="margin-bottom: 1.75rem;">
                        <label class="form-field-label">Deskripsi</label>
                        <textarea class="form-field-input" name="description" id="quiz-input-desc" rows="2" placeholder="Deskripsi singkat mengenai materi kuis ini..." required></textarea>
                    </div>

                    <!-- 2. Question Repeater -->
                    <div style="padding-top: 1.5rem; border-top: 1px dashed #222222;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                            <div>
                                <h4 style="font-family: var(--font-heading); font-size: 1rem; font-weight: 800; color: #ffffff; margin: 0;">
                                    Daftar Soal (<span id="quiz-question-counter">1</span>)
                                </h4>
                                <p style="font-size: 0.775rem; color: #a1a1aa; margin-top: 0.2rem;">Klik tanda radio pada pilihan untuk menentukan kunci jawaban yang benar.</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <!-- Hidden File Input for JSON Upload -->
                                <input type="file" id="import-quiz-json-input" accept=".json,application/json" style="display: none;">
                                
                                <button type="button" class="btn-secondary-outline" onclick="window.downloadQuizJsonTemplate()" title="Unduh format template kuis JSON" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <i data-lucide="download" style="width: 13px; height: 13px;"></i>
                                    <span>Template JSON</span>
                                </button>

                                <button type="button" class="btn-secondary-outline" onclick="document.getElementById('import-quiz-json-input').click()" title="Unggah kuis dari file format JSON" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <i data-lucide="upload" style="width: 13px; height: 13px;"></i>
                                    <span>Upload JSON</span>
                                </button>

                                <button type="button" id="btn-add-quiz-question" class="btn-secondary-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                    <span>Tambah Soal</span>
                                </button>
                            </div>
                        </div>

                        <!-- Question Repeater Stack -->
                        <div id="quiz-questions-repeater-stack" class="quiz-question-repeater-stack">
                        </div>
                    </div>

                    <!-- Bottom Studio Actions -->
                    <div class="quiz-form-bottom-bar" style="display: flex; align-items: center; justify-content: space-between; margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid #222222; flex-wrap: wrap; gap: 1rem;">
                        <button type="button" id="btn-add-another-question" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.5rem 1rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                            <span>Tambah Soal</span>
                        </button>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" onclick="window.switchQuizView('list')" class="btn-secondary-outline" style="padding: 0.5rem 1rem;">Batal</button>
                            <button type="submit" class="btn-primary-black" style="padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                                <span>Simpan Kuis</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- VIEW 2: QUIZZES DATA TABLE -->
        <div id="quiz-view-list" class="quiz-subview-panel" style="display: none;">
            <div class="supabase-panel-card" style="margin-bottom: 2rem;">
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <!-- Toolbar & Search -->
                <div class="panel-toolbar">
                    <div class="panel-toolbar-left" style="width: 100%;">
                        <div class="search-input-wrapper" style="max-width: 360px;">
                            <input type="text" id="quiz-search-input" class="panel-search-input" placeholder="Cari judul atau kategori kuis...">
                            <span class="search-shortcut-badge" title="Tekan '/' untuk mencari">/</span>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="panel-table-container">
                    <table class="supabase-data-table">
                        <thead>
                            <tr>
                                <th>JUDUL KUIS</th>
                                <th>KATEGORI</th>
                                <th>KESULITAN</th>
                                <th>DURASI</th>
                                <th style="text-align: right; width: 90px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="quiz-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

  const qTbody = document.getElementById("quiz-table-body");
  const qSearch = document.getElementById("quiz-search-input");
  const repeaterStack = document.getElementById(
    "quiz-questions-repeater-stack",
  );
  const counterEl = document.getElementById("quiz-question-counter");

  let questionCount = 0;

  function createQuestionBlock(index) {
    const card = document.createElement("div");
    card.className = "quiz-question-card";
    card.setAttribute("data-index", index);
    card.innerHTML = `
            <div class="q-card-top-bar">
                <span class="q-number-pill">Soal #${index + 1}</span>
                <button type="button" class="btn-remove-question" title="Hapus butir soal ini" onclick="removeQuestionBlock(${index})">
                    <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    <span>Hapus</span>
                </button>
            </div>
            
            <div class="form-field-group" style="margin-bottom: 1rem;">
                <label class="form-field-label">Pertanyaan</label>
                <textarea class="form-field-input" name="questions[${index}][question]" rows="2" placeholder="Tulis pertanyaan soal di sini..." required></textarea>
            </div>

            <!-- Integrated Radio Option Stack -->
            <div class="q-options-stack">
                <label class="form-field-label" style="margin-bottom: 0.4rem;">Pilihan Jawaban</label>
                
                <div class="q-option-row">
                    <label class="q-correct-radio-label" title="Tandai Pilihan A sebagai Kunci Jawaban Benar">
                        <input type="radio" name="questions[${index}][correct]" value="A" checked class="q-correct-radio">
                        <span class="q-option-badge">A</span>
                    </label>
                    <input type="text" class="form-field-input q-option-input" name="questions[${index}][option_a]" placeholder="Pilihan A" required>
                </div>

                <div class="q-option-row">
                    <label class="q-correct-radio-label" title="Tandai Pilihan B sebagai Kunci Jawaban Benar">
                        <input type="radio" name="questions[${index}][correct]" value="B" class="q-correct-radio">
                        <span class="q-option-badge">B</span>
                    </label>
                    <input type="text" class="form-field-input q-option-input" name="questions[${index}][option_b]" placeholder="Pilihan B" required>
                </div>

                <div class="q-option-row">
                    <label class="q-correct-radio-label" title="Tandai Pilihan C sebagai Kunci Jawaban Benar">
                        <input type="radio" name="questions[${index}][correct]" value="C" class="q-correct-radio">
                        <span class="q-option-badge">C</span>
                    </label>
                    <input type="text" class="form-field-input q-option-input" name="questions[${index}][option_c]" placeholder="Pilihan C" required>
                </div>

                <div class="q-option-row">
                    <label class="q-correct-radio-label" title="Tandai Pilihan D sebagai Kunci Jawaban Benar">
                        <input type="radio" name="questions[${index}][correct]" value="D" class="q-correct-radio">
                        <span class="q-option-badge">D</span>
                    </label>
                    <input type="text" class="form-field-input q-option-input" name="questions[${index}][option_d]" placeholder="Pilihan D" required>
                </div>
            </div>

            <!-- Optional Explanation Toggle -->
            <div class="q-extra-toggle-box">
                <button type="button" class="btn-toggle-optional" onclick="window.toggleQuestionExtra(${index})">
                    <i data-lucide="help-circle" style="width: 14px; height: 14px;"></i>
                    <span id="q-extra-label-${index}">+ Tambah Pembahasan</span>
                </button>
                <div class="q-extra-content" id="q-extra-${index}" style="display: none; margin-top: 0.6rem;">
                    <input type="text" class="form-field-input" name="questions[${index}][explanation]" placeholder="Penjelasan jawaban (opsional)">
                </div>
            </div>
        `;
    return card;
  }

  function syncQuestionBlocks() {
    if (!repeaterStack) return;
    const cards = repeaterStack.querySelectorAll(".quiz-question-card");
    questionCount = cards.length;
    if (counterEl) counterEl.textContent = questionCount;

    cards.forEach((card, idx) => {
      card.setAttribute("data-index", idx);
      const pill = card.querySelector(".q-number-pill");
      if (pill) pill.textContent = `Soal #${idx + 1}`;

      // Update input names
      const qText = card.querySelector('textarea[name*="[question]"]');
      if (qText) qText.name = `questions[${idx}][question]`;

      const optA = card.querySelector('input[name*="[option_a]"]');
      if (optA) optA.name = `questions[${idx}][option_a]`;

      const optB = card.querySelector('input[name*="[option_b]"]');
      if (optB) optB.name = `questions[${idx}][option_b]`;

      const optC = card.querySelector('input[name*="[option_c]"]');
      if (optC) optC.name = `questions[${idx}][option_c]`;

      const optD = card.querySelector('input[name*="[option_d]"]');
      if (optD) optD.name = `questions[${idx}][option_d]`;

      const radios = card.querySelectorAll(
        'input[type="radio"][name*="[correct]"]',
      );
      radios.forEach((r) => (r.name = `questions[${idx}][correct]`));

      const exp = card.querySelector('input[name*="[explanation]"]');
      if (exp) exp.name = `questions[${idx}][explanation]`;

      const extraBox = card.querySelector(".q-extra-content");
      if (extraBox) extraBox.id = `q-extra-${idx}`;

      const extraLabel = card.querySelector('[id^="q-extra-label-"]');
      if (extraLabel) extraLabel.id = `q-extra-label-${idx}`;

      const toggleBtn = card.querySelector(".btn-toggle-optional");
      if (toggleBtn)
        toggleBtn.setAttribute("onclick", `window.toggleQuestionExtra(${idx})`);

      // Disable delete if only 1 question remains
      const delBtn = card.querySelector(".btn-remove-question");
      if (delBtn) {
        delBtn.disabled = cards.length <= 1;
        delBtn.setAttribute("onclick", `removeQuestionBlock(${idx})`);
      }
    });

    if (window.lucide) window.lucide.createIcons();
  }

  window.toggleQuestionExtra = function (idx) {
    const extraContent = document.getElementById(`q-extra-${idx}`);
    const extraLabel = document.getElementById(`q-extra-label-${idx}`);
    if (!extraContent) return;
    if (extraContent.style.display === "none") {
      extraContent.style.display = "block";
      if (extraLabel) extraLabel.textContent = "- Sembunyikan Pembahasan";
      const input = extraContent.querySelector("input");
      if (input) input.focus();
    } else {
      extraContent.style.display = "none";
      if (extraLabel) extraLabel.textContent = "+ Tambah Pembahasan";
    }
  };

  window.addQuestionBlock = function () {
    if (!repeaterStack) return;
    const newCard = createQuestionBlock(questionCount);
    repeaterStack.appendChild(newCard);
    syncQuestionBlocks();
    const firstInput = newCard.querySelector("textarea");
    if (firstInput) firstInput.focus();
  };

  window.removeQuestionBlock = function (idx) {
    if (!repeaterStack) return;
    const cards = repeaterStack.querySelectorAll(".quiz-question-card");
    if (cards.length <= 1) {
      if (window.showGeistToast) {
        window.showGeistToast(
          "info",
          "Minimal 1 Soal",
          "Kuis wajib memiliki setidaknya 1 butir pertanyaan.",
        );
      }
      return;
    }
    if (cards[idx]) {
      cards[idx].remove();
      syncQuestionBlocks();
    }
  };

  // Initialize with 1 default question
  if (repeaterStack && repeaterStack.children.length === 0) {
    repeaterStack.appendChild(createQuestionBlock(0));
    syncQuestionBlocks();
  }

  const btnAddQ = document.getElementById("btn-add-quiz-question");
  const btnAddQ2 = document.getElementById("btn-add-another-question");
  if (btnAddQ) btnAddQ.addEventListener("click", window.addQuestionBlock);
  if (btnAddQ2) btnAddQ2.addEventListener("click", window.addQuestionBlock);

  // --- JSON QUIZ TEMPLATE DOWNLOAD & IMPORT LOGIC ---
  window.downloadQuizJsonTemplate = function () {
    const template = {
      title: "Contoh Kuis MikroTik OSPF Routing",
      category: "Routing",
      duration: 15,
      difficulty: "Mudah",
      description: "Ujian pemahaman dasar routing OSPF dan gateway.",
      questions: [
        {
          question:
            "Protokol routing manakah yang menggunakan algoritma link-state?",
          option_a: "RIP",
          option_b: "OSPF",
          option_c: "BGP",
          option_d: "Static Route",
          correct: "B",
          explanation:
            "OSPF (Open Shortest Path First) menggunakan algoritma link-state (Dijkstra).",
        },
        {
          question:
            "Berapakah nilai default administrative distance untuk OSPF pada RouterOS?",
          option_a: "110",
          option_b: "120",
          option_c: "90",
          option_d: "200",
          correct: "A",
          explanation:
            "Nilai default administrative distance untuk OSPF pada RouterOS adalah 110.",
        },
      ],
    };

    const blob = new Blob([JSON.stringify(template, null, 2)], {
      type: "application/json",
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "template_kuis.json";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  const jsonUploadInput = document.getElementById("import-quiz-json-input");
  if (jsonUploadInput) {
    jsonUploadInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (evt) {
        try {
          const parsed = JSON.parse(evt.target.result);
          let questionsArray = [];

          if (parsed && typeof parsed === "object" && !Array.isArray(parsed)) {
            // Populate Quiz Metadata if provided
            if (parsed.title) {
              const el = document.getElementById("quiz-input-title");
              if (el) el.value = parsed.title;
            }
            if (parsed.category) {
              const el = document.getElementById("quiz-input-category");
              if (el) el.value = parsed.category;
            }
            if (parsed.duration) {
              const el = document.getElementById("quiz-input-duration");
              if (el) el.value = parsed.duration;
            }
            if (parsed.difficulty) {
              const el = document.getElementById("quiz-input-difficulty");
              if (el) el.value = parsed.difficulty;
            }
            if (parsed.description) {
              const el = document.getElementById("quiz-input-desc");
              if (el) el.value = parsed.description;
            }
            if (Array.isArray(parsed.questions)) {
              questionsArray = parsed.questions;
            }
          } else if (Array.isArray(parsed)) {
            questionsArray = parsed;
          } else {
            throw new Error(
              "Format JSON harus berupa objek kuis atau array daftar pertanyaan.",
            );
          }

          if (questionsArray.length === 0) {
            throw new Error(
              "File JSON tidak memiliki butir pertanyaan yang valid.",
            );
          }

          // Clear repeater and populate new questions
          if (repeaterStack) {
            repeaterStack.innerHTML = "";
            questionsArray.forEach((item, idx) => {
              const qText = item.question || item.pertanyaan || "";
              const optA = item.option_a || item.pilihan_a || item.a || "";
              const optB = item.option_b || item.pilihan_b || item.b || "";
              const optC = item.option_c || item.pilihan_c || item.c || "";
              const optD = item.option_d || item.pilihan_d || item.d || "";
              let correct = (item.correct || item.kunci || item.jawaban || "A")
                .toUpperCase()
                .trim();
              if (!["A", "B", "C", "D"].includes(correct)) correct = "A";
              const exp =
                item.explanation || item.pembahasan || item.penjelasan || "";

              const block = createQuestionBlock(idx);
              const qInput = block.querySelector(
                'textarea[name*="[question]"]',
              );
              const aInput = block.querySelector('input[name*="[option_a]"]');
              const bInput = block.querySelector('input[name*="[option_b]"]');
              const cInput = block.querySelector('input[name*="[option_c]"]');
              const dInput = block.querySelector('input[name*="[option_d]"]');
              const expInput = block.querySelector(
                'input[name*="[explanation]"]',
              );
              const radio = block.querySelector(
                `input[type="radio"][value="${correct}"]`,
              );

              if (qInput) qInput.value = qText;
              if (aInput) aInput.value = optA;
              if (bInput) bInput.value = optB;
              if (cInput) cInput.value = optC;
              if (dInput) dInput.value = optD;
              if (radio) radio.checked = true;
              if (exp && expInput) {
                expInput.value = exp;
                const extraContent = block.querySelector(".q-extra-content");
                const extraLabel = block.querySelector(
                  '[id^="q-extra-label-"]',
                );
                if (extraContent) extraContent.style.display = "block";
                if (extraLabel)
                  extraLabel.textContent = "- Sembunyikan Pembahasan";
              }

              repeaterStack.appendChild(block);
            });
            syncQuestionBlocks();
          }

          if (window.showGeistToast) {
            window.showGeistToast(
              "success",
              "Import JSON Berhasil",
              `Berhasil memuat ${questionsArray.length} butir soal.`,
            );
          } else {
            alert(
              `Berhasil memuat ${questionsArray.length} butir soal dari file JSON.`,
            );
          }
        } catch (err) {
          if (window.showGeistToast) {
            window.showGeistToast(
              "error",
              "Gagal Membaca File JSON",
              err.message,
            );
          } else {
            alert("Gagal membaca file JSON: " + err.message);
          }
        } finally {
          jsonUploadInput.value = "";
        }
      };
      reader.readAsText(file);
    });
  }

  // Switch between Creation Studio View and Quizzes Table View
  window.switchQuizView = function (view) {
    const viewCreate = document.getElementById("quiz-view-create");
    const viewList = document.getElementById("quiz-view-list");
    const tabCreate = document.getElementById("tab-btn-quiz-create");
    const tabList = document.getElementById("tab-btn-quiz-list");

    if (view === "create") {
      if (viewCreate) viewCreate.style.display = "block";
      if (viewList) viewList.style.display = "none";
      if (tabCreate) tabCreate.classList.add("active");
      if (tabList) tabList.classList.remove("active");
      const titleInput = document.getElementById("quiz-input-title");
      if (titleInput) titleInput.focus();
    } else {
      if (viewCreate) viewCreate.style.display = "none";
      if (viewList) viewList.style.display = "block";
      if (tabCreate) tabCreate.classList.remove("active");
      if (tabList) tabList.classList.add("active");
    }
    if (window.lucide) window.lucide.createIcons();
  };

  window.openQuizStudio = function () {
    window.switchQuizView("create");
  };

  function renderQuizRows(list) {
    if (!qTbody) return;
    if (list.length === 0) {
      qTbody.innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="panel-empty-state">
                            <div class="empty-state-icon-box">
                                <i data-lucide="file-question" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div class="empty-state-title">Belum Ada Kuis</div>
                            <div class="empty-state-text">Mulai buat kuis baru untuk menambahkan ujian pembelajaran.</div>
                        </div>
                    </td>
                </tr>
            `;
      if (window.lucide) window.lucide.createIcons();
      return;
    }

    qTbody.innerHTML = list
      .map(
        (q) => `
            <tr>
                <td style="font-weight: 700; color: #ffffff;">${escapeHtml(q.title)}</td>
                <td><span class="role-pill">${escapeHtml(q.category || "Routing")}</span></td>
                <td><span class="status-badge status-active">${escapeHtml(q.difficulty || "Mudah")}</span></td>
                <td class="font-mono text-muted">${q.duration || 15} Menit</td>
                <td style="text-align: right;">
                    <div class="table-actions-group">
                        <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Kuis" onclick="confirmDeleteQuiz(${q.id}, '${escapeHtml(q.title).replace(/'/g, "\\'")}')">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("");

    if (window.lucide) window.lucide.createIcons();
  }

  renderQuizRows(rawQuizzes);

  // Live Quiz Search Filter
  if (qSearch) {
    qSearch.addEventListener("input", (e) => {
      const query = e.target.value.toLowerCase().trim();
      const filtered = rawQuizzes.filter(
        (q) =>
          (q.title && q.title.toLowerCase().includes(query)) ||
          (q.category && q.category.toLowerCase().includes(query)),
      );
      renderQuizRows(filtered);
    });
  }

  window.confirmDeleteQuiz = function (id, title) {
    showGeistConfirm(
      "Hapus Kuis",
      `Apakah Anda yakin ingin menghapus kuis "${title}"? Seluruh data pertanyaan di dalamnya akan ikut terhapus.`,
      "Hapus Kuis",
      () => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `${window.BASE_URL}/admin/quizzes/delete/${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">`;
        document.body.appendChild(form);
        form.submit();
      },
      true,
    );
  };
}

// ==========================================================================
// 4. MODUL 2: DAFTARKAN MEMBER (Focused Single Registration Form)
// ==========================================================================
function renderMemberSection() {
  const sec = document.getElementById("member-section");
  if (!sec) return;

  sec.innerHTML = `
        <div class="supabase-panel-card" style="margin-bottom: 2rem;">
            <!-- Precision Corner Crosshairs -->
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <form action="${window.BASE_URL}/admin/users/create" method="POST" id="register-member-form" style="padding: 1.75rem;">
                <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">
                
                <!-- Section Header -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #222222;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #ffffff; margin: 0;">Daftarkan Member Baru</h3>
                    <p style="font-size: 0.825rem; color: #a1a1aa; margin-top: 0.25rem;">Buat akun anggota baru untuk mengakses kuis dan materi pembelajaran.</p>
                </div>

                <!-- 2x2 Form Grid -->
                <div class="form-grid-2col" style="margin-bottom: 1.25rem;">
                    <!-- Field 1: Username -->
                    <div class="form-field-group">
                        <label class="form-field-label">Username</label>
                        <input type="text" class="form-field-input" name="username" id="reg-username" placeholder="budi_santoso" required autocomplete="off">
                    </div>

                    <!-- Field 2: Email -->
                    <div class="form-field-group">
                        <label class="form-field-label">Email</label>
                        <input type="email" class="form-field-input" name="email" id="reg-email" placeholder="budi@sekolah.sch.id" required autocomplete="off">
                    </div>
                </div>

                <div class="form-grid-2col" style="margin-bottom: 1.75rem;">
                    <!-- Field 3: Password -->
                    <div class="form-field-group">
                        <label class="form-field-label">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-field-input" name="password" id="reg-password" placeholder="Minimal 8 karakter" required minlength="8">
                            <button type="button" class="btn-toggle-password" data-target="reg-password" title="Lihat/Sembunyikan Password">
                                <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Field 4: Konfirmasi Password -->
                    <div class="form-field-group">
                        <label class="form-field-label">Konfirmasi Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-field-input" name="confirm_password" id="reg-confirm-password" placeholder="Ulangi password" required minlength="8">
                            <button type="button" class="btn-toggle-password" data-target="reg-confirm-password" title="Lihat/Sembunyikan Password">
                                <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid #222222;">
                    <button type="reset" class="btn-secondary-outline" style="padding: 0.5rem 1rem;">Batal</button>
                    <button type="submit" class="btn-primary-black" id="btn-submit-member" style="padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <i data-lucide="user-plus" style="width: 15px; height: 15px;"></i>
                        <span>Daftarkan Member</span>
                    </button>
                </div>
            </form>
        </div>
    `;

  const regPwdInput = document.getElementById("reg-password");
  const confirmPwdInput = document.getElementById("reg-confirm-password");
  const regForm = document.getElementById("register-member-form");

  // Bind Eye Toggles
  if (window.bindPasswordToggles) {
    window.bindPasswordToggles();
  }

  // Form Submit Validation
  if (regForm) {
    regForm.addEventListener("submit", (e) => {
      const pwd = regPwdInput ? regPwdInput.value : "";
      const cfm = confirmPwdInput ? confirmPwdInput.value : "";
      if (pwd !== cfm) {
        e.preventDefault();
        if (window.showGeistToast) {
          showGeistToast(
            "error",
            "Validasi Gagal",
            "Password dan Konfirmasi Password tidak cocok!",
          );
        } else {
          alert("Password dan Konfirmasi Password tidak cocok!");
        }
        if (confirmPwdInput) confirmPwdInput.focus();
        return false;
      }
      if (pwd.length < 8) {
        e.preventDefault();
        if (window.showGeistToast) {
          showGeistToast(
            "error",
            "Password Terlalu Pendek",
            "Password minimal harus 8 karakter.",
          );
        } else {
          alert("Password minimal harus 8 karakter.");
        }
        if (regPwdInput) regPwdInput.focus();
        return false;
      }
    });
  }

  if (window.lucide) window.lucide.createIcons();
}

// ==========================================================================
// 5. MODUL 3: MANAJEMEN MEMBER (High-Density Data Table & Floating Bulk Bar)
// ==============================================================================
function renderManageMemberSection() {
  const sec = document.getElementById("manage-section");
  if (!sec) return;

  const rawMembers = Array.isArray(window.NETQUIZ_MEMBERS)
    ? window.NETQUIZ_MEMBERS
    : [];

  sec.innerHTML = `
        <div class="supabase-panel-card">
            <!-- Precision Corner Crosshairs -->
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <!-- Top Toolbar (Anti-Slop Clean Input with Shortcut Badge) -->
            <div class="panel-toolbar">
                <div class="panel-toolbar-left">
                    <div class="search-input-wrapper">
                        <input type="text" id="member-search-input" class="panel-search-input" placeholder="Cari nama, email, atau username member...">
                        <span class="search-shortcut-badge" title="Tekan '/' pada keyboard untuk mencari">/</span>
                    </div>
                </div>
                <div class="panel-toolbar-right">
                    <div class="select-wrapper">
                        <select id="filter-role" class="panel-select">
                            <option value="all">Semua Role</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Siswa">Siswa</option>
                        </select>
                    </div>
                    <div class="select-wrapper">
                        <select id="filter-status" class="panel-select">
                            <option value="all">Semua Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <button type="button" class="btn-panel-outline" id="btn-export-csv" title="Export seluruh data member ke CSV">
                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div class="panel-table-container">
                <table class="supabase-data-table">
                    <thead>
                        <tr>
                            <th style="width: 44px; text-align: center;">
                                <input type="checkbox" class="panel-checkbox" id="select-all-members" title="Pilih Semua">
                            </th>
                            <th>MEMBER</th>
                            <th style="width: 130px;">ROLE</th>
                            <th style="width: 110px;">STATUS</th>
                            <th style="width: 140px;">TANGGAL BERGABUNG</th>
                            <th style="text-align: right; width: 110px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="member-table-body">
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pagination Bar -->
            <div class="panel-pagination-footer">
                <div class="pagination-info" id="pagination-info-text">
                    Menampilkan <span class="font-mono" style="font-weight: 700;">1-${rawMembers.length}</span> dari <span class="font-mono" style="font-weight: 700;">${rawMembers.length}</span> member
                </div>
                <div class="pagination-actions">
                    <button type="button" class="btn-pagination" disabled>Sebelumnya</button>
                    <button type="button" class="btn-pagination" disabled>Selanjutnya</button>
                </div>
            </div>
        </div>
    `;

  const tbody = document.getElementById("member-table-body");
  const searchInput = document.getElementById("member-search-input");
  const filterRole = document.getElementById("filter-role");
  const filterStatus = document.getElementById("filter-status");
  const selectAllCheckbox = document.getElementById("select-all-members");
  const floatingBulkBar = document.getElementById("floating-bulk-bar");
  const bulkCountBadge = document.getElementById("bulk-selected-count");
  const btnBulkDismiss = document.getElementById("btn-bulk-dismiss");
  const btnBulkExport = document.getElementById("btn-bulk-export");

  function renderRows(list) {
    if (!tbody) return;
    if (list.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="panel-empty-state">
                            <div class="empty-state-icon-box">
                                <i data-lucide="users" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div class="empty-state-title">Tidak Ada Member Ditemukan</div>
                            <div class="empty-state-text">Tidak ada data member yang cocok dengan kriteria pencarian Anda.</div>
                        </div>
                    </td>
                </tr>
            `;
      if (window.lucide) window.lucide.createIcons();
      return;
    }

    tbody.innerHTML = list
      .map((m) => {
        const initials = getInitials(m.username || m.email);
        const role =
          m.role || (m.email.includes("admin") ? "Administrator" : "Siswa");
        const dateStr = formatDate(m.created_at);
        const avatarColorClass = getAvatarBgClass(m.id || 1);
        const status = m.status || "Aktif";
        const statusClass =
          status === "Aktif"
            ? "status-active"
            : status === "Pending"
              ? "status-pending"
              : "status-inactive";
        const toggleStatus = status === "Aktif" ? "Nonaktif" : "Aktif";
        const toggleIcon = status === "Aktif" ? "user-x" : "user-check";
        const toggleTitle =
          status === "Aktif" ? "Nonaktifkan Member" : "Aktifkan Member";
        const safeUsername = escapeHtml(m.username).replace(/'/g, "\\'");
        const safeEmail = escapeHtml(m.email).replace(/'/g, "\\'");

        return `
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="panel-checkbox member-row-checkbox" value="${m.id}" data-username="${safeUsername}" data-email="${safeEmail}" data-status="${status}">
                    </td>
                    <td>
                        <div class="member-user-cell">
                            <div class="member-avatar ${avatarColorClass} font-mono">${escapeHtml(initials)}</div>
                            <div class="member-user-info">
                                <span class="member-name">${escapeHtml(m.username)}</span>
                                <span class="member-email font-mono">${escapeHtml(m.email)}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="role-pill ${role === "Administrator" ? "role-admin" : ""}">${escapeHtml(role)}</span></td>
                    <td><span class="status-badge ${statusClass}">${escapeHtml(status)}</span></td>
                    <td class="font-mono text-muted">${escapeHtml(dateStr)}</td>
                    <td style="text-align: right;">
                        <div class="table-actions-group">
                            <button type="button" class="btn-icon-action" title="Edit Member" onclick="openEditMemberModal(${m.id}, '${safeUsername}', '${safeEmail}')">
                                <i data-lucide="pencil"></i>
                            </button>
                            <form action="${window.BASE_URL}/admin/users/suspend/${m.id}" method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">
                                <input type="hidden" name="status" value="${toggleStatus}">
                                <button type="submit" class="btn-icon-action btn-action-toggle" title="${toggleTitle}">
                                    <i data-lucide="${toggleIcon}"></i>
                                </button>
                            </form>
                            <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Member" onclick="confirmDeleteMember(${m.id}, '${safeUsername}')">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
      })
      .join("");

    if (window.lucide) window.lucide.createIcons();

    // Update pagination info
    const paginationInfo = document.getElementById("pagination-info-text");
    if (paginationInfo) {
      paginationInfo.innerHTML = `Menampilkan <span class="font-mono" style="font-weight: 700;">1-${list.length}</span> dari <span class="font-mono" style="font-weight: 700;">${rawMembers.length}</span> member`;
    }

    bindRowCheckboxes();
  }

  renderRows(rawMembers);

  // Filter Logic with Debounce
  function filterData() {
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const roleVal = filterRole ? filterRole.value : "all";
    const statusVal = filterStatus ? filterStatus.value : "all";

    const filtered = rawMembers.filter((m) => {
      const matchQuery =
        (m.username && m.username.toLowerCase().includes(query)) ||
        (m.email && m.email.toLowerCase().includes(query));
      const mRole =
        m.role || (m.email.includes("admin") ? "Administrator" : "Siswa");
      const matchRole = roleVal === "all" || mRole === roleVal;
      const mStatus = m.status || "Aktif";
      const matchStatus = statusVal === "all" || mStatus === statusVal;

      return matchQuery && matchRole && matchStatus;
    });

    renderRows(filtered);
  }

  if (searchInput) searchInput.addEventListener("input", filterData);
  if (filterRole) filterRole.addEventListener("change", filterData);
  if (filterStatus) filterStatus.addEventListener("change", filterData);

  // Floating Bulk Action Bar Handlers
  function bindRowCheckboxes() {
    const rowCheckboxes = document.querySelectorAll(".member-row-checkbox");
    rowCheckboxes.forEach((cb) => {
      cb.addEventListener("change", updateBulkState);
    });
  }

  function updateBulkState() {
    const selected = document.querySelectorAll(".member-row-checkbox:checked");
    if (!floatingBulkBar || !bulkCountBadge) return;

    if (selected.length > 0) {
      bulkCountBadge.textContent = `${selected.length} Dipilih`;
      floatingBulkBar.classList.add("active");
    } else {
      floatingBulkBar.classList.remove("active");
      if (selectAllCheckbox) selectAllCheckbox.checked = false;
    }
  }

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const rowCheckboxes = document.querySelectorAll(".member-row-checkbox");
      rowCheckboxes.forEach((cb) => (cb.checked = this.checked));
      updateBulkState();
    });
  }

  if (btnBulkDismiss) {
    btnBulkDismiss.addEventListener("click", () => {
      const rowCheckboxes = document.querySelectorAll(".member-row-checkbox");
      rowCheckboxes.forEach((cb) => (cb.checked = false));
      if (selectAllCheckbox) selectAllCheckbox.checked = false;
      if (floatingBulkBar) floatingBulkBar.classList.remove("active");
    });
  }

  const btnBulkActivate = document.getElementById("btn-bulk-activate");
  const btnBulkSuspend = document.getElementById("btn-bulk-suspend");

  if (btnBulkActivate) {
    btnBulkActivate.addEventListener("click", () => {
      const selected = document.querySelectorAll(
        ".member-row-checkbox:checked",
      );
      if (selected.length === 0) return;
      showGeistToast(
        "success",
        "Aktivasi Berhasil",
        `${selected.length} member telah diaktifkan.`,
      );
      selected.forEach((cb) => {
        const row = cb.closest("tr");
        if (row) {
          const badge = row.querySelector(".status-badge");
          if (badge) {
            badge.className = "status-badge status-active";
            badge.textContent = "Aktif";
          }
        }
      });
      if (floatingBulkBar) floatingBulkBar.classList.remove("active");
      if (selectAllCheckbox) selectAllCheckbox.checked = false;
    });
  }

  if (btnBulkSuspend) {
    btnBulkSuspend.addEventListener("click", () => {
      const selected = document.querySelectorAll(
        ".member-row-checkbox:checked",
      );
      if (selected.length === 0) return;
      showGeistToast(
        "info",
        "Status Diperbarui",
        `${selected.length} member telah dinonaktifkan.`,
      );
      selected.forEach((cb) => {
        const row = cb.closest("tr");
        if (row) {
          const badge = row.querySelector(".status-badge");
          if (badge) {
            badge.className = "status-badge status-inactive";
            badge.textContent = "Nonaktif";
          }
        }
      });
      if (floatingBulkBar) floatingBulkBar.classList.remove("active");
      if (selectAllCheckbox) selectAllCheckbox.checked = false;
    });
  }

  // Bulk Export to CSV
  if (btnBulkExport) {
    btnBulkExport.addEventListener("click", () => {
      const selected = document.querySelectorAll(
        ".member-row-checkbox:checked",
      );
      if (selected.length === 0) return;

      let csv = "ID,Username,Email,Status\n";
      selected.forEach((cb) => {
        csv += `"${cb.value}","${cb.getAttribute("data-username")}","${cb.getAttribute("data-email")}","${cb.getAttribute("data-status")}"\n`;
      });

      downloadCsvFile(csv, "selected_members.csv");
      showGeistToast(
        "success",
        "Export Selesai",
        `${selected.length} data member terpilih berhasil diexport.`,
      );
    });
  }

  // Export All CSV
  const btnExportAll = document.getElementById("btn-export-csv");
  if (btnExportAll) {
    btnExportAll.addEventListener("click", () => {
      if (rawMembers.length === 0) {
        showGeistToast(
          "info",
          "Data Kosong",
          "Tidak ada member untuk diexport.",
        );
        return;
      }
      let csv = "ID,Username,Email,Role,Status,Tanggal Bergabung\n";
      rawMembers.forEach((m) => {
        csv += `"${m.id}","${m.username}","${m.email}","${m.role || "Siswa"}","${m.status || "Aktif"}","${m.created_at}"\n`;
      });
      downloadCsvFile(csv, "semua_member_netquiz.csv");
      showGeistToast(
        "success",
        "Export CSV Berhasil",
        `${rawMembers.length} member telah diunduh.`,
      );
    });
  }

  function downloadCsvFile(content, fileName) {
    const blob = new Blob([content], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  // Delete Member Dialog
  window.confirmDeleteMember = function (id, username) {
    showGeistConfirm(
      "Hapus Member",
      `Apakah Anda yakin ingin menghapus akun siswa "${username}"? Tindakan ini tidak dapat dibatalkan.`,
      "Hapus Member",
      () => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `${window.BASE_URL}/admin/users/delete/${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">`;
        document.body.appendChild(form);
        form.submit();
      },
      true,
    );
  };
}

// ==========================================================================
// 6. MODUL 4: MATERI BELAJAR (Segmented Studio & Live Preview Table)
// ==========================================================================
function renderMaterialsSection() {
  const sec = document.getElementById("materials-section");
  if (!sec) return;

  const materials = Array.isArray(window.NETQUIZ_MATERIALS)
    ? window.NETQUIZ_MATERIALS
    : [];

  sec.innerHTML = `
        <!-- Top Segmented Switcher -->
        <div class="quiz-segmented-switcher-bar" style="margin-bottom: 1.5rem;">
            <div class="quiz-segmented-control">
                <button type="button" id="tab-btn-material-create" class="quiz-segment-tab active" onclick="window.switchMaterialView('create')">
                    <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
                    <span>Tulis Materi Baru</span>
                </button>
                <button type="button" id="tab-btn-material-list" class="quiz-segment-tab" onclick="window.switchMaterialView('list')">
                    <i data-lucide="book-open" style="width: 14px; height: 14px;"></i>
                    <span>Daftar Materi (<span id="material-list-counter">${materials.length}</span>)</span>
                </button>
            </div>
        </div>

        <!-- VIEW 1: STUDIO FORM -->
        <div id="material-view-create" class="material-subview-panel">
            <div class="supabase-panel-card" style="margin-bottom: 2rem;">
                <!-- Precision Corner Crosshairs -->
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <form action="${window.BASE_URL}/admin/materials/create" method="POST" id="form-create-material" style="padding: 1.75rem;">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">
                    
                    <!-- Section Header -->
                    <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #222222;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #ffffff; margin: 0;">Tulis Artikel Materi Baru</h3>
                        <p style="font-size: 0.825rem; color: #a1a1aa; margin-top: 0.25rem;">Susun modul materi pembelajaran dan panduan konfigurasi MikroTik RouterOS.</p>
                    </div>

                    <!-- Field: Judul -->
                    <div class="form-field-group" style="margin-bottom: 1.25rem;">
                        <label class="form-field-label">Judul Materi</label>
                        <input type="text" class="form-field-input" name="title" id="material-input-title" placeholder="Contoh: Konfigurasi VLAN & Trunking pada MikroTik RouterOS" required autocomplete="off">
                    </div>

                    <!-- Grid 2-kolom: Kategori & Kesulitan -->
                    <div class="form-grid-2col" style="margin-bottom: 1.25rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Kategori</label>
                            <select class="panel-select" name="category" id="material-input-category" style="width: 100%;">
                                <option value="Routing">Routing</option>
                                <option value="Firewall & NAT">Firewall & NAT</option>
                                <option value="Wireless">Wireless</option>
                                <option value="Network Management">Network Management</option>
                            </select>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Tingkat Kesulitan</label>
                            <select class="panel-select" name="difficulty" id="material-input-difficulty" style="width: 100%;">
                                <option value="Mudah">Mudah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Sulit">Sulit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Editor Section Header & Mode Switcher -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div class="quiz-segmented-control" style="padding: 2px;">
                            <button type="button" class="quiz-segment-tab active" id="btn-mode-editor" style="padding: 4px 12px; font-size: 0.775rem;">
                                <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                <span>Editor Tulis</span>
                            </button>
                            <button type="button" class="quiz-segment-tab" id="btn-mode-preview" style="padding: 4px 12px; font-size: 0.775rem;">
                                <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                <span>Pratinjau Siswa (1:1)</span>
                            </button>
                            <button type="button" class="quiz-segment-tab" id="btn-mode-split" style="padding: 4px 12px; font-size: 0.775rem;">
                                <i data-lucide="columns" style="width: 13px; height: 13px;"></i>
                                <span>Berdampingan (Split)</span>
                            </button>
                        </div>
                        <span class="reading-time-pill" id="material-reading-time" style="font-size: 0.75rem; color: #a1a1aa; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                            <span>~1 menit baca</span>
                        </span>
                    </div>

                    <!-- Hidden JSON File Input for Material Upload -->
                    <input type="file" id="import-material-json-input" accept=".json,application/json" style="display: none;">

                    <!-- Comprehensive Studio Toolbar -->
                    <div class="editor-quick-toolbar" id="material-editor-toolbar" style="border-radius: 8px 8px 0 0;">
                        <!-- Group 1: Auto Format & Beautify -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn btn-accent" onclick="window.autoFormatMaterialContent()" title="Konversi otomatis teks / markdown / perintah CLI menjadi format HTML yang rapi">
                                <i data-lucide="sparkles" style="width: 12px; height: 12px;"></i>
                                <span>Auto Format</span>
                            </button>
                            <button type="button" class="toolbar-btn btn-primary-tool" onclick="window.formatHtmlContent()" title="Rapikan indentasi dan baris kode HTML">
                                <i data-lucide="align-left" style="width: 12px; height: 12px;"></i>
                                <span>Rapikan HTML</span>
                            </button>
                        </div>

                        <div class="toolbar-group-divider" aria-hidden="true"></div>

                        <!-- Group 2: Headings & Typography -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('h2')" title="Heading 2 (Judul Bab)">H2</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('h3')" title="Heading 3 (Sub-Bab)">H3</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('h4')" title="Heading 4 (Poin Penting)">H4</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('p')" title="Paragraf Teks">¶ P</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('bold')" title="Teks Tebal (Bold)"><b>B</b></button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('italic')" title="Teks Miring (Italic)"><i>I</i></button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('underline')" title="Garis Bawah (Underline)"><u>U</u></button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('strike')" title="Teks Coret (Strikethrough)"><s>S</s></button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('mark')" title="Teks Highlight (Marker)">Mark</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('code')" title="Inline Code">&#96;Code&#96;</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('kbd')" title="Tombol Keyboard">[Kbd]</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('hr')" title="Garis Pembatas (Divider)">── HR</button>
                        </div>

                        <div class="toolbar-group-divider" aria-hidden="true"></div>

                        <!-- Group 3: Lists & Tasks -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('ul')" title="Daftar Poin (Bullet List)">• List</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('ol')" title="Daftar Berurutan (Numbered List)">1. List</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('checklist')" title="Daftar Checklist (Task List)">☑ Task</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('dl')" title="Daftar Parameter / Key-Value">📋 Key-Val</button>
                        </div>

                        <div class="toolbar-group-divider" aria-hidden="true"></div>

                        <!-- Group 4: Code & Terminal CLI Blocks -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn btn-primary-tool" onclick="window.insertMaterialSnippet('cli_routeros')" title="Blok Terminal MikroTik RouterOS Lengkap">
                                <i data-lucide="terminal" style="width: 12px; height: 12px;"></i>
                                <span>RouterOS CLI</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('cli_bash')" title="Blok Terminal Linux Bash">
                                <i data-lucide="terminal" style="width: 12px; height: 12px;"></i>
                                <span>Linux Bash</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('cli_json')" title="Blok Kode Konfigurasi JSON">
                                <i data-lucide="file-code" style="width: 12px; height: 12px;"></i>
                                <span>JSON</span>
                            </button>
                        </div>

                        <div class="toolbar-group-divider" aria-hidden="true"></div>

                        <!-- Group 5: Callout Alerts -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('callout_note')" title="Kotak Catatan / Info (Biru)">💡 Info</button>
                            <button type="button" class="toolbar-btn btn-accent" onclick="window.insertMaterialSnippet('callout_tip')" title="Kotak Tips & Trik (Hijau)">✨ Tips</button>
                            <button type="button" class="toolbar-btn btn-warning-tool" onclick="window.insertMaterialSnippet('callout_warning')" title="Kotak Peringatan (Kuning)">⚠️ Peringatan</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('callout_danger')" title="Kotak Bahaya / Kritis (Merah)" style="color: #f87171;">🚨 Bahaya</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('callout_success')" title="Kotak Best Practice (Emerald)" style="color: #34d399;">✅ Best Practice</button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('quote')" title="Kutipan Referensi (Quote)">💬 Kutipan</button>
                        </div>

                        <div class="toolbar-group-divider" aria-hidden="true"></div>

                        <!-- Group 6: Rich Interactive Components -->
                        <div class="toolbar-group">
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('table')" title="Tabel Data Konfigurasi">
                                <i data-lucide="table" style="width: 12px; height: 12px;"></i>
                                <span>Tabel</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('step_guide')" title="Langkah Panduan Bertahap (Step 1, 2, 3)">
                                <i data-lucide="list-ordered" style="width: 12px; height: 12px;"></i>
                                <span>Step Guide</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('network_card')" title="Kartu Spesifikasi Jaringan (IP, Subnet, DNS)">
                                <i data-lucide="network" style="width: 12px; height: 12px;"></i>
                                <span>Network Card</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('details')" title="Kotak Buka-Tutup (Accordion FAQ)">
                                <i data-lucide="help-circle" style="width: 12px; height: 12px;"></i>
                                <span>Accordion</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('figure')" title="Gambar dengan Keterangan / Caption">
                                <i data-lucide="image" style="width: 12px; height: 12px;"></i>
                                <span>Gambar</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.insertMaterialSnippet('link')" title="Tautan / Link Eksternal">
                                <i data-lucide="link" style="width: 12px; height: 12px;"></i>
                                <span>Link</span>
                            </button>
                        </div>

                        <!-- Group 7: Utility Right Actions -->
                        <div style="margin-left: auto; display: flex; align-items: center; gap: 4px;">
                            <button type="button" class="toolbar-btn" onclick="window.copyMaterialHtml()" title="Salin seluruh kode HTML materi ke clipboard">
                                <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                <span>Salin HTML</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="window.clearMaterialEditor()" title="Kosongkan isi teks editor">
                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                <span>Bersihkan</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="downloadJsonTemplate()" title="Unduh Template JSON Materi">
                                <i data-lucide="download" style="width: 12px; height: 12px;"></i>
                                <span>Template JSON</span>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="document.getElementById('import-material-json-input').click()" title="Upload Materi dari Berkas JSON">
                                <i data-lucide="upload" style="width: 12px; height: 12px;"></i>
                                <span>Upload JSON</span>
                            </button>
                        </div>
                    </div>

                    <!-- Editor & Preview Containers (Supports Single Mode & Split Mode) -->
                    <div id="material-studio-container" style="margin-bottom: 1.5rem;">
                        <!-- Editor Textarea Shell -->
                        <div id="material-editor-wrapper" style="width: 100%;">
                            <div class="form-field-group" style="margin-bottom: 0;">
                                <textarea class="form-field-input" name="content" id="material-content-textarea" rows="12" placeholder="Tuliskan isi artikel materi atau gunakan fitur Auto Format di toolbar atas..." required style="border-top-left-radius: 0; border-top-right-radius: 0; min-height: 280px; font-family: var(--font-mono); font-size: 0.85rem; line-height: 1.65;"></textarea>
                            </div>
                        </div>

                        <!-- 100% Identical Student Reader Live Preview Box -->
                        <div id="material-preview-box" class="material-live-preview-box" style="display: none; width: 100%;">
                            <div id="material-preview-content-render" class="student-preview-shell"></div>
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; margin-top: 1.5rem; border-top: 1px solid #222222;">
                        <button type="button" onclick="window.switchMaterialView('list')" class="btn-secondary-outline" style="padding: 0.5rem 1rem;">Batal</button>
                        <button type="submit" class="btn-primary-black" style="padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                            <span>Simpan Materi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- VIEW 2: MATERI DATA TABLE -->
        <div id="material-view-list" class="material-subview-panel" style="display: none;">
            <div class="supabase-panel-card">
                <!-- Precision Corner Crosshairs -->
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <!-- Search Toolbar -->
                <div class="panel-toolbar">
                    <div class="panel-toolbar-left" style="width: 100%;">
                        <div class="search-input-wrapper" style="max-width: 360px;">
                            <input type="text" id="material-search-input" class="panel-search-input" placeholder="Cari judul modul atau materi...">
                            <span class="search-shortcut-badge" title="Tekan '/' pada keyboard untuk mencari">/</span>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="panel-table-container">
                    <table class="supabase-data-table">
                        <thead>
                            <tr>
                                <th>JUDUL MATERI</th>
                                <th>KATEGORI</th>
                                <th>KESULITAN</th>
                                <th style="text-align: right; width: 90px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="materials-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

  const matTbody = document.getElementById("materials-table-body");
  const matSearch = document.getElementById("material-search-input");

  function renderMatRows(list) {
    if (!matTbody) return;
    if (list.length === 0) {
      matTbody.innerHTML = `
                <tr>
                    <td colspan="4">
                        <div class="panel-empty-state">
                            <div class="empty-state-icon-box">
                                <i data-lucide="book-open" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div class="empty-state-title">Belum Ada Materi Belajar</div>
                            <div class="empty-state-text">Tulis modul materi pembelajaran baru untuk membantu pemahaman siswa.</div>
                        </div>
                    </td>
                </tr>
            `;
      if (window.lucide) window.lucide.createIcons();
      return;
    }

    matTbody.innerHTML = list
      .map(
        (m) => `
            <tr>
                <td style="font-weight: 700; color: #ffffff;">${escapeHtml(m.title)}</td>
                <td><span class="role-pill">${escapeHtml(m.category || "Routing")}</span></td>
                <td><span class="status-badge status-active">${escapeHtml(m.difficulty || "Mudah")}</span></td>
                <td style="text-align: right;">
                    <div class="table-actions-group">
                        <a href="${window.BASE_URL}/learn/${m.id}" target="_blank" class="btn-icon-action" title="Lihat Tampilan Siswa">
                            <i data-lucide="external-link"></i>
                        </a>
                        <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Materi" onclick="confirmDeleteMaterial(${m.id}, '${escapeHtml(m.title).replace(/'/g, "\\'")}')">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("");

    if (window.lucide) window.lucide.createIcons();
  }

  renderMatRows(materials);

  if (matSearch) {
    matSearch.addEventListener("input", (e) => {
      const query = e.target.value.toLowerCase().trim();
      const filtered = materials.filter(
        (m) =>
          (m.title && m.title.toLowerCase().includes(query)) ||
          (m.category && m.category.toLowerCase().includes(query)),
      );
      renderMatRows(filtered);
    });
  }

  // Switch between Creation Studio View and Materials Table View
  window.switchMaterialView = function (view) {
    const viewCreate = document.getElementById("material-view-create");
    const viewList = document.getElementById("material-view-list");
    const tabCreate = document.getElementById("tab-btn-material-create");
    const tabList = document.getElementById("tab-btn-material-list");

    if (view === "create") {
      if (viewCreate) viewCreate.style.display = "block";
      if (viewList) viewList.style.display = "none";
      if (tabCreate) tabCreate.classList.add("active");
      if (tabList) tabList.classList.remove("active");
      const titleInput = document.getElementById("material-input-title");
      if (titleInput) titleInput.focus();
    } else {
      if (viewCreate) viewCreate.style.display = "none";
      if (viewList) viewList.style.display = "block";
      if (tabCreate) tabCreate.classList.remove("active");
      if (tabList) tabList.classList.add("active");
    }
    if (window.lucide) window.lucide.createIcons();
  };

  window.openMaterialForm = function () {
    window.switchMaterialView("create");
  };

  window.closeMaterialForm = function () {
    window.switchMaterialView("list");
  };

  // ==========================================================================
  // MATERIAL STUDIO: ADVANCED ENGINE & 1:1 STUDENT READER PREVIEW
  // ==========================================================================

  const btnModeEditor = document.getElementById("btn-mode-editor");
  const btnModePreview = document.getElementById("btn-mode-preview");
  const btnModeSplit = document.getElementById("btn-mode-split");
  const studioContainer = document.getElementById("material-studio-container");
  const editorWrapper = document.getElementById("material-editor-wrapper");
  const previewBox = document.getElementById("material-preview-box");
  const previewRender = document.getElementById("material-preview-content-render");
  const contentTextarea = document.getElementById("material-content-textarea");
  const readingTimePill = document.getElementById("material-reading-time");
  const titleInput = document.getElementById("material-input-title");
  const catSelect = document.getElementById("material-input-category");
  const diffSelect = document.getElementById("material-input-difficulty");

  // Universal Material Snippet Insertion Engine
  window.insertMaterialSnippet = function (type) {
    if (!contentTextarea) return;
    const start = contentTextarea.selectionStart;
    const end = contentTextarea.selectionEnd;
    const text = contentTextarea.value;
    const selected = text.substring(start, end);

    let snippet = "";
    let cursorOffset = 0;

    switch (type) {
      case "h2":
        snippet = `<h2>${selected || "Judul Bab Baru"}</h2>`;
        break;
      case "h3":
        snippet = `<h3>${selected || "Sub-Judul Pembahasan"}</h3>`;
        break;
      case "h4":
        snippet = `<h4>${selected || "Poin Topik Spesifik"}</h4>`;
        break;
      case "p":
        snippet = `<p>${selected || "Tuliskan penjelasan materi secara mendalam di sini..."}</p>`;
        break;
      case "bold":
        snippet = `<strong>${selected || "teks tebal"}</strong>`;
        break;
      case "italic":
        snippet = `<em>${selected || "teks miring"}</em>`;
        break;
      case "underline":
        snippet = `<u>${selected || "teks garis bawah"}</u>`;
        break;
      case "strike":
        snippet = `<s>${selected || "teks coret"}</s>`;
        break;
      case "mark":
        snippet = `<mark>${selected || "teks sorot highlight"}</mark>`;
        break;
      case "code":
        snippet = `<code>${selected || "perintah_inline"}</code>`;
        break;
      case "kbd":
        snippet = `<kbd>${selected || "Ctrl"}</kbd> + <kbd>C</kbd>`;
        break;
      case "hr":
        snippet = `\n<hr class="section-divider">\n`;
        break;
      case "ul":
        snippet = `\n<ul>\n  <li>${selected || "Poin pembahasan pertama"}</li>\n  <li>Poin pembahasan kedua</li>\n  <li>Poin pembahasan ketiga</li>\n</ul>\n`;
        break;
      case "ol":
        snippet = `\n<ol>\n  <li>${selected || "Langkah pertama konfigurasi"}</li>\n  <li>Langkah kedua verifikasi status</li>\n  <li>Langkah ketiga pengujian koneksi</li>\n</ol>\n`;
        break;
      case "checklist":
        snippet = `\n<ul class="task-list">\n  <li><input type="checkbox" checked disabled> ${selected || "Pastikan router sudah menyala dan terhubung"}</li>\n  <li><input type="checkbox" disabled> Konfigurasi IP Address pada interface</li>\n  <li><input type="checkbox" disabled> Uji Ping ke Gateway</li>\n</ul>\n`;
        break;
      case "dl":
        snippet = `\n<div class="network-spec-card">\n  <div class="network-spec-title">📋 PARAMETER KONFIGURASI</div>\n  <div class="network-grid">\n    <div class="network-item">\n      <div class="network-label">Parameter</div>\n      <div class="network-value">${selected || "Value"}</div>\n    </div>\n  </div>\n</div>\n`;
        break;
      case "cli_routeros":
        snippet = `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">TERMINAL CLI // ROUTEROS</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${selected || "/ip address\nadd address=192.168.88.1/24 interface=ether2 comment=\"LAN Network\"\n\n/ip route\nadd dst-address=0.0.0.0/0 gateway=192.168.1.1"}</code></pre>\n  </div>\n</div>\n`;
        break;
      case "cli_bash":
        snippet = `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">TERMINAL BASH // LINUX</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${selected || "sudo apt update && sudo apt install -y tcpdump\ntcpdump -i eth0 -n -c 10 icmp"}</code></pre>\n  </div>\n</div>\n`;
        break;
      case "cli_json":
        snippet = `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">CONFIG PAYLOAD // JSON</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${selected || '{\n  "interface": "ether1",\n  "status": "up",\n  "mtu": 1500\n}'}</code></pre>\n  </div>\n</div>\n`;
        break;
      case "callout_note":
        snippet = `\n<div class="material-callout-quote callout-note">\n  <div class="callout-header">💡 CATATAN & INFORMASI</div>\n  <p>${selected || "Catatan penting: Pastikan firewall rule mengizinkan port komunikasi sebelum melanjutkan."}</p>\n</div>\n`;
        break;
      case "callout_tip":
        snippet = `\n<div class="material-callout-quote callout-tip">\n  <div class="callout-header">✨ TIPS & TRIK</div>\n  <p>${selected || "Tips: Gunakan tombol [Tab] dua kali pada terminal MikroTik untuk melihat daftar perintah yang tersedia."}</p>\n</div>\n`;
        break;
      case "callout_warning":
        snippet = `\n<div class="material-callout-quote callout-warning">\n  <div class="callout-header">⚠️ PERHATIAN / PERINGATAN</div>\n  <p>${selected || "Peringatan: Mengubah konfigurasi IP pada remote session dapat menyebabkan koneksi terputus jika salah input subnet."}</p>\n</div>\n`;
        break;
      case "callout_danger":
        snippet = `\n<div class="material-callout-quote callout-danger">\n  <div class="callout-header">🚨 PERINGATAN KRITIS</div>\n  <p>${selected || "Bahaya: Jangan pernah menghapus default configuration tanpa mengaktifkan Safe Mode terlebih dahulu!"}</p>\n</div>\n`;
        break;
      case "callout_success":
        snippet = `\n<div class="material-callout-quote callout-success">\n  <div class="callout-header">✅ BEST PRACTICE & HASIL</div>\n  <p>${selected || "Best practice: Selalu berikan komentar (comment) pada setiap rule firewall untuk memudahkan identifikasi dan audit."}</p>\n</div>\n`;
        break;
      case "quote":
        snippet = `\n<blockquote style="border-left: 3px solid #71717a; padding-left: 1rem; color: #a1a1aa; margin: 1.5rem 0; font-style: italic;">${selected || "Routing adalah proses memilih jalur terbaik untuk lalu lintas data melintasi beberapa jaringan."}</blockquote>\n`;
        break;
      case "table":
        snippet = `\n<div class="material-table-wrapper">\n  <table class="material-data-table">\n    <thead>\n      <tr>\n        <th>Parameter</th>\n        <th>Nilai Setting</th>\n        <th>Fungsi / Deskripsi</th>\n      </tr>\n    </thead>\n    <tbody>\n      <tr>\n        <td><code>chain</code></td>\n        <td><code>srcnat</code></td>\n        <td>Untuk manipulasi alamat sumber paket data</td>\n      </tr>\n      <tr>\n        <td><code>action</code></td>\n        <td><code>masquerade</code></td>\n        <td>Translasi IP privat menjadi IP publik dinamis</td>\n      </tr>\n    </tbody>\n  </table>\n</div>\n`;
        break;
      case "step_guide":
        snippet = `\n<div class="step-guide-container">\n  <div class="step-item">\n    <div class="step-number">1</div>\n    <div class="step-content">\n      <div class="step-title">${selected || "Konfigurasi IP Address"}</div>\n      <p>Masuk ke menu IP > Addresses dan tambahkan IP pada interface lokal.</p>\n    </div>\n  </div>\n  <div class="step-item">\n    <div class="step-number">2</div>\n    <div class="step-content">\n      <div class="step-title">Atur Default Gateway</div>\n      <p>Tambahkan route baru dengan dst-address <code>0.0.0.0/0</code> mengarah ke IP Modem.</p>\n    </div>\n  </div>\n</div>\n`;
        break;
      case "network_card":
        snippet = `\n<div class="network-spec-card">\n  <div class="network-spec-title">🌐 SPESIFIKASI JARINGAN // TOPOLOGI</div>\n  <div class="network-grid">\n    <div class="network-item">\n      <div class="network-label">IP LAN</div>\n      <div class="network-value">192.168.88.1/24</div>\n    </div>\n    <div class="network-item">\n      <div class="network-label">Gateway WAN</div>\n      <div class="network-value">192.168.1.1</div>\n    </div>\n    <div class="network-item">\n      <div class="network-label">DNS Server</div>\n      <div class="network-value">1.1.1.1, 8.8.8.8</div>\n    </div>\n    <div class="network-item">\n      <div class="network-label">Interface</div>\n      <div class="network-value">ether1 (WAN), ether2 (LAN)</div>\n    </div>\n  </div>\n</div>\n`;
        break;
      case "details":
        snippet = `\n<details class="material-details">\n  <summary>🔍 Pertanyaan Umum: ${selected || "Mengapa Router Tidak Bisa Ping ke Internet?"}</summary>\n  <div class="material-details-body">\n    <p>Periksa kembali parameter NAT (Masquerade), DNS Server pada <code>/ip dns</code>, dan Default Route.</p>\n  </div>\n</details>\n`;
        break;
      case "figure":
        snippet = `\n<figure>\n  <img src="${selected || 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop'}" alt="Diagram Topologi Jaringan">\n  <figcaption>Gambar 1.1: Skema Topologi Jaringan Lab RouterOS</figcaption>\n</figure>\n`;
        break;
      case "link":
        snippet = `<a href="https://wiki.mikrotik.com" target="_blank" rel="noopener noreferrer">${selected || "Dokumentasi Resmi MikroTik"}</a>`;
        break;
      default:
        snippet = selected;
    }

    contentTextarea.value = text.substring(0, start) + snippet + text.substring(end);
    const newPos = start + snippet.length;
    contentTextarea.focus();
    contentTextarea.setSelectionRange(newPos, newPos);
    updateReadingTime();
    updateLivePreviewIfActive();
  };

  // Auto-Format / Markdown & CLI to HTML Intelligent Converter
  window.autoFormatMaterialContent = function () {
    if (!contentTextarea) return;
    let raw = contentTextarea.value.trim();
    if (!raw) {
      if (window.showGeistToast) {
        window.showGeistToast("error", "Editor Kosong", "Tuliskan materi terlebih dahulu sebelum auto format.");
      } else {
        alert("Tuliskan materi terlebih dahulu sebelum auto format.");
      }
      return;
    }

    // 1. Convert Multi-line Code blocks ```routeros ... ``` or ```bash ... ```
    raw = raw.replace(/```(?:routeros|mikrotik)?\s*([\s\S]*?)```/gi, function (_, code) {
      const cleanCode = code.trim();
      return `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">TERMINAL CLI // ROUTEROS</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${cleanCode}</code></pre>\n  </div>\n</div>\n`;
    });

    raw = raw.replace(/```(?:bash|sh|linux)\s*([\s\S]*?)```/gi, function (_, code) {
      const cleanCode = code.trim();
      return `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">TERMINAL BASH // LINUX</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${cleanCode}</code></pre>\n  </div>\n</div>\n`;
    });

    raw = raw.replace(/```(?:json)\s*([\s\S]*?)```/gi, function (_, code) {
      const cleanCode = code.trim();
      return `\n<div class="terminal-block-wrap">\n  <div class="terminal-block-header">\n    <div class="terminal-dots-group">\n      <span class="terminal-dot dot-red"></span>\n      <span class="terminal-dot dot-yellow"></span>\n      <span class="terminal-dot dot-green"></span>\n    </div>\n    <span class="terminal-title-label">CONFIG // JSON</span>\n    <button type="button" class="btn-copy-code" onclick="copySnippetCode(this)">\n      <i data-lucide="copy" style="width: 11px; height: 11px;"></i>\n      <span>Salin</span>\n    </button>\n  </div>\n  <div class="terminal-block-body">\n    <pre><code>${cleanCode}</code></pre>\n  </div>\n</div>\n`;
    });

    raw = raw.replace(/```\s*([\s\S]*?)```/gi, function (_, code) {
      return `\n<pre><code>${code.trim()}</code></pre>\n`;
    });

    // 2. Convert Callout alerts: > [!NOTE], > [!TIP], > [!WARNING], > [!DANGER]
    raw = raw.replace(/^>\s*\[!NOTE\]\s*([\s\S]*?)(?=\n\n|$)/gim, function (_, content) {
      return `<div class="material-callout-quote callout-note">\n  <div class="callout-header">💡 CATATAN & INFORMASI</div>\n  <p>${content.replace(/^>\s*/gm, "").trim()}</p>\n</div>`;
    });
    raw = raw.replace(/^>\s*\[!TIP\]\s*([\s\S]*?)(?=\n\n|$)/gim, function (_, content) {
      return `<div class="material-callout-quote callout-tip">\n  <div class="callout-header">✨ TIPS & TRIK</div>\n  <p>${content.replace(/^>\s*/gm, "").trim()}</p>\n</div>`;
    });
    raw = raw.replace(/^>\s*\[!WARNING\]\s*([\s\S]*?)(?=\n\n|$)/gim, function (_, content) {
      return `<div class="material-callout-quote callout-warning">\n  <div class="callout-header">⚠️ PERHATIAN / PERINGATAN</div>\n  <p>${content.replace(/^>\s*/gm, "").trim()}</p>\n</div>`;
    });
    raw = raw.replace(/^>\s*\[!DANGER\]\s*([\s\S]*?)(?=\n\n|$)/gim, function (_, content) {
      return `<div class="material-callout-quote callout-danger">\n  <div class="callout-header">🚨 PERINGATAN KRITIS</div>\n  <p>${content.replace(/^>\s*/gm, "").trim()}</p>\n</div>`;
    });
    raw = raw.replace(/^>\s*\[!SUCCESS\]\s*([\s\S]*?)(?=\n\n|$)/gim, function (_, content) {
      return `<div class="material-callout-quote callout-success">\n  <div class="callout-header">✅ BEST PRACTICE</div>\n  <p>${content.replace(/^>\s*/gm, "").trim()}</p>\n</div>`;
    });

    // Generic blockquotes
    raw = raw.replace(/^>\s*(.+)$/gm, function (_, text) {
      if (text.includes("material-callout-quote") || text.includes("<div")) return text;
      return `<div class="material-callout-quote">\n  <p>${text.trim()}</p>\n</div>`;
    });

    // 3. Headings
    raw = raw.replace(/^#{4,}\s+(.+)$/gm, "<h4>$1</h4>");
    raw = raw.replace(/^###\s+(.+)$/gm, "<h3>$1</h3>");
    raw = raw.replace(/^##?\s+(.+)$/gm, "<h2>$1</h2>");

    // 4. Horizontal dividers
    raw = raw.replace(/^(?:---|___|\*\*\*)$/gm, '<hr class="section-divider">');

    // 5. Typography formatting: Bold, Italic, Strikethrough, Code
    raw = raw.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>");
    raw = raw.replace(/__(.+?)__/g, "<strong>$1</strong>");
    raw = raw.replace(/\*(.+?)\*/g, "<em>$1</em>");
    raw = raw.replace(/_(.+?)_/g, "<em>$1</em>");
    raw = raw.replace(/~~(.+?)~~/g, "<s>$1</s>");
    raw = raw.replace(/`([^`\n]+)`/g, "<code>$1</code>");

    // 6. Checklists (- [ ] item)
    raw = raw.replace(/(?:^|\n)- \[([ xX])\] (.+)/g, function (_, checked, text) {
      const isChecked = checked.toLowerCase() === "x" ? "checked" : "";
      return `\n<ul class="task-list"><li><input type="checkbox" ${isChecked} disabled> ${text}</li></ul>`;
    });

    // 7. Bullet lists (- item or * item)
    raw = raw.replace(/(?:^|\n)[-*]\s+(.+)/g, "\n<ul><li>$1</li></ul>");
    // Merge adjacent <ul> tags
    raw = raw.replace(/<\/ul>\s*<ul>/g, "");

    // 8. Numbered lists (1. item)
    raw = raw.replace(/(?:^|\n)\d+\.\s+(.+)/g, "\n<ol><li>$1</li></ol>");
    // Merge adjacent <ol> tags
    raw = raw.replace(/<\/ol>\s*<ol>/g, "");

    // 9. Markdown Tables
    raw = raw.replace(/((?:\|[^\n]+\|\r?\n)+)/g, function (tableBlock) {
      const lines = tableBlock.trim().split("\n").map((l) => l.trim()).filter((l) => l.startsWith("|"));
      if (lines.length >= 2) {
        const headerCells = lines[0].split("|").slice(1, -1).map((c) => c.trim());
        let htmlTable = `\n<div class="material-table-wrapper">\n  <table class="material-data-table">\n    <thead>\n      <tr>\n`;
        headerCells.forEach((h) => (htmlTable += `        <th>${h}</th>\n`));
        htmlTable += `      </tr>\n    </thead>\n    <tbody>\n`;

        const dataLines = lines.slice(lines[1].includes("---") ? 2 : 1);
        dataLines.forEach((row) => {
          const cells = row.split("|").slice(1, -1).map((c) => c.trim());
          if (cells.length > 0) {
            htmlTable += `      <tr>\n`;
            cells.forEach((cell) => (htmlTable += `        <td>${cell}</td>\n`));
            htmlTable += `      </tr>\n`;
          }
        });
        htmlTable += `    </tbody>\n  </table>\n</div>\n`;
        return htmlTable;
      }
      return tableBlock;
    });

    // 10. Wrap plain lines in <p> if not already inside HTML block
    const blocks = raw.split(/\n{2,}/);
    const parsedBlocks = blocks.map((b) => {
      const trimmed = b.trim();
      if (!trimmed) return "";
      if (
        trimmed.startsWith("<h2") ||
        trimmed.startsWith("<h3") ||
        trimmed.startsWith("<h4") ||
        trimmed.startsWith("<p") ||
        trimmed.startsWith("<ul") ||
        trimmed.startsWith("<ol") ||
        trimmed.startsWith("<div") ||
        trimmed.startsWith("<pre") ||
        trimmed.startsWith("<table") ||
        trimmed.startsWith("<figure") ||
        trimmed.startsWith("<details") ||
        trimmed.startsWith("<blockquote") ||
        trimmed.startsWith("<hr")
      ) {
        return trimmed;
      }
      return `<p>${trimmed.replace(/\n/g, "<br>")}</p>`;
    });

    contentTextarea.value = parsedBlocks.filter((b) => b).join("\n\n");
    updateReadingTime();
    updateLivePreviewIfActive();

    if (window.showGeistToast) {
      window.showGeistToast("success", "Auto Format Berhasil", "Teks materi berhasil diformat otomatis ke struktur HTML NetQuiz.");
    }
  };

  // Copy HTML to Clipboard
  window.copyMaterialHtml = function () {
    if (!contentTextarea || !contentTextarea.value.trim()) {
      if (window.showGeistToast) window.showGeistToast("error", "Editor Kosong", "Tidak ada konten untuk disalin.");
      return;
    }
    navigator.clipboard.writeText(contentTextarea.value).then(() => {
      if (window.showGeistToast) {
        window.showGeistToast("success", "Disalin ke Clipboard", "Kode HTML materi berhasil disalin.");
      }
    });
  };

  // Clear Editor
  window.clearMaterialEditor = function () {
    if (!contentTextarea || !contentTextarea.value.trim()) return;
    if (confirm("Apakah Anda yakin ingin mengosongkan teks editor materi ini?")) {
      contentTextarea.value = "";
      updateReadingTime();
      updateLivePreviewIfActive();
    }
  };

  // 100% Identical Student Reader Live Preview Generator
  function renderStudentReaderPreview() {
    if (!previewRender) return;

    const rawContent = contentTextarea ? contentTextarea.value : "";
    const titleVal = titleInput ? titleInput.value.trim() : "Judul Materi Pembelajaran";
    const catVal = catSelect ? catSelect.value : "Routing";
    const diffVal = diffSelect ? diffSelect.value : "Mudah";

    const cleanText = rawContent.replace(/<[^>]*>/g, " ").trim();
    const wordCount = cleanText ? cleanText.split(/\s+/).length : 0;
    const readTime = Math.max(1, Math.ceil(wordCount / 150));

    const diffLower = diffVal.toLowerCase();
    const diffClass = diffLower === "sedang" ? "diff-sedang" : diffLower === "sulit" ? "diff-sulit" : "diff-mudah";

    const catThemes = {
      Routing: { pillClass: "cat-routing" },
      "Firewall & NAT": { pillClass: "cat-firewall" },
      Wireless: { pillClass: "cat-wireless" },
      "Network Management": { pillClass: "cat-network" },
    };
    const theme = catThemes[catVal] || { pillClass: "cat-network" };

    const today = new Date().toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });

    const contentHtml = rawContent.trim() || `
      <div style="padding: 2.5rem 1rem; text-align: center; color: #71717a;">
        <i data-lucide="book-open" style="width: 32px; height: 32px; margin: 0 auto 0.5rem auto; opacity: 0.5;"></i>
        <p style="margin: 0; font-size: 0.9rem;">Pratinjau kosong. Tulis artikel materi di tab Editor untuk melihat tampilan persis seperti yang dilihat siswa.</p>
      </div>
    `;

    previewRender.innerHTML = `
      <article class="learn-article-panel" style="margin: 0; max-width: 100%;">
          <span class="panel-crosshair corner-tl">+</span>
          <span class="panel-crosshair corner-tr">+</span>
          <span class="panel-crosshair corner-bl">+</span>
          <span class="panel-crosshair corner-br">+</span>

          <!-- Article Header Meta -->
          <div class="article-header-meta">
              <span class="learn-category-pill ${theme.pillClass} font-mono">
                  ${catVal}
              </span>
              <span class="learn-diff-badge ${diffClass}">
                  Level: ${diffVal}
              </span>
              <span class="article-meta-info">
                  ⏱ ~${readTime} menit baca (${wordCount} kata)
              </span>
              <span class="article-meta-info">
                  • Dipublikasikan ${today}
              </span>
          </div>

          <!-- Article Title -->
          <h1 class="article-main-title font-sans">
              ${escapeHtml(titleVal || "Judul Materi Pembelajaran")}
          </h1>

          <!-- Article Body Content -->
          <div class="material-content-body font-sans">
              ${contentHtml}
          </div>

          <!-- Reader Bottom Bar -->
          <div class="reader-bottom-bar font-mono">
              <span class="btn-hero-secondary text-xs" style="opacity: 0.6; pointer-events: none;">
                  <span>← Kembali ke Materi</span>
              </span>
          </div>
      </article>
    `;

    if (window.lucide) window.lucide.createIcons();
  }

  function updateLivePreviewIfActive() {
    if (previewBox && previewBox.style.display !== "none") {
      renderStudentReaderPreview();
    }
  }

  // Active Copy Snippet Code within Terminal Blocks
  window.copySnippetCode = function (btn) {
    const wrap = btn.closest(".terminal-block-wrap");
    if (!wrap) return;
    const codeEl = wrap.querySelector("code");
    if (!codeEl) return;
    const codeText = codeEl.innerText;

    navigator.clipboard.writeText(codeText).then(() => {
      const originalHTML = btn.innerHTML;
      btn.innerHTML = `<i data-lucide="check" style="width: 11px; height: 11px; color: #10b981;"></i> <span style="color: #10b981;">Tersalin</span>`;
      if (window.lucide) window.lucide.createIcons();
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        if (window.lucide) window.lucide.createIcons();
      }, 1500);
    });
  };

  // Real-time Event Listeners
  function updateReadingTime() {
    if (!contentTextarea || !readingTimePill) return;
    const text = contentTextarea.value.replace(/<[^>]*>/g, " ").trim();
    const words = text ? text.split(/\s+/).length : 0;
    const minutes = Math.max(1, Math.ceil(words / 150));
    readingTimePill.innerHTML = `<i data-lucide="clock" style="width: 12px; height: 12px;"></i> <span>~${minutes} menit baca (${words} kata)</span>`;
    if (window.lucide) window.lucide.createIcons();
  }

  let debounceTimer = null;
  if (contentTextarea) {
    contentTextarea.addEventListener("input", () => {
      updateReadingTime();
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(updateLivePreviewIfActive, 120);
    });
  }

  if (titleInput) {
    titleInput.addEventListener("input", updateLivePreviewIfActive);
  }
  if (catSelect) {
    catSelect.addEventListener("change", updateLivePreviewIfActive);
  }
  if (diffSelect) {
    diffSelect.addEventListener("change", updateLivePreviewIfActive);
  }

  // Mode Switcher Controls
  if (btnModeEditor && btnModePreview && btnModeSplit && studioContainer && editorWrapper && previewBox) {
    btnModeEditor.addEventListener("click", () => {
      btnModeEditor.classList.add("active");
      btnModePreview.classList.remove("active");
      btnModeSplit.classList.remove("active");
      studioContainer.className = "";
      editorWrapper.style.display = "block";
      previewBox.style.display = "none";
    });

    btnModePreview.addEventListener("click", () => {
      btnModePreview.classList.add("active");
      btnModeEditor.classList.remove("active");
      btnModeSplit.classList.remove("active");
      studioContainer.className = "";
      editorWrapper.style.display = "none";
      previewBox.style.display = "block";
      renderStudentReaderPreview();
    });

    btnModeSplit.addEventListener("click", () => {
      btnModeSplit.classList.add("active");
      btnModeEditor.classList.remove("active");
      btnModePreview.classList.remove("active");
      studioContainer.className = "material-editor-split";
      editorWrapper.style.display = "block";
      previewBox.style.display = "block";
      renderStudentReaderPreview();
    });
  }

  // Handle JSON Material Upload & Autofill
  const jsonMatUploadInput = document.getElementById("import-material-json-input");
  if (jsonMatUploadInput) {
    jsonMatUploadInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (evt) {
        try {
          const data = JSON.parse(evt.target.result);

          if (!data || typeof data !== "object") {
            throw new Error("Format JSON tidak valid.");
          }

          if (data.title && titleInput) titleInput.value = data.title;
          if (data.category && catSelect) catSelect.value = data.category;
          if (data.difficulty && diffSelect) diffSelect.value = data.difficulty;
          if (data.content && contentTextarea) {
            contentTextarea.value = data.content;
            updateReadingTime();
            updateLivePreviewIfActive();
          }

          if (window.showGeistToast) {
            window.showGeistToast("success", "Import Berhasil", "Materi pembelajaran berhasil dimuat dari file JSON.");
          } else {
            alert("Materi pembelajaran berhasil dimuat dari file JSON.");
          }
        } catch (err) {
          if (window.showGeistToast) {
            window.showGeistToast("error", "Gagal Membaca File", "Gagal membaca file JSON: " + err.message);
          } else {
            alert("Gagal membaca file JSON: " + err.message);
          }
        } finally {
          jsonMatUploadInput.value = "";
        }
      };
      reader.readAsText(file);
    });
  }

  window.confirmDeleteMaterial = function (id, title) {
    showGeistConfirm(
      "Hapus Materi Belajar",
      `Apakah Anda yakin ingin menghapus materi "${title}"?`,
      "Hapus Materi",
      () => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `${window.BASE_URL}/admin/materials/delete/${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">`;
        document.body.appendChild(form);
        form.submit();
      },
      true,
    );
  };

  if (window.lucide) window.lucide.createIcons();
}

// ==========================================================================
// 7. MODUL 5: LENCANA PRESTASI (Segmented Studio & Minimalist Cards Grid)
// ==========================================================================
function renderBadgeSection() {
  const sec = document.getElementById("badge-section");
  if (!sec) return;

  const badges = Array.isArray(window.NETQUIZ_BADGES)
    ? window.NETQUIZ_BADGES
    : [];

  sec.innerHTML = `
        <!-- Top Segmented Switcher -->
        <div class="quiz-segmented-switcher-bar" style="margin-bottom: 1.5rem;">
            <div class="quiz-segmented-control">
                <button type="button" id="tab-btn-badge-create" class="quiz-segment-tab active" onclick="window.switchBadgeView('create')">
                    <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
                    <span>Buat Lencana Baru</span>
                </button>
                <button type="button" id="tab-btn-badge-list" class="quiz-segment-tab" onclick="window.switchBadgeView('list')">
                    <i data-lucide="award" style="width: 14px; height: 14px;"></i>
                    <span>Daftar Lencana (<span id="badge-list-counter">${badges.length}</span>)</span>
                </button>
            </div>
        </div>

        <!-- VIEW 1: STUDIO FORM -->
        <div id="badge-view-create" class="badge-subview-panel">
            <div class="supabase-panel-card" style="margin-bottom: 2rem;">
                <!-- Precision Corner Crosshairs -->
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <form action="${window.BASE_URL}/admin/badges/create" method="POST" id="form-create-badge" style="padding: 1.75rem;">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">
                    <input type="hidden" name="icon" id="badge-selected-icon" value="award">
                    
                    <!-- Section Header -->
                    <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #222222;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #ffffff; margin: 0;">Buat Lencana Prestasi Baru</h3>
                        <p style="font-size: 0.825rem; color: #a1a1aa; margin-top: 0.25rem;">Tentukan nama lencana, target kuis selesai, dan ikon visual penghargaan siswa.</p>
                    </div>

                    <!-- Grid 2-kolom: Nama Lencana & Target Kuis -->
                    <div class="form-grid-2col" style="margin-bottom: 1.25rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Nama Lencana</label>
                            <input type="text" class="form-field-input" name="title" id="badge-input-title" placeholder="Contoh: Routing Master MTCNA" required autocomplete="off">
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Target Kuis Selesai</label>
                            <input type="number" class="form-field-input" name="target_value" id="badge-input-target" value="5" min="1" required>
                        </div>
                    </div>

                    <!-- Visual Icon Picker Grid -->
                    <div class="form-field-group" style="margin-bottom: 1.25rem;">
                        <label class="form-field-label">Pilih Ikon Visual Lencana</label>
                        <div class="icon-picker-grid" id="badge-icon-picker">
                            <button type="button" class="icon-picker-option active" data-icon="award" title="Award"><i data-lucide="award"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="shield" title="Shield"><i data-lucide="shield"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="star" title="Star"><i data-lucide="star"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="zap" title="Zap"><i data-lucide="zap"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="flame" title="Flame"><i data-lucide="flame"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="target" title="Target"><i data-lucide="target"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="book-open" title="Book Open"><i data-lucide="book-open"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="trophy" title="Trophy"><i data-lucide="trophy"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="cpu" title="CPU Router"><i data-lucide="cpu"></i></button>
                            <button type="button" class="icon-picker-option" data-icon="terminal" title="Terminal CLI"><i data-lucide="terminal"></i></button>
                        </div>
                    </div>

                    <!-- Deskripsi Lencana -->
                    <div class="form-field-group" style="margin-bottom: 0;">
                        <label class="form-field-label">Deskripsi Lencana</label>
                        <input type="text" class="form-field-input" name="description" id="badge-input-desc" placeholder="Contoh: Diberikan kepada siswa yang telah menyelesaikan 5 ujian kategori Routing." required autocomplete="off">
                    </div>

                    <!-- Footer Action Bar -->
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; margin-top: 1.5rem; border-top: 1px solid #222222;">
                        <button type="button" onclick="window.switchBadgeView('list')" class="btn-secondary-outline" style="padding: 0.5rem 1rem;">Batal</button>
                        <button type="submit" class="btn-primary-black" style="padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                            <span>Simpan Lencana</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- VIEW 2: BADGES LIST GRID -->
        <div id="badge-view-list" class="badge-subview-panel" style="display: none;">
            ${
              badges.length === 0
                ? `
                <div class="supabase-panel-card" style="padding: 3.5rem 1.5rem; text-align: center;">
                    <span class="corner-crosshair corner-tl">+</span>
                    <span class="corner-crosshair corner-tr">+</span>
                    <span class="corner-crosshair corner-bl">+</span>
                    <span class="corner-crosshair corner-br">+</span>
                    <div class="panel-empty-state">
                        <div class="empty-state-icon-box">
                            <i data-lucide="award" style="width: 28px; height: 28px;"></i>
                        </div>
                        <div class="empty-state-title">Belum Ada Lencana Prestasi</div>
                        <div class="empty-state-text">Buat lencana baru untuk memotivasi pencapaian belajar siswa.</div>
                    </div>
                </div>
            `
                : `
                <div class="badges-3col-grid">
                    ${badges
                      .map(
                        (b) => `
                        <div class="supabase-panel-card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                            <span class="corner-crosshair corner-tl">+</span>
                            <span class="corner-crosshair corner-tr">+</span>
                            <span class="corner-crosshair corner-bl">+</span>
                            <span class="corner-crosshair corner-br">+</span>

                            <div>
                                <!-- Header Kartu -->
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                                    <div style="width: 38px; height: 38px; background-color: #ffffff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #FFF;">
                                        <i data-lucide="${escapeHtml(b.icon || "award")}" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Lencana" onclick="confirmDeleteBadge(${b.id}, '${escapeHtml(b.title).replace(/'/g, "\\'")}')">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                                <!-- Body Kartu -->
                                <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 800; color: #ffffff; margin: 0 0 0.35rem 0;">${escapeHtml(b.title)}</h4>
                                <span class="status-badge status-active" style="margin-bottom: 0.6rem;">Target: ${b.target_value || 1} Kuis Selesai</span>
                                <p style="font-size: 0.825rem; color: #a1a1aa; margin: 0; line-height: 1.45;">${escapeHtml(b.description || "")}</p>
                            </div>

                            <!-- Footer Kartu Ringkas -->
                            <div style="margin-top: 1rem; padding-top: 0.65rem; border-top: 1px solid #222222; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.75rem; color: #a1a1aa;" class="font-mono">Diperoleh oleh ${b.earned_count || 0} Siswa</span>
                                <span class="status-badge" style="background-color: #0f0f12; color: #a1a1aa; font-size: 0.7rem;">Aktif</span>
                            </div>
                        </div>
                    `,
                      )
                      .join("")}
                </div>
            `
            }
        </div>
    `;

  // Icon Picker Click Handlers
  const iconOptions = document.querySelectorAll(".icon-picker-option");
  const selectedIconInput = document.getElementById("badge-selected-icon");
  iconOptions.forEach((opt) => {
    opt.addEventListener("click", function () {
      iconOptions.forEach((o) => o.classList.remove("active"));
      this.classList.add("active");
      if (selectedIconInput) {
        selectedIconInput.value = this.getAttribute("data-icon");
      }
    });
  });

  // Switch between Creation Studio View and Badges Grid View
  window.switchBadgeView = function (view) {
    const viewCreate = document.getElementById("badge-view-create");
    const viewList = document.getElementById("badge-view-list");
    const tabCreate = document.getElementById("tab-btn-badge-create");
    const tabList = document.getElementById("tab-btn-badge-list");

    if (view === "create") {
      if (viewCreate) viewCreate.style.display = "block";
      if (viewList) viewList.style.display = "none";
      if (tabCreate) tabCreate.classList.add("active");
      if (tabList) tabList.classList.remove("active");
      const titleInput = document.getElementById("badge-input-title");
      if (titleInput) titleInput.focus();
    } else {
      if (viewCreate) viewCreate.style.display = "none";
      if (viewList) viewList.style.display = "block";
      if (tabCreate) tabCreate.classList.remove("active");
      if (tabList) tabList.classList.add("active");
    }
    if (window.lucide) window.lucide.createIcons();
  };

  window.openBadgeForm = function () {
    window.switchBadgeView("create");
  };

  window.closeBadgeForm = function () {
    window.switchBadgeView("list");
  };

  window.confirmDeleteBadge = function (id, title) {
    showGeistConfirm(
      "Hapus Lencana",
      `Apakah Anda yakin ingin menghapus lencana "${title}"?`,
      "Hapus Lencana",
      () => {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `${window.BASE_URL}/admin/badges/delete/${id}`;
        form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ""}">`;
        document.body.appendChild(form);
        form.submit();
      },
      true,
    );
  };

  if (window.lucide) window.lucide.createIcons();
}

// ==========================================================================
// 8. HELPER UTILITIES
// ==========================================================================
function getInitials(name) {
  if (!name) return "U";
  const parts = name.trim().split(/\s+/);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
}

function getAvatarBgClass(id) {
  const classes = [
    "",
    "avatar-blue",
    "avatar-purple",
    "avatar-amber",
    "avatar-emerald",
  ];
  return classes[id % classes.length];
}

function formatDate(dateString) {
  if (!dateString) return "12 Mei 2026";
  try {
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return dateString;
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  } catch (e) {
    return dateString;
  }
}

function escapeHtml(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
