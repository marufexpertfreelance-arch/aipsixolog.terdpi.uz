<?php
$title = $title ?? 'O\'qituvchilar statistikasi';
$stats = $stats ?? [];

$pageTitle = 'O\'qituvchilar statistikasi';
$extraStyles = '
    .teacher-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .teacher-stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 28px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .teacher-stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .teacher-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }
    .teacher-stat-card:hover::before {
        transform: scaleX(1);
    }
    .teacher-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 2px solid #f1f5f9;
    }
    .teacher-stat-user {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        flex: 1;
    }
    .teacher-stat-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 900;
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
        flex: 0 0 auto;
        position: relative;
        overflow: hidden;
    }
    .teacher-stat-avatar::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
        pointer-events: none;
    }
    .teacher-stat-info {
        flex: 1;
        min-width: 0;
    }
    .teacher-stat-name {
        font-size: 18px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 8px;
    }
    .teacher-stat-dept {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 1px solid rgba(102, 126, 234, 0.2);
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #667eea;
    }
    .teacher-stat-action {
        flex: 0 0 auto;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        text-decoration: none;
        font-weight: 800;
        font-size: 13px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .teacher-stat-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    .teacher-stat-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .teacher-stat-metric {
        padding: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .teacher-stat-metric:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .teacher-stat-value {
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1;
    }
    .teacher-stat-metric:nth-child(1) .teacher-stat-value {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-stat-metric:nth-child(2) .teacher-stat-value {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-stat-metric:nth-child(3) .teacher-stat-value {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-stat-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .teacher-summary {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 32px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        margin-top: 24px;
    }
    .teacher-summary-title {
        font-size: 22px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .teacher-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    .teacher-summary-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .teacher-summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .teacher-summary-card:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .teacher-summary-card:hover::before {
        transform: scaleX(1);
    }
    .teacher-summary-card:nth-child(1)::before {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    .teacher-summary-card:nth-child(2)::before {
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    }
    .teacher-summary-card:nth-child(3)::before {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    .teacher-summary-card:nth-child(4)::before {
        background: linear-gradient(90deg, #7c3aed 0%, #6d28d9 100%);
    }
    .teacher-summary-value {
        font-size: 36px;
        font-weight: 900;
        margin-bottom: 8px;
        line-height: 1;
    }
    .teacher-summary-card:nth-child(1) .teacher-summary-value {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-summary-card:nth-child(2) .teacher-summary-value {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-summary-card:nth-child(3) .teacher-summary-value {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-summary-card:nth-child(4) .teacher-summary-value {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .teacher-summary-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .empty-stats {
        padding: 80px 20px;
        text-align: center;
        background: white;
        border-radius: 20px;
        border: 2px dashed #e5e7eb;
    }
    .empty-stats-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .empty-stats h2 {
        font-size: 24px;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 12px;
    }
    .empty-stats p {
        color: #94a3b8;
        font-size: 16px;
    }
    @media (max-width: 768px) {
        .teacher-stats-grid {
            grid-template-columns: 1fr;
        }
        .teacher-stat-head {
            flex-direction: column;
            align-items: flex-start;
        }
        .teacher-stat-action {
            width: 100%;
            text-align: center;
        }
    }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <h1 class="admin-page-title">📊 O'qituvchilar statistikasi</h1>
    <p class="admin-page-subtitle">Har bir o'qituvchining test natijalari haqida umumiy ma'lumot</p>
</div>

<div class="admin-table-container" style="padding: 24px;">

    <?php if (empty($stats)): ?>
        <div class="empty-stats">
            <div class="empty-stats-icon">📊</div>
            <h2>Ma'lumotlar topilmadi</h2>
            <p>Hozircha o'qituvchilar testlardan o'tmagan.</p>
        </div>
    <?php else: ?>
                <div class="teacher-stats-grid">
                    <?php foreach ($stats as $stat): ?>
                        <?php
                        $teacher = $stat['teacher'];
                        $fullName = $teacher['full_name'] ?? 'Noma\'lum';
                        $department = $teacher['department'] ?? '-';
                        $initials = '';
                        $nameParts = explode(' ', $fullName);
                        foreach ($nameParts as $part) {
                            if (!empty($part)) {
                                $initials .= mb_substr($part, 0, 1);
                            }
                        }
                        $initials = mb_strtoupper(mb_substr($initials, 0, 2));
                        $teacherId = (int)($teacher['id'] ?? 0);
                        ?>
                        <div class="teacher-stat-card">
                            <div class="teacher-stat-head">
                                <div class="teacher-stat-user">
                                    <div class="teacher-stat-avatar">
                                        <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="teacher-stat-info">
                                        <div class="teacher-stat-name">
                                            <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="teacher-stat-dept">
                                            <?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </div>
                                <a class="teacher-stat-action" href="/admin/results/teachers?teacher_id=<?= urlencode((string)$teacherId) ?>">
                                    Natijalar
                                </a>
                            </div>

                            <div class="teacher-stat-metrics">
                                <div class="teacher-stat-metric">
                                    <div class="teacher-stat-value"><?= (int)($stat['total_tests'] ?? 0) ?></div>
                                    <div class="teacher-stat-label">Jami</div>
                                </div>
                                <div class="teacher-stat-metric">
                                    <div class="teacher-stat-value" style="color: #059669;"><?= (int)($stat['custom_tests'] ?? 0) ?></div>
                                    <div class="teacher-stat-label">Oddiy</div>
                                </div>
                                <div class="teacher-stat-metric">
                                    <div class="teacher-stat-value" style="color: #2563eb;"><?= (int)($stat['built_in_tests'] ?? 0) ?></div>
                                    <div class="teacher-stat-label">Psixologik</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

        <div class="teacher-summary">
            <div class="teacher-summary-title">
                📈 Umumiy statistika
            </div>
                    <div class="teacher-summary-grid">
                        <?php
                        $totalTeachers = count($stats);
                        $totalTests = array_sum(array_column($stats, 'total_tests'));
                        $totalCustomTests = array_sum(array_column($stats, 'custom_tests'));
                        $totalBuiltInTests = array_sum(array_column($stats, 'built_in_tests'));
                        ?>
                        <div class="teacher-summary-card">
                            <div class="teacher-summary-value" style="color: #10b981;">
                                <?= $totalTeachers ?>
                            </div>
                            <div class="teacher-summary-label">
                                Jami o'qituvchilar
                            </div>
                        </div>
                        <div class="teacher-summary-card">
                            <div class="teacher-summary-value" style="color: #2563eb;">
                                <?= $totalTests ?>
                            </div>
                            <div class="teacher-summary-label">
                                Jami topshirilgan testlar
                            </div>
                        </div>
                        <div class="teacher-summary-card">
                            <div class="teacher-summary-value" style="color: #059669;">
                                <?= $totalCustomTests ?>
                            </div>
                            <div class="teacher-summary-label">
                                Oddiy testlar
                            </div>
                        </div>
                        <div class="teacher-summary-card">
                            <div class="teacher-summary-value" style="color: #7c3aed;">
                                <?= $totalBuiltInTests ?>
                            </div>
                            <div class="teacher-summary-label">
                                Psixologik testlar
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>

