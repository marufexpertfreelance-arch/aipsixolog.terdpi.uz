<?php
$title = $title ?? 'O\'qituvchi kabineti';
$teacher = $teacher ?? [];
$assignedTests = $assignedTests ?? [];
$completedCustomTestIds = $completedCustomTestIds ?? [];
$eysenckResult = $eysenckResult ?? null;
$iqResult = $iqResult ?? null;
$lusherResult = $lusherResult ?? null;
$error = $error ?? null;

$fullName = $teacher['full_name'] ?? 'O\'qituvchi';
$email = $teacher['email'] ?? '';
$phone = $teacher['phone'] ?? '';
$department = $teacher['department'] ?? '';
$picture = $teacher['picture'] ?? '';
$hemisId = $teacher['hemis_id'] ?? '';
$hemisType = $teacher['hemis_type'] ?? '';
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 24px 0;
            z-index: 100;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px 24px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
            text-decoration: none;
        }
        
        .sidebar-logo img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        
        .sidebar-logo span {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            line-height: 1.3;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .sidebar-menu a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        
        .sidebar-menu a.active {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #047857;
            border-right: 3px solid #10b981;
        }
        
        .sidebar-menu a.logout {
            color: #ef4444;
        }
        
        .sidebar-menu a.logout:hover {
            background: #fef2f2;
        }
        
        .menu-icon {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        /* Top Bar */
        .topbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .topbar-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .topbar-user img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }
        
        .topbar-user-info {
            text-align: right;
        }
        
        .topbar-user-name {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        
        .topbar-user-role {
            font-size: 12px;
            color: #64748b;
        }
        
        /* Content */
        .content {
            padding: 32px;
        }
        
        /* Profile Card */
        .profile-card {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            border-radius: 16px;
            padding: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 32px;
            color: white;
        }
        
        .profile-avatar {
            position: relative;
            flex-shrink: 0;
        }
        
        .profile-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .profile-avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
        }
        
        .hemis-badge {
            position: absolute;
            bottom: -6px;
            right: -6px;
            background: #fbbf24;
            color: #78350f;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
            opacity: 0.9;
        }
        
        .profile-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .profile-stats {
            display: flex;
            gap: 12px;
        }
        
        .stat-item {
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 16px 24px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }
        
        .stat-label {
            font-size: 11px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Section */
        .section {
            margin-bottom: 32px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }
        
        .section-badge {
            background: #e2e8f0;
            color: #64748b;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Test Cards Grid */
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        
        .test-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        
        .test-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16,185,129,0.15);
        }
        
        .test-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .test-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .test-icon.eysenck { background: #fef3c7; }
        .test-icon.iq { background: #dbeafe; }
        .test-icon.lusher { background: #fce7f3; }
        .test-icon.custom { background: #dcfce7; }
        
        .test-card h3 {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
        }
        
        .test-card p {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        
        .test-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        
        .test-status.done { background: #dcfce7; color: #166534; }
        .test-status.pending { background: #fef3c7; color: #92400e; }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        
        .btn-primary {
            background: #10b981;
            color: white;
        }
        
        .btn-primary:hover {
            background: #059669;
        }
        
        .btn-outline {
            background: white;
            color: #10b981;
            border: 1px solid #10b981;
        }
        
        .btn-outline:hover {
            background: #ecfdf5;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
        }
        
        .empty-state-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }
        
        .empty-state p {
            color: #94a3b8;
            font-size: 14px;
        }
        
        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        /* Telegram Banner */
        .telegram-banner {
            background: linear-gradient(135deg, #0088cc 0%, #00b4d8 100%);
            border-radius: 16px;
            padding: 24px 28px;
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 8px 24px rgba(0,136,204,0.25);
        }
        
        .telegram-banner:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0,136,204,0.35);
        }
        
        .telegram-icon {
            width: 56px;
            height: 56px;
            background: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .telegram-content {
            flex: 1;
            color: white;
        }
        
        .telegram-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .telegram-desc {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .telegram-btn {
            background: white;
            color: #0088cc;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 24px;
            color: #94a3b8;
            font-size: 13px;
            margin-left: 260px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .footer { margin-left: 0; }
            .profile-card { flex-direction: column; text-align: center; }
            .profile-stats { justify-content: center; }
            .profile-meta { justify-content: center; }
            
            .telegram-banner {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="/teacher/dashboard" class="sidebar-logo">
            <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TerDPI">
            <span>Termiz davlat<br>pedagogika instituti</span>
        </a>
        
        <ul class="sidebar-menu">
            <li>
                <a href="/teacher/dashboard" class="active">
                    <span class="menu-icon">📊</span>
                    Bosh sahifa
                </a>
            </li>
            <li>
                <a href="/eysenck/start">
                    <span class="menu-icon">🎭</span>
                    Eysenck testi
                </a>
            </li>
            <li>
                <a href="/iq/start">
                    <span class="menu-icon">🧩</span>
                    IQ testi
                </a>
            </li>
            <li>
                <a href="/lusher/start">
                    <span class="menu-icon">🎨</span>
                    Lüscher testi
                </a>
            </li>
            <li style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                <a href="/teachers/logout" class="logout">
                    <span class="menu-icon">🚪</span>
                    Chiqish
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main -->
    <main class="main">
        <!-- Top Bar -->
        <header class="topbar">
            <h1 class="topbar-title">Dashboard</h1>
            <div class="topbar-user">
                <div class="topbar-user-info">
                    <div class="topbar-user-name"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="topbar-user-role">O'qituvchi</div>
                </div>
                <?php if ($picture): ?>
                    <img src="<?= htmlspecialchars($picture, ENT_QUOTES, 'UTF-8') ?>" alt="">
                <?php endif; ?>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php if ($picture): ?>
                        <img src="<?= htmlspecialchars($picture, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    <?php else: ?>
                        <div class="profile-avatar-placeholder">
                            <?= mb_substr($fullName, 0, 1, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($hemisType === 'employee'): ?>
                        <span class="hemis-badge">HEMIS</span>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h2 class="profile-name"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="profile-meta">
                        <?php if ($email): ?>
                            <span>📧 <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <?php if ($phone): ?>
                            <span>📱 <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <?php if ($hemisId): ?>
                            <span>🆔 HEMIS: <?= htmlspecialchars($hemisId, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?= count($assignedTests) ?></div>
                        <div class="stat-label">Tayinlangan</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= count($completedCustomTestIds) ?></div>
                        <div class="stat-label">Bajarilgan</div>
                    </div>
                </div>
            </div>

            <!-- Assigned Tests Section -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">📋 Tayinlangan testlar</h2>
                    <span class="section-badge"><?= count($assignedTests) ?> ta</span>
                </div>
                
                <?php if (!empty($assignedTests)): ?>
                <div class="tests-grid">
                    <?php foreach ($assignedTests as $test): ?>
                        <?php $isCompleted = in_array($test['id'], $completedCustomTestIds); ?>
                        <div class="test-card">
                            <div class="test-card-header">
                                <div class="test-icon custom">📝</div>
                                <h3><?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            </div>
                            <p><?= htmlspecialchars($test['description'] ?? 'Test tavsifi', ENT_QUOTES, 'UTF-8') ?></p>
                            
                            <?php if ($isCompleted): ?>
                                <div class="test-status done">✓ Bajarilgan</div>
                            <?php else: ?>
                                <div class="test-status pending">● Kutilmoqda</div>
                                <a href="/tests/take?id=<?= $test['id'] ?>" class="btn btn-primary">Boshlash</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>Hozircha sizga test tayinlanmagan</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Psychology Tests Section -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">🧠 Psixologik testlar</h2>
                    <span class="section-badge">3 ta</span>
                </div>
                
                <div class="tests-grid">
                    <!-- Eysenck -->
                    <div class="test-card">
                        <div class="test-card-header">
                            <div class="test-icon eysenck">🎭</div>
                            <h3>Eysenck temperament testi</h3>
                        </div>
                        <p>Temperamentingizni aniqlang: sangvinik, xolerik, flegmatik yoki melanxolik.</p>
                        <?php if ($eysenckResult): ?>
                            <a href="/eysenck/results" class="btn btn-outline">Natijalarni ko'rish</a>
                        <?php else: ?>
                            <a href="/eysenck/start" class="btn btn-primary">Boshlash</a>
                        <?php endif; ?>
                    </div>

                    <!-- IQ -->
                    <div class="test-card">
                        <div class="test-card-header">
                            <div class="test-icon iq">🧩</div>
                            <h3>IQ testi</h3>
                        </div>
                        <p>Mantiqiy fikrlash qobiliyatingizni sinab ko'ring.</p>
                        <?php if ($iqResult): ?>
                            <a href="/iq/results" class="btn btn-outline">Natijalarni ko'rish</a>
                        <?php else: ?>
                            <a href="/iq/start" class="btn btn-primary">Boshlash</a>
                        <?php endif; ?>
                    </div>

                    <!-- Lusher -->
                    <div class="test-card">
                        <div class="test-card-header">
                            <div class="test-icon lusher">🎨</div>
                            <h3>Lüscher rang testi</h3>
                        </div>
                        <p>Hissiy holatlaringizni ranglar orqali bilib oling.</p>
                        <?php if ($lusherResult): ?>
                            <a href="/lusher/results" class="btn btn-outline">Natijalarni ko'rish</a>
                        <?php else: ?>
                            <a href="/lusher/start" class="btn btn-primary">Boshlash</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Telegram Banner -->
        <a href="https://t.me/+IVXIhjPp-oljYTMy" target="_blank" rel="noopener" class="telegram-banner">
            <div class="telegram-icon">
                <svg width="36" height="36" viewBox="0 0 48 48" fill="none">
                    <path d="M24 4C12.96 4 4 12.96 4 24C4 35.04 12.96 44 24 44C35.04 44 44 35.04 44 24C44 12.96 35.04 4 24 4ZM33.64 17.36L30.36 32.52C30.12 33.6 29.44 33.88 28.52 33.36L23.52 29.64L21.12 31.96C20.84 32.24 20.6 32.48 20.08 32.48L20.4 27.36L29.76 18.88C30.16 18.52 29.68 18.32 29.16 18.68L17.52 26.12L12.6 24.56C11.52 24.24 11.48 23.44 12.8 22.88L32.24 15.64C33.12 15.32 33.92 15.92 33.64 17.36Z" fill="#0088cc"/>
                </svg>
            </div>
            <div class="telegram-content">
                <div class="telegram-title">📱 IT-loyihalar va dasturlash</div>
                <div class="telegram-desc">Dasturlash, IT-loyihalar va zamonaviy texnologiyalar haqida foydali ma'lumotlar!</div>
            </div>
            <div class="telegram-btn">Obuna bo'lish →</div>
        </a>
    </main>

    <footer class="footer">
        TerDPI talabalar psixologik xizmati © <?= date('Y') ?>
    </footer>
</body>
</html>
