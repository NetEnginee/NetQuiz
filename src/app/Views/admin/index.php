<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!-- Custom Styles for Admin -->
<style>
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
            Lihat Kuis Aktif
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

                        <div class="admin-form-group" style="margin-top: 0.65rem; margin-bottom: 0;">
                            <label class="admin-label" style="font-size: 0.8rem;">Kunci Jawaban Benar</label>
                            <select id="q-correct" class="admin-select">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
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
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                <h3 class="admin-card-title" style="margin-bottom: 0;">
                    <i data-lucide="award" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                    Buat Lencana Baru
                </h3>
                <button type="button" id="open-badge-modal-btn" class="btn-outline-primary"
                    style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="list" style="width: 1rem; height: 1rem;"></i>
                    Lihat Lencana Aktif
                </button>
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
</div>

<!-- Modal Pop-up: Active Quizzes List -->
<div id="quiz-list-modal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3
                style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="layout-list" style="width: 1.25rem; height: 1.25rem; color: #7c3aed;"></i>
                Daftar Kuis yang Aktif Saat Ini
            </h3>
            <button type="button" id="close-quiz-modal-btn" class="close-modal-btn">
                <i data-lucide="x" style="width: 1.25rem; height: 1.25rem;"></i>
            </button>
        </div>
        <div class="admin-modal-body">
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
    document.addEventListener('DOMContentLoaded', () => {
        // --- TAB SWITCHER LOGIC ---
        const tabButtons = document.querySelectorAll('.admin-tab-btn');
        const sections = document.querySelectorAll('.admin-section-content');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');

                // Remove active classes
                tabButtons.forEach(b => b.classList.remove('active'));
                sections.forEach(s => s.classList.remove('active'));

                // Set active clicked tab & target section
                btn.classList.add('active');
                const targetSec = document.getElementById(targetId);
                if (targetSec) targetSec.classList.add('active');

                // Prevent page scroll for short/scoped sections on desktop if content fits on screen
                const isShortSection = (targetId === 'badge-section' || targetId === 'profile-section' || targetId === 'manage-section');
                const isDesktop = window.innerWidth >= 1024;
                const contentFits = document.documentElement.scrollHeight <= window.innerHeight;

                if (isShortSection && isDesktop && contentFits) {
                    document.documentElement.style.overflowY = 'hidden';
                    document.body.style.overflowY = 'hidden';
                } else {
                    document.documentElement.style.overflowY = 'auto';
                    document.body.style.overflowY = 'auto';
                }
            });
        });

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
                image: currentImageBase64
            });

            qTextInput.value = '';
            qOptAInput.value = '';
            qOptBInput.value = '';
            qOptCInput.value = '';
            qOptDInput.value = '';
            qCorrectSelect.selectedIndex = 0;
            if (qImageInput) qImageInput.value = '';
            if (qImageFilename) qImageFilename.textContent = 'Belum ada gambar yang dipilih';
            if (qImageLabelText) qImageLabelText.textContent = 'Tambahkan Gambar';
            currentImageBase64 = '';

            updateDOM();
            qTextInput.focus();
            if (typeof checkQuestionInputs === 'function') checkQuestionInputs();
        });

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