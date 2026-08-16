/**
 * NetQuiz Admin Console - Unified Single Panel & Grid 12 Builder (Upgraded UX/UI)
 * Standards: Vercel Geist Light Theme, Dual-Layer CAD Blueprint, Zero AI Slop
 */

// ==========================================================================
// 1. GLOBAL TOAST & CONFIRM MODAL SYSTEM (GEIST STANDARDS)
// ==========================================================================

/** Global non-blocking floating toast notification */
window.showGeistToast = function(type, title, message, duration = 3500) {
    const toaster = document.getElementById('geist-toaster');
    if (!toaster) return;

    const toast = document.createElement('div');
    toast.className = `geist-toast toast-${type}`;
    
    let iconName = 'check-circle';
    if (type === 'error') iconName = 'alert-circle';
    else if (type === 'info') iconName = 'info';

    toast.innerHTML = `
        <div class="toast-icon-wrapper">
            <i data-lucide="${iconName}" style="width: 15px; height: 15px;"></i>
        </div>
        <div class="toast-body">
            <div class="toast-title">${escapeHtml(title)}</div>
            ${message ? `<div class="toast-message">${escapeHtml(message)}</div>` : ''}
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
        if (toast.classList.contains('hiding')) return;
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 220);
    };

    dismissTimeout = setTimeout(dismiss, duration);

    const closeBtn = toast.querySelector('.toast-close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            clearTimeout(dismissTimeout);
            dismiss();
        });
    }
};

/** Global custom confirmation modal dialog with focus trap & scroll lock */
window.showGeistConfirm = function(title, message, confirmText, onConfirm, isDanger = true) {
    const overlay = document.getElementById('geist-confirm-overlay');
    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-message');
    const cancelBtn = document.getElementById('btn-confirm-cancel');
    const submitBtn = document.getElementById('btn-confirm-submit');
    const iconContainer = document.getElementById('confirm-icon-container');

    if (!overlay || !submitBtn) {
        if (confirm(message)) onConfirm();
        return;
    }

    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;
    if (submitBtn) {
        submitBtn.textContent = confirmText || 'Lanjutkan';
        submitBtn.className = isDanger ? 'btn-confirm-danger' : 'btn-primary-black';
    }
    if (iconContainer) {
        iconContainer.className = isDanger ? 'confirm-icon-box confirm-icon-danger' : 'confirm-icon-box confirm-icon-info';
    }

    // Lock body scroll
    document.body.style.overflow = 'hidden';
    overlay.classList.add('active');

    if (cancelBtn) cancelBtn.focus();

    const handleConfirm = () => {
        cleanup();
        if (typeof onConfirm === 'function') onConfirm();
    };

    const handleCancel = () => {
        cleanup();
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Escape') {
            cleanup();
        } else if (e.key === 'Tab') {
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
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        submitBtn.removeEventListener('click', handleConfirm);
        cancelBtn.removeEventListener('click', handleCancel);
        document.removeEventListener('keydown', handleKeyDown);
    }

    submitBtn.addEventListener('click', handleConfirm);
    cancelBtn.addEventListener('click', handleCancel);
    document.addEventListener('keydown', handleKeyDown);
};

/** Update sidebar live counter badges */
window.updateSidebarCounters = function() {
    const quizzesCount = Array.isArray(window.NETQUIZ_QUIZZES) ? window.NETQUIZ_QUIZZES.length : 0;
    const membersCount = Array.isArray(window.NETQUIZ_MEMBERS) ? window.NETQUIZ_MEMBERS.length : 0;
    const materialsCount = Array.isArray(window.NETQUIZ_MATERIALS) ? window.NETQUIZ_MATERIALS.length : 0;
    const badgesCount = Array.isArray(window.NETQUIZ_BADGES) ? window.NETQUIZ_BADGES.length : 0;

    const elQ = document.getElementById('sidebar-count-quizzes');
    const elM = document.getElementById('sidebar-count-members');
    const elMat = document.getElementById('sidebar-count-materials');
    const elB = document.getElementById('sidebar-count-badges');

    if (elQ) elQ.textContent = quizzesCount;
    if (elM) elM.textContent = membersCount;
    if (elMat) elMat.textContent = materialsCount;
    if (elB) elB.textContent = badgesCount;
};

/** Helper to bind show/hide password eye buttons */
window.bindPasswordToggles = function() {
    const toggleBtns = document.querySelectorAll('.btn-toggle-password');
    toggleBtns.forEach(btn => {
        if (btn.getAttribute('data-bound')) return;
        btn.setAttribute('data-bound', 'true');

        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i data-lucide="eye-off" style="width: 15px; height: 15px;"></i>';
                this.title = 'Sembunyikan Kata Sandi';
            } else {
                input.type = 'password';
                this.innerHTML = '<i data-lucide="eye" style="width: 15px; height: 15px;"></i>';
                this.title = 'Lihat Kata Sandi';
            }
            if (window.lucide) window.lucide.createIcons();
        });
    });
};

//** Global Helper to Open and Close Quiz Studio */
window.openQuizStudio = function() {
    const container = document.getElementById('quiz-builder-container');
    if (container) {
        container.style.display = 'block';
        const titleInput = document.getElementById('quiz-input-title');
        if (titleInput) titleInput.focus();
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.closeQuizStudio = function() {
    const container = document.getElementById('quiz-builder-container');
    if (container) {
        container.style.display = 'none';
    }
};

/** Global Helper to Open and Close Material Form */
window.openMaterialForm = function() {
    const container = document.getElementById('material-form-container');
    if (container) {
        container.style.display = 'block';
        const titleInput = document.getElementById('material-input-title');
        if (titleInput) titleInput.focus();
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.closeMaterialForm = function() {
    const container = document.getElementById('material-form-container');
    if (container) {
        container.style.display = 'none';
    }
};

/** Global Helper to Open and Close Badge Form */
window.openBadgeForm = function() {
    const container = document.getElementById('badge-form-container');
    if (container) {
        container.style.display = 'block';
        const titleInput = container.querySelector('input[name="title"]');
        if (titleInput) titleInput.focus();
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.closeBadgeForm = function() {
    const container = document.getElementById('badge-form-container');
    if (container) {
        container.style.display = 'none';
    }
};

/** Global Helper to Open and Close Edit Member Modal */
window.openEditMemberModal = function(id, username, email) {
    const modal = document.getElementById('edit-member-modal');
    const form = document.getElementById('edit-member-form');
    if (!modal || !form) return;
    
    form.action = `${window.BASE_URL}/admin/users/update/${id}`;
    const unInput = document.getElementById('edit-member-username');
    const emInput = document.getElementById('edit-member-email');
    const pwInput = document.getElementById('edit-member-password');
    if (unInput) unInput.value = username || '';
    if (emInput) emInput.value = email || '';
    if (pwInput) pwInput.value = '';
    
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    
    if (unInput) setTimeout(() => unInput.focus(), 50);
    if (window.bindPasswordToggles) window.bindPasswordToggles();
    if (window.lucide) window.lucide.createIcons();
};

window.closeEditMemberModal = function() {
    const modal = document.getElementById('edit-member-modal');
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
};

// Global backdrop click & escape key listener for edit modal
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('edit-member-modal');
    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) closeEditMemberModal();
        });
    }
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.admin-modal-overlay.active');
            if (activeModal && activeModal.id === 'edit-member-modal') {
                closeEditMemberModal();
            }
        }
    });
});

// ==========================================================================
// 2. DOCUMENT INITIALIZATION
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {
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
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            const activeSection = document.querySelector('.admin-section-content.active');
            if (activeSection) {
                const searchInput = activeSection.querySelector('.panel-search-input');
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
// 3. MODUL 1: BUAT KUIS (Quizzes High-Density Table & Live Question Studio)
// ==========================================================================
function renderQuizSection() {
    const sec = document.getElementById('quiz-section');
    if (!sec) return;

    const rawQuizzes = Array.isArray(window.NETQUIZ_QUIZZES) ? window.NETQUIZ_QUIZZES : [];

    sec.innerHTML = `
        <div class="supabase-panel-card" style="margin-bottom: 2rem;">
            <!-- Precision Corner Crosshairs -->
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <!-- Quiz Studio Form (Collapsible) -->
            <div id="quiz-builder-container" style="display: none; padding: 1.5rem; border-bottom: 1px solid #E5E7EB; background-color: #FAFAFA;">
                <form id="form-create-quiz" action="${window.BASE_URL}/admin/quizzes" method="POST">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E7EB;">
                        <div>
                            <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">Studio Pembuatan Kuis & Soal Ujian</h3>
                            <p style="font-size: 0.8rem; color: #52525B; margin-top: 0.2rem;">Lengkapi data kuis, konfigurasi durasi, dan susun butir-butir pertanyaan.</p>
                        </div>
                        <button type="button" onclick="window.closeQuizStudio()" class="btn-secondary-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Tutup Studio</button>
                    </div>

                    <!-- 1. Metadata Kuis -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Judul Kuis / Ujian</label>
                            <input type="text" class="form-field-input" name="title" id="quiz-input-title" placeholder="Contoh: Ujian OSPF Routing MTCNA" required>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Kategori MikroTik</label>
                            <select class="panel-select" name="category" id="quiz-input-category" style="width: 100%;" required>
                                <option value="Routing">Routing & Gateway (MTCNA/MTCRE)</option>
                                <option value="Security">Firewall, NAT & Security</option>
                                <option value="Wireless">Wireless & CAPsMAN</option>
                                <option value="Network Management">Network Management & QoS</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Durasi Ujian (Menit)</label>
                            <input type="number" class="form-field-input" name="duration" id="quiz-input-duration" value="15" min="1" max="180" required>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Tingkat Kesulitan</label>
                            <select class="panel-select" name="difficulty" id="quiz-input-difficulty" style="width: 100%;" required>
                                <option value="Mudah">Mudah (Basic MTCNA)</option>
                                <option value="Sedang">Sedang (Intermediate MTCRE)</option>
                                <option value="Sulit">Sulit (Advanced MTCWE/MTCTCE)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field-group">
                        <label class="form-field-label">Deskripsi Cakupan Ujian</label>
                        <textarea class="form-field-input" name="description" id="quiz-input-desc" rows="2" placeholder="Tuliskan ringkasan materi atau tujuan dari ujian kuis ini..." required></textarea>
                    </div>

                    <!-- 2. Question Repeater Studio -->
                    <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px dashed #E5E7EB;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <div>
                                <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 800; color: #18181B; margin: 0;">
                                    Daftar Soal Pertanyaan (<span id="quiz-question-counter">1</span> Soal)
                                </h4>
                                <p style="font-size: 0.775rem; color: #71717A; margin-top: 0.15rem;">Setiap soal wajib memiliki teks pertanyaan, 4 pilihan jawaban, dan 1 kunci jawaban benar.</p>
                            </div>
                            <button type="button" id="btn-add-quiz-question" class="btn-secondary-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                <span>+ Tambah Soal</span>
                            </button>
                        </div>

                        <!-- Question Repeater Container -->
                        <div id="quiz-questions-repeater-stack" class="quiz-question-repeater-stack">
                            <!-- Injected dynamically via JS -->
                        </div>
                    </div>

                    <!-- Studio Actions -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #E5E7EB;">
                        <button type="button" id="btn-add-another-question" class="btn-secondary-outline" style="font-size: 0.825rem; padding: 0.45rem 0.9rem;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                            <span>+ Tambah Soal Lagi</span>
                        </button>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" onclick="window.closeQuizStudio()" class="btn-secondary-outline">Batal</button>
                            <button type="submit" class="btn-primary-black">
                                <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                                <span>Simpan & Terbitkan Kuis</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Toolbar & Search (Anti-Slop Clean Input with Shortcut Badge) -->
            <div class="panel-toolbar">
                <div class="panel-toolbar-left">
                    <div class="search-input-wrapper">
                        <input type="text" id="quiz-search-input" class="panel-search-input" placeholder="Cari judul kuis atau kategori...">
                        <span class="search-shortcut-badge" title="Tekan '/' pada keyboard untuk mencari">/</span>
                    </div>
                </div>
                <div class="panel-toolbar-right">
                    <button type="button" class="btn-primary-black" onclick="window.openQuizStudio()" style="font-size: 0.8rem; padding: 0.45rem 0.9rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        <span>+ Buat Kuis Baru</span>
                    </button>
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
    `;

    const qTbody = document.getElementById('quiz-table-body');
    const qSearch = document.getElementById('quiz-search-input');
    const repeaterStack = document.getElementById('quiz-questions-repeater-stack');
    const counterEl = document.getElementById('quiz-question-counter');

    let questionCount = 0;

    function createQuestionBlock(index) {
        const card = document.createElement('div');
        card.className = 'quiz-question-card';
        card.setAttribute('data-index', index);
        card.innerHTML = `
            <div class="q-card-top-bar">
                <span class="q-number-pill">SOAL #${index + 1}</span>
                <button type="button" class="btn-remove-question" title="Hapus butir soal ini" onclick="removeQuestionBlock(${index})">
                    <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    <span>Hapus</span>
                </button>
            </div>
            
            <div class="form-field-group" style="margin-bottom: 0.75rem;">
                <label class="form-field-label">Teks Pertanyaan Soal</label>
                <textarea class="form-field-input" name="questions[${index}][question]" rows="2" placeholder="Tuliskan pertanyaan atau skenario konfigurasi di sini..." required></textarea>
            </div>

            <div class="q-options-grid-2x2">
                <div class="form-field-group">
                    <label class="form-field-label">Pilihan A</label>
                    <input type="text" class="form-field-input" name="questions[${index}][option_a]" placeholder="Teks opsi pilihan A" required>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Pilihan B</label>
                    <input type="text" class="form-field-input" name="questions[${index}][option_b]" placeholder="Teks opsi pilihan B" required>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Pilihan C</label>
                    <input type="text" class="form-field-input" name="questions[${index}][option_c]" placeholder="Teks opsi pilihan C" required>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Pilihan D</label>
                    <input type="text" class="form-field-input" name="questions[${index}][option_d]" placeholder="Teks opsi pilihan D" required>
                </div>
            </div>

            <div class="q-meta-grid-2col">
                <div class="form-field-group">
                    <label class="form-field-label">Kunci Jawaban Benar</label>
                    <select class="panel-select" name="questions[${index}][correct]" style="width: 100%; font-weight: 700;" required>
                        <option value="A">Kunci: Pilihan A</option>
                        <option value="B">Kunci: Pilihan B</option>
                        <option value="C">Kunci: Pilihan C</option>
                        <option value="D">Kunci: Pilihan D</option>
                    </select>
                </div>
                <div class="form-field-group">
                    <label class="form-field-label">Penjelasan / Pembahasan Soal (Opsional)</label>
                    <input type="text" class="form-field-input" name="questions[${index}][explanation]" placeholder="Alasan mengapa kunci ini benar (ditampilkan pada ulasan ujian)">
                </div>
            </div>
        `;
        return card;
    }

    function syncQuestionBlocks() {
        if (!repeaterStack) return;
        const cards = repeaterStack.querySelectorAll('.quiz-question-card');
        questionCount = cards.length;
        if (counterEl) counterEl.textContent = questionCount;

        cards.forEach((card, idx) => {
            card.setAttribute('data-index', idx);
            const pill = card.querySelector('.q-number-pill');
            if (pill) pill.textContent = `SOAL #${idx + 1}`;

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

            const correct = card.querySelector('select[name*="[correct]"]');
            if (correct) correct.name = `questions[${idx}][correct]`;

            const exp = card.querySelector('input[name*="[explanation]"]');
            if (exp) exp.name = `questions[${idx}][explanation]`;

            // Disable delete if only 1 question remains
            const delBtn = card.querySelector('.btn-remove-question');
            if (delBtn) {
                delBtn.disabled = cards.length <= 1;
                delBtn.setAttribute('onclick', `removeQuestionBlock(${idx})`);
            }
        });

        if (window.lucide) window.lucide.createIcons();
    }

    window.addQuestionBlock = function() {
        if (!repeaterStack) return;
        const newCard = createQuestionBlock(questionCount);
        repeaterStack.appendChild(newCard);
        syncQuestionBlocks();
        const firstInput = newCard.querySelector('textarea');
        if (firstInput) firstInput.focus();
    };

    window.removeQuestionBlock = function(idx) {
        if (!repeaterStack) return;
        const cards = repeaterStack.querySelectorAll('.quiz-question-card');
        if (cards.length <= 1) {
            if (window.showGeistToast) {
                window.showGeistToast('info', 'Minimal 1 Soal', 'Kuis wajib memiliki setidaknya 1 butir pertanyaan.');
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

    const btnAddQ = document.getElementById('btn-add-quiz-question');
    const btnAddQ2 = document.getElementById('btn-add-another-question');
    if (btnAddQ) btnAddQ.addEventListener('click', window.addQuestionBlock);
    if (btnAddQ2) btnAddQ2.addEventListener('click', window.addQuestionBlock);

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
                            <div class="empty-state-text">Mulai buat kuis baru atau gunakan formulir kuis untuk menambahkan ujian MikroTik.</div>
                        </div>
                    </td>
                </tr>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        qTbody.innerHTML = list.map(q => `
            <tr>
                <td style="font-weight: 700; color: #18181B;">${escapeHtml(q.title)}</td>
                <td><span class="role-pill">${escapeHtml(q.category || 'Routing')}</span></td>
                <td><span class="status-badge status-active">${escapeHtml(q.difficulty || 'Mudah')}</span></td>
                <td class="font-mono text-muted">${q.duration || 15} Menit</td>
                <td style="text-align: right;">
                    <div class="table-actions-group">
                        <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Kuis" onclick="confirmDeleteQuiz(${q.id}, '${escapeHtml(q.title).replace(/'/g, "\\'")}')">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        if (window.lucide) window.lucide.createIcons();
    }

    renderQuizRows(rawQuizzes);

    // Live Quiz Search Filter
    if (qSearch) {
        qSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const filtered = rawQuizzes.filter(q => 
                (q.title && q.title.toLowerCase().includes(query)) ||
                (q.category && q.category.toLowerCase().includes(query))
            );
            renderQuizRows(filtered);
        });
    }

    // Toggle Form Handlers
    const btnToggle = document.getElementById('btn-toggle-quiz-builder');
    const btnClose = document.getElementById('btn-close-quiz-builder');
    const btnCancel = document.getElementById('btn-cancel-quiz-form');
    const container = document.getElementById('quiz-builder-container');
    const qTitleInput = document.getElementById('quiz-input-title');

    function openQuizStudio() {
        if (container) {
            container.style.display = 'block';
            if (qTitleInput) qTitleInput.focus();
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function closeQuizStudio() {
        if (container) container.style.display = 'none';
    }

    if (btnToggle) btnToggle.addEventListener('click', () => {
        if (container.style.display === 'none') {
            openQuizStudio();
        } else {
            closeQuizStudio();
        }
    });

    if (btnClose) btnClose.addEventListener('click', closeQuizStudio);
    if (btnCancel) btnCancel.addEventListener('click', closeQuizStudio);

    window.confirmDeleteQuiz = function(id, title) {
        showGeistConfirm(
            'Hapus Kuis',
            `Apakah Anda yakin ingin menghapus kuis "${title}"? Seluruh data pertanyaan di dalamnya akan ikut terhapus.`,
            'Hapus Kuis',
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${window.BASE_URL}/admin/quizzes/delete/${id}`;
                form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">`;
                document.body.appendChild(form);
                form.submit();
            },
            true
        );
    };
}

