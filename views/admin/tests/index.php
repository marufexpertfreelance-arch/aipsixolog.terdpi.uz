<?php $pageTitle = 'Barcha testlar'; ?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="admin-page-title">📋 Barcha testlar</h1>
            <p class="admin-page-subtitle">Yaratilgan testlarni boshqarish</p>
        </div>
        <a href="/admin/tests/create" class="btn-action btn-action-success" style="padding: 14px 28px; font-size: 15px;">
            + Yangi test yaratish
        </a>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-success" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #10b981; color: #065f46; padding: 16px 20px; border-radius: 12px; font-weight: 600; margin-bottom: 24px;">
        <strong>✓</strong> <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (empty($tests)): ?>
    <div class="stat-card" style="text-align: center; padding: 60px 20px;">
        <p style="color: #718096; font-size: 16px; margin: 0;">
            Hali hech qanday test yo'q. 
            <a href="/admin/tests/create" style="color: #667eea; font-weight: 600;">Birinchi testni yarating</a>
        </p>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nomi</th>
                    <th>Kategoriya</th>
                    <th>Savollar</th>
                    <th>Guruhlar</th>
                    <th>O'qituvchilar</th>
                    <th>Statistika</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tests as $t): ?>
                    <?php 
                    $allowedGroups = $t['allowed_groups'] ?? [];
                    if (!is_array($allowedGroups)) {
                        $allowedGroups = [];
                    }
                    $allowedGroups = array_filter($allowedGroups, fn($g) => !empty($g) && trim((string)$g) !== '');
                    $allowedGroups = array_values($allowedGroups);
                    
                    $allowedFaculties = $t['allowed_faculties'] ?? [];
                    if (!is_array($allowedFaculties)) {
                        $allowedFaculties = [];
                    }
                    $allowedFaculties = array_filter($allowedFaculties, fn($f) => !empty($f) && trim((string)$f) !== '');
                    $allowedFaculties = array_values($allowedFaculties);
                    
                    // Проверяем флаг "открыт для всех" ИЛИ если оба массива пусты (для обратной совместимости со старыми тестами)
                    $openForAll = ($t['open_for_all'] ?? false) || (empty($allowedGroups) && empty($allowedFaculties) && !isset($t['open_for_all']));
                    $hasRestrictions = !empty($allowedGroups) || !empty($allowedFaculties);
                    
                    $teacherData = $t['teacher_data'] ?? [];
                    $stats = $t['stats'] ?? ['total_students' => 0, 'passed_count' => 0];
                    ?>
                    <tr>
                        <td style="font-weight: 500; color: #718096;"><?= htmlspecialchars((string)($t['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div style="font-weight: 600; color: #1a202c;"><?= htmlspecialchars($t['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div style="font-size: 12px; color: #718096; margin-top: 4px;">
                                <?= htmlspecialchars($t['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($t['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge badge-info"><?= count($t['questions'] ?? []) ?></span>
                        </td>
                        <td>
                            <?php if ($openForAll && !$hasRestrictions): ?>
                                <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <span>🌐</span>
                                    <span>Barcha talabalar</span>
                                </span>
                            <?php elseif ($hasRestrictions): ?>
                                <?php 
                                $groupsCount = count($allowedGroups);
                                $facultiesCount = count($allowedFaculties);
                                ?>
                                <span class="badge badge-primary">
                                    <?php if ($groupsCount > 0 && $facultiesCount > 0): ?>
                                        <?= $groupsCount ?> guruh, <?= $facultiesCount ?> fakultet
                                    <?php elseif ($groupsCount > 0): ?>
                                        <?= $groupsCount ?> guruh
                                    <?php else: ?>
                                        <?= $facultiesCount ?> fakultet
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #a0aec0; font-size: 13px;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($teacherData)): ?>
                                <span class="badge badge-success"><?= count($teacherData) ?> o'qituvchi</span>
                            <?php else: ?>
                                <span style="color: #a0aec0; font-size: 13px;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($stats['total_students'] > 0): ?>
                                <div style="font-weight: 600; color: #10b981;"><?= $stats['passed_count'] ?> / <?= $stats['total_students'] ?></div>
                                <div style="font-size: 12px; color: #718096;"><?= round(($stats['passed_count'] / $stats['total_students']) * 100, 1) ?>%</div>
                            <?php else: ?>
                                <span style="color: #a0aec0;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/admin/tests/show?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-info">Ochish</a>
                                <a href="/admin/tests/select-groups?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">Guruhlar</a>
                                <a href="/admin/tests/select-teachers?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-success">O'qituvchilar</a>
                                <a href="/admin/tests/edit?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-primary">Tahrirlash</a>
                                <a href="/admin/tests/delete?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-danger" onclick="return confirm('Testni o\'chirishni xohlaysizmi?');">O'chirish</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>
