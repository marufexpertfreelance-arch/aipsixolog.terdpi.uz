<?php
/**
 * Страница начала теста Ranglar metodikasi
 */
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranglar Metodikasi</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #059669 0%, #10b981 25%, #34d399 50%, #6ee7b7 75%, #a7f3d0 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        body .main-content {
            background: transparent;
            max-width: 100%;
            padding: 0;
        }
        
        .nav-container {
            max-width: 100%;
            border-radius: 0;
            margin: 0;
            padding: 20px 40px;
            background: rgba(5, 150, 105, 0.85);
            backdrop-filter: blur(20px);
        }
        
        .nav-menu a {
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s;
        }
        
        .nav-menu a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .logo-text {
            color: rgba(255, 255, 255, 0.95);
        }
        
        .test-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .layout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .layout-header h1 {
            font-size: 42px;
            font-weight: 800;
            margin: 0 0 12px 0;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tagline {
            font-size: 18px;
            color: #6b7280;
            margin: 0;
        }
        
        .instructions {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
        }
        
        .instructions h3 {
            margin: 0 0 16px 0;
            color: #059669;
            font-size: 20px;
        }
        
        .instructions p {
            margin: 0 0 12px 0;
            line-height: 1.6;
            color: #374151;
        }
        
        .instructions ul {
            margin: 12px 0;
            padding-left: 24px;
            color: #374151;
        }
        
        .instructions li {
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        .btn-start {
            display: block;
            width: 100%;
            padding: 18px 32px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="/">Bosh sahifa</a></li>
                <?php if (!empty($_SESSION['hemis_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">
                            👤 <?= htmlspecialchars($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                    <li><a href="/hemis/logout">Chiqish</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="test-container">
            <div class="card">
                <div class="layout-header">
                    <h1>🎨 Ranglar Metodikasi</h1>
                    <p class="tagline">Ranglar orqali psixologik holatni baholash</p>
                </div>

                <div class="instructions">
                    <h3>Test haqida</h3>
                    <p><?= nl2br(htmlspecialchars($test['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                    
                    <h3>Test qanday o'tkaziladi?</h3>
                    <p><?= nl2br(htmlspecialchars($test['instructions'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                    
                    <ul>
                        <li><strong>1-bosqich:</strong> O'zingizga eng yoqadigan rangni tanlang.</li>
                        <li><strong>2-bosqich:</strong> Qaysi ranglarni yoqtirmasligingizni ko'rsating (bir yoki bir nechta).</li>
                    </ul>
                    
                    <p style="margin-top: 16px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
                        <strong>Eslatma:</strong> Testda hech qanday to'g'ri yoki noto'g'ri javob yo'q. Faqat o'z his-tuyg'ularingizga qarab javob bering.
                    </p>
                </div>

                <a href="/ranglar/select" class="btn-start">
                    Testni boshlash →
                </a>
            </div>
        </div>
    </main>
</body>
</html>