// ==========================================================================
// 4. MODUL 2: DAFTARKAN MEMBER (High-Precision Form & Recent Feed)
// ==========================================================================
function renderMemberSection() {
    const sec = document.getElementById('member-section');
    if (!sec) return;

    const rawMembers = Array.isArray(window.NETQUIZ_MEMBERS) ? window.NETQUIZ_MEMBERS : [];
    const recentMembers = rawMembers.slice(0, 5);

    sec.innerHTML = `
        <div class="member-provision-stack">
            <!-- 1. Formulir Pendaftaran Siswa Card -->
            <div class="supabase-panel-card">
                <!-- Precision Corner Crosshairs -->
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <form action="${window.BASE_URL}/admin/users/create" method="POST" id="register-member-form">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">
                    
                    <div class="provision-form-grid">
                        <!-- Field 1: Username -->
                        <div class="form-field-group" style="margin-bottom: 0;">
                            <div class="provision-field-header">
                                <label class="form-field-label" style="margin: 0;">Username Siswa</label>
                                <span class="font-mono text-muted" style="font-size: 0.7rem;">Wajib</span>
                            </div>
                            <input type="text" class="form-field-input" name="username" id="reg-username" placeholder="budi_santoso" required autocomplete="off">
                        </div>

                        <!-- Field 2: Email -->
                        <div class="form-field-group" style="margin-bottom: 0;">
                            <div class="provision-field-header">
                                <label class="form-field-label" style="margin: 0;">Alamat Email</label>
                                <span class="font-mono text-muted" style="font-size: 0.7rem;">Wajib</span>
                            </div>
                            <input type="email" class="form-field-input" name="email" id="reg-email" placeholder="budi@sekolah.sch.id" required autocomplete="off">
                        </div>

                        <!-- Field 3: Kata Sandi -->
                        <div class="form-field-group" style="margin-bottom: 0;">
                            <div class="provision-field-header">
                                <label class="form-field-label" style="margin: 0;">Kata Sandi Akses</label>
                                <button type="button" class="btn-inline-generate" id="btn-generate-pw" title="Buat password acak">
                                    <i data-lucide="key" style="width: 12px; height: 12px;"></i>
                                    <span>Acak Sandi</span>
                                </button>
                            </div>
                            <div class="password-input-wrapper">
                                <input type="password" class="form-field-input" name="password" id="reg-password" placeholder="Minimal 8 karakter" required minlength="8">
                                <button type="button" class="btn-toggle-password" data-target="reg-password" title="Lihat/Sembunyikan Kata Sandi">
                                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                </button>
                            </div>
                            <div id="password-strength-bar" class="password-strength-container" style="margin-top: 0.4rem;">
                                <div class="password-strength-track">
                                    <div id="password-strength-fill" class="password-strength-fill"></div>
                                </div>
                                <span id="password-strength-text" class="password-strength-text"></span>
                            </div>
                        </div>

                        <!-- Field 4: Konfirmasi Kata Sandi -->
                        <div class="form-field-group" style="margin-bottom: 0;">
                            <div class="provision-field-header">
                                <label class="form-field-label" style="margin: 0;">Konfirmasi Kata Sandi</label>
                                <span id="password-match-msg" class="password-match-msg font-mono" style="font-size: 0.7rem;"></span>
                            </div>
                            <div class="password-input-wrapper">
                                <input type="password" class="form-field-input" name="confirm_password" id="reg-confirm-password" placeholder="Ulangi kata sandi" required minlength="8">
                                <button type="button" class="btn-toggle-password" data-target="reg-confirm-password" title="Lihat/Sembunyikan Kata Sandi">
                                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Bar -->
                    <div class="provision-footer">
                        <span class="provision-footer-note font-mono">Akun berstatus aktif dan dapat langsung digunakan login.</span>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="reset" class="btn-secondary-outline" id="btn-reset-member-form">Batal</button>
                            <button type="submit" class="btn-primary-black" id="btn-submit-member">
                                <i data-lucide="user-plus" style="width: 14px; height: 14px;"></i>
                                <span>Daftarkan Siswa</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2. Recent Registered Members Feed -->
            <div class="supabase-panel-card">
                <!-- Precision Corner Crosshairs -->
                <span class="corner-crosshair corner-tl">+</span>
                <span class="corner-crosshair corner-tr">+</span>
                <span class="corner-crosshair corner-bl">+</span>
                <span class="corner-crosshair corner-br">+</span>

                <div class="recent-members-header">
                    <span class="recent-members-title">
                        <i data-lucide="users" style="width: 15px; height: 15px; color: #71717A;"></i>
                        <span>Member Terdaftar Terbaru (${recentMembers.length})</span>
                    </span>
                    <a href="#manage-section" class="btn-inline-generate" onclick="const b=document.querySelector('.sidebar-menu-btn[data-target=manage-section]'); if(b) b.click();">
                        <span>Lihat Semua Member &rarr;</span>
                    </a>
                </div>

                <div class="panel-table-container">
                    <table class="supabase-data-table">
                        <thead>
                            <tr>
                                <th>MEMBER</th>
                                <th style="width: 130px;">ROLE</th>
                                <th style="width: 110px;">STATUS</th>
                                <th style="width: 150px;">TANGGAL BERGABUNG</th>
                                <th style="text-align: right; width: 110px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="recent-members-tbody">
                            ${recentMembers.length === 0 ? `
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #71717A; padding: 2rem;">Belum ada member terdaftar.</td>
                                </tr>
                            ` : recentMembers.map(m => {
                                const initials = getInitials(m.username || m.email);
                                const role = m.role || (m.email.includes('admin') ? 'Administrator' : 'Siswa');
                                const dateStr = formatDate(m.created_at);
                                const avatarColorClass = getAvatarBgClass(m.id || 1);
                                const status = m.status || 'Aktif';
                                const statusClass = status === 'Aktif' ? 'status-active' : 'status-inactive';
                                const safeUsername = escapeHtml(m.username).replace(/'/g, "\\'");
                                const safeEmail = escapeHtml(m.email).replace(/'/g, "\\'");

                                return `
                                    <tr>
                                        <td>
                                            <div class="member-user-cell">
                                                <div class="member-avatar ${avatarColorClass} font-mono">${escapeHtml(initials)}</div>
                                                <div class="member-user-info">
                                                    <span class="member-name">${escapeHtml(m.username)}</span>
                                                    <span class="member-email font-mono">${escapeHtml(m.email)}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="role-pill ${role === 'Administrator' ? 'role-admin' : ''}">${escapeHtml(role)}</span></td>
                                        <td><span class="status-badge ${statusClass}">${escapeHtml(status)}</span></td>
                                        <td class="font-mono text-muted">${escapeHtml(dateStr)}</td>
                                        <td style="text-align: right;">
                                            <div class="table-actions-group">
                                                <button type="button" class="btn-icon-action" title="Salin Email" onclick="navigator.clipboard.writeText('${safeEmail}'); showGeistToast('success', 'Email Tersalin', '${safeEmail}');">
                                                    <i data-lucide="copy"></i>
                                                </button>
                                                <button type="button" class="btn-icon-action" title="Edit Member" onclick="const b=document.querySelector('.sidebar-menu-btn[data-target=manage-section]'); if(b) b.click(); setTimeout(() => openEditMemberModal(${m.id}, '${safeUsername}', '${safeEmail}'), 100);">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    const regPwdInput = document.getElementById('reg-password');
    const confirmPwdInput = document.getElementById('reg-confirm-password');
    const strengthFill = document.getElementById('password-strength-fill');
    const strengthText = document.getElementById('password-strength-text');
    const matchMsg = document.getElementById('password-match-msg');
    const btnGenPw = document.getElementById('btn-generate-pw');
    const regForm = document.getElementById('register-member-form');

    // 1-Click Password Generator
    if (btnGenPw && regPwdInput && confirmPwdInput) {
        btnGenPw.addEventListener('click', () => {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$%&*';
            let generated = '';
            for (let i = 0; i < 12; i++) {
                generated += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            regPwdInput.type = 'text';
            confirmPwdInput.type = 'text';
            regPwdInput.value = generated;
            confirmPwdInput.value = generated;

            // Trigger strength calculation
            regPwdInput.dispatchEvent(new Event('input'));
            checkPasswordMatch();

            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(generated).catch(() => {});
            }

            showGeistToast('info', 'Password Dibuat', `Password acak tersalin ke clipboard.`);
        });
    }

    // Password Strength Indicator
    if (regPwdInput && strengthFill && strengthText) {
        regPwdInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 1) score++;
            if (val.length >= 8) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            if (val.length === 0) {
                strengthFill.style.width = '0%';
                strengthFill.style.backgroundColor = '#E5E7EB';
                strengthText.textContent = '';
            } else if (score <= 2) {
                strengthFill.style.width = '33%';
                strengthFill.style.backgroundColor = '#EF4444';
                strengthText.textContent = 'Lemah';
                strengthText.style.color = '#EF4444';
            } else if (score <= 3) {
                strengthFill.style.width = '66%';
                strengthFill.style.backgroundColor = '#F59E0B';
                strengthText.textContent = 'Sedang';
                strengthText.style.color = '#F59E0B';
            } else {
                strengthFill.style.width = '100%';
                strengthFill.style.backgroundColor = '#10B981';
                strengthText.textContent = 'Kuat';
                strengthText.style.color = '#10B981';
            }
            if (confirmPwdInput && confirmPwdInput.value.length > 0) {
                checkPasswordMatch();
            }
        });
    }

    function checkPasswordMatch() {
        if (!regPwdInput || !confirmPwdInput || !matchMsg) return;
        const pwd = regPwdInput.value;
        const cfm = confirmPwdInput.value;
        if (cfm.length === 0) { matchMsg.textContent = ''; return; }
        if (pwd === cfm) {
            matchMsg.textContent = '✓ Cocok';
            matchMsg.style.color = '#10B981';
        } else {
            matchMsg.textContent = '✗ Tidak cocok';
            matchMsg.style.color = '#EF4444';
        }
    }

    if (confirmPwdInput) {
        confirmPwdInput.addEventListener('input', checkPasswordMatch);
    }

    // Form Submit Handling
    if (regForm) {
        regForm.addEventListener('submit', (e) => {
            const pwd = regPwdInput ? regPwdInput.value : '';
            const cfm = confirmPwdInput ? confirmPwdInput.value : '';
            if (pwd !== cfm) {
                e.preventDefault();
                showGeistToast('error', 'Validasi Gagal', 'Kata sandi dan konfirmasi kata sandi tidak cocok!');
                if (confirmPwdInput) confirmPwdInput.focus();
                return false;
            }
            if (pwd.length < 8) {
                e.preventDefault();
                showGeistToast('error', 'Validasi Gagal', 'Kata sandi minimal harus 8 karakter!');
                if (regPwdInput) regPwdInput.focus();
                return false;
            }
        });
    }

    bindPasswordToggles();
}

