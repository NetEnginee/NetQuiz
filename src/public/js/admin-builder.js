// --- NETQUIZ VISUAL BUILDER (ELEMENTOR MODE) STATE ---
let builderBlocks = [];
let activeBuilderMode = 'edit';
let originalMaterialTitle = '';

function checkTitleChanged() {
    const currentTitle = document.getElementById('builder-meta-title').value.trim();
    const saveBtn = document.getElementById('builder-save-btn');
    if (!saveBtn) return;
    
    if (currentTitle === '' || currentTitle === originalMaterialTitle) {
        saveBtn.disabled = true;
    } else {
        saveBtn.disabled = false;
    }
}

function toggleMobileSidebar(forceState) {
    const sidebar = document.getElementById('builder-sidebar');
    if (!sidebar) return;
    if (forceState !== undefined) {
        if (forceState) {
            sidebar.classList.add('mobile-open');
        } else {
            sidebar.classList.remove('mobile-open');
        }
    } else {
        sidebar.classList.toggle('mobile-open');
    }
}

function toggleClassicEditor() {
    const container = document.getElementById('classic-editor-container');
    const btn = document.getElementById('classic-editor-toggle-btn');
    if (container.style.display === 'none') {
        container.style.display = 'block';
        btn.innerText = 'Sembunyikan Editor HTML Klasik';
    } else {
        container.style.display = 'none';
        btn.innerText = 'Tampilkan Editor HTML Klasik';
    }
}

