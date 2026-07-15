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

<script>
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

                // Prevent page scroll for short/scoped sections on desktop if content fits on screen
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
                categoryInput.value = btn.getAttribute('data-value');
            });
        });

        // Active Quiz Modal Selectors
        const activeModal = document.getElementById('quiz-list-modal');
        const openActiveModalBtn = document.getElementById('open-quiz-modal-btn');
        const closeActiveModalBtn = document.getElementById('close-quiz-modal-btn');

        openActiveModalBtn.addEventListener('click', () => {
            // Check current active section tab ID
            const activeSection = document.querySelector('.admin-section-content.active');
            const activeId = activeSection ? activeSection.getAttribute('id') : '';

            // Get elements
            const modalTitle = document.getElementById('active-list-modal-title');
            const quizzesContainer = document.getElementById('modal-list-quizzes');
            const badgesContainer = document.getElementById('modal-list-badges');
            const materialsContainer = document.getElementById('modal-list-materials');

            // Reset visibility
            if (quizzesContainer) quizzesContainer.style.display = 'none';
            if (badgesContainer) badgesContainer.style.display = 'none';
            if (materialsContainer) materialsContainer.style.display = 'none';

            // Show relevant active list and change title
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

        closeActiveModalBtn.addEventListener('click', () => {
            activeModal.classList.remove('show');
        });

        activeModal.addEventListener('click', (e) => {
            if (e.target === activeModal) {
                activeModal.classList.remove('show');
            }
        });

        // Saved Questions Modal Selectors
        const savedModal = document.getElementById('saved-questions-modal');
        const openSavedModalBtn = document.getElementById('open-saved-modal-btn');
        const closeSavedModalBtn = document.getElementById('close-saved-modal-btn');
        const modalSavedList = document.getElementById('saved-questions-list');

        openSavedModalBtn.addEventListener('click', () => {
            savedModal.classList.add('show');
        });

        closeSavedModalBtn.addEventListener('click', () => {
            savedModal.classList.remove('show');
        });

        savedModal.addEventListener('click', (e) => {
            if (e.target === savedModal) {
                savedModal.classList.remove('show');
            }
        });

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
            tempForm.action = '<?= BASE_URL ?>/admin/badge/delete/' + id;
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

            savedCountEl.textContent = savedQuestions.length;

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
            if (typeof checkQuestionInputs === 'function') checkQuestionInputs();
        });

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

                importFileName.textContent = file.name;
                const reader = new FileReader();

                reader.onload = function (evt) {
                    try {
                        const content = evt.target.result;
                        let questionsImported = [];

                        if (file.name.endsWith('.json')) {
                            const parsed = JSON.parse(content);
                            let questionsArray = [];

                            // Check if format is a wrapper object with metadata or direct array
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
                                if (row.length <= 1 && row[0] === '') continue; // Skip empty rows

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
                            // Auto-fill default metadata if fields are empty
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
                        importFileInput.value = ''; // Reset input file
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

        // 1. Buat Kuis: General Info Input Listeners to trigger updateDOM
        if (quizTitleInput && quizDescInput && quizDurationInput) {
            quizTitleInput.addEventListener('input', updateDOM);
            quizDurationInput.addEventListener('input', updateDOM);
            quizDescInput.addEventListener('input', updateDOM);
        }

        // 2. Buat Kuis: Question Builder Form Validation (Simpan Soal button)
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

        // 3. Daftarkan Akun Form Validation (Daftarkan Anggota Baru button)
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

        // 4. Lencana Form Validation (Buat Lencana button)
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

        // 5. Pengaturan Profil Form Validation (Simpan Profil button with dirty check)
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
                }, 500); // Wait for transition animation to finish before removing
            }, 2000); // 2 seconds timeout
        });
    });
</script>

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
                            <span style="font-size: 0.75rem; font-weight: 600; color: #475569;">Gambar</span>
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

<script>
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
        const rowItem = document.querySelector(`button[onclick="editMaterialVisual(${id})"]`);
        const originalContent = rowItem.innerHTML;
        rowItem.disabled = true;
        rowItem.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width: 0.85rem; height: 0.85rem; display: inline-block;"></i>...';
        if (window.lucide) window.lucide.createIcons();

        fetch(`<?= BASE_URL ?>/admin/material/get/${id}`)
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
                form.action = `<?= BASE_URL ?>/admin/material/update/${data.id}`;
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
        document.getElementById('edit-material-id').value = '';
        document.getElementById('form-material-title').value = '';
        document.getElementById('form-material-category').value = 'Routing';
        document.getElementById('form-material-difficulty').value = 'Mudah';
        document.getElementById('form-material-content').value = '';

        const form = document.getElementById('create-material-form');
        form.action = `<?= BASE_URL ?>/admin/material/create`;
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
            } else if (tagName === 'img') {
                blocks.push({
                    id: 'block_' + Math.random().toString(36).substr(2, 9),
                    type: 'image',
                    imageSrc: el.getAttribute('src') || '',
                    imageAlt: el.getAttribute('alt') || ''
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
        const canvas = document.getElementById('builder-canvas');
        const emptyState = document.getElementById('canvas-empty-state');

        const oldWrappers = canvas.querySelectorAll('.builder-block-wrapper');
        oldWrappers.forEach(w => w.remove());

        if (builderBlocks.length === 0) {
            emptyState.style.display = 'flex';
            return;
        } else {
            emptyState.style.display = 'none';
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
                            <input type="text" value="${block.imageSrc || ''}" oninput="updateBlockImageSrc('${block.id}', this.value)" placeholder="<?= BASE_URL ?>/uploads/nama-file.png" style="flex: 1; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Alt:</span>
                            <input type="text" value="${block.imageAlt || ''}" oninput="updateBlockImageAlt('${block.id}', this.value)" placeholder="Deskripsi" style="width: 120px; height: 32px; padding: 0 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div style="text-align: center; border: 1px dashed #cbd5e1; border-radius: 6px; background: #fff; padding: 0.5rem;">
                            ${block.imageSrc ? `<img src="${block.imageSrc}" alt="${block.imageAlt || ''}" style="max-height: 200px; max-width: 100%; border-radius: 4px; display: inline-block;">` : `<span style="font-size: 0.75rem; color: #94a3b8;"><i data-lucide="image" style="width: 1rem; height: 1rem; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i> Belum ada gambar dimasukkan</span>`}
                        </div>
                    </div>
                `;
            }

            wrapper.innerHTML = controlsHtml + contentHtml;

            // NATIVE DRAG & DROP LOGIC (Only in Edit Mode)
            if (activeBuilderMode === 'edit') {
                wrapper.setAttribute('draggable', 'true');
                
                wrapper.addEventListener('dragstart', (e) => {
                    // Bypass dragging if user is interacting with text inputs or editables
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
                    
                    // Rebuild builderBlocks array based on new DOM order
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

</script>