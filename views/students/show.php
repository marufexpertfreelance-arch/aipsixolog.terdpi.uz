<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termiz davlat pedagogika instituti</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="/admin/tests">Mening testlarim</a></li>
                <li><a href="/admin/tests/create">Test yaratish</a></li>
                <li><a href="/students">Talabalar</a></li>
                <li><a href="/admin/results">Natijalar</a></li>
                <li><a href="/admin/results/statistics">Statistika</a></li>
                    <li><a href="/admin/logout">Chiqish</a></li>
            </ul>
            <div class="nav-right"></div>
        </div>
    </nav>

    <main class="main-content">
        <div class="layout-header">
            <h1>Talaba kartasi</h1>
            <p class="tagline">
                Bu yerda HEMIS dan olingan asosiy ma'lumotlar va psixologik modul (demo) birlashtirilgan.
            </p>
        </div>

        <div class="card">
            <h2>Asosiy ma'lumotlar</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                <div>
                    <strong style="color: var(--text-secondary); font-size: 14px;">ID:</strong>
                    <div style="font-size: 16px; margin-top: 4px;">
                        <?= htmlspecialchars((string)($student['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div>
                    <strong style="color: var(--text-secondary); font-size: 14px;">To'liq ism:</strong>
                    <div style="font-size: 16px; margin-top: 4px;">
                        <?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div>
                    <strong style="color: var(--text-secondary); font-size: 14px;">Guruh:</strong>
                    <div style="font-size: 16px; margin-top: 4px;">
                        <?= htmlspecialchars($student['group'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div>
                    <strong style="color: var(--text-secondary); font-size: 14px;">Fakultet:</strong>
                    <div style="font-size: 16px; margin-top: 4px;">
                        <?= htmlspecialchars($student['faculty'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div>
                    <strong style="color: var(--text-secondary); font-size: 14px;">Holat:</strong>
                    <div style="font-size: 16px; margin-top: 4px;">
                        <?= htmlspecialchars($student['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>

            <h2 style="margin-top: 32px;">Psixologik ma'lumotlar (demo)</h2>
            <div style="margin-top: 16px; padding: 20px; background: #f9fafb; border-radius: var(--radius-md);">
                <p style="margin: 0 0 12px 0;">
                    <strong>Xavf darajasi:</strong>
                    <?php 
                    $risk = $student['risk_level'] ?? 'Aniqlanmagan';
                    $riskClass = 'result-score';
                    if (str_contains(mb_strtolower($risk), 'yuqori') || str_contains(mb_strtolower($risk), 'high')) {
                        $riskClass .= ' score-high';
                    } elseif (str_contains(mb_strtolower($risk), 'o\'rta') || str_contains(mb_strtolower($risk), 'medium') || str_contains(mb_strtolower($risk), 'сред')) {
                        $riskClass .= ' score-medium';
                    } else {
                        $riskClass .= ' score-low';
                    }
                    ?>
                    <span class="<?= $riskClass ?>" style="margin-left: 12px;">
                        <?= htmlspecialchars($risk, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </p>

                <p style="margin: 12px 0 0 0;">
                    <strong>Psixolog eslatmalari:</strong><br>
                    <span style="color: var(--text-secondary);">
                        <?= nl2br(htmlspecialchars($student['notes'] ?? 'Hali hech qanday yozuv yo\'q.', ENT_QUOTES, 'UTF-8')) ?>
                    </span>
                </p>

                <p class="muted small" style="margin: 12px 0 0 0;">
                    Oxirgi maslahat:
                    <?= htmlspecialchars($student['last_consultation'] ?? 'ma\'lumotlar yo\'q', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <h3 style="margin-top: 32px;">O'tkazilgan testlar</h3>
            <?php if (!empty($student['tests']) && is_array($student['tests'])): ?>
                <div style="margin-top: 16px;">
                    <?php foreach ($student['tests'] as $test): ?>
                        <div style="padding: 12px; background: #f9fafb; border-radius: var(--radius-md); margin-bottom: 8px;">
                            <span class="result-score score-medium" style="margin-right: 12px;">
                                <?= htmlspecialchars($test['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <strong><?= htmlspecialchars($test['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>:</strong>
                            <?= htmlspecialchars($test['result'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="muted small">Hali test natijalari yo'q.</p>
            <?php endif; ?>

            <div style="margin-top: 32px;">
                <a class="btn-secondary" href="/students">← Talabalar ro'yxatiga</a>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <p class="footer-text">TerDPI talabalar psixologik xizmati &copy; <?= date('Y') ?></p>
            <p class="footer-links">
                <a href="https://student.terdpi.uz" target="_blank" rel="noopener">HEMIS</a>
            </p>
        </div>
    </footer>
</body>
</html>