function openVisualBuilderFromForm() {
    const titleVal = document.getElementById('form-material-title').value;
    const catVal = document.getElementById('form-material-category').value;
    const diffVal = document.getElementById('form-material-difficulty').value;
    const contentVal = document.getElementById('form-material-content').value;

    // Set the original title to track updates
    const editId = document.getElementById('edit-material-id').value;
    originalMaterialTitle = editId ? titleVal.trim() : '';

    document.getElementById('builder-meta-title').value = titleVal;
    document.getElementById('builder-meta-category').value = catVal;
    document.getElementById('builder-meta-difficulty').value = diffVal;

    updateBuilderMetaTitle();
    checkTitleChanged();

    // Sync preview badge state
    const diffEl = document.getElementById('canvas-meta-difficulty');
    diffEl.innerText = diffVal;
    diffEl.className = '';
    if (diffVal === 'Mudah') {
        diffEl.style.backgroundColor = '#ecfdf5';
        diffEl.style.color = '#059669';
    } else if (diffVal === 'Sedang') {
        diffEl.style.backgroundColor = '#fffbeb';
        diffEl.style.color = '#d97706';
    } else {
        diffEl.style.backgroundColor = '#fef2f2';
        diffEl.style.color = '#dc2626';
    }
    document.getElementById('canvas-meta-category').innerText = catVal;

    builderBlocks = parseHtmlToBlocks(contentVal);

    document.getElementById('visual-builder-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    setBuilderMode('edit');
    renderBuilderBlocks();
}

function editMaterialVisual(id) {
    const config = window.NetQuizConfig || { baseUrl: '' };
    const rowItem = document.querySelector(`button[onclick="editMaterialVisual(${id})"]`);
    const originalContent = rowItem.innerHTML;
    rowItem.disabled = true;
    rowItem.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width: 0.85rem; height: 0.85rem; display: inline-block;"></i>...';
    if (window.lucide) window.lucide.createIcons();

    fetch(`${config.baseUrl}/admin/material/get/${id}`)
        .then(res => res.json())
        .then(data => {
            rowItem.disabled = false;
            rowItem.innerHTML = originalContent;
            if (window.lucide) window.lucide.createIcons();

            const listModal = document.getElementById('quiz-list-modal');
            if (listModal) {
                listModal.classList.remove('active');
            }

            document.getElementById('edit-material-id').value = data.id;
            document.getElementById('form-material-title').value = data.title;
            document.getElementById('form-material-category').value = data.category;
            document.getElementById('form-material-difficulty').value = data.difficulty || 'Mudah';
            document.getElementById('form-material-content').value = data.content;

            const form = document.getElementById('create-material-form');
            form.action = `${config.baseUrl}/admin/material/update/${data.id}`;
            document.getElementById('form-material-card-title').innerHTML = `
                <i data-lucide="edit" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Sunting Materi: ${data.title}
            `;
            document.getElementById('btn-publish-material-text').innerText = 'Simpan Perubahan';
            document.getElementById('btn-cancel-edit-material').style.display = 'flex';
            if (window.lucide) window.lucide.createIcons();

            document.getElementById('materials-section').scrollIntoView({ behavior: 'smooth' });

            openVisualBuilderFromForm();
        })
        .catch(err => {
            rowItem.disabled = false;
            rowItem.innerHTML = originalContent;
            if (window.lucide) window.lucide.createIcons();
            alert('Gagal mengambil data materi: ' + err.message);
        });
}

function resetMaterialForm() {
    const config = window.NetQuizConfig || { baseUrl: '' };
    document.getElementById('edit-material-id').value = '';
    document.getElementById('form-material-title').value = '';
    document.getElementById('form-material-category').value = 'Routing';
    document.getElementById('form-material-difficulty').value = 'Mudah';
    document.getElementById('form-material-content').value = '';

    const form = document.getElementById('create-material-form');
    form.action = `${config.baseUrl}/admin/material/create`;
    document.getElementById('form-material-card-title').innerHTML = `
        <i data-lucide="book-open" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
        Buat Materi Belajar Baru
    `;
    document.getElementById('btn-publish-material-text').innerText = 'Publikasikan Materi';
    document.getElementById('btn-cancel-edit-material').style.display = 'none';
    if (window.lucide) window.lucide.createIcons();
}

function closeVisualBuilder() {
    document.getElementById('visual-builder-overlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function saveVisualBuilder(shouldSubmit = false) {
    const html = compileBlocksToHtml();

    const titleVal = document.getElementById('builder-meta-title').value;
    const catVal = document.getElementById('builder-meta-category').value;
    const diffVal = document.getElementById('builder-meta-difficulty').value;

    document.getElementById('form-material-title').value = titleVal;
    document.getElementById('form-material-category').value = catVal;
    document.getElementById('form-material-difficulty').value = diffVal;
    document.getElementById('form-material-content').value = html;

    const statusText = document.getElementById('builder-status-text');
    statusText.style.opacity = '1';
    setTimeout(() => { statusText.style.opacity = '0'; }, 1500);

    if (shouldSubmit) {
        document.getElementById('save-confirm-modal').style.display = 'flex';
    }
}

function closeSaveConfirmModal(confirmSave) {
    document.getElementById('save-confirm-modal').style.display = 'none';
    if (confirmSave) {
        sessionStorage.setItem('just_saved_material', 'true');
        closeVisualBuilder();
        document.getElementById('create-material-form').submit();
    }
}

function setSidebarTab(tab) {
    const widgetsBtn = document.getElementById('sidebar-tab-widgets');
    const settingsBtn = document.getElementById('sidebar-tab-settings');
    const widgetsPanel = document.getElementById('sidebar-widgets-panel');
    const settingsPanel = document.getElementById('sidebar-settings-panel');

    if (tab === 'widgets') {
        widgetsBtn.style.color = '#7c3aed';
        widgetsBtn.style.borderBottomColor = '#7c3aed';
        settingsBtn.style.color = '#64748b';
        settingsBtn.style.borderBottomColor = 'transparent';
        widgetsPanel.style.display = 'flex';
        settingsPanel.style.display = 'none';
    } else {
        settingsBtn.style.color = '#7c3aed';
        settingsBtn.style.borderBottomColor = '#7c3aed';
        widgetsBtn.style.color = '#64748b';
        widgetsBtn.style.borderBottomColor = 'transparent';
        settingsPanel.style.display = 'flex';
        widgetsPanel.style.display = 'none';
    }
}

function setBuilderMode(mode) {
    activeBuilderMode = mode;
    const editBtn = document.getElementById('builder-tab-edit');
    const previewBtn = document.getElementById('builder-tab-preview');
    const sidebar = document.getElementById('builder-sidebar');
    const canvas = document.getElementById('builder-canvas');
    const mobileToggleBtn = document.getElementById('mobile-sidebar-toggle');

    if (mode === 'edit') {
        editBtn.style.backgroundColor = '#ffffff';
        editBtn.style.color = '#0f172a';
        previewBtn.style.backgroundColor = 'transparent';
        previewBtn.style.color = '#64748b';
        sidebar.style.display = 'flex';
        if (mobileToggleBtn) {
            mobileToggleBtn.style.setProperty('display', '', 'important');
        }
        canvas.classList.remove('preview-mode');
        renderBuilderBlocks();
    } else {
        previewBtn.style.backgroundColor = '#ffffff';
        previewBtn.style.color = '#0f172a';
        editBtn.style.backgroundColor = 'transparent';
        editBtn.style.color = '#64748b';
        sidebar.style.display = 'none';
        if (mobileToggleBtn) {
            mobileToggleBtn.style.setProperty('display', 'none', 'important');
        }
        canvas.classList.add('preview-mode');
        renderPreviewInCanvas();
    }
}

function renderPreviewInCanvas() {
    const canvas = document.getElementById('builder-canvas');
    const html = compileBlocksToHtml();

    const oldWrappers = canvas.querySelectorAll('.builder-block-wrapper');
    oldWrappers.forEach(w => w.remove());

    const previewContainer = document.createElement('div');
    previewContainer.className = 'builder-block-wrapper';
    previewContainer.style.border = 'none';
    previewContainer.style.padding = '0';
    previewContainer.style.margin = '0';
    previewContainer.innerHTML = html;
    canvas.appendChild(previewContainer);
}

function updateBuilderMetaTitle() {
    const val = document.getElementById('builder-meta-title').value;
    document.getElementById('canvas-meta-title').innerText = val || 'Judul Materi Pembelajaran';
    checkTitleChanged();
}

document.addEventListener('DOMContentLoaded', () => {
    const metaCatSelect = document.getElementById('builder-meta-category');
    const metaDiffSelect = document.getElementById('builder-meta-difficulty');

    if (metaCatSelect && metaDiffSelect) {
        metaCatSelect.addEventListener('change', (e) => {
            document.getElementById('canvas-meta-category').innerText = e.target.value;
        });
        metaDiffSelect.addEventListener('change', (e) => {
            const val = e.target.value;
            const diffEl = document.getElementById('canvas-meta-difficulty');
            diffEl.innerText = val;
            diffEl.className = '';
            if (val === 'Mudah') {
                diffEl.style.backgroundColor = '#ecfdf5';
                diffEl.style.color = '#059669';
            } else if (val === 'Sedang') {
                diffEl.style.backgroundColor = '#fffbeb';
                diffEl.style.color = '#d97706';
            } else {
                diffEl.style.backgroundColor = '#fef2f2';
                diffEl.style.color = '#dc2626';
            }
        });
    }
});

function addBuilderBlock(type) {
    let block = {
        id: 'block_' + Math.random().toString(36).substr(2, 9),
        type: type,
        content: ''
    };

    if (type === 'callout') {
        block.calloutType = 'info';
        block.content = 'Isi kotak info penting di sini...';
    } else if (type === 'code') {
        block.language = 'plaintext';
        block.content = '';
    } else if (type === 'image') {
        block.imageSrc = '';
        block.imageAlt = '';
    } else if (type === 'h2') {
        block.content = 'Subjudul H2 baru';
    } else if (type === 'h3') {
        block.content = 'Detail H3 baru';
    } else if (type === 'p') {
        block.content = 'Tulis teks paragraf di sini...';
    } else if (type === 'list') {
        block.content = '<li>Item daftar bullet pertama</li><li>Item daftar bullet kedua</li>';
    } else if (type === 'olist') {
        block.content = '<li>Langkah angka pertama</li><li>Langkah angka kedua</li>';
    } else if (type === 'divider') {
        block.content = '';
    } else if (type === 'accordion') {
        block.title = 'Judul Akordeon FAQ baru';
        block.content = 'Isi penjelasan akordeon atau jawaban FAQ di sini...';
    } else if (type === 'button') {
        block.btnText = 'Klik Di Sini';
        block.btnUrl = '#';
    } else if (type === 'video') {
        block.videoUrl = '';
    } else if (type === 'terminal') {
        block.prompt = '[admin@MikroTik] >';
        block.content = 'ip address print';
    } else if (type === 'quote') {
        block.content = 'Tulis kalimat kutipan penting di sini...';
    } else if (type === 'table') {
        block.content = '<tr><td>IP Address</td><td>192.168.88.1/24</td></tr><tr><td>Interface</td><td>ether1</td></tr>';
    } else if (type === 'tabs') {
        block.title1 = 'Tab 1';
        block.content1 = 'Konten Tab Pertama...';
        block.title2 = 'Tab 2';
        block.content2 = 'Konten Tab Kedua...';
    } else if (type === 'iconbox') {
        block.icon = 'info';
        block.title = 'Info Penting';
        block.content = 'Isi penjelasan dari kotak info di sini...';
    } else if (type === 'progress') {
        block.label = 'Progres Konfigurasi';
        block.percent = '70';
    } else if (type === 'alert') {
        block.alertType = 'success';
        block.content = 'Konfigurasi IP Address pada Router MikroTik telah berhasil diselesaikan!';
    } else if (type === 'card') {
        block.imageSrc = 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500';
        block.cardTitle = 'Perangkat Jaringan MikroTik';
        block.content = 'MikroTik merupakan perangkat jaringan tangguh yang digunakan secara luas untuk manajemen routing, hotspot, bandwidth limiter, dan firewall.';
    } else if (type === 'timeline') {
        block.content = `
<div class="step-item">
    <div class="step-node">1</div>
    <div class="step-content">
        <h5>Langkah Pertama</h5>
        <p>Buka program Winbox pada komputer Anda dan lakukan scan MAC Address router MikroTik.</p>
    </div>
</div>
<div class="step-item">
    <div class="step-node">2</div>
    <div class="step-content">
        <h5>Langkah Kedua</h5>
        <p>Klik tombol Connect untuk masuk ke menu utama RouterOS MikroTik secara GUI.</p>
    </div>
</div>`;
    }

    builderBlocks.push(block);
    renderBuilderBlocks();
    
    // Auto-close sidebar on mobile after adding block
    if (window.innerWidth <= 1024) {
        toggleMobileSidebar(false);
    }
}

function updateBlockContent(id, content) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].content = content;
    }
}

