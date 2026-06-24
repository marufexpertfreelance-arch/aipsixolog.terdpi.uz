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
        
        /* Навигация на всю ширину */
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
        
        /* Мобильное меню */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            margin-left: auto;
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(30, 58, 138, 0.98);
            z-index: 1000;
            padding: 20px;
            overflow-y: auto;
        }
        
        .mobile-menu.active {
            display: block;
        }
        
        .mobile-menu-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 32px;
            cursor: pointer;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .mobile-menu-nav {
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .mobile-menu-nav a {
            display: block;
            padding: 16px 20px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
        }
        
        .mobile-menu-nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Медиа-запросы для мобильных */
        @media (max-width: 768px) {
            /* Навигация */
            .nav-container {
                padding: 12px 16px !important;
                flex-wrap: wrap;
            }
            
            .logo-text {
                font-size: 14px !important;
            }
            
            .nav-menu {
                display: none !important;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            /* Заголовок */
            .header-section {
                padding: 30px 20px !important;
            }
            
            .header-section h1 {
                font-size: 28px !important;
            }
            
            .header-section p {
                font-size: 16px !important;
            }
            
            .container-max-width {
                padding: 0 16px !important;
                margin: 20px auto !important;
            }
            
            .card {
                padding: 24px 20px !important;
            }
            
            /* Карточка */
            .card {
                padding: 20px 16px !important;
            }
            
            .card h2 {
                font-size: 20px !important;
            }
            
            .card h3 {
                font-size: 18px !important;
                margin-top: 24px !important;
                margin-bottom: 12px !important;
            }
            
            /* Результаты */
            .results-container h3 {
                font-size: 18px !important;
                margin-bottom: 16px !important;
            }
            
            .results-container h4 {
                font-size: 16px !important;
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }
            
            .score-display {
                font-size: 24px !important;
            }
            
            .category-display {
                font-size: 16px !important;
            }
            
            .results-container {
                padding: 16px !important;
                margin-bottom: 24px !important;
            }
            
            .scale-result-card {
                padding: 16px !important;
                margin-bottom: 16px !important;
            }
            
            .score-box {
                padding: 12px !important;
                margin-bottom: 12px !important;
            }
            
            .category-box {
                padding: 12px !important;
            }
            
            /* Вопросы */
            .question-item {
                padding: 16px !important;
                margin-bottom: 20px !important;
            }
            
            .question-item > div:first-child {
                font-size: 14px !important;
                margin-bottom: 10px !important;
            }
            
            .answer-box {
                padding: 10px !important;
                margin-top: 10px !important;
            }
            
            .answer-box > div:first-child {
                font-size: 12px !important;
                margin-bottom: 6px !important;
            }
            
            .answer-box > div:last-child {
                font-size: 14px !important;
            }
            
            /* Кнопки */
            .btn,
            .btn-secondary,
            .btn-large {
                padding: 12px 20px !important;
                font-size: 14px !important;
                min-height: 44px;
                width: 100%;
                text-align: center;
            }
            
            /* Информация о дате */
            .date-info {
                padding: 12px !important;
                font-size: 13px !important;
            }
            
            /* Заголовок с кнопкой */
            .card > div:first-child {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
            }
            
            .card > div:first-child .btn-secondary {
                width: 100%;
            }
            
            /* Результаты */
            .results-container {
                padding: 20px !important;
            }
            
            .results-container h3 {
                font-size: 20px !important;
            }
            
            .scale-result-card {
                padding: 20px !important;
            }
            
            .scale-result-card h4 {
                font-size: 18px !important;
            }
            
            .score-display {
                font-size: 32px !important;
            }
            
            .category-display {
                font-size: 20px !important;
            }
        }
        
        @media (max-width: 480px) {
            .header-section h1 {
                font-size: 24px !important;
            }
            
            .score-display {
                font-size: 28px !important;
            }
            
            .category-display {
                font-size: 18px !important;
            }
            
            .results-container {
                padding: 16px !important;
            }
        }
    </style>
</head>
<body>
    <?php
    $homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : '/dashboard';
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
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">☰</button>
            <ul class="nav-menu">
                <li><a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">Shaxsiy kabinet</a></li>
                <li><a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">Bosh sahifa</a></li>
                <?php if (!empty($_SESSION['hemis_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                <?php endif; ?>
                <li><a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>">Chiqish</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Мобильное меню -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-menu-close" onclick="toggleMobileMenu()" aria-label="Close menu">×</button>
        <nav class="mobile-menu-nav">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" onclick="toggleMobileMenu()">Shaxsiy kabinet</a>
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" onclick="toggleMobileMenu()">Bosh sahifa</a>
            <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" onclick="toggleMobileMenu()">Chiqish</a>
        </nav>
    </div>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
    </script>

    <main class="main-content">
        <!-- Улучшенный заголовок -->
        <div class="header-section" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%); border-radius: 0; padding: 50px 40px; margin-bottom: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2; max-width: 1400px; margin: 0 auto; text-align: center;">
                <h1 style="font-size: 48px; font-weight: 800; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0 0 16px 0; letter-spacing: -1px;">
                    <?= htmlspecialchars($test['title'] ?? 'Test natijalari', ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p style="font-size: 20px; color: #4b5563; margin: 0; font-weight: 400;">
                    Sizning test natijalaringiz va javoblaringiz.
                </p>
            </div>
            <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: linear-gradient(135deg, rgba(30,58,138,0.1) 0%, rgba(37,99,235,0.1) 100%); border-radius: 50%; z-index: 1;"></div>
            <div style="position: absolute; bottom: -80px; left: -80px; width: 300px; height: 300px; background: linear-gradient(135deg, rgba(37,99,235,0.08) 0%, rgba(30,58,138,0.08) 100%); border-radius: 50%; z-index: 1;"></div>
        </div>

        <div class="container container-max-width" style="max-width: 1400px; margin: 40px auto; padding: 0 20px;">
            <div class="card" style="border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); padding: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e5e7eb;">
                <h2 style="margin: 0; font-size: 32px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 12px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="stroke: #2563eb;">
                        <path d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Test natijalari
                </h2>
                <a href="/dashboard" class="btn-secondary" style="padding: 12px 24px; background: #f3f4f6; color: #4b5563; border: 2px solid #e5e7eb; border-radius: 12px; font-weight: 600; font-size: 15px; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#1f2937'; this.style.borderColor='#d1d5db';" onmouseout="this.style.background='#f3f4f6'; this.style.color='#4b5563'; this.style.borderColor='#e5e7eb';">Orqaga</a>
            </div>

            <?php
            $submittedAt = $result['submitted_at'] ?? $result['completed_at'] ?? '';
            $dateStr = $submittedAt ? date('d.m.Y H:i', strtotime($submittedAt)) : '';
            ?>
            
            <?php if ($dateStr): ?>
                <div class="date-info" style="padding: 20px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; margin-bottom: 32px; border-left: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <p style="margin: 0; color: #475569; font-size: 15px;">
                        <strong style="color: #1e293b;">Yakunlangan vaqti:</strong> <span style="color: #2563eb; font-weight: 600;"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></span>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Расчетные результаты и интерпретация -->
            <?php
            $calculatedScore = $result['calculated_score'] ?? null;
            $interpretation = $result['interpretation'] ?? null;
            $scales = $result['scales'] ?? null;
            
            // Проверяем, есть ли результаты (обычные или multi_scale)
            $hasResults = ($calculatedScore !== null || $interpretation !== null) || ($scales !== null && !empty($scales));
            
            if ($hasResults):
            ?>
                <div class="results-container" style="padding: 32px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 20px; margin-bottom: 40px; border: 2px solid #3b82f6; box-shadow: 0 8px 24px rgba(59, 130, 246, 0.2); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%); border-radius: 50%;"></div>
                    <h3 style="margin: 0 0 24px 0; font-size: 28px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 14px; position: relative; z-index: 2;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="stroke: #2563eb; stroke-width: 2;">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Test natijalari
                    </h3>
                    
                    <?php if ($scales !== null && !empty($scales)): ?>
                        <!-- Результаты по нескольким шкалам -->
                        <?php foreach ($scales as $scaleName => $scaleResult): ?>
                            <div class="scale-result-card" style="margin-bottom: 28px; padding: 28px; background: white; border-radius: 16px; border: 2px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 6px 20px rgba(59, 130, 246, 0.15)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
                                <h4 style="margin: 0 0 20px 0; font-size: 24px; font-weight: 800; color: #1e293b; padding-bottom: 16px; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 4px; height: 24px; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); border-radius: 2px;"></span>
                                    <?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?>
                                </h4>
                                
                                <?php if (isset($scaleResult['score'])): ?>
                                    <div class="score-box" style="margin-bottom: 20px; padding: 24px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 12px; border: 2px solid #bfdbfe;">
                                        <div style="font-size: 14px; color: #64748b; margin-bottom: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Hisoblangan ball:
                                        </div>
                                        <div class="score-display" style="font-size: 42px; font-weight: 900; color: #2563eb; line-height: 1;">
                                            <?= htmlspecialchars((string)$scaleResult['score'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php 
                                $scaleInterpretation = $scaleResult['interpretation'] ?? null;
                                if ($scaleInterpretation && isset($scaleInterpretation['category'])): 
                                ?>
                                    <div class="category-box" style="padding: 24px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 16px; border: 2px solid #93c5fd; position: relative; z-index: 2;">
                                        <div style="font-size: 13px; color: #64748b; margin-bottom: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Kategoriya:
                                        </div>
                                        <div class="category-display" style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 16px;">
                                            <?= htmlspecialchars($scaleInterpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <?php if (!empty($scaleInterpretation['description'])): ?>
                                            <div style="padding-top: 16px; border-top: 2px solid #bfdbfe; font-size: 16px; color: #475569; line-height: 1.7;">
                                                <?= nl2br(htmlspecialchars($scaleInterpretation['description'], ENT_QUOTES, 'UTF-8')) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Обычные результаты (одна шкала) -->
                        <?php if ($calculatedScore !== null): ?>
                            <div class="score-box" style="margin-bottom: 24px; padding: 28px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 16px; border: 2px solid #bfdbfe; position: relative; z-index: 2;">
                                <div style="font-size: 14px; color: #64748b; margin-bottom: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Hisoblangan ball:
                                </div>
                                <div class="score-display" style="font-size: 48px; font-weight: 900; color: #2563eb; line-height: 1;">
                                    <?= htmlspecialchars((string)$calculatedScore, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($interpretation && isset($interpretation['category'])): ?>
                            <div class="category-box" style="padding: 28px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 16px; border: 2px solid #93c5fd; position: relative; z-index: 2;">
                                <div style="font-size: 13px; color: #64748b; margin-bottom: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Kategoriya:
                                </div>
                                <div class="category-display" style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 16px;">
                                    <?= htmlspecialchars($interpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if (!empty($interpretation['description'])): ?>
                                    <div style="padding-top: 16px; border-top: 2px solid #bfdbfe; font-size: 16px; color: #475569; line-height: 1.7;">
                                        <?= nl2br(htmlspecialchars($interpretation['description'], ENT_QUOTES, 'UTF-8')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h3 style="margin-top: 32px; margin-bottom: 16px;">Savollar va javoblar</h3>
            
            <?php if (empty($test['questions'])): ?>
                <p class="muted">Testda savollar topilmadi.</p>
            <?php else: ?>
                <ol style="list-style: decimal; padding-left: 24px; margin: 0;">
                    <?php 
                    $answers = $result['answers'] ?? [];
                    foreach ($test['questions'] as $i => $q): 
                    ?>
                        <li class="question-item" style="margin-bottom: 32px; padding: 20px; background: #f9fafb; border-radius: var(--radius-md);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <div style="font-weight: 600; font-size: 16px; flex: 1;">
                                    <?= htmlspecialchars($q['text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>

                            <?php
                            // Получаем ответ студента по индексу вопроса
                            $studentAnswer = '';
                            if (isset($answers[$i])) {
                                $studentAnswer = is_string($answers[$i]) ? $answers[$i] : (is_array($answers[$i]) ? implode(', ', $answers[$i]) : '');
                            }
                            $questionType = $q['type'] ?? 'text';
                            ?>

                            <div class="answer-box" style="margin-top: 12px; padding: 12px; background: white; border-radius: 8px; border: 2px solid #667eea;">
                                <div style="font-size: 13px; color: #667eea; margin-bottom: 8px; font-weight: 600;">
                                    Sizning javobingiz:
                                </div>
                                <div style="font-size: 15px; color: #111827;">
                                    <?php if ($questionType === 'multiple_select'): ?>
                                        <?php if (!empty($studentAnswer)): ?>
                                            <?php
                                            // Для multiple_select ответы разделены запятой
                                            $answersList = explode(', ', $studentAnswer);
                                            foreach ($answersList as $answerItem) {
                                                $trimmed = trim($answerItem);
                                                if (!empty($trimmed)) {
                                                    echo '<div style="padding: 8px 12px; background: #f0f9ff; border-radius: 6px; margin-bottom: 4px; border-left: 3px solid #667eea;">';
                                                    echo '✓ ' . htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8');
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Javob berilmagan</span>
                                        <?php endif; ?>
                                    <?php elseif ($questionType === 'multiple_choice'): ?>
                                        <?php if (!empty($studentAnswer)): ?>
                                            <div style="padding: 8px 12px; background: #f0f9ff; border-radius: 6px; border-left: 3px solid #667eea;">
                                                ✓ <?= htmlspecialchars($studentAnswer, ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Javob berilmagan</span>
                                        <?php endif; ?>
                                    <?php elseif ($questionType === 'scale'): ?>
                                        <?php if (!empty($studentAnswer)): ?>
                                            <strong style="font-size: 20px; color: #667eea;"><?= htmlspecialchars($studentAnswer, ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span style="color: var(--text-secondary); margin-left: 8px;">/ 5</span>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Javob berilmagan</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (!empty($studentAnswer)): ?>
                                            <div style="padding: 12px; background: #f9fafb; border-radius: 6px; white-space: pre-wrap;">
                                                <?= nl2br(htmlspecialchars($studentAnswer, ENT_QUOTES, 'UTF-8')) ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Javob berilmagan</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <div style="margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="/dashboard" class="btn btn-large" style="padding: 14px 28px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(30, 58, 138, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">Shaxsiy kabinetga qaytish</a>
            </div>
            </div>
        </div>
    </main>

    <footer class="main-footer" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%); color: rgba(255, 255, 255, 0.9); padding: 40px 0; margin-top: 64px; border-top: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);">
        <div class="footer-container" style="max-width: 1400px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 24px; text-align: center;">
            <p class="footer-text" style="margin: 0; font-size: 15px; font-weight: 500; color: rgba(255, 255, 255, 0.85); letter-spacing: 0.3px;">TerDPI talabalar psixologik xizmati &copy; <?= date('Y') ?></p>
            <p class="footer-links" style="margin: 0;">
                <a href="https://student.terdpi.uz" target="_blank" rel="noopener" style="color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 15px; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.color='#ffffff'; this.style.textDecoration='underline';" onmouseout="this.style.color='rgba(255, 255, 255, 0.85)'; this.style.textDecoration='none';">HEMIS</a>
            </p>
        </div>
    </footer>
</body>
</html>
