<?php
/**
 * Страница результатов теста Ranglar metodikasi
 */
$preferredColor = $results['preferred_color'] ?? null;
$preferredInterpretation = $results['preferred_interpretation'] ?? '';
$rejectedInterpretations = $results['rejected_interpretations'] ?? [];
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranglar Metodikasi - Natijalar</title>
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
        
        .results-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-bottom: 32px;
        }
        
        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .color-display {
            text-align: center;
            margin: 32px 0;
        }
        
        .color-box-large {
            width: 150px;
            height: 150px;
            margin: 0 auto 16px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .color-name-large {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .interpretation {
            background: #f0fdf4;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #10b981;
            margin: 24px 0;
            line-height: 1.8;
            color: #374151;
        }
        
        .rejected-colors {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }
        
        .rejected-item {
            background: #fef2f2;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #dc2626;
        }
        
        .rejected-color-box {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            background: #059669;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #047857;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container" style="max-width: 100%; border-radius: 0; margin: 0; padding: 20px 40px; background: rgba(5, 150, 105, 0.85); backdrop-filter: blur(20px);">
            <a href="/" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text" style="color: rgba(255, 255, 255, 0.95);">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="/" style="color: rgba(255, 255, 255, 0.9);">Bosh sahifa</a></li>
                <?php if (!empty($_SESSION['hemis_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">👤 <?= htmlspecialchars($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi', ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <li><a href="/hemis/logout" style="color: rgba(255, 255, 255, 0.9);">Chiqish</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="results-container">
            <div class="result-card">
                <h1 class="section-title">🎨 Ranglar Metodikasi - Natijalar</h1>
                
                <?php if ($preferredColor): ?>
                    <div class="color-display">
                        <div class="color-box-large" style="background-color: <?= htmlspecialchars($preferredColor['code'], ENT_QUOTES, 'UTF-8') ?>"></div>
                        <div class="color-name-large"><?= htmlspecialchars($preferredColor['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <p style="color: #6b7280; margin-top: 8px;"><?= htmlspecialchars($preferredColor['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    
                    <div class="interpretation">
                        <h3 style="margin: 0 0 12px 0; color: #059669;">Yoqtirilgan rang tahlili:</h3>
                        <p style="margin: 0;"><?= nl2br(htmlspecialchars($preferredInterpretation, ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($rejectedInterpretations)): ?>
                    <h2 style="font-size: 22px; font-weight: 600; color: #1f2937; margin-top: 40px; margin-bottom: 24px;">
                        Rad etilgan ranglar va tahlil:
                    </h2>
                    
                    <div class="rejected-colors">
                        <?php foreach ($rejectedInterpretations as $item): ?>
                            <div class="rejected-item">
                                <div class="rejected-color-box" style="background-color: <?= htmlspecialchars($item['color']['code'], ENT_QUOTES, 'UTF-8') ?>"></div>
                                <h4 style="margin: 0 0 8px 0; color: #1f2937;"><?= htmlspecialchars($item['color']['name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280;">
                                    <?= nl2br(htmlspecialchars($item['interpretation'], ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 40px;">
                    <a href="/dashboard" class="btn-back">Dashboardga qaytish</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