function updateBlockCalloutType(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].calloutType = val;
        renderBuilderBlocks();
    }
}

function updateBlockLanguage(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].language = val;
    }
}

function updateBlockImageSrc(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].imageSrc = val;
        renderBuilderBlocks();
    }
}

function updateBlockImageAlt(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].imageAlt = val;
    }
}

function updateBlockAccordionTitle(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].title = val;
    }
}

function updateBlockBtnText(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].btnText = val;
        renderBuilderBlocks();
    }
}

function updateBlockBtnUrl(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].btnUrl = val;
    }
}

function updateBlockVideoUrl(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].videoUrl = val;
        renderBuilderBlocks();
    }
}

function updateBlockTerminalPrompt(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].prompt = val;
    }
}

/* Tabs updaters */
function updateBlockTabsTitle1(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].title1 = val;
    }
}

function updateBlockTabsContent1(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].content1 = val;
    }
}

function updateBlockTabsTitle2(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].title2 = val;
    }
}

function updateBlockTabsContent2(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].content2 = val;
    }
}

function updateBlockIconboxIcon(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].icon = val;
        renderBuilderBlocks();
    }
}

function updateBlockIconboxTitle(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].title = val;
    }
}

function updateBlockIconboxDesc(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].content = val;
    }
}

function updateBlockProgressLabel(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].label = val;
    }
}

function updateBlockProgressPercent(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].percent = val;
        renderBuilderBlocks();
    }
}

function updateBlockAlertType(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].alertType = val;
        renderBuilderBlocks();
    }
}

function updateBlockCardImage(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].imageSrc = val;
        renderBuilderBlocks();
    }
}

function updateBlockCardTitle(id, val) {
    const idx = builderBlocks.findIndex(b => b.id === id);
    if (idx !== -1) {
        builderBlocks[idx].cardTitle = val;
    }
}

function deleteBlock(id) {
    builderBlocks = builderBlocks.filter(b => b.id !== id);
    renderBuilderBlocks();
}

function moveBlockUp(idx) {
    if (idx > 0) {
        const temp = builderBlocks[idx];
        builderBlocks[idx] = builderBlocks[idx - 1];
        builderBlocks[idx - 1] = temp;
        renderBuilderBlocks();
    }
}

function moveBlockDown(idx) {
    if (idx < builderBlocks.length - 1) {
        const temp = builderBlocks[idx];
        builderBlocks[idx] = builderBlocks[idx + 1];
        builderBlocks[idx + 1] = temp;
        renderBuilderBlocks();
    }
}

