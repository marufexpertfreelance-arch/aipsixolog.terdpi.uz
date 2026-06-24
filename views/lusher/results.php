<?php
/**
 * Страница результатов теста Люшера
 */
$colors = $test['colors'] ?? [];
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lyusher Testi - Natijalar</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #2563eb 50%, #3b82f6 75%, #60a5fa 100%);
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
            background: rgba(30, 58, 138, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
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
        
        .results-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-bottom: 32px;
        }
        
        .layout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .layout-header h1 {
            font-size: 42px;
            font-weight: 800;
            margin: 0 0 12px 0;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tagline {
            font-size: 18px;
            color: #6b7280;
            margin: 0;
        }
        
        .selection-display {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 12px;
            margin: 24px 0;
        }
        
        .selection-item {
            text-align: center;
        }
        
        .color-box {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 12px;
            margin-bottom: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .position-number {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .interpretation-section {
            margin-top: 32px;
        }
        
        .interpretation-card {
            padding: 24px;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border-radius: 16px;
            border: 2px solid #e5e7eb;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }
        
        .interpretation-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .interpretation-card h4 {
            margin: 0 0 16px 0;
            font-size: 20px;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .color-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            margin-right: 12px;
            margin-bottom: 8px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 32px 0 24px 0;
            padding-bottom: 12px;
            border-bottom: 3px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <?php
    $homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
    $displayName = !empty($_SESSION['teacher_user'])
        ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
        : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
    $logoutUrl = !empty($_SESSION['teacher_user']) ? '/teachers/logout' : '/hemis/logout';
    ?>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">Bosh sahifa</a></li>
                <?php if (!empty($_SESSION['hemis_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">
                            👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                <?php endif; ?>
                <?php if (!empty($_SESSION['teacher_user']) || !empty($_SESSION['hemis_user'])): ?>
                    <li><a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>">Chiqish</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="results-container">
            <div class="result-card">
                <div class="layout-header">
                    <h1>🎨 Lyusher Testi Natijalari</h1>
                    <p class="tagline">Sizning psixologik holatingiz va shaxsiyat xususiyatlaringiz</p>
                </div>

                <!-- Первый раунд -->
                <div class="interpretation-section">
                    <h3 class="section-title">Birinchi bosqich - Ranglarni yoqtirish tartibi</h3>
                    <div class="selection-display">
                        <?php 
                        $round1 = $results['round1_selection'] ?? [];
                        foreach ($round1 as $position => $colorId): 
                            $color = $colors[$colorId] ?? null;
                            if ($color):
                        ?>
                            <div class="selection-item">
                                <div class="position-number"><?= $position + 1 ?></div>
                                <div class="color-box" style="background: <?= htmlspecialchars($color['code'], ENT_QUOTES, 'UTF-8') ?>;">
                                    <?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Второй раунд -->
                <div class="interpretation-section">
                    <h3 class="section-title">Ikkinchi bosqich - Ranglarni yoqtirmaslik tartibi</h3>
                    <div class="selection-display">
                        <?php 
                        $round2 = $results['round2_selection'] ?? [];
                        foreach ($round2 as $position => $colorId): 
                            $color = $colors[$colorId] ?? null;
                            if ($color):
                        ?>
                            <div class="selection-item">
                                <div class="position-number"><?= $position + 1 ?></div>
                                <div class="color-box" style="background: <?= htmlspecialchars($color['code'], ENT_QUOTES, 'UTF-8') ?>;">
                                    <?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Предпочитаемые цвета -->
            <?php if (!empty($results['preferred'])): ?>
                <div class="result-card">
                    <h3 class="section-title">Yoqtirilgan ranglar (Asosiy xususiyatlar)</h3>
                    <?php foreach ($results['preferred'] as $item): ?>
                        <div class="interpretation-card">
                            <h4>
                                <span class="color-badge" style="background: <?= htmlspecialchars($item['color']['code'], ENT_QUOTES, 'UTF-8') ?>; color: white;">
                                    <?= htmlspecialchars($item['color']['name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <?= htmlspecialchars($item['position_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (Pozitsiya <?= $item['position'] ?>)
                            </h4>
                            <p style="color: #374151; line-height: 1.8; margin: 0;">
                                <?= htmlspecialchars($item['interpretation'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php if (!empty($item['position_desc'])): ?>
                                <p style="color: #6b7280; font-size: 14px; margin: 12px 0 0 0; font-style: italic;">
                                    <?= htmlspecialchars($item['position_desc'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Нейтральные цвета -->
            <?php if (!empty($results['neutral'])): ?>
                <div class="result-card">
                    <h3 class="section-title">Neytral ranglar</h3>
                    <?php foreach ($results['neutral'] as $item): ?>
                        <div class="interpretation-card">
                            <h4>
                                <span class="color-badge" style="background: <?= htmlspecialchars($item['color']['code'], ENT_QUOTES, 'UTF-8') ?>; color: white;">
                                    <?= htmlspecialchars($item['color']['name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <?= htmlspecialchars($item['position_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (Pozitsiya <?= $item['position'] ?>)
                            </h4>
                            <p style="color: #374151; line-height: 1.8; margin: 0;">
                                <?= htmlspecialchars($item['interpretation'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Отвергаемые цвета и стресс-факторы -->
            <?php if (!empty($results['rejected'])): ?>
                <div class="result-card">
                    <h3 class="section-title">Rad etilgan ranglar va stress omillari</h3>
                    <?php foreach ($results['rejected'] as $item): ?>
                        <div class="interpretation-card" style="<?= $item['is_stress'] ? 'border-color: #ef4444; background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);' : '' ?>">
                            <h4>
                                <span class="color-badge" style="background: <?= htmlspecialchars($item['color']['code'], ENT_QUOTES, 'UTF-8') ?>; color: white;">
                                    <?= htmlspecialchars($item['color']['name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <?= htmlspecialchars($item['position_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (Pozitsiya <?= $item['position'] ?>)
                                <?php if ($item['is_stress']): ?>
                                    <span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-left: 8px;">STRESS</span>
                                <?php endif; ?>
                            </h4>
                            <p style="color: #374151; line-height: 1.8; margin: 0;">
                                <?= htmlspecialchars($item['interpretation'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 32px;">
                <a href="/" class="btn btn-large" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(30, 64, 175, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 64, 175, 0.3)';">🏠 Bosh sahifaga qaytish</a>
            </div>
        </div>
    </main>

    <footer class="main-footer" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%); color: rgba(255, 255, 255, 0.9); padding: 40px 0; margin-top: 64px; border-top: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);">
        <div class="footer-container" style="max-width: 1400px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 24px; text-align: center;">
            <p class="footer-text" style="margin: 0; font-size: 15px; font-weight: 500; color: rgba(255, 255, 255, 0.85); letter-spacing: 0.3px;">TerDPI talabalar psixologik xizmati &copy; <?= date('Y') ?></p>
        </div>
    </footer>
</body>
</html>

