// --- NETQUIZ ADMIN DASHBOARD CONTROLLER (EXTERNAL JS) ---

// Global function to trigger template.json download for import
function downloadJsonTemplate() {
    const templateData = {
        "title": "Contoh Judul Materi Pembelajaran",
        "category": "Routing",
        "content": "<h2>Sub-Bab Materi</h2><p>Tulis paragraf penjelasan materi di sini...</p><h3>Contoh Konfigurasi CLI</h3><pre><code>/ip route add gateway=192.168.1.1</code></pre>"
    };
    const jsonString = JSON.stringify(templateData, null, 2);
    const blob = new Blob([jsonString], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "template_materi.json";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Global editor helper function to insert HTML tags at cursor position
function insertHtmlTag(tagOpen, tagClose = '') {
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
    const tab = '    '; // 4 spaces indentation
    let formatted = '';

    // Normalize spacing and clean extra whitespace between elements
    html = html.replace(/\s+/g, ' ').replace(/>\s+</g, '><');

    // Tokenize HTML tags and text contents
    const tokens = html.split(/(<\/?[^>]+>)/g);

    for (let i = 0; i < tokens.length; i++) {
        let token = tokens[i].trim();
        if (!token) continue;

        if (token.startsWith('</')) {
            // Closing tag: reduce indentation level
            indent = Math.max(0, indent - 1);
            formatted += '\n' + tab.repeat(indent) + token;
        } else if (token.startsWith('<') && !token.startsWith('<!') && !token.endsWith('/>')) {
            // Opening tag: append and increase indentation (skip self-closing tags)
            const isSelfClosing = /<(img|br|hr|input|link|meta)/i.test(token);
            formatted += '\n' + tab.repeat(indent) + token;
            if (!isSelfClosing) {
                indent++;
            }
        } else {
            // Raw text node
            formatted += '\n' + tab.repeat(indent) + token;
        }
    }

    // Apply formatted value back to the editor field
    textarea.value = formatted.trim();
}

document.addEventListener('DOMContentLoaded', () => {
    const config = window.NetQuizConfig || { baseUrl: '' };

    // --- TAB SWITCHER LOGIC ---
    const tabButtons = document.querySelectorAll('.admin-tab-btn');
    const sections = document.querySelectorAll('.admin-section-content');

    function activateTab(targetId) {
        const targetBtn = document.querySelector(`.admin-tab-btn[data-target="${targetId}"]`);
        const targetSec = document.getElementById(targetId);

        if (targetBtn && targetSec) {
            // Remove active classes
            tabButtons.forEach(b => b.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));

            // Set active classes
            targetBtn.classList.add('active');
            targetSec.classList.add('active');

            // Update hash in URL quietly without page jump
            if (window.location.hash !== '#' + targetId) {
                history.replaceState(null, '', '#' + targetId);
            }

            // Prevent page scroll for scoped sections on desktop if content fits on screen
            const isShortSection = (targetId === 'badge-section' || targetId === 'profile-section' || targetId === 'manage-section' || targetId === 'materials-section');
            const isDesktop = window.innerWidth >= 1024;
            const contentFits = document.documentElement.scrollHeight <= window.innerHeight;

            if (isShortSection && isDesktop && contentFits) {
                document.documentElement.style.overflowY = 'hidden';
                document.body.style.overflowY = 'hidden';
            } else {
                document.documentElement.style.overflowY = 'auto';
                document.body.style.overflowY = 'auto';
            }

            // Update active list button text dynamically
            const modalBtnText = document.getElementById('open-quiz-modal-text');
            if (modalBtnText) {
                if (targetId === 'badge-section') {
                    modalBtnText.innerText = 'Lihat Lencana Aktif';
                } else if (targetId === 'materials-section') {
                    modalBtnText.innerText = 'Lihat Materi Aktif';
                } else {
                    modalBtnText.innerText = 'Lihat Kuis Aktif';
                }
            }

            // Auto-open visual builder when clicking Materi Belajar (unless just saved)
            if (targetId === 'materials-section') {
                if (typeof openVisualBuilderFromForm === 'function') {
                    if (sessionStorage.getItem('just_saved_material') === 'true') {
                        sessionStorage.removeItem('just_saved_material');
                    } else {
                        openVisualBuilderFromForm();
                    }
                }
            }
        }
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            activateTab(targetId);
        });
    });

    // Initialize active tab from URL hash
    function initTabFromHash() {
        const currentHash = window.location.hash.substring(1);
        if (currentHash && document.getElementById(currentHash) && document.querySelector(`.admin-tab-btn[data-target="${currentHash}"]`)) {
            activateTab(currentHash);
        } else {
            activateTab('quiz-section');
        }
    }

    // Run on load and on hash change
    initTabFromHash();
    window.addEventListener('hashchange', initTabFromHash);

    // --- JSON MATERIAL IMPORT LOGIC ---
    const jsonFileInput = document.getElementById('import-material-json');
    if (jsonFileInput) {
        jsonFileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const data = JSON.parse(event.target.result);

                    // Validate JSON format
                    if (!data.title || !data.content || !data.category) {
                        alert('Format JSON tidak valid! File JSON harus memiliki properti "title", "content", dan "category".');
                        return;
                    }

                    // Fill in the form fields
                    const form = document.getElementById('create-material-form');
                    if (form) {
                        document.getElementById('form-material-title').value = data.title;
                        document.getElementById('form-material-category').value = data.category;
                        document.getElementById('form-material-difficulty').value = data.difficulty || 'Mudah';
                        document.getElementById('form-material-content').value = data.content;

                        // Open visual builder and go directly to preview mode
                        openVisualBuilderFromForm(true);
                        setBuilderMode('preview');

                        alert('Materi berhasil diimpor dari file JSON dan langsung diarahkan ke halaman Pratinjau (Preview) Visual Builder.');
                    }
                } catch (err) {
                    alert('Gagal membaca file JSON: ' + err.message);
                }
            };
            reader.readAsText(file);

            // Reset file input value so same file can be selected again
            jsonFileInput.value = '';
        });
    }

    // --- MEMBER REGISTRATION VALIDATION ---
    const registerForm = document.getElementById('register-member-form');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const passwordInput = registerForm.querySelector('input[name="password"]');
            if (passwordInput && passwordInput.value.length < 8) {
                e.preventDefault();
                alert('Password sementara harus minimal 8 karakter!');
            }
        });
    }

    // Category Button Selectors
    const categoryInput = document.getElementById('selected-category-input');
    const categoryButtons = document.querySelectorAll('.category-select-btn');

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (categoryInput) {
                categoryInput.value = btn.getAttribute('data-value');
            }
        });
    });

    // Active Quiz Modal Selectors
    const activeModal = document.getElementById('quiz-list-modal');
    const openActiveModalBtn = document.getElementById('open-quiz-modal-btn');
    const closeActiveModalBtn = document.getElementById('close-quiz-modal-btn');

    if (openActiveModalBtn && activeModal) {
        openActiveModalBtn.addEventListener('click', () => {
            const activeSection = document.querySelector('.admin-section-content.active');
            const activeId = activeSection ? activeSection.getAttribute('id') : '';

            const modalTitle = document.getElementById('active-list-modal-title');
            const quizzesContainer = document.getElementById('modal-list-quizzes');
            const badgesContainer = document.getElementById('modal-list-badges');
            const materialsContainer = document.getElementById('modal-list-materials');

            if (quizzesContainer) quizzesContainer.style.display = 'none';
            if (badgesContainer) badgesContainer.style.display = 'none';
            if (materialsContainer) materialsContainer.style.display = 'none';

            if (activeId === 'badge-section') {
                if (modalTitle) modalTitle.innerText = 'Daftar Lencana yang Aktif Saat Ini';
                if (badgesContainer) badgesContainer.style.display = 'block';
            } else if (activeId === 'materials-section') {
                if (modalTitle) modalTitle.innerText = 'Daftar Materi yang Aktif Saat Ini';
                if (materialsContainer) materialsContainer.style.display = 'block';
            } else {
                if (modalTitle) modalTitle.innerText = 'Daftar Kuis yang Aktif Saat Ini';
                if (quizzesContainer) quizzesContainer.style.display = 'block';
            }

            activeModal.classList.add('show');
        });
    }

    if (closeActiveModalBtn && activeModal) {
        closeActiveModalBtn.addEventListener('click', () => {
            activeModal.classList.remove('show');
        });
    }

    if (activeModal) {
        activeModal.addEventListener('click', (e) => {
            if (e.target === activeModal) {
                activeModal.classList.remove('show');
            }
        });
    }

    // Saved Questions Modal Selectors
    const savedModal = document.getElementById('saved-questions-modal');
    const openSavedModalBtn = document.getElementById('open-saved-modal-btn');
    const closeSavedModalBtn = document.getElementById('close-saved-modal-btn');
    const modalSavedList = document.getElementById('saved-questions-list');

    if (openSavedModalBtn && savedModal) {
        openSavedModalBtn.addEventListener('click', () => {
            savedModal.classList.add('show');
        });
    }

    if (closeSavedModalBtn && savedModal) {
        closeSavedModalBtn.addEventListener('click', () => {
            savedModal.classList.remove('show');
        });
    }

    if (savedModal) {
        savedModal.addEventListener('click', (e) => {
            if (e.target === savedModal) {
                savedModal.classList.remove('show');
            }
        });
    }

    // Badge List Modal Selectors
    const badgeModal = document.getElementById('badge-list-modal');
    const openBadgeModalBtn = document.getElementById('open-badge-modal-btn');
    const closeBadgeModalBtn = document.getElementById('close-badge-modal-btn');

    if (openBadgeModalBtn && badgeModal) {
        openBadgeModalBtn.addEventListener('click', () => {
            badgeModal.classList.add('show');
        });
    }

    if (closeBadgeModalBtn && badgeModal) {
        closeBadgeModalBtn.addEventListener('click', () => {
            badgeModal.classList.remove('show');
        });
    }

    window.deleteBadgeSingle = function (id) {
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = `${config.baseUrl}/admin/badge/delete/${id}`;
        document.body.appendChild(tempForm);
        tempForm.submit();
    };

    if (badgeModal) {
        badgeModal.addEventListener('click', (e) => {
            if (e.target === badgeModal) {
                badgeModal.classList.remove('show');
            }
        });

        // Multi-select Badges logic
        const toggleSelectModeBtn = document.getElementById('btn-toggle-select-mode');
        const bulkDeleteBtn = document.getElementById('btn-bulk-delete-badges');
        const badgeCheckboxes = document.querySelectorAll('.badge-item-checkbox');

        let isSelectMode = false;

        function updateBulkDeleteButtonState() {
            const checkedCount = document.querySelectorAll('.badge-item-checkbox:checked').length;
            if (bulkDeleteBtn) {
                if (checkedCount > 0 && isSelectMode) {
                    bulkDeleteBtn.disabled = false;
                    bulkDeleteBtn.style.opacity = '1';
                    bulkDeleteBtn.style.cursor = 'pointer';
                } else {
                    bulkDeleteBtn.disabled = true;
                    bulkDeleteBtn.style.opacity = '0.5';
                    bulkDeleteBtn.style.cursor = 'not-allowed';
                }
            }
        }

        if (toggleSelectModeBtn) {
            toggleSelectModeBtn.addEventListener('click', () => {
                isSelectMode = !isSelectMode;
                if (isSelectMode) {
                    toggleSelectModeBtn.textContent = 'Batal';
                    badgeCheckboxes.forEach(cb => {
                        cb.style.display = 'inline-block';
                    });
                } else {
                    toggleSelectModeBtn.textContent = 'Pilih';
                    badgeCheckboxes.forEach(cb => {
                        cb.checked = false;
                        cb.style.display = 'none';
                    });
                }
                updateBulkDeleteButtonState();
            });
        }

        badgeCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateBulkDeleteButtonState();
            });
        });
    }

    // Question Builder Selectors
    const hiddenContainer = document.getElementById('hidden-inputs-container');
    const savedCountEl = document.getElementById('saved-count');
    const addBtn = document.getElementById('add-question-btn');

    // Form fields
    const qTextInput = document.getElementById('q-text');
    const qOptAInput = document.getElementById('q-opt-a');
    const qOptBInput = document.getElementById('q-opt-b');
    const qOptCInput = document.getElementById('q-opt-c');
    const qOptDInput = document.getElementById('q-opt-d');
    const qCorrectSelect = document.getElementById('q-correct');
    const qExplanationInput = document.getElementById('q-explanation');

    let savedQuestions = [];

    const quizTitleInput = document.querySelector('#create-quiz-form input[name="title"]');
    const quizDurationInput = document.querySelector('#create-quiz-form input[name="duration"]');
    const quizDescInput = document.querySelector('#create-quiz-form textarea[name="description"]');

    function updateDOM() {
        if (!modalSavedList || !hiddenContainer || !savedCountEl) return;
        modalSavedList.innerHTML = '';
        hiddenContainer.innerHTML = '';

        const submitQuizBtn = document.getElementById('btn-submit-quiz');
        const isQuizInfoValid = quizTitleInput && quizTitleInput.value.trim() !== '' && quizDurationInput && quizDurationInput.value.trim() !== '' && quizDescInput && quizDescInput.value.trim() !== '';

        if (submitQuizBtn) {
            if (savedQuestions.length >= 1 && isQuizInfoValid) {
                submitQuizBtn.disabled = false;
                submitQuizBtn.style.opacity = '1';
                submitQuizBtn.style.cursor = 'pointer';
            } else {
                submitQuizBtn.disabled = true;
                submitQuizBtn.style.opacity = '0.5';
                submitQuizBtn.style.cursor = 'not-allowed';
            }
        }

        if (savedQuestions.length === 0) {
            modalSavedList.innerHTML = `
                <div style="font-size: 0.85rem; color: #94a3b8; font-style: italic; padding: 1.5rem 0; text-align: center;">
                    Belum ada soal yang disimpan. Tambahkan soal menggunakan formulir di luar modal.
                </div>
            `;
            savedCountEl.textContent = '0';
            return;
        }

        savedCountEl.textContent = savedQuestions.length.toString();

        savedQuestions.forEach((q, index) => {
            const item = document.createElement('div');
            item.className = 'quiz-row-item';
            item.style.backgroundColor = '#f8fafc';
            item.style.padding = '0.75rem 1rem';
            item.style.borderRadius = '8px';
            item.style.border = '1px solid #e2e8f0';
            item.innerHTML = `
                <div class="quiz-row-info">
                    <span style="font-weight: 700; font-size: 0.85rem; color: #0f172a;">#${index + 1}: ${q.question}</span>
                    <span style="font-size: 0.75rem; color: #64748b;">Pilihan: [A: ${q.option_a}] [B: ${q.option_b}] [C: ${q.option_c}] [D: ${q.option_d}] &bull; Jawaban: <strong style="color: #7c3aed;">${q.correct}</strong></span>
                    ${q.explanation ? `<span style="font-size: 0.75rem; color: #475569; display: block; margin-top: 0.25rem;"><strong>Penjelasan:</strong> ${escapeHtml(q.explanation)}</span>` : ''}
                    ${q.image ? '<span style="font-size: 0.7rem; color: #0d9488;"><i data-lucide="image" style="width: 0.8rem; height: 0.8rem;"></i> Termasuk Gambar</span>' : ''}
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
                <input type="hidden" name="questions[${index}][explanation]" value="${escapeHtml(q.explanation || '')}">
                <input type="hidden" name="questions[${index}][image]" value="${escapeHtml(q.image || '')}">
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

    let currentImageBase64 = '';
    const qImageInput = document.getElementById('q-image');
    const qImageFilename = document.getElementById('q-image-filename');
    const qImageLabelText = document.getElementById('q-image-label-text');

    if (qImageInput) {
        qImageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 61440) {
                    alert('Ukuran gambar maksimal adalah 60KB.');
                    this.value = '';
                    currentImageBase64 = '';
                    if (qImageFilename) qImageFilename.textContent = 'Belum ada gambar yang dipilih';
                    if (qImageLabelText) qImageLabelText.textContent = 'Tambahkan Gambar';
                    return;
                }
                if (qImageFilename) qImageFilename.textContent = file.name;
                if (qImageLabelText) qImageLabelText.textContent = 'Ganti Gambar';

                const reader = new FileReader();
                reader.onload = function (e) {
                    currentImageBase64 = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                currentImageBase64 = '';
                if (qImageFilename) qImageFilename.textContent = 'Belum ada gambar yang dipilih';
                if (qImageLabelText) qImageLabelText.textContent = 'Tambahkan Gambar';
            }
        });
    }

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const text = qTextInput.value.trim();
            const optA = qOptAInput.value.trim();
            const optB = qOptBInput.value.trim();
            const optC = qOptCInput.value.trim();
            const optD = qOptDInput.value.trim();
            const correct = qCorrectSelect.value;
            const explanation = qExplanationInput ? qExplanationInput.value.trim() : '';

            if (!text || !optA || !optB || !optC || !optD) {
                alert('Silakan isi seluruh teks soal dan semua pilihan jawaban terlebih dahulu.');
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
                image: currentImageBase64
            });

            qTextInput.value = '';
            qOptAInput.value = '';
            qOptBInput.value = '';
            qOptCInput.value = '';
            qOptDInput.value = '';
            if (qExplanationInput) qExplanationInput.value = '';
            qCorrectSelect.selectedIndex = 0;
            if (qImageInput) qImageInput.value = '';
            if (qImageFilename) qImageFilename.textContent = 'Belum ada gambar yang dipilih';
            if (qImageLabelText) qImageLabelText.textContent = 'Tambahkan Gambar';
            currentImageBase64 = '';

            updateDOM();
            qTextInput.focus();
            checkQuestionInputs();
        });
    }

    // Auto-Import Questions logic
    const importFileInput = document.getElementById('import-quiz-file');
    const importFileName = document.getElementById('import-file-name');
    const downloadJsonBtn = document.getElementById('download-template-json');
    const downloadCsvBtn = document.getElementById('download-template-csv');

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
            } else if (c === ',' && !inQuotes) {
                row.push('');
            } else if ((c === '\r' || c === '\n') && !inQuotes) {
                if (c === '\r' && next === '\n') {
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
        importFileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (importFileName) importFileName.textContent = file.name;
            const reader = new FileReader();

            reader.onload = function (evt) {
                try {
                    const content = evt.target.result;
                    let questionsImported = [];

                    if (file.name.endsWith('.json')) {
                        const parsed = JSON.parse(content);
                        let questionsArray = [];

                        if (!Array.isArray(parsed) && parsed.questions && Array.isArray(parsed.questions)) {
                            if (parsed.title && quizTitleInput) quizTitleInput.value = parsed.title;
                            if (parsed.description && quizDescInput) quizDescInput.value = parsed.description;
                            if (parsed.duration !== undefined && quizDurationInput) quizDurationInput.value = parsed.duration;
                            if (parsed.difficulty) {
                                const diffSelect = document.querySelector('#create-quiz-form select[name="difficulty"]');
                                if (diffSelect) diffSelect.value = parsed.difficulty;
                            }
                            if (parsed.category) {
                                const catInput = document.getElementById('selected-category-input');
                                if (catInput) {
                                    catInput.value = parsed.category;
                                    const catBtns = document.querySelectorAll('.category-select-btn');
                                    catBtns.forEach(btn => {
                                        if (btn.getAttribute('data-value') === parsed.category) {
                                            btn.classList.add('active');
                                        } else {
                                            btn.classList.remove('active');
                                        }
                                    });
                                }
                            }
                            questionsArray = parsed.questions;
                        } else if (Array.isArray(parsed)) {
                            questionsArray = parsed;
                        } else {
                            throw new Error('Format JSON harus berupa array berisi list objek pertanyaan, atau objek kuis dengan properti "questions".');
                        }

                        questionsArray.forEach((item, idx) => {
                            const question = item.question || item.pertanyaan || '';
                            const option_a = item.option_a || item.pilihan_a || item.a || '';
                            const option_b = item.option_b || item.pilihan_b || item.b || '';
                            const option_c = item.option_c || item.pilihan_c || item.c || '';
                            const option_d = item.option_d || item.pilihan_d || item.d || '';
                            const correct = (item.correct || item.kunci || item.jawaban || 'A').toUpperCase().trim();
                            const explanation = item.explanation || item.penjelasan || '';

                            if (question && option_a && option_b && option_c && option_d) {
                                questionsImported.push({
                                    question,
                                    option_a,
                                    option_b,
                                    option_c,
                                    option_d,
                                    correct: ['A', 'B', 'C', 'D'].includes(correct) ? correct : 'A',
                                    explanation,
                                    image: ''
                                });
                            } else {
                                console.warn(`Pertanyaan index ${idx} dilewati karena data kurang lengkap.`);
                            }
                        });
                    } else if (file.name.endsWith('.csv')) {
                        const rows = parseCSV(content);
                        if (rows.length < 2) {
                            throw new Error('File CSV kosong atau tidak memiliki baris data.');
                        }
                        const headers = rows[0].map(h => h.trim().toLowerCase());
                        const map = {};
                        headers.forEach((h, index) => {
                            if (h.includes('question') || h.includes('soal') || h === 'pertanyaan') map.question = index;
                            else if (h.includes('option_a') || h === 'a' || h.includes('pilihan_a')) map.option_a = index;
                            else if (h.includes('option_b') || h === 'b' || h.includes('pilihan_b')) map.option_b = index;
                            else if (h.includes('option_c') || h === 'c' || h.includes('pilihan_c')) map.option_c = index;
                            else if (h.includes('option_d') || h === 'd' || h.includes('pilihan_d')) map.option_d = index;
                            else if (h.includes('correct') || h.includes('kunci') || h.includes('jawaban')) map.correct = index;
                            else if (h.includes('explanation') || h.includes('penjelasan')) map.explanation = index;
                        });

                        if (map.question === undefined || map.option_a === undefined || map.option_b === undefined || map.option_c === undefined || map.option_d === undefined) {
                            throw new Error('Format kolom CSV tidak sesuai. Pastikan memiliki kolom: question, option_a, option_b, option_c, option_d, correct, explanation');
                        }

                        for (let i = 1; i < rows.length; i++) {
                            const row = rows[i];
                            if (row.length <= 1 && row[0] === '') continue;

                            const question = row[map.question] ? row[map.question].trim() : '';
                            const option_a = row[map.option_a] ? row[map.option_a].trim() : '';
                            const option_b = row[map.option_b] ? row[map.option_b].trim() : '';
                            const option_c = row[map.option_c] ? row[map.option_c].trim() : '';
                            const option_d = row[map.option_d] ? row[map.option_d].trim() : '';
                            const correct = row[map.correct] ? row[map.correct].trim().toUpperCase() : 'A';
                            const explanation = row[map.explanation] ? row[map.explanation].trim() : '';

                            if (question && option_a && option_b && option_c && option_d) {
                                questionsImported.push({
                                    question,
                                    option_a,
                                    option_b,
                                    option_c,
                                    option_d,
                                    correct: ['A', 'B', 'C', 'D'].includes(correct) ? correct : 'A',
                                    explanation,
                                    image: ''
                                });
                            }
                        }
                    }

                    if (questionsImported.length === 0) {
                        alert('Tidak ada soal valid yang berhasil di-import dari file.');
                    } else {
                        if (quizTitleInput && quizTitleInput.value.trim() === '') {
                            const today = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                            quizTitleInput.value = `Kuis Hasil Import - ${today}`;
                        }
                        if (quizDescInput && quizDescInput.value.trim() === '') {
                            quizDescInput.value = `Kuis dinamis yang dibuat otomatis dari import berkas soal pada ${new Date().toLocaleString('id-ID')}.`;
                        }
                        if (quizDurationInput && (quizDurationInput.value.trim() === '' || quizDurationInput.value === '0')) {
                            quizDurationInput.value = '30';
                        }

                        savedQuestions = savedQuestions.concat(questionsImported);
                        updateDOM();
                        alert(`Berhasil meng-import ${questionsImported.length} soal ke dalam daftar kuis.`);
                    }
                } catch (err) {
                    alert('Gagal memproses file: ' + err.message);
                } finally {
                    importFileInput.value = '';
                }
            };

            reader.readAsText(file);
        });
    }

    // Template downloads
    if (downloadJsonBtn) {
        downloadJsonBtn.addEventListener('click', function () {
            const template = [
                {
                    "question": "Contoh pertanyaan kuis MikroTik OSPF?",
                    "option_a": "Jawaban A",
                    "option_b": "Jawaban B",
                    "option_c": "Jawaban C",
                    "option_d": "Jawaban D",
                    "correct": "A",
                    "explanation": "Penjelasan mengapa jawaban A adalah kunci jawaban yang benar."
                }
            ];
            const blob = new Blob([JSON.stringify(template, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template_kuis.json';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    if (downloadCsvBtn) {
        downloadCsvBtn.addEventListener('click', function () {
            const csvContent = "question,option_a,option_b,option_c,option_d,correct,explanation\n" +
                "\"Contoh pertanyaan kuis MikroTik OSPF?\",\"Jawaban A\",\"Jawaban B\",\"Jawaban C\",\"Jawaban D\",\"A\",\"Penjelasan mengapa jawaban A adalah kunci jawaban yang benar.\"";
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template_kuis.csv';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    if (quizTitleInput && quizDescInput && quizDurationInput) {
        quizTitleInput.addEventListener('input', updateDOM);
        quizDurationInput.addEventListener('input', updateDOM);
        quizDescInput.addEventListener('input', updateDOM);
    }

    function checkQuestionInputs() {
        if (addBtn && qTextInput && qOptAInput && qOptBInput && qOptCInput && qOptDInput) {
            const q = qTextInput.value.trim();
            const a = qOptAInput.value.trim();
            const b = qOptBInput.value.trim();
            const c = qOptCInput.value.trim();
            const d = qOptDInput.value.trim();
            if (q !== '' && a !== '' && b !== '' && c !== '' && d !== '') {
                addBtn.disabled = false;
                addBtn.style.opacity = '1';
                addBtn.style.cursor = 'pointer';
            } else {
                addBtn.disabled = true;
                addBtn.style.opacity = '0.5';
                addBtn.style.cursor = 'not-allowed';
            }
        }
    }

    if (qTextInput) {
        qTextInput.addEventListener('input', checkQuestionInputs);
        qOptAInput.addEventListener('input', checkQuestionInputs);
        qOptBInput.addEventListener('input', checkQuestionInputs);
        qOptCInput.addEventListener('input', checkQuestionInputs);
        qOptDInput.addEventListener('input', checkQuestionInputs);
        checkQuestionInputs();
    }

    // 3. Buat Kuis: Member Registration validation
    const regUsername = document.querySelector('#register-member-form input[name="username"]');
    const regEmail = document.querySelector('#register-member-form input[name="email"]');
    const regPassword = document.querySelector('#register-member-form input[name="password"]');
    const regBtn = document.querySelector('#register-member-form button[type="submit"]');

    if (regUsername && regEmail && regPassword && regBtn) {
        const checkRegInputs = () => {
            const u = regUsername.value.trim();
            const e = regEmail.value.trim();
            const p = regPassword.value;
            if (u !== '' && e !== '' && p.length >= 8) {
                regBtn.disabled = false;
                regBtn.style.opacity = '1';
                regBtn.style.cursor = 'pointer';
            } else {
                regBtn.disabled = true;
                regBtn.style.opacity = '0.5';
                regBtn.style.cursor = 'not-allowed';
            }
        };
        regUsername.addEventListener('input', checkRegInputs);
        regEmail.addEventListener('input', checkRegInputs);
        regPassword.addEventListener('input', checkRegInputs);
        checkRegInputs();
    }

    // 4. Lencana Form Validation
    const badgeTitle = document.querySelector('#create-badge-form input[name="title"]');
    const badgeDesc = document.querySelector('#create-badge-form input[name="description"]');
    const badgeTarget = document.querySelector('#create-badge-form input[name="target_value"]');
    const badgeBtn = document.querySelector('#create-badge-form button[type="submit"]');

    if (badgeTitle && badgeDesc && badgeTarget && badgeBtn) {
        const checkBadgeInputs = () => {
            const t = badgeTitle.value.trim();
            const d = badgeDesc.value.trim();
            const v = badgeTarget.value.trim();
            if (t !== '' && d !== '' && v !== '') {
                badgeBtn.disabled = false;
                badgeBtn.style.opacity = '1';
                badgeBtn.style.cursor = 'pointer';
            } else {
                badgeBtn.disabled = true;
                badgeBtn.style.opacity = '0.5';
                badgeBtn.style.cursor = 'not-allowed';
            }
        };
        badgeTitle.addEventListener('input', checkBadgeInputs);
        badgeDesc.addEventListener('input', checkBadgeInputs);
        badgeTarget.addEventListener('input', checkBadgeInputs);
        checkBadgeInputs();
    }

    // 5. Pengaturan Profil Form Validation
    const profForm = document.getElementById('update-profile-form');
    const profUsername = profForm ? profForm.querySelector('input[name="username"]') : null;
    const profEmail = profForm ? profForm.querySelector('input[name="email"]') : null;
    const profPassword = profForm ? profForm.querySelector('input[name="password"]') : null;
    const profBtn = profForm ? profForm.querySelector('button[type="submit"]') : null;

    if (profUsername && profEmail && profPassword && profBtn) {
        const initUsername = profUsername.value;
        const initEmail = profEmail.value;
        const initPassword = '';

        const checkProfInputs = () => {
            const u = profUsername.value.trim();
            const e = profEmail.value.trim();
            const p = profPassword.value;
            const isChanged = (u !== initUsername || e !== initEmail || p !== initPassword);

            if (u !== '' && e !== '' && isChanged) {
                profBtn.disabled = false;
                profBtn.style.opacity = '1';
                profBtn.style.cursor = 'pointer';
            } else {
                profBtn.disabled = true;
                profBtn.style.opacity = '0.5';
                profBtn.style.cursor = 'not-allowed';
            }
        };
        profUsername.addEventListener('input', checkProfInputs);
        profEmail.addEventListener('input', checkProfInputs);
        profPassword.addEventListener('input', checkProfInputs);
        checkProfInputs();
    }

    // --- ALERT AUTO-DISMISS LOGIC ---
    const adminAlerts = document.querySelectorAll('.admin-alert');
    adminAlerts.forEach(alertEl => {
        setTimeout(() => {
            alertEl.style.opacity = '0';
            setTimeout(() => {
                alertEl.remove();
            }, 500);
        }, 2000);
    });
});