function compileBlocksToHtml() {
    let html = '';
    builderBlocks.forEach(b => {
        if (b.type === 'h2') {
            html += `<h2>${b.content}</h2>\n`;
        } else if (b.type === 'h3') {
            html += `<h3>${b.content}</h3>\n`;
        } else if (b.type === 'p') {
            html += `<p>${b.content}</p>\n`;
        } else if (b.type === 'list') {
            html += `<ul>${b.content}</ul>\n`;
        } else if (b.type === 'olist') {
            html += `<ol>${b.content}</ol>\n`;
        } else if (b.type === 'divider') {
            html += `<hr class="material-divider">\n`;
        } else if (b.type === 'accordion') {
            html += `<details class="material-accordion"><summary>${b.title}</summary><div class="material-accordion-content">${b.content}</div></details>\n`;
        } else if (b.type === 'button') {
            html += `<div class="material-btn-wrapper"><a href="${b.btnUrl || '#'}" class="material-btn">${b.btnText || 'Tautan'}</a></div>\n`;
        } else if (b.type === 'video') {
            html += `<div class="material-video-container"><iframe src="${b.videoUrl || ''}" allowfullscreen></iframe></div>\n`;
        } else if (b.type === 'callout') {
            html += `<div class="material-callout material-callout-${b.calloutType || 'info'}">${b.content}</div>\n`;
        } else if (b.type === 'code') {
            const escaped = b.content
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
            html += `<pre><code class="language-${b.language || 'plaintext'}">${escaped}</code></pre>\n`;
        } else if (b.type === 'image') {
            html += `<img src="${b.imageSrc}" alt="${b.imageAlt || ''}" class="material-img">\n`;
        } else if (b.type === 'quote') {
            html += `<blockquote class="material-quote">${b.content}</blockquote>\n`;
        } else if (b.type === 'terminal') {
            html += `<div class="material-terminal"><div class="prompt">${b.prompt || '[admin@MikroTik] &gt;'}</div><pre>${b.content}</pre></div>\n`;
        } else if (b.type === 'iconbox') {
            html += `<div class="material-icon-box"><div class="icon-wrapper"><i data-lucide="${b.icon || 'info'}"></i></div><div><h4>${b.title}</h4><p>${b.content}</p></div></div>\n`;
        } else if (b.type === 'table') {
            html += `<table class="material-table"><thead><tr><th>Parameter</th><th>Value</th></tr></thead><tbody>${b.content}</tbody></table>\n`;
        } else if (b.type === 'tabs') {
            const uniqueId = 'tab_' + Math.random().toString(36).substr(2, 9);
            html += `
<div class="material-tabs" id="${uniqueId}">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="document.querySelectorAll('#${uniqueId} .tab-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.querySelectorAll('#${uniqueId} .tab-pane').forEach(p=>p.classList.remove('active')); document.getElementById('${uniqueId}_1').classList.add('active');">${b.title1 || 'Tab 1'}</button>
        <button class="tab-btn" onclick="document.querySelectorAll('#${uniqueId} .tab-btn').forEach(b=>b.classList.remove('active')); this.classList.add('active'); document.querySelectorAll('#${uniqueId} .tab-pane').forEach(p=>p.classList.remove('active')); document.getElementById('${uniqueId}_2').classList.add('active');">${b.title2 || 'Tab 2'}</button>
    </div>
    <div class="tabs-body">
        <div class="tab-pane active" id="${uniqueId}_1">${b.content1 || ''}</div>
        <div class="tab-pane" id="${uniqueId}_2">${b.content2 || ''}</div>
    </div>
</div>\n`;
        } else if (b.type === 'progress') {
            html += `
<div class="material-progress">
    <div class="progress-info">
        <span>${b.label || 'Progress'}</span>
        <span>${b.percent || '50'}%</span>
    </div>
    <div class="progress-bg">
        <div class="progress-fill" style="width: ${b.percent || '50'}%;"></div>
    </div>
</div>\n`;
        } else if (b.type === 'alert') {
            html += `<div class="material-alert material-alert-${b.alertType || 'success'}">${b.content}</div>\n`;
        } else if (b.type === 'card') {
            html += `
<div class="material-card-widget">
    ${b.imageSrc ? `<img src="${b.imageSrc}" class="card-image" alt="card image">` : ''}
    <div class="card-content">
        <h4>${b.cardTitle || 'Judul Card'}</h4>
        <p>${b.content || 'Deskripsi card...'}</p>
    </div>
</div>\n`;
        } else if (b.type === 'timeline') {
            html += `<div class="material-timeline-steps">${b.content}</div>\n`;
        }
    });
    return html;
}

