<?php
$pageTitle = "Ma'lumotlarni sinxronlash";
require __DIR__ . '/../components/layout-header.php';
?>

<style>
    .sync-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .sync-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 32px;
        margin-bottom: 24px;
    }
    
    .sync-card h2 {
        margin: 0 0 24px 0;
        font-size: 20px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .sync-card h2 .icon {
        font-size: 24px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .stat-item {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }
    
    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 8px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    
    .last-updated {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 16px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    
    .last-updated.no-data {
        background: #fef3c7;
        border-color: #fde68a;
    }
    
    .last-updated .icon {
        font-size: 20px;
    }
    
    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .upload-area.dragover {
        border-color: #667eea;
        background: #eef2ff;
    }
    
    .upload-area .icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
    
    .upload-area h3 {
        margin: 0 0 8px 0;
        color: #334155;
        font-size: 18px;
    }
    
    .upload-area p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .upload-area input[type="file"] {
        display: none;
    }
    
    .btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 20px;
    }
    
    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .btn-upload:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-danger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-danger:hover {
        background: #fecaca;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }
    
    .alert-warning.sync-hint {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }
    
    .instructions {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 20px 24px;
        margin-top: 24px;
    }
    
    .instructions h4 {
        margin: 0 0 12px 0;
        color: #1e40af;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .instructions ol {
        margin: 0;
        padding-left: 24px;
        color: #3b82f6;
    }
    
    .instructions li {
        margin-bottom: 8px;
    }
    
    .instructions code {
        background: #dbeafe;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 13px;
    }
    
    .file-name {
        margin-top: 12px;
        padding: 10px 16px;
        background: #e0e7ff;
        border-radius: 8px;
        color: #3730a3;
        font-weight: 500;
        display: none;
    }
    
    .file-name.visible {
        display: block;
    }
</style>

<div class="admin-page-header">
    <h1 class="admin-page-title">🔄 Ma'lumotlarni sinxronlash</h1>
    <p class="admin-page-subtitle">HEMIS API dan fakultetlar, yo'nalishlar va guruhlarni yuklash</p>
</div>

<div class="sync-container">
    <?php if (!empty($_SESSION['sync_success'])): ?>
        <div class="alert alert-success">
            <span>✅</span>
            <span><?= htmlspecialchars($_SESSION['sync_success']) ?></span>
        </div>
        <?php unset($_SESSION['sync_success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['sync_error'])): ?>
        <div class="alert alert-error">
            <span>❌</span>
            <span><?= htmlspecialchars($_SESSION['sync_error']) ?></span>
        </div>
        <?php unset($_SESSION['sync_error']); ?>
    <?php endif; ?>

    <?php if (($stats['groups_count'] ?? 0) === 0): ?>
        <div class="alert alert-warning sync-hint">
            <span>⚠️</span>
            <span>Guruhlar hali yuklanmagan. Avval <strong>Avto-sinxronlash</strong>ni ishga tushiring yoki <strong>JSON faylni</strong> yuklang. Testlarga guruhlarni tanlash uchun avval ma'lumotlarni sinxronlash kerak.</span>
        </div>
    <?php endif; ?>

    <div class="sync-card">
        <h2><span class="icon">⚡</span> Avto-sinxronlash (HEMIS dan)</h2>
        <p style="color: #64748b; margin-top: -8px; margin-bottom: 20px;">
            Bu usul fayl yuklamasdan, to'g'ridan-to'g'ri HEMIS API dan guruhlarni yuklaydi.
        </p>

        <form action="/admin/settings/sync/auto" method="POST" id="syncForm" onsubmit="return confirmSync();">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">limit</label>
                    <input type="number" name="limit" value="200" min="1" max="500" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;" />
                </div>
                <div>
                    <label style="display:block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">start page</label>
                    <input type="number" name="start_page" value="1" min="1" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;" />
                </div>
                <div>
                    <label style="display:block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">max pages (ixtiyoriy)</label>
                    <input type="number" name="max_pages" id="max_pages" placeholder="Bo'sh qoldiring - barcha sahifalar" min="1" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;" />
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        ⚠️ Bo'sh qoldirsangiz, barcha sahifalar yuklanadi
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Bearer token (agar kerak bo'lsa)</label>
                <input type="text" name="bearer_token" placeholder="Bearer token" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px;" />
                <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Agar API token talab qilsa, tokenni shu yerga kiriting. Agar talab qilmasa — bo'sh qoldiring.
                </div>
            </div>

            <div style="text-align:center;">
                <button type="submit" class="btn-upload">⚡ Avto-sinxronlashni boshlash</button>
            </div>
        <p class="text-muted small mb-0" style="margin-top: 16px; color: #64748b; font-size: 14px; text-align: center;">Avval guruhlarni sinxronlashni bajaring, keyin testlarga guruhlarni tanlash mumkin.</p>
        </form>
    </div>

    <div class="sync-card">
        <h2><span class="icon">📊</span> Joriy statistika</h2>
        
        <?php if ($stats['last_updated']): ?>
            <div class="last-updated">
                <span class="icon">✅</span>
                <span>Oxirgi yangilanish: <strong><?= htmlspecialchars($stats['last_updated']) ?></strong></span>
            </div>
        <?php else: ?>
            <div class="last-updated no-data">
                <span class="icon">⚠️</span>
                <span>Ma'lumotlar hali yuklanmagan</span>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?= $stats['faculties_count'] ?></div>
                <div class="stat-label">Fakultetlar</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['specialties_count'] ?></div>
                <div class="stat-label">Yo'nalishlar</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['groups_count'] ?></div>
                <div class="stat-label">Guruhlar</div>
            </div>
        </div>
        
        <?php if ($stats['pagination']): ?>
            <div style="text-align: center; color: #64748b; font-size: 14px;">
                📄 HEMIS da jami: <?= $stats['pagination']['totalCount'] ?? '?' ?> guruh
                <?php if (($stats['pagination']['page'] ?? 1) < ($stats['pagination']['pageCount'] ?? 1)): ?>
                    <br><span style="color: #f59e0b;">⚠️ Faqat <?= $stats['pagination']['page'] ?>/<?= $stats['pagination']['pageCount'] ?> sahifa yuklangan</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="sync-card">
        <h2><span class="icon">📤</span> JSON faylni yuklash</h2>
        
        <form action="/admin/settings/sync/import" method="POST" enctype="multipart/form-data" id="upload-form">
            <div class="upload-area" id="upload-area">
                <div class="icon">📁</div>
                <h3>JSON faylni tanlang yoki shu yerga tashlang</h3>
                <p>HEMIS API dan yuklangan group-list.json faylini yuklang</p>
                <input type="file" name="json_file" id="json-file" accept=".json,application/json">
                <div class="file-name" id="file-name"></div>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn-upload" id="upload-btn" disabled>
                    📤 Yuklash va saqlash
                </button>
            </div>
        </form>
        
        <div class="instructions">
            <h4>📋 Qanday yuklash kerak:</h4>
            <ol>
                <li>HEMIS API Swagger sahifasini oching: <code>https://student.terdpi.uz/swagger</code></li>
                <li><code>/v1/data/group-list</code> endpointini toping</li>
                <li><code>limit</code> parametrini <code>200</code> ga o'zgartiring (ko'proq ma'lumot uchun)</li>
                <li>"Execute" tugmasini bosing</li>
                <li>"Download" tugmasini bosib JSON faylni yuklab oling</li>
                <li>Yuqoridagi maydonga faylni tashlang yoki tanlang</li>
                <li>Barcha sahifalarni yuklash uchun jarayonni takrorlang (page=2, 3, ...)</li>
            </ol>
        </div>
    </div>

    <?php if ($stats['groups_count'] > 0): ?>
    <div class="sync-card">
        <h2><span class="icon">🗑️</span> Ma'lumotlarni tozalash</h2>
        <p style="color: #64748b; margin-bottom: 20px;">
            Barcha yuklangan fakultetlar, yo'nalishlar va guruhlarni o'chirish.
            Bu amal qaytarib bo'lmaydi!
        </p>
        <form action="/admin/settings/sync/clear" method="POST" onsubmit="return confirm('Haqiqatan ham barcha ma\'lumotlarni o\'chirmoqchimisiz?');">
            <button type="submit" class="btn-danger">
                🗑️ Barcha ma'lumotlarni o'chirish
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
(function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('json-file');
    const fileName = document.getElementById('file-name');
    const uploadBtn = document.getElementById('upload-btn');
    
    // Клик по области загрузки
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Выбор файла
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = '📄 ' + this.files[0].name;
            fileName.classList.add('visible');
            uploadBtn.disabled = false;
        } else {
            fileName.classList.remove('visible');
            uploadBtn.disabled = true;
        }
    });
    
    // Drag & Drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileName.textContent = '📄 ' + e.dataTransfer.files[0].name;
            fileName.classList.add('visible');
            uploadBtn.disabled = false;
        }
    });
})();

// Функция подтверждения синхронизации
function confirmSync() {
    const maxPagesInput = document.getElementById('max_pages');
    const maxPages = maxPagesInput ? maxPagesInput.value.trim() : '';
    
    if (maxPages === '') {
        return confirm('⚠️ Diqqat! "Max pages" maydoni bo\'sh. Barcha sahifalar yuklanadi. Bu uzoq vaqt olishi mumkin.\n\nDavom etasizmi?');
    }
    
    return confirm('Avto-sinxronlashni boshlaymizmi? Bu bir necha soniya vaqt olishi mumkin.');
}
</script>

<?php require __DIR__ . '/../components/layout-footer.php'; ?>
