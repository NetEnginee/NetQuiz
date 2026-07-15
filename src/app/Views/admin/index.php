<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Admin -->
<style>
    /* Force visible overflow on scroll ancestors to allow position: sticky to work */
    main,
    .dashboard-container,
    #materials-section {
        overflow: visible !important;
    }

    html,
    body {
        -ms-overflow-style: none;
        /* IE dan Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, dan Opera */
    }

    .admin-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        animation: fadeIn 0.35s ease-out forwards;
    }

    .admin-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .stats-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.05);
    }

    .stats-icon-wrapper {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stats-info {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .stats-card-num {
        font-size: 1.75rem;
        font-weight: 850;
        color: #0f172a;
        line-height: 1.1;
    }

    .stats-card-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    /* Navigation Tab Switcher */
    .admin-tab-bar {
        display: flex;
        gap: 0.85rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1.25rem;
        flex-wrap: wrap;
        position: sticky;
        top: 4.2rem;
        background-color: #f8fafc;
        padding-top: 1rem;
        z-index: 30;
    }

    .admin-tab-btn {
        background-color: #ffffff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1.35rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .admin-tab-btn:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .admin-tab-btn.active {
        background-color: #f5f3ff;
        color: #7c3aed;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    /* Content Sections */
    .admin-section-content {
        display: none;
        animation: fadeIn 0.3s ease-out forwards;
    }

    .admin-section-content.active {
        display: block;
    }

    .form-grid-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        align-items: start;
    }

    @media (min-width: 1024px) {
        .form-grid-layout {
            grid-template-columns: 0.95fr 1.05fr;
        }
    }

    .admin-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        padding: 1.75rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        box-sizing: border-box;
    }

    .admin-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .btn-outline-primary {
        background-color: #ffffff;
        color: #7c3aed;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .btn-outline-primary:hover {
        background-color: #f5f3ff;
        border-color: #ddd6fe;
        color: #7c3aed;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08);
    }

    .quiz-row-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.15rem 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 1rem;
    }

    .quiz-row-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .quiz-row-info {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .quiz-row-title {
        font-weight: 750;
        color: #0f172a;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .quiz-row-meta {
        font-size: 0.8rem;
        color: #64748b;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-category {
        background-color: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
        padding: 0.15rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .btn-danger-sm {
        background-color: #fff1f2;
        color: #f43f5e;
        border: 1px solid #fecaca;
        padding: 0.45rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-danger-sm:hover {
        background-color: #f43f5e;
        border-color: #f43f5e;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(244, 63, 94, 0.15);
    }

    .admin-form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .admin-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
    }

    .admin-input,
    .admin-select,
    .admin-textarea {
        width: 100%;
        padding: 0.75rem 0.95rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        outline: none;
        box-sizing: border-box;
        transition: all 0.2s ease;
    }

    .admin-input:focus,
    .admin-select:focus,
    .admin-textarea:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.08);
    }

    .question-builder-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        background-color: #f8fafc;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.01);
    }

    .remove-question-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 0.25rem;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .remove-question-btn:hover {
        color: #f43f5e;
        background-color: #fff1f2;
    }

    .btn-add-question {
        background-color: #ffffff;
        color: #0d9488;
        border: 1px solid #ccfbf1;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .btn-add-question:hover {
        background-color: #0d9488;
        border-color: #0d9488;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(13, 148, 136, 0.15);
    }

    /* Category Button Selection */
    .category-btn-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        width: 100%;
    }

    .category-select-btn {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .category-select-btn:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    .category-select-btn.active {
        background-color: #f5f3ff;
        color: #7c3aed;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    /* Pop-up Modal Styling */
    .admin-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }

    .admin-modal.show {
        opacity: 1;
        pointer-events: auto;
    }

    .admin-modal-content {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        width: calc(100% - 2.5rem);
        max-width: 750px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        transform: translateY(20px);
        overflow: hidden;
    }

    .admin-modal-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-modal-body {
        padding: 1.5rem 1.75rem;
        overflow-y: auto;
        flex: 1;
    }

    .close-modal-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #64748b;
        padding: 0.25rem;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .close-modal-btn:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }

    @keyframes modalSlideUp {
        to {
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Publish Button */
    .btn-publish {
        background-color: #7c3aed;
        color: #ffffff;
        border: none;
        padding: 0.85rem 1.5rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
    }

    .btn-publish:hover {
        background-color: #6d28d9;
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.25);
        transform: translateY(-1px);
    }

    .btn-publish:active {
        transform: translateY(0);
    }

    /* Table Styling for User Management */
    .admin-table-container {
        width: 100%;
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .admin-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .admin-table td {
        padding: 1rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }

    .admin-table tr:hover td {
        background-color: #f8fafc;
    }

    .btn-edit-sm {
        background-color: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
        padding: 0.45rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-edit-sm:hover {
        background-color: #7c3aed;
        border-color: #7c3aed;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);
    }
</style>

<div class="admin-container">
    <!-- Header Row (Breadcrumbs + Trigger Button) -->
    <div class="admin-header-row">
        <nav class="breadcrumb"
            style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0;">
            <span style="color: #0f172a;">Panel Admin</span>
        </nav>

        <button type="button" id="open-quiz-modal-btn" class="btn-outline-primary">
            <i data-lucide="eye" style="width: 1.1rem; height: 1.1rem;"></i>
            <span id="open-quiz-modal-text">Lihat Kuis Aktif</span>
        </button>
    </div>

    <!-- Alerts -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="admin-alert"
            style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.75rem; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: opacity 0.5s ease;">
            <i data-lucide="check-circle" style="width: 1.15rem; height: 1.15rem;"></i>
            <?= htmlspecialchars($_SESSION['admin_success']);
            unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="admin-alert"
            style="background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.75rem; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: opacity 0.5s ease;">
            <i data-lucide="alert-triangle" style="width: 1.15rem; height: 1.15rem;"></i>
            <?= htmlspecialchars($_SESSION['admin_error']);
            unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Stats row -->
    <div class="stats-row">
        <div class="stats-card">
            <div class="stats-icon-wrapper" style="background-color: #f5f3ff; color: #7c3aed;">
                <i data-lucide="help-circle" style="width: 1.5rem; height: 1.5rem;"></i>
            </div>
            <div class="stats-info">
                <span class="stats-card-num"><?= $stats['total_quizzes'] ?></span>
                <span class="stats-card-label">Total Kuis</span>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon-wrapper" style="background-color: #f0fdfa; color: #0d9488;">
                <i data-lucide="users" style="width: 1.5rem; height: 1.5rem;"></i>
            </div>
            <div class="stats-info">
                <span class="stats-card-num"><?= $stats['total_users'] ?></span>
                <span class="stats-card-label">Siswa Terdaftar</span>
            </div>
        </div>
    </div>

    <!-- Admin Section Tabs Switcher -->
    <div class="admin-tab-bar">
        <button type="button" class="admin-tab-btn active" data-target="quiz-section">
            Buat Kuis
        </button>
        <button type="button" class="admin-tab-btn" data-target="member-section">
            Daftarkan Akun
        </button>
        <button type="button" class="admin-tab-btn" data-target="manage-section">
            Manajemen Akun
        </button>
        <button type="button" class="admin-tab-btn" data-target="badge-section">
            Lencana
        </button>
        <button type="button" class="admin-tab-btn" data-target="materials-section">
            Materi Belajar
        </button>
        <button type="button" class="admin-tab-btn" data-target="profile-section">
            Pengaturan Profil
        </button>
    </div>

    <!-- SECTION 1: CREATE QUIZ (Informasi Kuis & Pertanyaan Kuis) -->
    <div id="quiz-section" class="admin-section-content active">
        <form id="create-quiz-form" method="POST" action="<?= BASE_URL ?>/admin/quiz/create"
            enctype="multipart/form-data">
            <?= \App\Core\Security::csrfField() ?>
            <div class="form-grid-layout">

                <!-- LEFT CARD: General Quiz Information -->
                <div class="admin-card">
                    <h3 class="admin-card-title">
                        <i data-lucide="info" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                        Informasi Kuis
                    </h3>

                    <div class="admin-form-group">
                        <label class="admin-label">Judul Kuis</label>
                        <input type="text" name="title" class="admin-input" placeholder="Masukkan judul kuis..."
                            required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Waktu Pengerjaan (Menit)</label>
                        <input type="number" name="duration" class="admin-input" min="0"
                            placeholder="0 untuk tanpa batas waktu..." value="0" required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Kategori</label>
                        <input type="hidden" name="category" id="selected-category-input" value="Routing">
                        <div class="category-btn-group">
                            <button type="button" class="category-select-btn active" data-value="Routing">
                                Routing
                            </button>
                            <button type="button" class="category-select-btn" data-value="Firewall & NAT">
                                Firewall & NAT
                            </button>
                            <button type="button" class="category-select-btn" data-value="Wireless">
                                Wireless
                            </button>
                            <button type="button" class="category-select-btn" data-value="Network Management">
                                Net Management
                            </button>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-label">Tingkat Kesulitan</label>
                        <select name="difficulty" class="admin-select"
                            style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.65rem 0.85rem; font-size: 0.9rem; font-family: inherit; color: #0f172a; outline: none; transition: border-color 0.2s;"
                            required>
                            <option value="Mudah">Mudah</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Sulit">Sulit</option>
                        </select>
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Deskripsi Kuis</label>
                        <textarea name="description" rows="5" class="admin-textarea"
                            placeholder="Tulis deskripsi singkat mengenai materi kuis ini..." style="resize: vertical;"
                            required></textarea>
                    </div>

                </div>

                <!-- RIGHT CARD: Questions Builder -->
                <div class="admin-card">
                    <h3 class="admin-card-title" style="padding-bottom: 0.75rem;">
                        <i data-lucide="help-circle" style="width: 1.25rem; height: 1.25rem; color: #0d9488;"></i>
                        Pertanyaan Kuis
                    </h3>

                    <!-- Trigger Button to View Saved Questions in Modal -->
                    <button type="button" id="open-saved-modal-btn" class="btn-outline-primary"
                        style="width: 100%; justify-content: center; margin-bottom: 0.5rem;">
                        <i data-lucide="list-todo" style="width: 1.05rem; height: 1.05rem;"></i>
                        Lihat Soal Tersimpan (<span id="saved-count">0</span>)
                    </button>

                    <!-- Import Questions from File -->
                    <div
                        style="background-color: #f0fdfa; border: 1.5px dashed #0d9488; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span
                                style="font-weight: 700; font-size: 0.85rem; color: #0d9488; display: flex; align-items: center; gap: 0.4rem;">
                                <i data-lucide="upload-cloud" style="width: 1.1rem; height: 1.1rem;"></i>
                                Auto-Fill via File (JSON / CSV)
                            </span>
                            <div style="display: flex; gap: 0.25rem;">
                                <button type="button" id="download-template-json" class="btn-outline-primary"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.7rem; border-color: #0d9488; color: #0d9488; display: inline-flex; align-items: center; gap: 0.2rem; background: #ffffff;">
                                    <i data-lucide="download" style="width: 0.75rem; height: 0.75rem;"></i> JSON
                                </button>
                                <button type="button" id="download-template-csv" class="btn-outline-primary"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.7rem; border-color: #0d9488; color: #0d9488; display: inline-flex; align-items: center; gap: 0.2rem; background: #ffffff;">
                                    <i data-lucide="download" style="width: 0.75rem; height: 0.75rem;"></i> CSV
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <input type="file" id="import-quiz-file" accept=".json,.csv" style="display: none;">
                            <label for="import-quiz-file" class="btn-outline-primary"
                                style="padding: 0.4rem 0.85rem; border-color: #0d9488; color: #0d9488; cursor: pointer; margin: 0; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff;">
                                <i data-lucide="file-text" style="width: 1rem; height: 1rem;"></i>
                                Unggah File
                            </label>
                            <span id="import-file-name"
                                style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Pilih berkas JSON /
                                CSV...</span>
                        </div>
                    </div>

                    <!-- Hidden inputs container for form submission -->
                    <div id="hidden-inputs-container"></div>

                    <!-- Input Form for adding a single question -->
                    <div class="question-builder-card"
                        style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 0;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span style="font-weight: 700; font-size: 0.85rem; color: #334155;">
                                Formulir Input Soal
                            </span>
                            <button type="button" id="add-question-btn" class="btn-add-question"
                                style="background-color: #0d9488; color: white; border-style: solid; padding: 0.4rem 0.85rem;">
                                <i data-lucide="plus" style="width: 0.9rem; height: 0.9rem;"></i>
                                Simpan Soal
                            </button>
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 0.75rem;">
                            <label class="admin-label" style="font-size: 0.8rem;">Teks Pertanyaan</label>
                            <input type="text" id="q-text" class="admin-input"
                                placeholder="Masukkan teks pertanyaan...">
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 0.75rem;">
                            <label class="admin-label" style="font-size: 0.8rem;">Gambar Soal (Opsional, Maks
                                60KB)</label>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <input type="file" id="q-image" accept="image/*" style="display: none;">
                                <label for="q-image" class="btn-outline-primary"
                                    style="padding: 0.4rem 0.85rem; cursor: pointer; margin: 0; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i data-lucide="image" style="width: 1rem; height: 1rem;"></i>
                                    <span id="q-image-label-text">Tambahkan Gambar</span>
                                </label>
                                <span id="q-image-filename"
                                    style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Belum ada gambar yang
                                    dipilih</span>
                            </div>
                        </div>

                        <div
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-bottom: 0.65rem;">
                            <div>
                                <label class="admin-label" style="font-size: 0.75rem; color: #64748b;">Pilihan A</label>
                                <input type="text" id="q-opt-a" class="admin-input" placeholder="Jawaban A">
                            </div>
                            <div>
                                <label class="admin-label" style="font-size: 0.75rem; color: #64748b;">Pilihan B</label>
                                <input type="text" id="q-opt-b" class="admin-input" placeholder="Jawaban B">
                            </div>
                        </div>
                        <div
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-bottom: 0.65rem;">
                            <div>
                                <label class="admin-label" style="font-size: 0.75rem; color: #64748b;">Pilihan C</label>
                                <input type="text" id="q-opt-c" class="admin-input" placeholder="Jawaban C">
                            </div>
                            <div>
                                <label class="admin-label" style="font-size: 0.75rem; color: #64748b;">Pilihan D</label>
                                <input type="text" id="q-opt-d" class="admin-input" placeholder="Jawaban D">
                            </div>
                        </div>

                        <div class="admin-form-group" style="margin-top: 0.65rem;">
                            <label class="admin-label" style="font-size: 0.8rem;">Kunci Jawaban Benar</label>
                            <select id="q-correct" class="admin-select">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="admin-form-group" style="margin-top: 0.65rem; margin-bottom: 0;">
                            <label class="admin-label" style="font-size: 0.8rem;">Penjelasan Kunci Jawaban
                                (Opsional)</label>
                            <textarea id="q-explanation" class="admin-textarea" rows="3"
                                placeholder="Masukkan penjelasan kenapa pilihan tersebut benar..."
                                style="resize: vertical; font-size: 0.85rem; padding: 0.5rem;"></textarea>
                        </div>
                    </div>
                    <button type="submit" id="btn-submit-quiz" class="btn-publish" disabled
                        style="width: 100%; margin-top: 1.5rem; opacity: 0.5; cursor: not-allowed;">
                        <i data-lucide="save" style="width: 1.25rem; height: 1.25rem;"></i>
                        Simpan & Publikasikan Kuis
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- SECTION 2: REGISTER ACCOUNT (Daftarkan Akun Anggota/Murid) -->
    <div id="member-section" class="admin-section-content">
        <div class="admin-card">
            <h3 class="admin-card-title">
                <i data-lucide="user-plus" style="width: 1.25rem; height: 1.25rem; color: #0d9488;"></i>
                Daftarkan Akun Anggota / Murid Baru
            </h3>

            <form id="register-member-form" method="POST" action="<?= BASE_URL ?>/admin/member/create">
                <?= \App\Core\Security::csrfField() ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Username</label>
                        <input type="text" name="username" class="admin-input" placeholder="Masukkan nama pengguna..."
                            required autocomplete="off">
                    </div>
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Alamat Email</label>
                        <input type="email" name="email" class="admin-input" placeholder="nama@email.com" required
                            autocomplete="off">
                    </div>
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Password Sementara</label>
                        <input type="password" name="password" class="admin-input" placeholder="Minimal 8 karakter..."
                            required autocomplete="off">
                    </div>
                </div>

                <button type="submit" class="btn-publish"
                    style="background-color: #0d9488; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15); margin-top: 1.5rem; width: 100%;">
                    <i data-lucide="user-plus" style="width: 1.25rem; height: 1.25rem;"></i>
                    Daftarkan Anggota Baru
                </button>
            </form>
        </div>
    </div>

    <!-- SECTION 3: MANAGE ACCOUNTS (Manajemen Akun Siswa CRUD) -->
    <div id="manage-section" class="admin-section-content">
        <div class="admin-card">
            <h3 class="admin-card-title">
                <i data-lucide="users" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Manajemen Akun Pengguna / Siswa
            </h3>

            <div class="admin-table-container" style="max-height: 380px; overflow-y: auto; padding-right: 0.25rem;">
                <?php if (empty($users_list)): ?>
                    <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                        <i data-lucide="users"
                            style="width: 2.5rem; height: 2.5rem; stroke-width: 1.5; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        <p style="font-size: 0.9rem; font-weight: 500;">Belum ada anggota terdaftar.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($users_list as $usr): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($usr['username']) ?></td>
                                    <td><?= htmlspecialchars($usr['email']) ?></td>
                                    <td style="display: flex; gap: 0.5rem;">

                                        <form method="POST" action="<?= BASE_URL ?>/admin/member/delete/<?= $usr['id'] ?>"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?');"
                                            style="margin: 0;">
                                            <?= \App\Core\Security::csrfField() ?>
                                            <button type="submit" class="btn-danger-sm" style="padding: 0.45rem 0.85rem;">
                                                <i data-lucide="trash-2" style="width: 0.85rem; height: 0.85rem;"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SECTION 4: BADGES MANAGEMENT (Manajemen Lencana) -->
    <div id="badge-section" class="admin-section-content">
        <div class="admin-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; ">
                <h3 class="admin-card-title" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                    <i data-lucide="award" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                    Buat Lencana Baru
                </h3>
            </div>

            <form id="create-badge-form" method="POST" action="<?= BASE_URL ?>/admin/badge/create">
                <?= \App\Core\Security::csrfField() ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <!-- Nama Lencana -->
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Nama Lencana</label>
                        <input type="text" name="title" class="admin-input" placeholder="Masukkan nama lencana..."
                            required autocomplete="off">
                    </div>
                    <!-- Deskripsi Lencana -->
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Deskripsi Lencana</label>
                        <input type="text" name="description" class="admin-input"
                            placeholder="Deskripsikan cara mendapatkan..." required autocomplete="off">
                    </div>
                    <!-- Icon Lencana -->
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Icon Lencana</label>
                        <select name="icon" class="admin-input" required>
                            <option value="award">Award</option>
                            <option value="crown">Crown</option>
                            <option value="star">Star</option>
                            <option value="target">Target</option>
                            <option value="shield">Shield</option>
                            <option value="route">Route</option>
                            <option value="wifi">Wifi</option>
                            <option value="server">Server</option>
                            <option value="play">Play</option>
                        </select>
                    </div>
                    <!-- Target Nilai -->
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Target Nilai</label>
                        <input type="number" name="target_value" class="admin-input" min="1" placeholder="Misal: 10"
                            required>
                    </div>
                    <!-- Syarat Pencapaian (Metrik) -->
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Syarat Pencapaian (Metrik)</label>
                        <select name="metric" class="admin-input" required>
                            <option value="completed_quizzes">Total Kuis Selesai</option>
                            <option value="total_score">Total Akumulasi Skor</option>
                            <option value="perfect_scores">Jumlah Skor 100</option>
                            <option value="category_routing">Kuis Routing Selesai</option>
                            <option value="category_firewall">Kuis Firewall & NAT Selesai</option>
                            <option value="category_wireless">Kuis Wireless Selesai</option>
                            <option value="category_network">Kuis Network Management Selesai</option>
                        </select>
                    </div>
                    <!-- Tombol Buat Lencana -->
                    <div class="admin-form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
                        <button type="submit" class="btn-publish"
                            style="width: 100%; height: 44px; display: flex; align-items: center; justify-content: center; box-sizing: border-box; padding: 0 1rem; margin-top: 0;">
                            <i data-lucide="plus-circle" style="width: 1.1rem; height: 1.1rem;"></i>
                            Buat Lencana
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION 5: PROFILE SETTINGS (Profil & Sesi Admin) -->
    <div id="profile-section" class="admin-section-content">
        <div class="admin-card">
            <h3 class="admin-card-title">
                <i data-lucide="user-cog" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Profil & Sesi Admin
            </h3>

            <form id="update-profile-form" method="POST" action="<?= BASE_URL ?>/admin/profile/update">
                <?= \App\Core\Security::csrfField() ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Username Admin</label>
                        <input type="text" name="username" class="admin-input"
                            value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required autocomplete="off">
                    </div>
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Alamat Email Admin</label>
                        <input type="email" name="email" class="admin-input"
                            value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required autocomplete="off">
                    </div>
                </div>
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-top: 1.25rem; align-items: flex-end;">
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <label class="admin-label">Ganti Password Baru (Opsional)</label>
                        <input type="password" name="password" class="admin-input"
                            placeholder="Kosongkan jika tidak diganti..." autocomplete="off">
                    </div>
                    <div class="admin-form-group" style="margin-bottom: 0;">
                        <button type="submit" class="btn-publish"
                            style="width: 100%; height: 44px; display: flex; align-items: center; justify-content: center; box-sizing: border-box; padding: 0 1rem;">
                            <i data-lucide="save" style="width: 1.1rem; height: 1.1rem;"></i>
                            Simpan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION 6: MATERIALS MANAGEMENT (Materi Belajar) -->
    <div id="materials-section" class="admin-section-content">
        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">


            <!-- Create Material Card -->
            <div class="admin-card">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.5rem;">
                    <h3 id="form-material-card-title" class="admin-card-title"
                        style="margin: 0; border: none; padding: 0;">
                        <i data-lucide="book-open" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                        Buat Materi Belajar Baru
                    </h3>
                    <div style="display: inline-flex; gap: 0.5rem;">
                        <button type="button" onclick="downloadJsonTemplate();"
                            style="display: inline-flex; align-items: center; gap: 0.35rem; height: 32px; padding: 0 0.75rem; font-size: 0.75rem; border-radius: 6px; background-color: #ffffff; border: 1px solid #64748b; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; cursor: pointer; transition: all 0.15s; outline: none;"
                            onmouseover="this.style.backgroundColor='#f8fafc'; this.style.borderColor='#475569'; this.style.color='#475569';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#64748b'; this.style.color='#64748b';">
                            <i data-lucide="download" style="width: 0.95rem; height: 0.95rem;"></i>
                            Unduh Template
                        </button>
                        <input type="file" id="import-material-json" accept=".json" style="display: none;">
                        <button type="button" onclick="document.getElementById('import-material-json').click();"
                            style="display: inline-flex; align-items: center; gap: 0.35rem; height: 32px; padding: 0 0.75rem; font-size: 0.75rem; border-radius: 6px; background-color: #ffffff; border: 1px solid #7c3aed; color: #7c3aed; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; cursor: pointer; transition: all 0.15s; outline: none;"
                            onmouseover="this.style.backgroundColor='#f5f3ff'"
                            onmouseout="this.style.backgroundColor='#ffffff'">
                            <i data-lucide="upload-cloud" style="width: 0.95rem; height: 0.95rem;"></i>
                            Impor JSON
                        </button>
                    </div>
                </div>
                <form id="create-material-form" method="POST" action="<?= BASE_URL ?>/admin/material/create">
                    <?= \App\Core\Security::csrfField() ?>
                    <input type="hidden" id="edit-material-id" value="">

                    <div class="admin-form-group">
                        <label class="admin-label">Judul Materi</label>
                        <input type="text" name="title" id="form-material-title" class="admin-input"
                            placeholder="Masukkan judul materi..." required>
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 1.25rem;">
                        <label class="admin-label">Kategori</label>
                        <select name="category" id="form-material-category" class="admin-input"
                            style="height: 44px; padding: 0 0.75rem;">
                            <option value="Routing">Routing</option>
                            <option value="Firewall & NAT">Firewall & NAT</option>
                            <option value="Wireless">Wireless</option>
                            <option value="Network Management">Network Management</option>
                        </select>
                    </div>
                    <input type="hidden" name="difficulty" id="form-material-difficulty" value="Mudah">

                    <!-- Hidden input to store compiled HTML from Visual Builder -->
                    <input type="hidden" name="content" id="form-material-content" value="">

                    <!-- Launch Visual Builder button (The default path now) -->
                    <button type="button" onclick="openVisualBuilderFromForm()" class="btn-publish"
                        style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 48px; font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none; font-weight: 700; font-size: 0.95rem; margin-top: 1rem; box-shadow: 0 4px 12px rgba(124,58,237,0.25);"
                        onmouseover="this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.transform='translateY(0)';">
                        <span id="btn-publish-material-text">Mulai Desain & Publikasikan (Elementor Mode)</span>
                    </button>

                    <button type="button" id="btn-cancel-edit-material" onclick="resetMaterialForm()"
                        class="btn-publish"
                        style="width: 100%; display: none; align-items: center; justify-content: center; gap: 0.5rem; height: 44px; background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; margin-top: 0.5rem; font-family: 'Plus Jakarta Sans', sans-serif;"
                        onmouseover="this.style.backgroundColor='#f1f5f9'"
                        onmouseout="this.style.backgroundColor='#ffffff'">
                        Batal Sunting
                    </button>
                </form>
            </div>


        </div>
    </div>
</div>

<!-- Modal Pop-up: Active Items List (Quizzes, Badges, Materials) -->
<div id="quiz-list-modal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3
                style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="layout-list" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                <span id="active-list-modal-title">Daftar Kuis yang Aktif Saat Ini</span>
            </h3>
            <button type="button" id="close-quiz-modal-btn" class="close-modal-btn">
                <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
            </button>
        </div>
        <div class="admin-modal-body">
            <!-- Quizzes List Container -->
            <div id="modal-list-quizzes" class="modal-section-content">
                <?php if (empty($quizzes)): ?>
                    <div
                        style="text-align: center; padding: 3rem 1rem; color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                        <i data-lucide="folder-open" style="width: 2.5rem; height: 2.5rem; stroke-width: 1.5;"></i>
                        <span style="font-size: 0.9rem; font-weight: 500;">Belum ada kuis aktif yang terdaftar.</span>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column;">
                        <?php foreach ($quizzes as $quiz): ?>
                            <div class="quiz-row-item">
                                <div class="quiz-row-info">
                                    <span class="quiz-row-title"><?= htmlspecialchars($quiz['title']) ?></span>
                                    <div class="quiz-row-meta">
                                        <span class="badge-category"><?= htmlspecialchars($quiz['category']) ?></span>
                                        <span style="color: #cbd5e1;">&bull;</span>
                                        <span
                                            style="font-weight: 500; font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($quiz['description']) ?></span>
                                    </div>
                                </div>
                                <form method="POST" action="<?= BASE_URL ?>/admin/quiz/delete/<?= $quiz['id'] ?>"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuis ini?');">
                                    <?= \App\Core\Security::csrfField() ?>
                                    <button type="submit" class="btn-danger-sm">
                                        <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Badges List Container -->
            <div id="modal-list-badges" class="modal-section-content" style="display: none;">
                <?php if (empty($badges_list)): ?>
                    <div
                        style="text-align: center; padding: 3rem 1rem; color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                        <i data-lucide="award" style="width: 2.5rem; height: 2.5rem; stroke-width: 1.5;"></i>
                        <span style="font-size: 0.9rem; font-weight: 500;">Belum ada lencana aktif yang terdaftar.</span>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column;">
                        <?php foreach ($badges_list as $badge): ?>
                            <div class="quiz-row-item">
                                <div class="quiz-row-info">
                                    <span class="quiz-row-title"><?= htmlspecialchars($badge['title']) ?></span>
                                    <div class="quiz-row-meta">
                                        <span class="badge-category"
                                            style="background-color: #e0e7ff; color: #4f46e5;"><?= htmlspecialchars($badge['metric']) ?></span>
                                        <span style="color: #cbd5e1;">&bull;</span>
                                        <span
                                            style="font-weight: 500; font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($badge['description']) ?></span>
                                    </div>
                                </div>
                                <form method="POST" action="<?= BASE_URL ?>/admin/badge/delete/<?= $badge['id'] ?>"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus lencana ini?');">
                                    <?= \App\Core\Security::csrfField() ?>
                                    <button type="submit" class="btn-danger-sm">
                                        <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Materials List Container -->
            <div id="modal-list-materials" class="modal-section-content" style="display: none;">
                <?php if (empty($materials_list)): ?>
                    <div
                        style="text-align: center; padding: 3rem 1rem; color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                        <i data-lucide="book-open" style="width: 2.5rem; height: 2.5rem; stroke-width: 1.5;"></i>
                        <span style="font-size: 0.9rem; font-weight: 500;">Belum ada materi aktif yang terdaftar.</span>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column;">
                        <?php foreach ($materials_list as $mat): ?>
                            <div class="quiz-row-item">
                                <div class="quiz-row-info">
                                    <span class="quiz-row-title"><?= htmlspecialchars($mat['title']) ?></span>
                                    <div class="quiz-row-meta">
                                        <span class="badge-category"
                                            style="background-color: #f5f3ff; color: #7c3aed;"><?= htmlspecialchars($mat['category']) ?></span>
                                        <span style="color: #cbd5e1;">&bull;</span>
                                        <span style="font-weight: 500; font-size: 0.85rem; color: #64748b;">Materi
                                            Belajar</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button type="button" class="btn-primary-sm" onclick="editMaterialVisual(<?= $mat['id'] ?>)"
                                        style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.75rem; border-radius: 6px; border: 1px solid #7c3aed; background-color: #7c3aed; color: #ffffff; cursor: pointer; transition: all 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <i data-lucide="edit" style="width: 0.85rem; height: 0.85rem;"></i>
                                        Edit
                                    </button>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/material/delete/<?= $mat['id'] ?>"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?');"
                                        style="margin: 0;">
                                        <?= \App\Core\Security::csrfField() ?>
                                        <button type="submit" class="btn-danger-sm">
                                            <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pop-up: Saved Questions List -->
<div id="saved-questions-modal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3
                style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="help-circle" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Daftar Soal yang Disimpan
            </h3>
            <button type="button" id="close-saved-modal-btn" class="close-modal-btn">
                <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
            </button>
        </div>
        <div class="admin-modal-body">
            <div id="saved-questions-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <!-- Filled dynamically by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Pop-up: Active Badges List -->
<div id="badge-list-modal" class="admin-modal">
    <style>
        #badge-list-modal .admin-modal-body::-webkit-scrollbar {
            display: none;
        }
    </style>
    <div class="admin-modal-content" style="max-width: 600px;">
        <div class="admin-modal-header">
            <h3
                style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="award" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Daftar Lencana Aktif
            </h3>
            <button type="button" id="close-badge-modal-btn" class="close-modal-btn">
                <i data-lucide="x" style="width: 1.15rem; height: 1.15rem;"></i>
            </button>
        </div>
        <div class="admin-modal-body" style="scrollbar-width: none; -ms-overflow-style: none;">
            <?php if (empty($badges_list)): ?>
                <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8; font-style: italic; font-size: 0.9rem;">
                    Belum ada lencana yang terdaftar.
                </div>
            <?php else: ?>
                <form method="POST" action="<?= BASE_URL ?>/admin/badge/delete-bulk" id="bulk-delete-badges-form">
                    <?= \App\Core\Security::csrfField() ?>
                    <!-- Action Bar for Multi-select -->
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                        <button type="button" id="btn-toggle-select-mode" class="btn-outline-primary"
                            style="padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                            Pilih
                        </button>
                        <button type="submit" id="btn-bulk-delete-badges" class="btn-danger-sm" disabled
                            style="padding: 0.4rem 1rem; border-radius: 6px; font-weight: 700; display: flex; align-items: center; gap: 0.45rem; opacity: 0.5; cursor: not-allowed;">
                            <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                            Hapus
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($badges_list as $badge): ?>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                                <div style="display: flex; gap: 0.75rem; align-items: center; flex: 1; min-width: 0;">
                                    <!-- Checkbox for multi-select (hidden by default) -->
                                    <input type="checkbox" name="selected_badges[]" value="<?= $badge['id'] ?>"
                                        class="badge-item-checkbox"
                                        style="width: 1.1rem; height: 1.1rem; cursor: pointer; margin-right: 0.25rem; display: none;">

                                    <div
                                        style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background-color: #f1f5f9; color: #7c3aed; border-radius: 50%; flex-shrink: 0;">
                                        <i data-lucide="<?= htmlspecialchars($badge['icon']) ?>"
                                            style="width: 1.2rem; height: 1.2rem;"></i>
                                    </div>
                                    <div style="min-width: 0; flex: 1;">
                                        <strong
                                            style="color: #0f172a; font-size: 0.9rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($badge['title']) ?></strong>
                                        <span
                                            style="color: #64748b; font-size: 0.75rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($badge['description']) ?></span>
                                        <span
                                            style="display: inline-block; margin-top: 2px; font-size: 0.65rem; font-weight: 700; color: #4f46e5; background-color: #e0e7ff; padding: 1px 6px; border-radius: 4px;">
                                            <?= htmlspecialchars($badge['metric']) ?> &ge; <?= $badge['target_value'] ?>
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn-danger-sm" style="padding: 0.4rem 0.7rem; border-radius: 6px;"
                                    title="Hapus Lencana"
                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus lencana ini?')) { deleteBadgeSingle(<?= $badge['id'] ?>); }">
                                    <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Expose Global App Config securely to External JS -->
<script>
    window.NetQuizConfig = {
        baseUrl: '<?= BASE_URL ?>'
    };
</script>

<!-- Load External Admin Dashboard Script -->
<script src="<?= BASE_URL ?>/js/admin-dashboard.js?v=<?= time() ?>" defer></script>

<!-- NETQUIZ VISUAL PAGE BUILDER (ELEMENTOR MODE) OVERLAY -->
<div id="visual-builder-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #f8fafc; z-index: 99999; font-family: 'Plus Jakarta Sans', sans-serif; flex-direction: column;">
    <!-- Top Navbar -->
    <div class="builder-header-container"
        style="height: 64px; background-color: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; flex-shrink: 0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="font-weight: 800; font-size: 1.2rem; color: #0f172a; letter-spacing: -0.02em;">
                Net<span style="color: #7c3aed;">Quiz</span> <span
                    style="font-size: 0.85rem; font-weight: 500; color: #64748b; margin-left: 0.5rem; padding: 0.2rem 0.5rem; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0;">Visual
                    Page Builder</span>
            </div>
        </div>

        <!-- Center Tabs (Edit vs Preview) -->
        <div style="display: flex; background-color: #f1f5f9; padding: 0.25rem; border-radius: 8px; gap: 0.25rem;">
            <button type="button" id="builder-tab-edit" onclick="setBuilderMode('edit')"
                style="border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.15s; background-color: #ffffff; color: #0f172a; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i> Sunting
            </button>
            <button type="button" id="builder-tab-preview" onclick="setBuilderMode('preview')"
                style="border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.15s; background-color: transparent; color: #64748b; display: flex; align-items: center; gap: 0.35rem;">
                <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i> Pratinjau
            </button>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.75rem; align-items: center;" class="builder-actions-wrapper">
            <span id="builder-status-text"
                style="font-size: 0.8rem; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 0.25rem; opacity: 0; transition: opacity 0.3s;">
                <i data-lucide="check-circle-2" style="width: 0.9rem; height: 0.9rem;"></i> <span class="status-text">Tersimpan</span>
            </span>
            <button type="button" onclick="closeVisualBuilder()"
                style="border: 1px solid #cbd5e1; background-color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.35rem;"
                onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='#ffffff'">
                <i data-lucide="x" style="width: 0.95rem; height: 0.95rem;"></i> <span class="btn-text">Batal</span>
            </button>
            <button type="button" id="builder-save-btn" onclick="saveVisualBuilder(true)"
                style="border: none; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); padding: 0.5rem 1.25rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; color: #ffffff; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 10px -3px rgba(124, 58, 237, 0.35);"
                onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                <i data-lucide="save" style="width: 0.95rem; height: 0.95rem;"></i> <span class="btn-text">Simpan Materi</span>
            </button>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="builder-workspace-container" style="display: flex; flex: 1; min-height: 0;">
        <!-- Left Sidebar -->
        <div id="builder-sidebar"
            style="width: 320px; background-color: #ffffff; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; flex-shrink: 0; min-height: 0;">
            <div style="display: flex; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; align-items: center; justify-content: space-between; padding-right: 0.5rem;">
                <button type="button" id="sidebar-tab-widgets" onclick="setSidebarTab('widgets')"
                    style="flex: 1; border: none; background: transparent; padding: 0.9rem; font-size: 0.85rem; font-weight: 700; color: #7c3aed; border-bottom: 2px solid #7c3aed; cursor: pointer;">
                    Elemen
                </button>
                <button type="button" id="sidebar-tab-settings" onclick="setSidebarTab('settings')"
                    style="flex: 1; border: none; background: transparent; padding: 0.9rem; font-size: 0.85rem; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; cursor: pointer;">
                    Pengaturan Page
                </button>
                <button type="button" id="mobile-sidebar-close" onclick="toggleMobileSidebar(false)" style="border: none; background: transparent; color: #64748b; padding: 0.5rem; cursor: pointer;">
                    <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
            </div>

            <div style="flex: 1; overflow-y: auto; padding: 1.25rem; min-height: 0;">
                <!-- Widgets Panel -->
                <div id="sidebar-widgets-panel" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div
                        style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: -0.25rem;">
                        Elemen Dasar</div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <div onclick="addBuilderBlock('h2')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem;">
                                H2</div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Subjudul (H2)</span>
                        </div>
                        <div onclick="addBuilderBlock('h3')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                H3</div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Detail (H3)</span>
                        </div>
                        <div onclick="addBuilderBlock('p')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="align-left" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Paragraf</span>
                        </div>
                        <div onclick="addBuilderBlock('list')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="list" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Daftar Bullet</span>
                        </div>
                        <div onclick="addBuilderBlock('olist')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="list-ordered" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Daftar Angka</span>
                        </div>
                        <div onclick="addBuilderBlock('divider')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="minus" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Garis Pembatas</span>
                        </div>
                    </div>

                    <div
                        style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.5rem; margin-bottom: -0.25rem;">
                        Elemen Interaktif & Media</div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <div onclick="addBuilderBlock('code')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="code-2" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Blok Kode</span>
                        </div>
                        <div onclick="addBuilderBlock('terminal')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="terminal" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Terminal OS</span>
                        </div>
                        <div onclick="addBuilderBlock('callout')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="alert-circle" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Kotak Info</span>
                        </div>
                        <div onclick="addBuilderBlock('quote')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="quote" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Kutipan Quote</span>
                        </div>
                        <div onclick="addBuilderBlock('accordion')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="chevrons-up-down" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Akordeon FAQ</span>
                        </div>
                        <div onclick="addBuilderBlock('tabs')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="folder-open" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Tab Konten</span>
                        </div>
                        <div onclick="addBuilderBlock('iconbox')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="info" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Kotak Info Box</span>
                        </div>
                        <div onclick="addBuilderBlock('table')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="table" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Tabel Data</span>
                        </div>
                        <div onclick="addBuilderBlock('button')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="link" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Tombol Tautan</span>
                        </div>
                        <div onclick="addBuilderBlock('video')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="video" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Video Embed</span>
                        </div>
                        <div onclick="addBuilderBlock('image')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="image" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Gambar</span>
                        </div>
                        <div onclick="addBuilderBlock('progress')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="sliders" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Progress Bar</span>
                        </div>
                        <div onclick="addBuilderBlock('alert')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="alert-triangle" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Alert Box</span>
                        </div>
                        <div onclick="addBuilderBlock('card')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="credit-card" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Card Box</span>
                        </div>
                        <div onclick="addBuilderBlock('timeline')"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; text-align: center; cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;"
                            onmouseover="this.style.borderColor='#7c3aed'; this.style.backgroundColor='#f5f3ff';"
                            onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';">
                            <div
                                style="color: #7c3aed; background-color: #f5f3ff; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="git-commit" style="width: 1.2rem; height: 1.2rem;"></i>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Timeline Langkah</span>
                        </div>
                    </div>
                </div>

                <!-- Page Settings Panel -->
                <div id="sidebar-settings-panel" style="display: none; flex-direction: column; gap: 1.25rem;">
                    <div class="admin-form-group">
                        <label class="admin-label" style="font-size: 0.8rem;">Judul Materi</label>
                        <input type="text" id="builder-meta-title" oninput="updateBuilderMetaTitle()"
                            class="admin-input" style="height: 40px; font-size: 0.9rem;" placeholder="Judul materi...">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label" style="font-size: 0.8rem;">Kategori</label>
                        <select id="builder-meta-category" class="admin-input"
                            style="height: 40px; font-size: 0.9rem; padding: 0 0.5rem;">
                            <option value="Routing">Routing</option>
                            <option value="Firewall & NAT">Firewall & NAT</option>
                            <option value="Wireless">Wireless</option>
                            <option value="Network Management">Network Management</option>
                        </select>
                    </div>
                    <input type="hidden" id="builder-meta-difficulty" value="Mudah">
                </div>
            </div>

            <div
                style="padding: 1rem; border-top: 1px solid #f1f5f9; background-color: #f8fafc; font-size: 0.75rem; color: #64748b; line-height: 1.4;">
                <i data-lucide="help-circle"
                    style="width: 0.9rem; height: 0.9rem; display: inline; vertical-align: middle; margin-right: 0.2rem; color: #7c3aed;"></i>
                Klik langsung pada teks di kanvas sebelah kanan untuk mengedit isi konten secara visual.
            </div>
        </div>

        <!-- Canvas area -->
        <div class="builder-canvas-area" style="flex: 1; padding: 2.5rem; overflow-y: auto; background-color: #f8fafc;">
            <div
                style="width: 100%; max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- Mock Page Title Area -->
                <div
                    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 0.75rem;">
                    <div
                        style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.8rem; font-weight: 600; color: #7c3aed;">
                        <span id="canvas-meta-category">Routing</span>
                        <span id="canvas-meta-difficulty" style="display: none;">Mudah</span>
                    </div>
                    <h1 id="canvas-meta-title"
                        style="font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2; letter-spacing: -0.025em; word-break: break-word;">
                        Judul Materi Pembelajaran
                    </h1>
                    <div style="height: 1px; background-color: #f1f5f9; margin-top: 0.5rem;"></div>
                </div>

                <!-- The Canvas -->
                <div id="builder-canvas" class="material-body"
                    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); min-height: 400px; display: flex; flex-direction: column; gap: 1rem; position: relative;">
                    <!-- Placeholder when empty -->
                    <div id="canvas-empty-state"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 5rem 2rem; border: 2px dashed #cbd5e1; border-radius: 8px; color: #94a3b8; text-align: center;">
                        <i data-lucide="layout"
                            style="width: 3.5rem; height: 3.5rem; stroke-width: 1.25; color: #cbd5e1;"></i>
                        <div>
                            <p style="font-weight: 700; font-size: 1rem; color: #475569; margin: 0 0 0.25rem 0;">Kanvas
                                Masih Kosong</p>
                            <p
                                style="font-size: 0.8rem; color: #94a3b8; max-width: 280px; margin: 0; line-height: 1.4;">
                                Klik widget di panel kiri untuk mulai mendesain tata letak materi belajar Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Action Button for mobile widget drawer -->
    <button type="button" id="mobile-sidebar-toggle" onclick="toggleMobileSidebar()" style="display: none; position: fixed; bottom: 2rem; right: 2rem; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; width: 60px; height: 60px; border-radius: 50%; border: none; align-items: center; justify-content: center; box-shadow: 0 6px 20px rgba(124,58,237,0.45); z-index: 9999; cursor: pointer; transition: all 0.2s ease;">
        <i data-lucide="plus" style="width: 1.75rem; height: 1.75rem;"></i>
    </button>

    <!-- Custom Save Confirm Modal -->
    <div id="save-confirm-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); z-index: 100000; align-items: center; justify-content: center;">
        <div class="admin-modal-content" style="max-width: 440px; width: 90%; background: #ffffff; border-radius: 16px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); text-align: center;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1.25rem;">
                <div style="background-color: #f5f3ff; color: #7c3aed; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(124,58,237,0.15);">
                    <i data-lucide="save" style="width: 1.75rem; height: 1.75rem;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">Simpan Perubahan?</h3>
                    <p style="font-size: 0.9rem; color: #64748b; margin: 0; line-height: 1.5;">Apakah Anda yakin ingin menyimpan seluruh perubahan pada materi belajar ini?</p>
                </div>
                <div style="display: flex; gap: 0.75rem; width: 100%; margin-top: 0.5rem;">
                    <button type="button" onclick="closeSaveConfirmModal(false)" style="flex: 1; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; padding: 0.75rem; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 0.85rem; transition: all 0.15s; outline: none;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='#ffffff'">Batal</button>
                    <button type="button" onclick="closeSaveConfirmModal(true)" style="flex: 1; border: none; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: #ffffff; padding: 0.75rem; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; box-shadow: 0 4px 12px rgba(124,58,237,0.25); transition: all 0.15s; outline: none;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">Ya, Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Builder block elements */
    .builder-block-wrapper {
        position: relative;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.5rem;
        border: 1.5px dashed transparent;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
    }

    .builder-block-wrapper:hover {
        border-color: #a78bfa;
        background-color: #faf5ff;
    }

    .builder-block-controls {
        position: absolute;
        top: -14px;
        right: 12px;
        display: none;
        gap: 0.25rem;
        z-index: 100;
    }

    .builder-block-wrapper:hover .builder-block-controls {
        display: flex;
    }

    /* Drag & Drop Visual Styles */
    .builder-block-wrapper.dragging {
        opacity: 0.4;
        border: 1.5px dashed #7c3aed !important;
        background-color: #f5f3ff !important;
        transform: scale(0.99);
    }

    .builder-block-wrapper.drag-over {
        border-top: 3px solid #7c3aed !important;
        margin-top: 10px;
    }

    .builder-control-btn {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #475569;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.15s;
        outline: none;
    }

    .builder-control-btn:hover {
        background-color: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
        transform: translateY(-1px);
    }

    #mobile-sidebar-close,
    #mobile-sidebar-toggle {
        display: none;
    }

    #builder-save-btn:disabled {
        background: #cbd5e1 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
        pointer-events: none !important;
    }

    /* Hide controls in preview mode */
    #builder-canvas.preview-mode .builder-block-wrapper {
        border: none;
        padding: 0;
        margin-bottom: 0;
        background: transparent;
    }

    #builder-canvas.preview-mode .builder-block-controls,
    #builder-canvas.preview-mode .builder-block-controls-select {
        display: none !important;
    }

    #builder-canvas.preview-mode .builder-editable {
        outline: none;
    }

    #builder-canvas.preview-mode [contenteditable="true"] {
        contenteditable: false !important;
    }

    #builder-canvas.preview-mode textarea,
    #builder-canvas.preview-mode input,
    #builder-canvas.preview-mode select {
        pointer-events: none;
        border: none;
        background: transparent;
        padding: 0;
    }

    /* Spin animation for loading state */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Media queries for mobile visual builder view */
    @media (max-width: 1024px) {
        #visual-builder-overlay {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .builder-header-container {
            height: 60px !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            padding: 0 0.75rem !important;
            gap: 0.5rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .builder-header-container > div:first-child {
            display: none !important;
        }
        .builder-workspace-container {
            flex-direction: row !important;
            position: relative;
            height: calc(100vh - 60px) !important;
            overflow: hidden !important;
        }
        #mobile-sidebar-toggle {
            display: flex !important;
        }
        #mobile-sidebar-close {
            display: block !important;
        }
        #builder-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            height: 100vh !important;
            width: 300px !important;
            max-height: none !important;
            z-index: 99999 !important;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #builder-sidebar.mobile-open {
            transform: translateX(0) !important;
        }
        
        /* Compact tab controls */
        #builder-tab-edit, #builder-tab-preview {
            padding: 0.4rem 0.65rem !important;
            font-size: 0.8rem !important;
        }
        
        /* Compact actions buttons & status */
        .builder-actions-wrapper {
            gap: 0.4rem !important;
        }
        .builder-actions-wrapper button {
            padding: 0.4rem 0.65rem !important;
            font-size: 0.8rem !important;
        }
        .builder-actions-wrapper .btn-text,
        .builder-actions-wrapper .status-text {
            display: none !important;
        }
    }
        #sidebar-widgets-panel {
            padding: 1rem !important;
            gap: 1rem !important;
        }
        #sidebar-widgets-panel > div[style*="display: grid;"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.5rem !important;
        }
        .builder-canvas-area {
            padding: 1.25rem 0.75rem !important;
            height: 100% !important;
            width: 100% !important;
        }
        .builder-block-controls {
            top: 2px !important;
            right: 2px !important;
        }
        .builder-block-wrapper {
            padding: 0.5rem !important;
        }
    }
</style>
<!-- Load External Visual Page Builder Script -->
<script src="<?= BASE_URL ?>/js/admin-builder.js?v=<?= time() ?>" defer></script>