// ==========================================================================
// 5. MODUL 3: MANAJEMEN MEMBER (High-Density Data Table & Floating Bulk Bar)
// ==========================================================================
function renderManageMemberSection() {
    const sec = document.getElementById('manage-section');
    if (!sec) return;

    const rawMembers = Array.isArray(window.NETQUIZ_MEMBERS) ? window.NETQUIZ_MEMBERS : [];

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

    const tbody = document.getElementById('member-table-body');
    const searchInput = document.getElementById('member-search-input');
    const filterRole = document.getElementById('filter-role');
    const filterStatus = document.getElementById('filter-status');
    const selectAllCheckbox = document.getElementById('select-all-members');
    const floatingBulkBar = document.getElementById('floating-bulk-bar');
    const bulkCountBadge = document.getElementById('bulk-selected-count');
    const btnBulkDismiss = document.getElementById('btn-bulk-dismiss');
    const btnBulkExport = document.getElementById('btn-bulk-export');

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

        tbody.innerHTML = list.map(m => {
            const initials = getInitials(m.username || m.email);
            const role = m.role || (m.email.includes('admin') ? 'Administrator' : 'Siswa');
            const dateStr = formatDate(m.created_at);
            const avatarColorClass = getAvatarBgClass(m.id || 1);
            const status = m.status || 'Aktif';
            const statusClass = status === 'Aktif' ? 'status-active' : (status === 'Pending' ? 'status-pending' : 'status-inactive');
            const toggleStatus = status === 'Aktif' ? 'Nonaktif' : 'Aktif';
            const toggleIcon = status === 'Aktif' ? 'user-x' : 'user-check';
            const toggleTitle = status === 'Aktif' ? 'Nonaktifkan Member' : 'Aktifkan Member';
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
                    <td><span class="role-pill ${role === 'Administrator' ? 'role-admin' : ''}">${escapeHtml(role)}</span></td>
                    <td><span class="status-badge ${statusClass}">${escapeHtml(status)}</span></td>
                    <td class="font-mono text-muted">${escapeHtml(dateStr)}</td>
                    <td style="text-align: right;">
                        <div class="table-actions-group">
                            <button type="button" class="btn-icon-action" title="Edit Member" onclick="openEditMemberModal(${m.id}, '${safeUsername}', '${safeEmail}')">
                                <i data-lucide="pencil"></i>
                            </button>
                            <form action="${window.BASE_URL}/admin/users/suspend/${m.id}" method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">
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
        }).join('');

        if (window.lucide) window.lucide.createIcons();

        // Update pagination info
        const paginationInfo = document.getElementById('pagination-info-text');
        if (paginationInfo) {
            paginationInfo.innerHTML = `Menampilkan <span class="font-mono" style="font-weight: 700;">1-${list.length}</span> dari <span class="font-mono" style="font-weight: 700;">${rawMembers.length}</span> member`;
        }

        bindRowCheckboxes();
    }

    renderRows(rawMembers);

    // Filter Logic with Debounce
    function filterData() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const roleVal = filterRole ? filterRole.value : 'all';
        const statusVal = filterStatus ? filterStatus.value : 'all';

        const filtered = rawMembers.filter(m => {
            const matchQuery = (m.username && m.username.toLowerCase().includes(query)) || (m.email && m.email.toLowerCase().includes(query));
            const mRole = m.role || (m.email.includes('admin') ? 'Administrator' : 'Siswa');
            const matchRole = (roleVal === 'all') || (mRole === roleVal);
            const mStatus = m.status || 'Aktif';
            const matchStatus = (statusVal === 'all') || (mStatus === statusVal);

            return matchQuery && matchRole && matchStatus;
        });

        renderRows(filtered);
    }

    if (searchInput) searchInput.addEventListener('input', filterData);
    if (filterRole) filterRole.addEventListener('change', filterData);
    if (filterStatus) filterStatus.addEventListener('change', filterData);

    // Floating Bulk Action Bar Handlers
    function bindRowCheckboxes() {
        const rowCheckboxes = document.querySelectorAll('.member-row-checkbox');
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkState);
        });
    }

    function updateBulkState() {
        const selected = document.querySelectorAll('.member-row-checkbox:checked');
        if (!floatingBulkBar || !bulkCountBadge) return;

        if (selected.length > 0) {
            bulkCountBadge.textContent = `${selected.length} Dipilih`;
            floatingBulkBar.classList.add('active');
        } else {
            floatingBulkBar.classList.remove('active');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const rowCheckboxes = document.querySelectorAll('.member-row-checkbox');
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkState();
        });
    }

    if (btnBulkDismiss) {
        btnBulkDismiss.addEventListener('click', () => {
            const rowCheckboxes = document.querySelectorAll('.member-row-checkbox');
            rowCheckboxes.forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            if (floatingBulkBar) floatingBulkBar.classList.remove('active');
        });
    }

    // Bulk Export to CSV
    if (btnBulkExport) {
        btnBulkExport.addEventListener('click', () => {
            const selected = document.querySelectorAll('.member-row-checkbox:checked');
            if (selected.length === 0) return;

            let csv = 'ID,Username,Email,Status\n';
            selected.forEach(cb => {
                csv += `"${cb.value}","${cb.getAttribute('data-username')}","${cb.getAttribute('data-email')}","${cb.getAttribute('data-status')}"\n`;
            });

            downloadCsvFile(csv, 'selected_members.csv');
            showGeistToast('success', 'Export Selesai', `${selected.length} data member terpilih berhasil diexport.`);
        });
    }

    // Export All CSV
    const btnExportAll = document.getElementById('btn-export-csv');
    if (btnExportAll) {
        btnExportAll.addEventListener('click', () => {
            if (rawMembers.length === 0) {
                showGeistToast('info', 'Data Kosong', 'Tidak ada member untuk diexport.');
                return;
            }
            let csv = 'ID,Username,Email,Role,Status,Tanggal Bergabung\n';
            rawMembers.forEach(m => {
                csv += `"${m.id}","${m.username}","${m.email}","${m.role || 'Siswa'}","${m.status || 'Aktif'}","${m.created_at}"\n`;
            });
            downloadCsvFile(csv, 'semua_member_netquiz.csv');
            showGeistToast('success', 'Export CSV Berhasil', `${rawMembers.length} member telah diunduh.`);
        });
    }

    function downloadCsvFile(content, fileName) {
        const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    // Delete Member Dialog
    window.confirmDeleteMember = function(id, username) {
        showGeistConfirm(
            'Hapus Member',
            `Apakah Anda yakin ingin menghapus akun siswa "${username}"? Tindakan ini tidak dapat dibatalkan.`,
            'Hapus Member',
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${window.BASE_URL}/admin/users/delete/${id}`;
                form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">`;
                document.body.appendChild(form);
                form.submit();
            },
            true
        );
    };
}