function parseHtmlToBlocks(html) {
    if (!html || !html.trim()) return [];

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const container = doc.body;
    const blocks = [];

    for (let i = 0; i < container.children.length; i++) {
        const el = container.children[i];
        const tagName = el.tagName.toLowerCase();

        if (tagName === 'h2') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'h2',
                content: el.innerHTML
            });
        } else if (tagName === 'h3') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'h3',
                content: el.innerHTML
            });
        } else if (tagName === 'p') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'p',
                content: el.innerHTML
            });
        } else if (tagName === 'div' && el.classList.contains('material-callout')) {
            let type = 'info';
            if (el.classList.contains('material-callout-success')) type = 'success';
            else if (el.classList.contains('material-callout-warning')) type = 'warning';
            else if (el.classList.contains('material-callout-danger')) type = 'danger';

            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'callout',
                content: el.innerHTML,
                calloutType: type
            });
        } else if (tagName === 'blockquote' && el.classList.contains('material-quote')) {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'quote',
                content: el.innerHTML
            });
        } else if (tagName === 'div' && el.classList.contains('material-terminal')) {
            const promptEl = el.querySelector('.prompt');
            const preEl = el.querySelector('pre');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'terminal',
                prompt: promptEl ? promptEl.innerText : '[admin@MikroTik] >',
                content: preEl ? preEl.innerText : ''
            });
        } else if (tagName === 'div' && el.classList.contains('material-icon-box')) {
            const iconEl = el.querySelector('i');
            const h4El = el.querySelector('h4');
            const pEl = el.querySelector('p') || el.querySelector('div:last-child');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'iconbox',
                icon: iconEl ? iconEl.getAttribute('data-lucide') || 'info' : 'info',
                title: h4El ? h4El.innerHTML : 'Info Penting',
                content: pEl ? pEl.innerHTML : ''
            });
        } else if (tagName === 'table' && el.classList.contains('material-table')) {
            const tbody = el.querySelector('tbody');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'table',
                content: tbody ? tbody.innerHTML : el.innerHTML
            });
        } else if (tagName === 'div' && el.classList.contains('material-tabs')) {
            const tabBtns = el.querySelectorAll('.tabs-header .tab-btn');
            const tabPanes = el.querySelectorAll('.tabs-body .tab-pane');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'tabs',
                title1: tabBtns[0] ? tabBtns[0].innerText : 'Tab 1',
                content1: tabPanes[0] ? tabPanes[0].innerHTML : '',
                title2: tabBtns[1] ? tabBtns[1].innerText : 'Tab 2',
                content2: tabPanes[1] ? tabPanes[1].innerHTML : ''
            });
        } else if (tagName === 'pre') {
            const codeEl = el.querySelector('code');
            let codeContent = el.innerText;
            let lang = 'plaintext';
            if (codeEl) {
                codeContent = codeEl.innerText;
                const match = codeEl.className.match(/language-(\w+)/);
                if (match) lang = match[1];
            }
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'code',
                content: codeContent,
                language: lang
            });
        } else if (tagName === 'ul') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'list',
                content: el.innerHTML
            });
        } else if (tagName === 'ol') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'olist',
                content: el.innerHTML
            });
        } else if (tagName === 'hr') {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'divider',
                content: ''
            });
        } else if (tagName === 'details') {
            const summaryEl = el.querySelector('summary');
            const contentEl = el.querySelector('.material-accordion-content') || el.querySelector('div') || el;
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'accordion',
                title: summaryEl ? summaryEl.innerHTML : 'Judul Akordeon',
                content: contentEl ? contentEl.innerHTML : 'Isi penjelasan...'
            });
        } else if (tagName === 'div' && el.classList.contains('material-btn-wrapper')) {
            const linkEl = el.querySelector('a');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'button',
                btnText: linkEl ? linkEl.innerText : 'Tautan',
                btnUrl: linkEl ? linkEl.getAttribute('href') : '#'
            });
        } else if (tagName === 'div' && el.classList.contains('material-video-container')) {
            const iframeEl = el.querySelector('iframe');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'video',
                videoUrl: iframeEl ? iframeEl.getAttribute('src') : ''
            });
        } else if (tagName === 'div' && el.classList.contains('material-progress')) {
            const infoText = el.querySelector('.progress-info span:first-child');
            const fillEl = el.querySelector('.progress-fill');
            let pct = '50';
            if (fillEl && fillEl.style.width) {
                pct = fillEl.style.width.replace('%', '');
            }
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'progress',
                label: infoText ? infoText.innerText : 'Progress',
                percent: pct
            });
        } else if (tagName === 'div' && el.classList.contains('material-alert')) {
            let alertType = 'success';
            if (el.classList.contains('material-alert-info')) alertType = 'info';
            else if (el.classList.contains('material-alert-warning')) alertType = 'warning';
            else if (el.classList.contains('material-alert-danger')) alertType = 'danger';
            
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'alert',
                alertType: alertType,
                content: el.innerHTML
            });
        } else if (tagName === 'div' && el.classList.contains('material-card-widget')) {
            const imgEl = el.querySelector('.card-image');
            const h4El = el.querySelector('h4');
            const pEl = el.querySelector('p');
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'card',
                imageSrc: imgEl ? imgEl.getAttribute('src') || '' : '',
                cardTitle: h4El ? h4El.innerHTML : 'Judul Card',
                content: pEl ? pEl.innerHTML : 'Isi deskripsi card...'
            });
        } else if (tagName === 'div' && el.classList.contains('material-timeline-steps')) {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'timeline',
                content: el.innerHTML
            });
        } else {
            blocks.push({
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type: 'p',
                content: el.outerHTML
            });
        }
    }
    return blocks;
}

