<?php
/**
 * Страница вопроса IQ теста
 */
$progress = round(($currentQuestion / $totalQuestions) * 100);
$homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$displayName = !empty($_SESSION['teacher_user'])
    ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
    : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
$isTeacher = !empty($_SESSION['teacher_user']);
$primaryColor = $isTeacher ? '#10b981' : '#6366f1';
$primaryGradient = $isTeacher ? 'linear-gradient(135deg, #047857 0%, #10b981 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)';
$primaryLight = $isTeacher ? '#ecfdf5' : '#eef2ff';
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQ Testi - Savol <?= $currentQuestion ?></title>
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
        
        .nav-info {
            display: flex;
            align-items: center;
            gap: 16px;
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
        
        .nav-exit {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .nav-exit:hover {
            background: #fef2f2;
            color: #ef4444;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        
        .progress-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .progress-title {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
        }
        
        .progress-count {
            font-size: 14px;
            font-weight: 700;
            color: <?= $primaryColor ?>;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: <?= $primaryGradient ?>;
            border-radius: 10px;
            transition: width 0.4s ease;
        }
        
        .question-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        
        .question-header {
            background: <?= $primaryGradient ?>;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .question-number {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: white;
        }
        
        .question-meta {
            color: white;
        }
        
        .question-meta h2 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        
        .question-meta p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .question-body {
            padding: 32px;
        }
        
        .question-text {
            font-size: 20px;
            font-weight: 600;
            line-height: 1.6;
            color: #0f172a;
            margin-bottom: 32px;
        }
        
        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }
        
        .option {
            display: flex;
            align-items: center;
            padding: 18px 24px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .option:hover {
            background: <?= $primaryLight ?>;
            border-color: <?= $primaryColor ?>;
        }
        
        .option input[type="radio"] {
            display: none;
        }
        
        .option-radio {
            width: 22px;
            height: 22px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-right: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        
        .option-radio::after {
            content: '';
            width: 10px;
            height: 10px;
            background: <?= $primaryColor ?>;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s;
        }
        
        .option input[type="radio"]:checked + .option-radio {
            border-color: <?= $primaryColor ?>;
        }
        
        .option input[type="radio"]:checked + .option-radio::after {
            opacity: 1;
            transform: scale(1);
        }
        
        .option:has(input[type="radio"]:checked) {
            background: <?= $primaryLight ?>;
            border-color: <?= $primaryColor ?>;
        }
        
        .option-letter {
            width: 28px;
            height: 28px;
            background: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .option:has(input[type="radio"]:checked) .option-letter {
            background: <?= $primaryColor ?>;
            color: white;
        }
        
        .option-text {
            font-size: 15px;
            font-weight: 500;
            color: #334155;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }
        
        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }
        
        .btn-primary {
            background: <?= $primaryGradient ?>;
            color: white;
            box-shadow: 0 4px 14px <?= $isTeacher ? 'rgba(16,185,129,0.35)' : 'rgba(99,102,241,0.35)' ?>;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px <?= $isTeacher ? 'rgba(16,185,129,0.45)' : 'rgba(99,102,241,0.45)' ?>;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
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
            .question-header { padding: 20px 24px; }
            .question-body { padding: 24px; }
            .question-text { font-size: 18px; }
            .nav-buttons { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-brand">
            <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TerDPI">
            <span>Termiz davlat pedagogika instituti</span>
        </a>
        
        <div class="nav-info">
            <div class="nav-user">
                🧩 IQ testi
            </div>
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-exit">
                ✕ Chiqish
            </a>
        </div>
    </nav>

    <main class="container">
        <div class="progress-section">
            <div class="progress-header">
                <span class="progress-title">Test jarayoni</span>
                <span class="progress-count"><?= $currentQuestion ?> / <?= $totalQuestions ?></span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress ?>%;"></div>
            </div>
        </div>

        <div class="question-card">
            <div class="question-header">
                <div class="question-number"><?= $currentQuestion ?></div>
                <div class="question-meta">
                    <h2>Savol</h2>
                    <p><?= $progress ?>% bajarildi</p>
                </div>
            </div>
            
            <div class="question-body">
                <form method="POST" action="/iq/answer" id="answerForm">
                    <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                    
                    <p class="question-text">
                        <?= htmlspecialchars($question['text'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="options">
                        <?php 
                        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                        foreach ($question['options'] as $index => $option): 
                        ?>
                            <label class="option">
                                <input type="radio" name="answer" value="<?= $index ?>" required
                                    <?= ($currentAnswer === $index) ? 'checked' : '' ?>
                                    onchange="document.getElementById('answerForm').submit();">
                                <span class="option-radio"></span>
                                <span class="option-letter"><?= $letters[$index] ?? ($index + 1) ?></span>
                                <span class="option-text"><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="nav-buttons">
                        <?php if ($currentQuestion > 1): ?>
                            <a href="/iq/question?q=<?= $currentQuestion - 1 ?>" class="btn btn-secondary">
                                ← Oldingi
                            </a>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">
                            Keyingisi →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        TerDPI talabalar psixologik xizmati © <?= date('Y') ?>
    </footer>
</body>
</html>