// ==========================================================================
// 6. MODUL 4: MATERI BELAJAR (Quick Formatting Toolbar & Live Split Preview)
// ==========================================================================
function renderMaterialsSection() {
    const sec = document.getElementById('materials-section');
    if (!sec) return;

    const materials = Array.isArray(window.NETQUIZ_MATERIALS) ? window.NETQUIZ_MATERIALS : [];

    sec.innerHTML = `
        <div class="supabase-panel-card">
            <!-- Precision Corner Crosshairs -->
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <!-- Create Material Form (Collapsible) -->
            <div id="material-form-container" style="display: none; padding: 1.5rem; border-bottom: 1px solid #E5E7EB; background-color: #FAFAFA;">
                <form action="${window.BASE_URL}/admin/materials/create" method="POST" id="form-create-material">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <h3 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">Penulisan Artikel Materi Baru</h3>
                            <p style="font-size: 0.8rem; color: #52525B; margin-top: 0.2rem;">Gunakan toolbar formatting untuk menyusun artikel RouterOS yang rapi.</p>
                        </div>
                        <button type="button" onclick="window.closeMaterialForm()" class="btn-secondary-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Tutup</button>
                    </div>

                    <div class="form-field-group">
                        <label class="form-field-label">Judul Materi</label>
                        <input type="text" class="form-field-input" name="title" id="material-input-title" placeholder="Contoh: Konfigurasi VLAN & Trunking pada MikroTik RouterOS" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Kategori</label>
                            <select class="panel-select" name="category" style="width: 100%;">
                                <option value="Routing">Routing</option>
                                <option value="Firewall & NAT">Firewall & NAT</option>
                                <option value="Wireless">Wireless</option>
                                <option value="Network Management">Network Management</option>
                            </select>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Tingkat Kesulitan</label>
                            <select class="panel-select" name="difficulty" style="width: 100%;">
                                <option value="Mudah">Mudah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Sulit">Sulit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Editor / Live Preview Switch Tabs -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <div class="editor-mode-switch">
                            <button type="button" class="editor-mode-btn active" id="btn-mode-editor">Editor Kode</button>
                            <button type="button" class="editor-mode-btn" id="btn-mode-preview">Pratinjau Siswa</button>
                        </div>
                        <span class="reading-time-pill" id="material-reading-time">
                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                            <span>~1 menit baca</span>
                        </span>
                    </div>

                    <!-- Quick Formatting Toolbar -->
                    <div id="material-editor-wrapper">
                        <div class="editor-quick-toolbar">
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<h2>', '</h2>')" title="Heading 2">H2</button>
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<h3>', '</h3>')" title="Heading 3">H3</button>
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<strong>', '</strong>')" title="Bold"><b>B</b></button>
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<em>', '</em>')" title="Italic"><i>I</i></button>
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<pre><code>', '</code></pre>')" title="CLI Command Block">&lt;/&gt; CLI</button>
                            <button type="button" class="toolbar-btn" onclick="insertHtmlTag('<p>', '</p>')" title="Paragraf">&lt;p&gt;</button>
                            <button type="button" class="toolbar-btn" onclick="formatHtmlContent()" title="Auto Format HTML">Format HTML</button>
                            <button type="button" class="toolbar-btn" onclick="downloadJsonTemplate()" title="Unduh Template JSON">Template JSON</button>
                        </div>
                        <div class="form-field-group" style="margin-bottom: 0;">
                            <textarea class="form-field-input" name="content" id="material-content-textarea" rows="6" placeholder="Tuliskan isi artikel materi menggunakan tag HTML atau gunakan toolbar di atas..." required style="border-top-left-radius: 0; border-top-right-radius: 0;"></textarea>
                        </div>
                    </div>

                    <!-- Live Student Preview Box -->
                    <div id="material-preview-box" class="material-live-preview-box" style="display: none;">
                        <p style="color: #52525B; font-style: italic;">Pratinjau kosong. Tulis isi materi terlebih dahulu.</p>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
                        <button type="submit" class="btn-primary-black">
                            <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                            <span>Publikasikan Materi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Toolbar & Search (Anti-Slop Clean Input with Shortcut Badge) -->
            <div class="panel-toolbar">
                <div class="panel-toolbar-left">
                    <div class="search-input-wrapper">
                        <input type="text" id="material-search-input" class="panel-search-input" placeholder="Cari judul modul atau materi...">
                        <span class="search-shortcut-badge" title="Tekan '/' pada keyboard untuk mencari">/</span>
                    </div>
                </div>
                <div class="panel-toolbar-right">
                    <button type="button" class="btn-primary-black" onclick="window.openMaterialForm()" style="font-size: 0.8rem; padding: 0.45rem 0.9rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        <span>+ Tulis Artikel Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table -->
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
    `;

    const matTbody = document.getElementById('materials-table-body');
    const matSearch = document.getElementById('material-search-input');

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

        matTbody.innerHTML = list.map(m => `
            <tr>
                <td style="font-weight: 700; color: #18181B;">${escapeHtml(m.title)}</td>
                <td><span class="role-pill">${escapeHtml(m.category || 'Routing')}</span></td>
                <td><span class="status-badge status-active">${escapeHtml(m.difficulty || 'Mudah')}</span></td>
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
        `).join('');

        if (window.lucide) window.lucide.createIcons();
    }

    renderMatRows(materials);

    if (matSearch) {
        matSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const filtered = materials.filter(m => 
                (m.title && m.title.toLowerCase().includes(query)) ||
                (m.category && m.category.toLowerCase().includes(query))
            );
            renderMatRows(filtered);
        });
    }

    // Editor / Live Preview Mode Switch
    const btnModeEditor = document.getElementById('btn-mode-editor');
    const btnModePreview = document.getElementById('btn-mode-preview');
    const editorWrapper = document.getElementById('material-editor-wrapper');
    const previewBox = document.getElementById('material-preview-box');
    const contentTextarea = document.getElementById('material-content-textarea');
    const readingTimePill = document.getElementById('material-reading-time');

    function updateReadingTime() {
        if (!contentTextarea || !readingTimePill) return;
        const text = contentTextarea.value.replace(/<[^>]*>/g, ' ').trim();
        const words = text ? text.split(/\s+/).length : 0;
        const minutes = Math.max(1, Math.ceil(words / 150));
        readingTimePill.innerHTML = `<i data-lucide="clock" style="width: 12px; height: 12px;"></i> <span>~${minutes} menit baca (${words} kata)</span>`;
        if (window.lucide) window.lucide.createIcons();
    }

    if (contentTextarea) {
        contentTextarea.addEventListener('input', updateReadingTime);
    }

    if (btnModeEditor && btnModePreview && editorWrapper && previewBox) {
        btnModeEditor.addEventListener('click', () => {
            btnModeEditor.classList.add('active');
            btnModePreview.classList.remove('active');
            editorWrapper.style.display = 'block';
            previewBox.style.display = 'none';
        });

        btnModePreview.addEventListener('click', () => {
            btnModePreview.classList.add('active');
            btnModeEditor.classList.remove('active');
            editorWrapper.style.display = 'none';
            previewBox.style.display = 'block';

            const rawContent = contentTextarea ? contentTextarea.value : '';
            previewBox.innerHTML = rawContent.trim() || '<p style="color: #52525B; font-style: italic;">Pratinjau kosong. Tulis isi materi terlebih dahulu.</p>';
        });
    }

    // Toggle Form Handlers
    const btnClose = document.getElementById('btn-close-material-form');
    const formContainer = document.getElementById('material-form-container');
    if (btnClose && formContainer) {
        btnClose.addEventListener('click', () => {
            formContainer.style.display = 'none';
        });
    }

    window.confirmDeleteMaterial = function(id, title) {
        showGeistConfirm(
            'Hapus Materi Belajar',
            `Apakah Anda yakin ingin menghapus materi "${title}"?`,
            'Hapus Materi',
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${window.BASE_URL}/admin/materials/delete/${id}`;
                form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">`;
                document.body.appendChild(form);
                form.submit();
            },
            true
        );
    };
}

