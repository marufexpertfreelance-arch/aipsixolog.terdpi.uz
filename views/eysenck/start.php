<?php
/**
 * Страница начала теста Айзенка
 */
$homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$displayName = !empty($_SESSION['teacher_user'])
    ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
    : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
$logoutUrl = !empty($_SESSION['teacher_user']) ? '/teachers/logout' : '/hemis/logout';
$isTeacher = !empty($_SESSION['teacher_user']);
$primaryColor = $isTeacher ? '#10b981' : '#6366f1';
$primaryGradient = $isTeacher ? 'linear-gradient(135deg, #047857 0%, #10b981 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)';
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eysenck Temperament Testi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* Navigation */
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 40px;
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
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        
        .nav-brand span {
            font-weight: 700;
            font-size: 16px;
            color: #0f172a;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .nav-links a:hover {
            color: <?= $primaryColor ?>;
        }
        
        .nav-links a.logout {
            color: #ef4444;
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f1f5f9;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
        }
        
        /* Main Content */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 24px;
        }
        
        /* Test Card */
        .test-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        
        .test-header {
            background: <?= $primaryGradient ?>;
            padding: 48px 40px;
            text-align: center;
            color: white;
        }
        
        .test-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 24px;
        }
        
        .test-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        
        .test-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .test-body {
            padding: 40px;
        }
        
        .test-description {
            font-size: 16px;
            line-height: 1.8;
            color: #64748b;
            margin-bottom: 32px;
        }
        
        /* Instructions */
        .instructions {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 32px;
        }
        
        .instructions h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .instructions ul {
            list-style: none;
            padding: 0;
        }
        
        .instructions li {
            padding: 10px 0;
            padding-left: 28px;
            position: relative;
            color: #475569;
            font-size: 15px;
            border-bottom: 1px solid rgba(3,105,161,0.1);
        }
        
        .instructions li:last-child {
            border-bottom: none;
        }
        
        .instructions li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #0369a1;
            font-weight: 700;
        }
        
        /* Stats */
        .test-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .stat-item {
            flex: 1;
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: <?= $primaryColor ?>;
        }
        
        .stat-label {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
        
        /* Button */
        .btn-start {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 20px 32px;
            background: <?= $primaryGradient ?>;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 16px <?= $isTeacher ? 'rgba(16,185,129,0.35)' : 'rgba(99,102,241,0.35)' ?>;
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px <?= $isTeacher ? 'rgba(16,185,129,0.45)' : 'rgba(99,102,241,0.45)' ?>;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 40px 24px;
            color: #94a3b8;
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 16px;
            }
            
            .nav-brand span {
                display: none;
            }
            
            .container {
                padding: 24px 16px;
            }
            
            .test-header {
                padding: 32px 24px;
            }
            
            .test-header h1 {
                font-size: 24px;
            }
            
            .test-body {
                padding: 24px;
            }
            
            .test-stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-brand">
            <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TerDPI">
            <span>Termiz davlat pedagogika instituti</span>
        </a>
        
        <div class="nav-links">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">← Orqaga</a>
            <div class="nav-user">
                👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" class="logout">Chiqish</a>
        </div>
    </nav>

    <main class="container">
        <div class="test-card">
            <div class="test-header">
                <div class="test-icon">🎭</div>
                <h1>Eysenck Temperament Testi</h1>
                <p>Temperamentingizni aniqlash uchun psixologik test</p>
            </div>
            
            <div class="test-body">
                <p class="test-description">
                    <?= htmlspecialchars($test['description'] ?? 'Bu test sizning temperamentingizni aniqlash uchun mo\'ljallangan. Test natijasida siz o\'zingizning asosiy temperament turini (sangvinik, xolerik, flegmatik yoki melanxolik) bilib olasiz.', ENT_QUOTES, 'UTF-8') ?>
                </p>
                
                <div class="test-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?= count($test['questions'] ?? []) ?></div>
                        <div class="stat-label">Savollar soni</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">15-20</div>
                        <div class="stat-label">Daqiqa</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">Ha/Yo'q</div>
                        <div class="stat-label">Javob turi</div>
                    </div>
                </div>
                
                <div class="instructions">
                    <h3>📋 Ko'rsatmalar</h3>
                    <ul>
                        <li>Har bir savolga <strong>"Ha"</strong> yoki <strong>"Yo'q"</strong> javob bering</li>
                        <li>O'ylab qoling va hal qilganingizdan keyin javob bering</li>
                        <li>Hech qanday savolni o'tkazib yubormang</li>
                        <li>To'g'ri yoki noto'g'ri javob yo'q - o'zingizga xos javob bering</li>
                    </ul>
                </div>
                
                <a href="/eysenck/question?q=1" class="btn-start">
                    🚀 Testni boshlash
                </a>
            </div>
        </div>
    </main>

    <footer class="footer">
        TerDPI talabalar psixologik xizmati © <?= date('Y') ?>
    </footer>
</body>
</html>
