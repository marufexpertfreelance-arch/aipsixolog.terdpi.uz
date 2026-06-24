<?php
$pageTitle = 'Guruh statistikasi';
$extraStyles = '
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            padding: 24px;
            background: white;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card.sust {
            border-color: #ef4444;
        }
        
        .stat-card.ortacha {
            border-color: #f59e0b;
        }
        
        .stat-card.yuqori {
            border-color: #3b82f6;
        }
        
        .stat-card.juda-yuqori {
            border-color: #10b981;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 24px;
        }
        
        .stat-card.sust .stat-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .stat-card.ortacha .stat-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .stat-card.yuqori .stat-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .stat-card.juda-yuqori .stat-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 8px 0;
        }
        
        .stat-card.sust .stat-value {
            color: #ef4444;
        }
        
        .stat-card.ortacha .stat-value {
            color: #f59e0b;
        }
        
        .stat-card.yuqori .stat-value {
            color: #3b82f6;
        }
        
        .stat-card.juda-yuqori .stat-value {
            color: #10b981;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .stat-percentage {
            font-size: 18px;
            font-weight: 600;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .students-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .students-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        
        .students-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        
        .students-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .temperament-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .temperament-sust {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .temperament-ortacha {
            background: #fef3c7;
            color: #92400e;
        }
        
        .temperament-yuqori {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .temperament-juda-yuqori {
            background: #d1fae5;
            color: #065f46;
        }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">📊 <?= htmlspecialchars($group ?? '', ENT_QUOTES, 'UTF-8') ?> guruhi statistikasi</h1>
    <p class="admin-page-subtitle"><?= htmlspecialchars($test['title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?> test natijalari</p>
</div>

<div class="admin-table-container" style="padding: 24px;">
            <!-- Статистика по категориям -->
            <div class="stats-cards">
                <div class="stat-card sust">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-label">Sust</div>
                    <div class="stat-value"><?= htmlspecialchars((string)($stats['categories']['Sust'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                    <div class="stat-percentage" style="color: #ef4444;">
                        <?= htmlspecialchars((string)($stats['percentages']['Sust'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                    </div>
                </div>
                <div class="stat-card ortacha">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-label">O'rtacha</div>
                    <div class="stat-value"><?= htmlspecialchars((string)($stats['categories']['O\'rtacha'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                    <div class="stat-percentage" style="color: #f59e0b;">
                        <?= htmlspecialchars((string)($stats['percentages']['O\'rtacha'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                    </div>
                </div>
                <div class="stat-card yuqori">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-label">Yuqori</div>
                    <div class="stat-value"><?= htmlspecialchars((string)($stats['categories']['Yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                    <div class="stat-percentage" style="color: #3b82f6;">
                        <?= htmlspecialchars((string)($stats['percentages']['Yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                    </div>
                </div>
                <div class="stat-card juda-yuqori">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-label">Juda yuqori</div>
                    <div class="stat-value"><?= htmlspecialchars((string)($stats['categories']['Juda yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?> ta</div>
                    <div class="stat-percentage" style="color: #10b981;">
                        <?= htmlspecialchars((string)($stats['percentages']['Juda yuqori'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%
                    </div>
                </div>
            </div>

            <!-- Таблица студентов -->
            <h3 style="margin-bottom: 20px; margin-top: 32px;">Talabalar ro'yxati</h3>
            <?php if (!empty($stats['students'])): ?>
                <div style="overflow-x: auto;">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>T/R</th>
                                <th>FIO</th>
                                <th>Guruh</th>
                                <th>E</th>
                                <th>N</th>
                                <th>L</th>
                                <th>Xususiyati</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['students'] as $index => $student): ?>
                                <?php 
                                $result = $student['result'] ?? null;
                                $temperament = $result['temperament'] ?? null;
                                
                                // Определяем класс для темперамента
                                $tempClass = '';
                                if ($temperament === 'Melancholic') {
                                    $tempClass = 'temperament-sust';
                                    $tempLabel = 'Sust';
                                } elseif ($temperament === 'Phlegmatic') {
                                    $tempClass = 'temperament-ortacha';
                                    $tempLabel = 'O\'rtacha';
                                } elseif ($temperament === 'Sanguine') {
                                    $tempClass = 'temperament-yuqori';
                                    $tempLabel = 'Yuqori';
                                } elseif ($temperament === 'Choleric') {
                                    $tempClass = 'temperament-juda-yuqori';
                                    $tempLabel = 'Juda yuqori';
                                } else {
                                    $tempLabel = 'Qiymatlanmagam';
                                }
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($student['name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($student['group'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= $result ? htmlspecialchars((string)($result['E'] ?? '-'), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                    <td><?= $result ? htmlspecialchars((string)($result['N'] ?? '-'), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                    <td><?= $result ? htmlspecialchars((string)($result['L'] ?? '-'), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                    <td>
                                        <?php if ($tempClass): ?>
                                            <span class="temperament-badge <?= $tempClass ?>">
                                                <?= htmlspecialchars($tempLabel, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #6b7280;"><?= htmlspecialchars($tempLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="muted">Bu guruhda talabalar topilmadi.</p>
            <?php endif; ?>

            <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <a href="/admin/results/test-groups?test_id=<?= urlencode($test_id ?? 'eysenck') ?>" class="btn-secondary">← Guruhlar ro'yxatiga qaytish</a>
            </div>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