// ==========================================================================
// 7. MODUL 5: LENCANA PRESTASI (Visual Icon Picker & 3-Col Grid)
// ==========================================================================
function renderBadgeSection() {
    const sec = document.getElementById('badge-section');
    if (!sec) return;

    const badges = Array.isArray(window.NETQUIZ_BADGES) ? window.NETQUIZ_BADGES : [];

    sec.innerHTML = `
        <!-- Form Badge (Collapsible) -->
        <div id="badge-form-container" style="display: none; margin-bottom: 1.5rem;" class="supabase-panel-card">
            <!-- Precision Corner Crosshairs -->
            <span class="corner-crosshair corner-tl">+</span>
            <span class="corner-crosshair corner-tr">+</span>
            <span class="corner-crosshair corner-bl">+</span>
            <span class="corner-crosshair corner-br">+</span>

            <div style="padding: 1.25rem 1.5rem;">
                <form action="${window.BASE_URL}/admin/badges/create" method="POST">
                    <input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">
                    <input type="hidden" name="icon" id="badge-selected-icon" value="award">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-family: var(--font-heading); font-size: 1.05rem; font-weight: 800; color: #18181B; margin: 0;">Formulir Lencana Baru</h4>
                            <p style="font-size: 0.8rem; color: #52525B; margin-top: 0.2rem;">Tentukan nama lencana, ikon visual, dan target metrik pencapaian siswa.</p>
                        </div>
                        <button type="button" onclick="window.closeBadgeForm()" class="btn-secondary-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Tutup</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-field-group">
                            <label class="form-field-label">Nama Lencana</label>
                            <input type="text" class="form-field-input" name="title" placeholder="Contoh: Routing Master MTCNA" required>
                        </div>
                        <div class="form-field-group">
                            <label class="form-field-label">Nilai Target (Kuis Selesai)</label>
                            <input type="number" class="form-field-input" name="target_value" value="5" min="1" required>
                        </div>
                    </div>

                    <!-- Visual Icon Picker Grid -->
                    <div class="form-field-group">
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

                    <div class="form-field-group">
                        <label class="form-field-label">Deskripsi Lencana</label>
                        <input type="text" class="form-field-input" name="description" placeholder="Deskripsi syarat perolehan..." required>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
                        <button type="submit" class="btn-primary-black">
                            <i data-lucide="award" style="width: 15px; height: 15px;"></i>
                            <span>Simpan Lencana</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3-Column Grid Layout -->
        <div class="badges-3col-grid">
            ${badges.map(b => `
                <div class="supabase-panel-card" style="padding: 1.25rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <!-- Precision Corner Crosshairs -->
                    <span class="corner-crosshair corner-tl">+</span>
                    <span class="corner-crosshair corner-tr">+</span>
                    <span class="corner-crosshair corner-bl">+</span>
                    <span class="corner-crosshair corner-br">+</span>

                    <div>
                        <!-- Header Kartu -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                            <div style="width: 38px; height: 38px; background-color: #18181B; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #FFF;">
                                <i data-lucide="${escapeHtml(b.icon || 'award')}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <button type="button" class="btn-icon-action btn-action-danger" title="Hapus Lencana" onclick="confirmDeleteBadge(${b.id}, '${escapeHtml(b.title).replace(/'/g, "\\'")}')">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                        <!-- Body Kartu -->
                        <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 800; color: #18181B; margin: 0 0 0.3rem 0;">${escapeHtml(b.title)}</h4>
                        <span class="status-badge status-active" style="margin-bottom: 0.6rem;">Target: ${b.target_value || 1} Kuis</span>
                        <p style="font-size: 0.825rem; color: #52525B; margin: 0; line-height: 1.4;">${escapeHtml(b.description)}</p>
                    </div>

                    <!-- Footer Kartu -->
                    <div style="margin-top: 1.1rem; padding-top: 0.75rem; border-top: 1px solid #E5E7EB;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span style="font-size: 0.75rem; color: #52525B;" class="font-mono">Diperoleh oleh ${b.earned_count || 0} Siswa</span>
                            <span style="font-size: 0.7rem; font-weight: 700; color: #18181B;" class="font-mono">${Math.min(Math.round(((b.earned_count || 0) / (b.target_value || 1)) * 100), 100)}%</span>
                        </div>
                        <div class="badge-progress-track">
                            <div class="badge-progress-fill" style="width: ${Math.min(Math.round(((b.earned_count || 0) / (b.target_value || 1)) * 100), 100)}%;"></div>
                        </div>
                    </div>
                </div>
            `).join('')}

            <!-- 1 Kartu Draf Kosong Dashed Border Pemicu Alternatif -->
            <button type="button" class="badge-card-dashed-draft" onclick="window.openBadgeForm()">
                <i data-lucide="plus-circle" style="width: 28px; height: 28px; margin-bottom: 0.5rem;"></i>
                <span style="font-family: var(--font-heading); font-size: 0.875rem; font-weight: 700;">+ Buat Lencana Baru</span>
            </button>
        </div>
    `;

    // Icon Picker Click Handlers
    const iconOptions = document.querySelectorAll('.icon-picker-option');
    const selectedIconInput = document.getElementById('badge-selected-icon');
    iconOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            iconOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            if (selectedIconInput) {
                selectedIconInput.value = this.getAttribute('data-icon');
            }
        });
    });

    const btnClose = document.getElementById('btn-close-badge-form');
    const formContainer = document.getElementById('badge-form-container');
    if (btnClose && formContainer) {
        btnClose.addEventListener('click', () => {
            formContainer.style.display = 'none';
        });
    }

    window.confirmDeleteBadge = function(id, title) {
        showGeistConfirm(
            'Hapus Lencana',
            `Apakah Anda yakin ingin menghapus lencana "${title}"?`,
            'Hapus Lencana',
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${window.BASE_URL}/admin/badges/delete/${id}`;
                form.innerHTML = `<input type="hidden" name="csrf_token" value="${window.CSRF_TOKEN || ''}">`;
                document.body.appendChild(form);
                form.submit();
            },
            true
        );
    };
}

// ==========================================================================
// 8. HELPER UTILITIES
// ==========================================================================
function getInitials(name) {
    if (!name) return 'U';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function getAvatarBgClass(id) {
    const classes = ['', 'avatar-blue', 'avatar-purple', 'avatar-amber', 'avatar-emerald'];
    return classes[id % classes.length];
}

function formatDate(dateString) {
    if (!dateString) return '12 Mei 2026';
    try {
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    } catch (e) {
        return dateString;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
