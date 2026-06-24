<?php
$title = $title ?? 'O\'qituvchilar ro\'yxati';
$teachers = $teachers ?? [];
$success = $success ?? null;
$error = $error ?? null;

$pageTitle = 'O\'qituvchilar ro\'yxati';
$extraStyles = '
    .teachers-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .teacher-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 28px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .teacher-card::before {
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
    .teacher-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }
    .teacher-card:hover::before {
        transform: scaleX(1);
    }
    .teacher-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }
    .teacher-avatar {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 900;
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    .teacher-avatar::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
        pointer-events: none;
    }
    .teacher-info {
        flex: 1;
        min-width: 0;
    }
    .teacher-name {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .teacher-department {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 1px solid rgba(102, 126, 234, 0.2);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #667eea;
    }
    .teacher-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .teacher-detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .teacher-detail-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .teacher-detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }
    .teacher-detail-value code {
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-family: "SF Mono", Monaco, "Cascadia Code", "Roboto Mono", Consolas, "Courier New", monospace;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .teacher-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }
    .teacher-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .teacher-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
    }
    .teacher-meta-icon {
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 4px;
        font-size: 10px;
    }
    .btn-delete-teacher {
        padding: 10px 20px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-delete-teacher:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    .empty-state {
        padding: 80px 20px;
        text-align: center;
        background: white;
        border-radius: 20px;
        border: 2px dashed #e5e7eb;
    }
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .empty-state h2 {
        font-size: 24px;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 12px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 16px;
    }
    .stats-summary {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 28px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-top: 24px;
    }
    .stats-summary-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .stats-summary-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .stats-summary-label {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stats-summary-value {
        font-size: 32px;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .stats-summary-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        padding: 10px 20px;
        border: 2px solid #667eea;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-summary-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    @media (max-width: 768px) {
        .teachers-container {
            grid-template-columns: 1fr;
        }
        .teacher-details {
            grid-template-columns: 1fr;
        }
    }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="admin-page-title">👨‍🏫 O'qituvchilar ro'yxati</h1>
            <p class="admin-page-subtitle">Ro'yxatdan o'tgan o'qituvchilarni ko'rish va boshqarish</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="/admin/results/teachers" class="btn-action btn-action-info" style="padding: 12px 20px; text-decoration: none;">
                📊 Natijalar
            </a>
            <a href="/admin/results/teacher-statistics" class="btn-action btn-action-success" style="padding: 12px 20px; text-decoration: none;">
                📈 Statistika
            </a>
        </div>
    </div>
</div>

<div class="admin-table-container" style="padding: 24px;">

    <?php if (!empty($success)): ?>
        <div style="padding: 16px 20px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 2px solid #10b981; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
            <strong style="color: #059669;">✅</strong>
            <span style="color: #065f46; margin-left: 8px; font-weight: 600;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="padding: 16px 20px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #ef4444; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
            <strong style="color: #dc2626;">⚠️</strong>
            <span style="color: #991b1b; margin-left: 8px; font-weight: 600;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($teachers)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">👨‍🏫</div>
            <h2>Hozircha o'qituvchilar yo'q</h2>
            <p>O'qituvchilar ro'yxatdan o'tgandan keyin bu yerda ko'rinadi.</p>
        </div>
    <?php else: ?>
        <div class="teachers-container">
            <?php foreach ($teachers as $teacher): ?>
                <?php
                $fullName = $teacher['full_name'] ?? '';
                $nameParts = explode(' ', $fullName);
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $initials .= mb_substr($part, 0, 1);
                    }
                }
                $initials = mb_strtoupper(mb_substr($initials, 0, 2));
                ?>
                <div class="teacher-card">
                    <div class="teacher-header">
                        <div class="teacher-avatar">
                            <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="teacher-info">
                            <div class="teacher-name">
                                <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="teacher-department">
                                🏢 <?= htmlspecialchars($teacher['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>

                    <div class="teacher-details">
                        <div class="teacher-detail-item">
                            <div class="teacher-detail-label">Login</div>
                            <div class="teacher-detail-value">
                                <code><?= htmlspecialchars($teacher['login'] ?? '-', ENT_QUOTES, 'UTF-8') ?></code>
                            </div>
                        </div>
                        <div class="teacher-detail-item">
                            <div class="teacher-detail-label">Email</div>
                            <div class="teacher-detail-value">
                                <?= htmlspecialchars($teacher['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <div class="teacher-detail-item">
                            <div class="teacher-detail-label">Telefon</div>
                            <div class="teacher-detail-value">
                                <?= htmlspecialchars($teacher['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <div class="teacher-detail-item">
                            <div class="teacher-detail-label">ID</div>
                            <div class="teacher-detail-value">
                                #<?= htmlspecialchars((string)($teacher['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>

                    <div class="teacher-footer">
                        <div class="teacher-meta">
                            <?php
                            $registeredAt = $teacher['registered_at'] ?? '';
                            if ($registeredAt):
                            ?>
                                <div class="teacher-meta-item">
                                    <div class="teacher-meta-icon">📅</div>
                                    <span>Ro'yxatdan o'tgan: <?= date('d.m.Y', strtotime($registeredAt)) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php
                            $lastLogin = $teacher['last_login'] ?? '';
                            if ($lastLogin):
                            ?>
                                <div class="teacher-meta-item">
                                    <div class="teacher-meta-icon">🕐</div>
                                    <span>Oxirgi kirish: <?= date('d.m.Y H:i', strtotime($lastLogin)) ?></span>
                                </div>
                            <?php else: ?>
                                <div class="teacher-meta-item">
                                    <div class="teacher-meta-icon">⏳</div>
                                    <span>Hali kirmagan</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="/admin/teachers/delete" style="display: inline;"
                              onsubmit="return confirm('Haqiqatan ham bu o\'qituvchini o\'chirmoqchimisiz?');">
                            <input type="hidden" name="teacher_id" value="<?= htmlspecialchars((string)($teacher['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn-delete-teacher">🗑️ O'chirish</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="stats-summary">
            <div class="stats-summary-content">
                <div class="stats-summary-item">
                    <div class="stats-summary-label">Jami o'qituvchilar</div>
                    <div class="stats-summary-value"><?= count($teachers) ?></div>
                </div>
                <div style="flex: 1;"></div>
                <a href="/teachers/register" class="stats-summary-link">
                    ➕ Yangi o'qituvchi qo'shish
                </a>
            </div>
            <p style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #f1f5f9; color: #64748b; font-size: 14px;">
                💡 O'qituvchilar <a href="/teachers/register" style="color: #667eea; text-decoration: none; font-weight: 700;">/teachers/register</a> sahifasidan ro'yxatdan o'tishlari mumkin.
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>

