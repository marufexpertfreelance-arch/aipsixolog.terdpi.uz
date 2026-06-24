<?php
/**
 * Личный кабинет студента - Профессиональный дизайн
 */
use App\Services\TestStorage;
$testStorage = new TestStorage();
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaxsiy kabinet - TerDPI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 40px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .nav-brand img {
            width: 44px;
            height: 44px;
            border-radius: 10px;
        }
        
        .nav-brand span {
            font-weight: 700;
            font-size: 16px;
            color: #0f172a;
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 24px;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .nav-link {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: 10px;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        
        .nav-link.logout:hover {
            background: #fef2f2;
            color: #ef4444;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 25%, #8b5cf6 50%, #a78bfa 75%, #c4b5fd 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            padding: 48px 40px;
            color: white;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        
        .hero p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }
        
        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            margin-top: -60px;
            position: relative;
            z-index: 10;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }
        
        .profile-avatar {
            flex-shrink: 0;
        }
        
        .profile-avatar img {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            object-fit: cover;
            border: 4px solid #e0e7ff;
        }
        
        .profile-avatar-placeholder {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            border: 4px solid #e0e7ff;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 20px;
        }
        
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        
        .profile-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e2e8f0;
        }
        
        .profile-item-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        
        .profile-item-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Section */
        .section {
            margin-top: 32px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .section-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        
        /* Test Cards Grid */
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .test-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            transition: all 0.3s;
        }
        
        .test-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
        
        .test-card-header {
            padding: 24px;
            text-align: center;
        }
        
        .test-card-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .test-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        
        .test-card-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }
        
        .test-card-stats {
            display: flex;
            border-top: 1px solid #f1f5f9;
        }
        
        .test-stat {
            flex: 1;
            padding: 14px;
            text-align: center;
            border-right: 1px solid #f1f5f9;
        }
        
        .test-stat:last-child {
            border-right: none;
        }
        
        .test-stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #6366f1;
        }
        
        .test-stat-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .test-card-action {
            padding: 16px 24px;
            background: #f8fafc;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(99,102,241,0.35);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99,102,241,0.45);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(16,185,129,0.35);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.45);
        }
        
        /* Results */
        .results-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .result-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }
        
        .result-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        
        .result-icon {
            width: 60px;
            height: 60px;
            background: #f1f5f9;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }
        
        .result-info {
            flex: 1;
        }
        
        .result-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        
        .result-meta {
            font-size: 14px;
            color: #64748b;
        }
        
        .result-action {
            flex-shrink: 0;
        }
        
        .btn-outline {
            background: white;
            color: #6366f1;
            border: 2px solid #e0e7ff;
            box-shadow: none;
        }
        
        .btn-outline:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .quick-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: white;
            border-radius: 12px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .quick-action:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        /* Telegram Banner */
        .telegram-banner {
            background: linear-gradient(135deg, #0088cc 0%, #00b4d8 100%);
            border-radius: 20px;
            padding: 28px 32px;
            margin-top: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 10px 40px rgba(0,136,204,0.25);
        }
        
        .telegram-banner:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0,136,204,0.35);
        }
        
        .telegram-icon {
            width: 64px;
            height: 64px;
            background: white;
            border-radius: 16px;
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
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .telegram-desc {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .telegram-btn {
            background: white;
            color: #0088cc;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 32px;
            color: #94a3b8;
            font-size: 14px;
        }
        
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .navbar { padding: 0 16px; }
            .nav-brand span { display: none; }
            .hero { padding: 32px 16px; }
            .hero h1 { font-size: 24px; }
            .container { padding: 24px 16px; }
            
            .profile-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 24px;
                margin-top: -40px;
            }
            
            .profile-avatar img,
            .profile-avatar-placeholder {
                width: 100px;
                height: 100px;
            }
            
            .profile-name { font-size: 22px; }
            .profile-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            
            .tests-grid { grid-template-columns: 1fr; }
            
            .result-card {
                flex-direction: column;
                text-align: center;
            }
            
            .result-action { width: 100%; }
            .result-action .btn { width: 100%; }
            
            .telegram-banner {
                flex-direction: column;
                text-align: center;
                padding: 24px;
            }
            
            .quick-actions { justify-content: center; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/dashboard" class="nav-brand">
            <img src="/images/logo.png" alt="TerDPI">
            <span>Termiz davlat pedagogika instituti</span>
        </a>
        
        <div class="nav-actions">
            <a href="/dashboard" class="nav-link">Shaxsiy kabinet</a>
            <?php if (!empty($_SESSION['hemis_user'])): ?>
                <div class="nav-user">
                    👤 <?= htmlspecialchars($user['name'] ?? 'Talaba', ENT_QUOTES, 'UTF-8') ?>
                </div>
                <a href="/hemis/logout" class="nav-link logout">Chiqish</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <div class="hero-content">
            <h1>👋 Xush kelibsiz, <?= htmlspecialchars(explode(' ', $user['name'] ?? 'Talaba')[0], ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p>Psixologik testlar va natijalaringiz</p>
        </div>
    </div>

    <div class="container">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-avatar">
                <?php if (!empty($user['image'])): ?>
                    <img src="<?= htmlspecialchars($user['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Rasm">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">👤</div>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h2 class="profile-name"><?= htmlspecialchars($user['name'] ?? 'Foydalanuvchi', ENT_QUOTES, 'UTF-8') ?></h2>
                
                <div class="profile-grid">
                    <?php if (!empty($user['student_id'])): ?>
                    <div class="profile-item">
                        <div class="profile-item-label">ID</div>
                        <div class="profile-item-value"><?= htmlspecialchars($user['student_id'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['group'])): ?>
                    <div class="profile-item">
                        <div class="profile-item-label">Guruh</div>
                        <div class="profile-item-value"><?= htmlspecialchars($user['group'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['specialty'])): ?>
                    <div class="profile-item">
                        <div class="profile-item-label">Yo'nalish</div>
                        <div class="profile-item-value"><?= htmlspecialchars($user['specialty'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['faculty'])): ?>
                    <div class="profile-item">
                        <div class="profile-item-label">Fakultet</div>
                        <div class="profile-item-value"><?= htmlspecialchars($user['faculty'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['semester'])): ?>
                    <div class="profile-item">
                        <div class="profile-item-label">Semestr</div>
                        <div class="profile-item-value"><?= htmlspecialchars($user['semester'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($error)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-top: 24px; color: #dc2626;">
            ⚠️ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>

        <!-- Tests Section -->
        <?php if (!empty($temperamentTests)): ?>
        <div class="section">
            <div class="section-header">
                <div class="section-icon">🧠</div>
                <h2 class="section-title">Psixologik testlar</h2>
            </div>
            
            <div class="tests-grid">
                <?php foreach ($temperamentTests as $test): ?>
                <div class="test-card">
                    <div class="test-card-header">
                        <div class="test-card-icon">
                            <?php
                            $icons = [
                                'eysenck' => '🎭',
                                'iq' => '🧩',
                                'lusher' => '🎨',
                                'aggression' => '🔥'
                            ];
                            echo $icons[$test['id']] ?? '📝';
                            ?>
                        </div>
                        <h3 class="test-card-title"><?= htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="test-card-desc"><?= htmlspecialchars($test['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    
                    <div class="test-card-stats">
                        <div class="test-stat">
                            <div class="test-stat-value"><?= $test['questions_count'] ?></div>
                            <div class="test-stat-label">Savollar</div>
                        </div>
                        <div class="test-stat">
                            <div class="test-stat-value"><?= $test['duration'] ?></div>
                            <div class="test-stat-label">Vaqt</div>
                        </div>
                    </div>
                    
                    <div class="test-card-action">
                        <?php 
                        $hasResult = false;
                        if ($test['id'] === 'eysenck' && $hasEysenckResults) $hasResult = true;
                        if ($test['id'] === 'iq' && ($hasIqResults ?? false)) $hasResult = true;
                        if ($test['id'] === 'lusher' && ($hasLusherResults ?? false)) $hasResult = true;
                        if ($test['id'] === 'aggression' && ($hasAggressionResults ?? false)) $hasResult = true;
                        ?>
                        
                        <?php if ($hasResult): ?>
                            <a href="<?= $test['results_url'] ?>" class="btn btn-success">✅ Natijalarni ko'rish</a>
                        <?php else: ?>
                            <a href="<?= $test['url'] ?>" class="btn btn-primary">Testni boshlash →</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Custom Tests -->
        <?php if (!empty($customTests)): ?>
        <div class="section">
            <div class="section-header">
                <div class="section-icon">📋</div>
                <h2 class="section-title">Psixolog testlari</h2>
            </div>
            
            <div class="tests-grid">
                <?php foreach ($customTests as $test): ?>
                <div class="test-card">
                    <div class="test-card-header">
                        <div class="test-card-icon">📝</div>
                        <h3 class="test-card-title"><?= htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="test-card-desc"><?= htmlspecialchars($test['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    
                    <div class="test-card-stats">
                        <div class="test-stat">
                            <div class="test-stat-value"><?= $test['questions_count'] ?></div>
                            <div class="test-stat-label">Savollar</div>
                        </div>
                        <div class="test-stat">
                            <div class="test-stat-value"><?= $test['duration'] ?></div>
                            <div class="test-stat-label">Vaqt</div>
                        </div>
                    </div>
                    
                    <div class="test-card-action">
                        <?php if (!empty($test['is_complete'])): ?>
                            <a href="<?= $test['results_url'] ?>" class="btn btn-success">✅ Natijalarni ko'rish</a>
                        <?php else: ?>
                            <a href="<?= $test['url'] ?>" class="btn btn-primary">Testni boshlash →</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Results Section -->
        <?php if (!empty($studentResults)): ?>
        <div class="section">
            <div class="section-header">
                <div class="section-icon">📊</div>
                <h2 class="section-title">Mening natijalarim</h2>
            </div>
            
            <div class="results-list">
                <?php foreach ($studentResults as $result): ?>
                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                    <div class="result-card">
                        <div class="result-icon">🎭</div>
                        <div class="result-info">
                            <h3 class="result-title">Temperament testi</h3>
                            <p class="result-meta">Sizning temperamentingiz aniqlangan</p>
                        </div>
                        <div class="result-action">
                            <a href="/eysenck/results" class="btn btn-outline">Natijalarni ko'rish →</a>
                        </div>
                    </div>
                    
                    <?php elseif (($result['test_type'] ?? '') === 'iq'): ?>
                    <?php
                    $iqScore = $result['calculated_score'] ?? 0;
                    $dateStr = !empty($result['submitted_at']) ? date('d.m.Y', strtotime($result['submitted_at'])) : '';
                    ?>
                    <div class="result-card">
                        <div class="result-icon">🧩</div>
                        <div class="result-info">
                            <h3 class="result-title">IQ Test - <?= htmlspecialchars((string)$iqScore, ENT_QUOTES, 'UTF-8') ?> ball</h3>
                            <p class="result-meta"><?= $dateStr ?></p>
                        </div>
                        <div class="result-action">
                            <a href="/iq/results" class="btn btn-outline">Natijalarni ko'rish →</a>
                        </div>
                    </div>
                    
                    <?php elseif (($result['test_type'] ?? '') === 'lusher'): ?>
                    <?php
                    $dateStr = !empty($result['completed_at']) ? date('d.m.Y', strtotime($result['completed_at'])) : '';
                    ?>
                    <div class="result-card">
                        <div class="result-icon">🎨</div>
                        <div class="result-info">
                            <h3 class="result-title">Lüscher rangli test</h3>
                            <p class="result-meta"><?= $dateStr ?></p>
                        </div>
                        <div class="result-action">
                            <a href="/lusher/results" class="btn btn-outline">Natijalarni ko'rish →</a>
                        </div>
                    </div>
                    
                    <?php elseif (($result['test_type'] ?? '') === 'aggression'): ?>
                    <?php
                    $aggrScore = $result['calculated_score'] ?? 0;
                    $aggrCat = $result['interpretation']['category'] ?? '';
                    $dateStr = !empty($result['submitted_at']) ? date('d.m.Y', strtotime($result['submitted_at'])) : '';
                    ?>
                    <div class="result-card">
                        <div class="result-icon">🔥</div>
                        <div class="result-info">
                            <h3 class="result-title">Tajovuz holati tashxisi - <?= htmlspecialchars((string)$aggrScore, ENT_QUOTES, 'UTF-8') ?> ball</h3>
                            <p class="result-meta"><?= htmlspecialchars($aggrCat, ENT_QUOTES, 'UTF-8') ?> • <?= $dateStr ?></p>
                        </div>
                        <div class="result-action">
                            <a href="/aggression/results" class="btn btn-outline">Natijalarni ko'rish →</a>
                        </div>
                    </div>
                    
                    <?php elseif (($result['test_type'] ?? '') === 'custom'): ?>
                    <?php
                    $testId = (int)($result['test_id'] ?? 0);
                    $test = $testId > 0 ? $testStorage->findById($testId) : null;
                    if ($test === null) continue;
                    $testTitle = $result['test_title'] ?? $test['title'] ?? 'Test';
                    $dateStr = !empty($result['submitted_at']) ? date('d.m.Y', strtotime($result['submitted_at'])) : '';
                    ?>
                    <div class="result-card">
                        <div class="result-icon">📝</div>
                        <div class="result-info">
                            <h3 class="result-title"><?= htmlspecialchars($testTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="result-meta"><?= $dateStr ?></p>
                        </div>
                        <div class="result-action">
                            <a href="/tests/results?id=<?= $testId ?>" class="btn btn-outline">Natijalarni ko'rish →</a>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="section">
            <div class="section-header">
                <div class="section-icon">⚡</div>
                <h2 class="section-title">Tezkor amallar</h2>
            </div>
            
            <div class="quick-actions">
                <a href="/students" class="quick-action">📋 Talabalar ro'yxati</a>
                <?php if (!$hasEysenckResults): ?>
                    <a href="/eysenck/start" class="quick-action">🎭 Temperament testini boshlash</a>
                <?php endif; ?>
                <?php if (!($hasIqResults ?? false)): ?>
                    <a href="/iq/start" class="quick-action">🧩 IQ testini boshlash</a>
                <?php endif; ?>
                <?php if (!($hasAggressionResults ?? false)): ?>
                    <a href="/aggression/start" class="quick-action">🔥 Tajovuz tashxisini boshlash</a>
                <?php endif; ?>
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
    </div>

    <footer class="footer">
        <p>TerDPI talabalar psixologik xizmati © <?= date('Y') ?></p>
        <p><a href="https://student.terdpi.uz" target="_blank" rel="noopener">HEMIS</a></p>
    </footer>
</body>
</html>
