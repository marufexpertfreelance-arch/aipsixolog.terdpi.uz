<?php
/**
 * Страница начала теста Люшера
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
    <title>Lüscher Rang Testi</title>
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
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 24px;
        }
        
        .test-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        
        .test-header {
            background: linear-gradient(135deg, #ec4899 0%, #f472b6 50%, #fb7185 100%);
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
        
        .instructions {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 32px;
        }
        
        .instructions h3 {
            font-size: 16px;
            font-weight: 700;
            color: #be185d;
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
            color: #9d174d;
            font-size: 15px;
            border-bottom: 1px solid rgba(190,24,93,0.1);
        }
        
        .instructions li:last-child {
            border-bottom: none;
        }
        
        .instructions li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #be185d;
            font-weight: 700;
        }
        
        .color-preview {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 32px;
        }
        
        .color-dot {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
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
            color: #ec4899;
        }
        
        .stat-label {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
        
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
        
        .footer {
            text-align: center;
            padding: 40px 24px;
            color: #94a3b8;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 12px 16px; }
            .nav-brand span { display: none; }
            .container { padding: 24px 16px; }
            .test-header { padding: 32px 24px; }
            .test-header h1 { font-size: 24px; }
            .test-body { padding: 24px; }
            .test-stats { flex-direction: column; }
            .color-dot { width: 32px; height: 32px; }
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
                <div class="test-icon">🎨</div>
                <h1>Lüscher Rang Testi</h1>
                <p>Ranglar orqali psixologik holatni aniqlash</p>
            </div>
            
            <div class="test-body">
                <p class="test-description">
                    <?= htmlspecialchars($test['description'] ?? 'Bu test sizning hissiy holatlaringizni ranglar orqali aniqlash uchun mo\'ljallangan. Test natijasida siz o\'zingizning psixologik holatlaringiz haqida ma\'lumot olasiz.', ENT_QUOTES, 'UTF-8') ?>
                </p>
                
                <div class="color-preview">
                    <div class="color-dot" style="background: #4a4a4a;"></div>
                    <div class="color-dot" style="background: #0066cc;"></div>
                    <div class="color-dot" style="background: #00aa00;"></div>
                    <div class="color-dot" style="background: #ff0000;"></div>
                    <div class="color-dot" style="background: #ffff00;"></div>
                    <div class="color-dot" style="background: #cc66cc;"></div>
                    <div class="color-dot" style="background: #8b4513;"></div>
                    <div class="color-dot" style="background: #1a1a1a;"></div>
                </div>
                
                <div class="test-stats">
                    <div class="stat-item">
                        <div class="stat-value">8</div>
                        <div class="stat-label">Ranglar</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">2</div>
                        <div class="stat-label">Bosqich</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">5-10</div>
                        <div class="stat-label">Daqiqa</div>
                    </div>
                </div>
                
                <div class="instructions">
                    <h3>📋 Ko'rsatmalar</h3>
                    <ul>
                        <li><strong>1-bosqich:</strong> Ranglarni sizga eng yoqadigan tartibdan eng kam yoqadigangizga qarab tartibga soling</li>
                        <li><strong>2-bosqich:</strong> Jarayonni qaytadan takrorlang</li>
                        <li>Faqat o'z his-tuyg'ularingizga qarab javob bering</li>
                        <li>To'g'ri yoki noto'g'ri javob yo'q</li>
                    </ul>
                </div>
                
                <a href="/lusher/round1" class="btn-start">
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
