<?php
$pageTitle = ($test['title'] ?? 'Test') . ' - Guruhlar statistikasi';
$extraStyles = '
    .faculty-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 32px;
    }
    .faculty-link {
        display: block;
        padding: 16px 20px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        text-decoration: none;
        color: #374151;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .faculty-link:hover {
        border-color: #667eea;
        background: #f9fafb;
        transform: translateX(4px);
    }
    .faculty-link.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }
    .tg-stat-card {
        padding: 24px;
        background: white;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        text-align: center;
        transition: all 0.3s ease;
    }
    .tg-stat-card.sust { border-color: #ef4444; }
    .tg-stat-card.ortacha { border-color: #f59e0b; }
    .tg-stat-card.yuqori { border-color: #3b82f6; }
    .tg-stat-card.juda-yuqori { border-color: #10b981; }
    .tg-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
    }
    .tg-stat-card.sust .tg-stat-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .tg-stat-card.ortacha .tg-stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .tg-stat-card.yuqori .tg-stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .tg-stat-card.juda-yuqori .tg-stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .tg-stat-value { font-size: 32px; font-weight: 700; margin: 8px 0; }
    .tg-stat-card.sust .tg-stat-value { color: #ef4444; }
    .tg-stat-card.ortacha .tg-stat-value { color: #f59e0b; }
    .tg-stat-card.yuqori .tg-stat-value { color: #3b82f6; }
    .tg-stat-card.juda-yuqori .tg-stat-value { color: #10b981; }
    .tg-stat-label { font-size: 14px; color: #6b7280; margin-bottom: 4px; }
    .tg-stat-percentage { font-size: 18px; font-weight: 600; }
    .groups-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }
    .group-card {
        padding: 16px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .group-card:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= htmlspecialchars($test['title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="admin-page-subtitle">Guruhlar kesimida statistika</p>
</div>

<div class="admin-table-container" style="padding: 24px;">
        <div class="card" style="background: white; border-radius: 12px; padding: 24px;">
            <h2 style="margin-bottom: 20px;">Fakultetlarni bo'limidan kerakli guruhlar ro'yxatini tanlang.</h2>
            
            <!-- Список факультетов -->
            <div class="faculty-list">
                <?php foreach ($faculties ?? [] as $faculty): ?>
                    <?php 
                    $facultyName = $faculty['name'] ?? '';
                    $isSelected = $selected_faculty === $facultyName;
                    ?>
                    <a href="/admin/results/test-groups?test_id=<?= urlencode($test_id ?? 'eysenck') ?>&faculty=<?= urlencode($facultyName) ?>" 
                       class="faculty-link <?= $isSelected ? 'selected' : '' ?>">
                        <?= htmlspecialchars($facultyName, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selected_faculty && $faculty_stats): ?>
                <!-- Статистика по факультету -->
                <h3 style="margin-bottom: 20px; margin-top: 32px;">Fakultet kesimida statistika</h3>
                <div class="stats-cards">
                    <div class="stat-card sust">
                        <div class="stat-icon">🎓</div>
                        <div class="stat-label">Sust</div>
                        <div class="stat-value"><?= htmlspecialchars((string)($faculty_stats['categories']['Sust'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                        <div class="stat-percentage" style="color: #ef4444;">
                            <?= htmlspecialchars((string)($faculty_stats['percentages']['Sust'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                        </div>
                    </div>
                    <div class="stat-card ortacha">
                        <div class="stat-icon">🎓</div>
                        <div class="stat-label">O'rtacha</div>
                        <div class="stat-value"><?= htmlspecialchars((string)($faculty_stats['categories']['O\'rtacha'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                        <div class="stat-percentage" style="color: #f59e0b;">
                            <?= htmlspecialchars((string)($faculty_stats['percentages']['O\'rtacha'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                        </div>
                    </div>
                    <div class="stat-card yuqori">
                        <div class="stat-icon">🎓</div>
                        <div class="stat-label">Yuqori</div>
                        <div class="stat-value"><?= htmlspecialchars((string)($faculty_stats['categories']['Yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                        <div class="stat-percentage" style="color: #3b82f6;">
                            <?= htmlspecialchars((string)($faculty_stats['percentages']['Yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                        </div>
                    </div>
                    <div class="stat-card juda-yuqori">
                        <div class="stat-icon">🎓</div>
                        <div class="stat-label">Juda yuqori</div>
                        <div class="stat-value"><?= htmlspecialchars((string)($faculty_stats['categories']['Juda yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                        <div class="stat-percentage" style="color: #10b981;">
                            <?= htmlspecialchars((string)($faculty_stats['percentages']['Juda yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                        </div>
                    </div>
                </div>

                <!-- Сетка групп -->
                <h3 style="margin-bottom: 20px; margin-top: 32px;">Guruhlar kesimida</h3>
                <?php if (!empty($groups)): ?>
                    <div class="groups-grid">
                        <?php foreach ($groups as $groupName): ?>
                            <a href="/admin/results/group?test_id=<?= urlencode($test_id ?? 'eysenck') ?>&group=<?= urlencode($groupName) ?>" 
                               class="group-card">
                                <?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted">Bu fakultetda guruhlar topilmadi.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="muted" style="text-align: center; padding: 40px 20px;">
                    Statistika ko'rish uchun fakultetni tanlang.
                </p>
            <?php endif; ?>

            <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <a href="/admin/results/test?id=<?= urlencode($test_id ?? 'eysenck') ?>" class="btn-secondary">← Test natijalariga qaytish</a>
            </div>
        </div>
</div>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>

