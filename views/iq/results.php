<?php
/**
 * Страница результатов IQ теста
 */
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termiz davlat pedagogika instituti</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Профессиональный фон как в dashboard */
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
        
        /* Навигация */
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
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            margin-bottom: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
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
            <div class="layout-header">
                <h1>Test natijalari</h1>
                <p class="tagline">Sizning IQ ko'rsatkichingiz</p>
            </div>

            <!-- Основной результат -->
            <div class="result-card fade-in" style="text-align: center; padding: 48px 32px;">
                <div style="font-size: 80px; margin-bottom: 24px;">
                    🧠
                </div>
                <h2 style="font-size: 48px; margin-bottom: 16px; color: var(--primary-color); font-weight: 800;">
                    IQ: <?= $results['iq_score'] ?>
                </h2>
                <h3 style="font-size: 28px; margin-bottom: 16px; color: var(--text-primary); font-weight: 700;">
                    <?= htmlspecialchars($results['category']['name'], ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <p style="font-size: 18px; color: var(--text-secondary); line-height: 1.8; max-width: 600px; margin: 0 auto;">
                    <?= htmlspecialchars($results['category']['description'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <!-- Детальные баллы -->
            <div class="result-card">
                <h3 style="font-size: 24px; margin-bottom: 24px; color: var(--text-primary);">Batafsil ko'rsatkichlar</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-top: 24px;">
                    <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: var(--radius-md);">
                        <div style="font-size: 32px; margin-bottom: 8px;">✅</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            To'g'ri javoblar
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: var(--primary-color);">
                            <?= $results['correct_answers'] ?> / <?= $results['total_questions'] ?>
                        </div>
                        <div style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">
                            <?= $results['percentage'] ?>%
                        </div>
                    </div>

                    <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: var(--radius-md);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📊</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            IQ Ball
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: #ef4444;">
                            <?= $results['iq_score'] ?>
                        </div>
                        <div style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">
                            <?= htmlspecialchars($results['category']['name'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: var(--radius-md);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📈</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                            Foiz
                        </div>
                        <div style="font-size: 32px; font-weight: 700; color: #10b981;">
                            <?= $results['percentage'] ?>%
                        </div>
                        <div style="font-size: 14px; color: var(--text-secondary); margin-top: 4px;">
                            To'g'ri javoblar
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация о тесте -->
            <div class="result-card">
                <h3 style="font-size: 24px; margin-bottom: 16px; color: var(--text-primary);">IQ haqida qo'shimcha ma'lumot</h3>
                <div style="line-height: 1.8; color: var(--text-secondary); font-size: 16px;">
                    <p style="margin-bottom: 16px;">
                        IQ (Intellektual Quotient) - bu intellektual qobiliyatlarni baholash ko'rsatkichi. 
                        O'rtacha IQ 100 ballga teng. Ko'pchilik odamlar (taxminan 68%) 85-115 ball oralig'ida bo'ladi.
                    </p>
                    <p style="margin-bottom: 16px;">
                        <strong>Eslatma:</strong> Bu test natijasi faqat bir ko'rsatkichdir va boshqa omillar ham muhimdir. 
                        IQ balli intellektning barcha jihatlarini to'liq aks ettirmaydi.
                    </p>
                    <p>
                        Test yakunlangan vaqti: <strong><?= htmlspecialchars($results['completed_at'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 32px;">
                <a href="/dashboard" class="btn btn-large" style="font-size: 18px; padding: 18px 40px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; border: none; border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(30, 58, 138, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">🏠 Bosh sahifaga qaytish</a>
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