function renderBuilderBlocks() {
    const config = window.NetQuizConfig || { baseUrl: '' };
    const canvas = document.getElementById('builder-canvas');
    const emptyState = document.getElementById('canvas-empty-state');

    const oldWrappers = canvas.querySelectorAll('.builder-block-wrapper');
    oldWrappers.forEach(w => w.remove());

    if (builderBlocks.length === 0) {
        if (emptyState) emptyState.style.display = 'flex';
        return;
    } else {
        if (emptyState) emptyState.style.display = 'none';
    }

    builderBlocks.forEach((block, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'builder-block-wrapper';
        wrapper.dataset.id = block.id;

        let controlsHtml = `
            <div class="builder-block-controls">
                <button type="button" class="builder-control-btn" onclick="moveBlockUp(${index})" title="Pindahkan Ke Atas"><i data-lucide="chevron-up" style="width: 0.85rem; height: 0.85rem;"></i></button>
                <button type="button" class="builder-control-btn" onclick="moveBlockDown(${index})" title="Pindahkan Ke Bawah"><i data-lucide="chevron-down" style="width: 0.85rem; height: 0.85rem;"></i></button>
                <button type="button" class="builder-control-btn" onclick="deleteBlock('${block.id}')" title="Hapus Elemen" style="color: #ef4444;"><i data-lucide="trash-2" style="width: 0.85rem; height: 0.85rem;"></i></button>
            </div>
        `;

        let contentHtml = '';

        if (block.type === 'h2') {
            contentHtml = `<h2 class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; outline: none; border-bottom: 2px solid transparent; min-height: 2rem;">${block.content || 'Subjudul H2 baru'}</h2>`;
        } else if (block.type === 'h3') {
            contentHtml = `<h3 class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; outline: none; min-height: 1.5rem;">${block.content || 'Detail H3 baru'}</h3>`;
        } else if (block.type === 'p') {
            contentHtml = `<p class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; outline: none; min-height: 1.5rem; line-height: 1.75;">${block.content || 'Tulis teks paragraf di sini...'}</p>`;
        } else if (block.type === 'list') {
            contentHtml = `<ul class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; outline: none; padding-left: 1.5rem; min-height: 2rem;">${block.content || '<li>Item daftar pertama</li><li>Item daftar kedua</li>'}</ul>`;
        } else if (block.type === 'olist') {
            contentHtml = `<ol class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; outline: none; padding-left: 1.5rem; min-height: 2rem;">${block.content || '<li>Item berurutan pertama</li><li>Item berurutan kedua</li>'}</ol>`;
        } else if (block.type === 'divider') {
            contentHtml = `<hr class="material-divider" style="margin: 1rem 0;">`;
        } else if (block.type === 'quote') {
            contentHtml = `<blockquote class="material-quote builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none; margin: 0;">${block.content || 'Tulis kalimat kutipan penting di sini...'}</blockquote>`;
        } else if (block.type === 'terminal') {
            contentHtml = `
                <div class="material-terminal" style="margin: 0; position: relative;">
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem;" class="builder-block-controls-select">
                        <span style="font-size: 0.7rem; color: #4ade80;">Prompt:</span>
                        <input type="text" value="${block.prompt || '[admin@MikroTik] >'}" oninput="updateBlockTerminalPrompt('${block.id}', this.value)" style="background: #1e293b; color: #4ade80; border: none; font-size: 0.75rem; border-radius: 4px; padding: 2px 6px; width: 150px; font-family: monospace; outline: none;">
                    </div>
                    <textarea oninput="updateBlockContent('${block.id}', this.value)" style="width: 100%; background: transparent; color: #f1f5f9; font-family: monospace; border: none; resize: vertical; min-height: 50px; outline: none; font-size: 0.85rem; line-height: 1.5; margin: 0; padding: 0;" placeholder="Tulis baris perintah terminal di sini...">${block.content || ''}</textarea>
                </div>
            `;
        } else if (block.type === 'iconbox') {
            contentHtml = `
                <div class="material-icon-box" style="margin: 0; position: relative; width: 100%;">
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem;" class="builder-block-controls-select">
                        <select onchange="updateBlockIconboxIcon('${block.id}', this.value)" style="border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.7rem; padding: 2px 4px; background: #fff; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <option value="info" ${block.icon === 'info' ? 'selected' : ''}>Info</option>
                            <option value="help-circle" ${block.icon === 'help-circle' ? 'selected' : ''}>Bantuan</option>
                            <option value="check-circle" ${block.icon === 'check-circle' ? 'selected' : ''}>Verifikasi</option>
                            <option value="alert-triangle" ${block.icon === 'alert-triangle' ? 'selected' : ''}>Peringatan</option>
                            <option value="settings" ${block.icon === 'settings' ? 'selected' : ''}>Setting</option>
                        </select>
                    </div>
                    <div class="icon-wrapper" style="pointer-events: none;"><i data-lucide="${block.icon || 'info'}" style="width: 1.5rem; height: 1.5rem;"></i></div>
                    <div style="flex: 1;">
                        <h4 class="builder-editable" contenteditable="true" onblur="updateBlockIconboxTitle('${block.id}', this.innerHTML)" style="outline: none;">${block.title || 'Judul Info Box'}</h4>
                        <div class="builder-editable" contenteditable="true" onblur="updateBlockIconboxDesc('${block.id}', this.innerHTML)" style="outline: none; color: #475569; font-size: 0.9rem;">${block.content || 'Isi deskripsi info box...'}</div>
                    </div>
                </div>
            `;
        } else if (block.type === 'table') {
            contentHtml = `
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #f8fafc; display: flex; flex-direction: column; gap: 0.75rem; width: 100%;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #7c3aed; text-transform: uppercase;">Table Widget (Edit baris data di bawah):</div>
                    <table class="material-table" style="margin: 0; background: #fff; width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding: 0.5rem 0.75rem;">Parameter</th>
                                <th style="padding: 0.5rem 0.75rem;">Value / Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none;">
                            ${block.content || '<tr><td>IP Address</td><td>192.168.88.1/24</td></tr><tr><td>Interface</td><td>ether1</td></tr>'}
                        </tbody>
                    </table>
                    <div style="font-size: 0.7rem; color: #64748b;"><i data-lucide="info" style="width: 0.8rem; height: 0.8rem; display: inline; vertical-align: middle;"></i> Tip: Ubah isi tabel secara langsung. Anda dapat menekan Enter untuk menambah baris baru &lt;tr&gt; di dalam kode tabel.</div>
                </div>
            `;
        } else if (block.type === 'tabs') {
            contentHtml = `
                <div class="material-tabs" style="margin: 0; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #f8fafc; display: flex; flex-direction: column; gap: 0.75rem; width: 100%;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #7c3aed; text-transform: uppercase;">Tabs Widget (Ubah tab di bawah):</div>
                    <div style="display: flex; gap: 0.5rem;" class="builder-block-controls-select">
                        <input type="text" value="${block.title1 || 'Tab 1'}" oninput="updateBlockTabsTitle1('${block.id}', this.value)" placeholder="Judul Tab 1" style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                        <input type="text" value="${block.title2 || 'Tab 2'}" oninput="updateBlockTabsTitle2('${block.id}', this.value)" placeholder="Judul Tab 2" style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #475569;">Konten Tab 1:</div>
                    <div class="builder-editable" contenteditable="true" onblur="updateBlockTabsContent1('${block.id}', this.innerHTML)" style="outline: none; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.85rem;">${block.content1 || 'Konten Tab Pertama...'}</div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #475569;">Konten Tab 2:</div>
                    <div class="builder-editable" contenteditable="true" onblur="updateBlockTabsContent2('${block.id}', this.innerHTML)" style="outline: none; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.85rem;">${block.content2 || 'Konten Tab Kedua...'}</div>
                </div>
            `;
        } else if (block.type === 'accordion') {
            contentHtml = `
                <div class="material-accordion" style="margin: 0; position: relative;">
                    <div style="background-color: #f8fafc; padding: 0.85rem 1.25rem; border-bottom: 1px solid #f1f5f9; font-weight: 700; font-size: 0.95rem; color: #0f172a; display: flex; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; color: #7c3aed; margin-right: 0.5rem; text-transform: uppercase;">Accordion Title:</span>
                        <div class="builder-editable" contenteditable="true" onblur="updateBlockAccordionTitle('${block.id}', this.innerHTML)" style="outline: none; flex: 1;">${block.title || 'Judul Akordeon'}</div>
                    </div>
                    <div class="material-accordion-content builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none; padding: 1.25rem; background: #fff; font-size: 0.9rem;">${block.content || 'Isi detail penjelasan FAQ/Akordeon di sini...'}</div>
                </div>
            `;
        } else if (block.type === 'button') {
            contentHtml = `
                <div class="material-btn-wrapper" style="margin: 0; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #f8fafc; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Teks:</span>
                        <input type="text" value="${block.btnText || ''}" oninput="updateBlockBtnText('${block.id}', this.value)" placeholder="Nama Tombol" style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">URL:</span>
                        <input type="text" value="${block.btnUrl || ''}" oninput="updateBlockBtnUrl('${block.id}', this.value)" placeholder="https://..." style="flex: 2; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="text-align: center; margin-top: 0.25rem;">
                        <span class="material-btn" style="pointer-events: none; margin: 0;">${block.btnText || 'Tombol Tautan'}</span>
                    </div>
                </div>
            `;
        } else if (block.type === 'video') {
            contentHtml = `
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background-color: #f8fafc; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">URL Youtube Embed:</span>
                        <input type="text" value="${block.videoUrl || ''}" oninput="updateBlockVideoUrl('${block.id}', this.value)" placeholder="https://www.youtube.com/embed/..." style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div class="material-video-container" style="margin: 0; pointer-events: none;">
                        ${block.videoUrl ? `<iframe src="${block.videoUrl}"></iframe>` : `<div style="text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.8rem;"><i data-lucide="video" style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Masukkan URL YouTube Embed Anda</div>`}
                    </div>
                </div>
            `;
        } else if (block.type === 'callout') {
            contentHtml = `
                <div class="material-callout material-callout-${block.calloutType || 'info'}" style="position: relative; margin: 0;">
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; display: flex; gap: 0.25rem; align-items: center;" class="builder-block-controls-select">
                        <select onchange="updateBlockCalloutType('${block.id}', this.value)" style="border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.7rem; padding: 2px 4px; background: #fff; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <option value="info" ${block.calloutType === 'info' ? 'selected' : ''}>Info</option>
                            <option value="success" ${block.calloutType === 'success' ? 'selected' : ''}>Sukses</option>
                            <option value="warning" ${block.calloutType === 'warning' ? 'selected' : ''}>Peringatan</option>
                            <option value="danger" ${block.calloutType === 'danger' ? 'selected' : ''}>Bahaya</option>
                        </select>
                    </div>
                    <div class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none; min-height: 1.5rem;">${block.content || 'Isi kotak info penting di sini...'}</div>
                </div>
            `;
        } else if (block.type === 'code') {
            contentHtml = `
                <div style="background-color: #0f172a; padding: 1.25rem; border-radius: 8px; position: relative;">
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; display: flex; gap: 0.25rem;" class="builder-block-controls-select">
                        <select onchange="updateBlockLanguage('${block.id}', this.value)" style="border: none; border-radius: 4px; font-size: 0.7rem; padding: 2px 4px; background: #334155; color: #fff; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <option value="plaintext" ${block.language === 'plaintext' ? 'selected' : ''}>Plaintext</option>
                            <option value="php" ${block.language === 'php' ? 'selected' : ''}>PHP</option>
                            <option value="bash" ${block.language === 'bash' ? 'selected' : ''}>Bash / Shell</option>
                            <option value="json" ${block.language === 'json' ? 'selected' : ''}>JSON</option>
                            <option value="html" ${block.language === 'html' ? 'selected' : ''}>HTML</option>
                        </select>
                    </div>
                    <textarea oninput="updateBlockContent('${block.id}', this.value)" style="width: 100%; background: transparent; color: #e2e8f0; font-family: 'Courier New', Courier, monospace; border: none; resize: vertical; min-height: 80px; outline: none; font-size: 0.85rem; line-height: 1.5; margin: 0; padding: 0;" placeholder="Tulis baris kode di sini...">${block.content || ''}</textarea>
                </div>
            `;
        } else if (block.type === 'image') {
            contentHtml = `
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; background-color: #f8fafc; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">URL Gambar:</span>
                        <input type="text" value="${block.imageSrc || ''}" oninput="updateBlockImageSrc('${block.id}', this.value)" placeholder="${config.baseUrl}/uploads/nama-file.png" style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Alt:</span>
                        <input type="text" value="${block.imageAlt || ''}" oninput="updateBlockImageAlt('${block.id}', this.value)" placeholder="Deskripsi" style="width: 120px; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    <div style="text-align: center; border: 1px dashed #cbd5e1; border-radius: 6px; background: #fff; padding: 0.5rem;">
                        ${block.imageSrc ? `<img src="${block.imageSrc}" alt="${block.imageAlt || ''}" style="max-height: 200px; max-width: 100%; border-radius: 4px; display: inline-block;">` : `<span style="font-size: 0.75rem; color: #94a3b8;"><i data-lucide="image" style="width: 1rem; height: 1rem; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Belum ada gambar dimasukkan</span>`}
                    </div>
                </div>
            `;
        } else if (block.type === 'progress') {
            contentHtml = `
                <div class="material-progress" style="margin: 0;">
                    <div class="progress-info">
                        <span contenteditable="true" onblur="updateBlockProgressLabel('${block.id}', this.innerText)" style="outline: none; font-weight: 700;">${block.label || 'Progress'}</span>
                        <span>${block.percent || '50'}%</span>
                    </div>
                    <div class="progress-bg">
                        <div class="progress-fill" style="width: ${block.percent || '50'}%;"></div>
                    </div>
                    <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; color: #64748b;">Persentase:</span>
                        <input type="range" min="0" max="100" value="${block.percent || '50'}" oninput="updateBlockProgressPercent('${block.id}', this.value)" style="flex: 1; height: 6px; cursor: pointer;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569; width: 30px; text-align: right;">${block.percent || '50'}%</span>
                    </div>
                </div>
            `;
        } else if (block.type === 'alert') {
            contentHtml = `
                <div class="material-alert material-alert-${block.alertType || 'success'}" style="margin: 0; position: relative;">
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem;" class="builder-block-controls-select">
                        <select onchange="updateBlockAlertType('${block.id}', this.value)" style="border: none; border-radius: 4px; font-size: 0.7rem; padding: 2px 4px; background: #fff; border: 1px solid #cbd5e1; color: #475569; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <option value="success" ${block.alertType === 'success' ? 'selected' : ''}>Success</option>
                            <option value="info" ${block.alertType === 'info' ? 'selected' : ''}>Info</option>
                            <option value="warning" ${block.alertType === 'warning' ? 'selected' : ''}>Warning</option>
                            <option value="danger" ${block.alertType === 'danger' ? 'selected' : ''}>Danger</option>
                        </select>
                    </div>
                    <div class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none; width: 100%; min-height: 1.5rem; padding-right: 4.5rem;">${block.content || 'Isi pesan alert...'}</div>
                </div>
            `;
        } else if (block.type === 'card') {
            contentHtml = `
                <div class="material-card-widget" style="margin: 0;">
                    <div style="padding: 0.75rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; gap: 0.5rem; align-items: center;" class="builder-block-controls-select">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Gambar Card URL:</span>
                        <input type="text" value="${block.imageSrc || ''}" oninput="updateBlockCardImage('${block.id}', this.value)" placeholder="https://unsplash.com/image.jpg" style="flex: 1; height: 28px; padding: 0 0.5rem; font-size: 0.75rem; border-radius: 4px; border: 1px solid #cbd5e1; outline: none;">
                    </div>
                    ${block.imageSrc ? `<img src="${block.imageSrc}" class="card-image" alt="card image" style="width: 100%; height: 180px; object-fit: cover;">` : ''}
                    <div class="card-content">
                        <h4 contenteditable="true" onblur="updateBlockCardTitle('${block.id}', this.innerText)" style="outline: none; font-weight: 700;">${block.cardTitle || 'Judul Card'}</h4>
                        <p class="builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="outline: none; margin: 0; min-height: 1.5rem;">${block.content || 'Deskripsi card...'}</p>
                    </div>
                </div>
            `;
        } else if (block.type === 'timeline') {
            contentHtml = `
                <div class="material-timeline-steps builder-editable" contenteditable="true" onblur="updateBlockContent('${block.id}', this.innerHTML)" style="margin: 0; padding-left: 2rem; outline: none;">
                    ${block.content || '<div class="step-item"><div class="step-node">1</div><div class="step-content"><h5>Langkah 1</h5><p>Tulis langkah pertama...</p></div></div>'}
                </div>
            `;
        }

        wrapper.innerHTML = controlsHtml + contentHtml;

        // NATIVE DRAG & DROP LOGIC (Only in Edit Mode)
        if (activeBuilderMode === 'edit') {
            wrapper.setAttribute('draggable', 'true');
            
            wrapper.addEventListener('dragstart', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.closest('[contenteditable="true"]')) {
                    e.preventDefault();
                    return;
                }
                e.dataTransfer.effectAllowed = 'move';
                wrapper.classList.add('dragging');
                window.draggedElement = wrapper;
            });

            wrapper.addEventListener('dragend', () => {
                wrapper.classList.remove('dragging');
                const allWrappers = canvas.querySelectorAll('.builder-block-wrapper');
                allWrappers.forEach(w => w.classList.remove('drag-over'));
                
                const currentIds = Array.from(canvas.querySelectorAll('.builder-block-wrapper')).map(w => w.dataset.id);
                const newBlocks = [];
                currentIds.forEach(id => {
                    const block = builderBlocks.find(b => b.id === id);
                    if (block) newBlocks.push(block);
                });
                builderBlocks = newBlocks;
                renderBuilderBlocks();
            });

            wrapper.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                
                if (window.draggedElement && window.draggedElement !== wrapper) {
                    const rect = wrapper.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    canvas.insertBefore(window.draggedElement, next ? wrapper.nextSibling : wrapper);
                }
            });

            wrapper.addEventListener('drop', (e) => {
                e.preventDefault();
            });
        }

        canvas.appendChild(wrapper);
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
}
