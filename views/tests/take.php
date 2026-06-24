<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termiz davlat pedagogika instituti</title>
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
        
        /* Навигация для страницы теста */
        .main-nav {
            position: relative;
            z-index: 1000;
            padding: 16px 24px;
            margin: 0;
        }
        
        .nav-container {
            background: rgba(30, 58, 138, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 16px 24px;
            max-width: 1400px;
            margin: 16px auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
        
        .test-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.95) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.8);
        }
        
        .test-header h1 {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
        }
        
        .test-header p {
            color: #64748b;
            font-size: 17px;
            margin: 0;
            line-height: 1.6;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.95) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.8);
        }
        
        .progress-text {
            font-size: 15px;
            color: #475569;
            margin-bottom: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .progress-fill {
            height: 10px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            border-radius: 10px;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .question-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.95) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            margin-bottom: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.8);
            display: none;
        }
        
        .question-card.active {
            display: block;
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px) scale(0.98); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }
        
        .question-number {
            font-size: 13px;
            color: #2563eb;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            padding: 6px 12px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 8px;
        }
        
        .question-text {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 32px;
            line-height: 1.6;
            letter-spacing: -0.3px;
        }
        
        .question-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .option-label {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .option-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .option-label:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-color: #cbd5e1;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .option-label:hover::before {
            left: 100%;
        }
        
        .option-label input {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: #2563eb;
        }
        
        .option-label input:checked + .option-text {
            color: #1e3a8a;
            font-weight: 700;
        }
        
        .option-label:has(input:checked) {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #2563eb;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2), inset 0 0 0 1px rgba(37, 99, 235, 0.1);
            transform: translateX(4px);
        }
        
        .option-text {
            flex: 1;
            font-size: 17px;
            color: #334155;
            line-height: 1.5;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.95) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.8);
        }
        
        .nav-btn {
            padding: 16px 36px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 140px;
        }
        
        .btn-prev {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #475569;
            border: 2px solid #e2e8f0;
        }
        
        .btn-prev:hover:not(:disabled) {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            border-color: #cbd5e1;
        }
        
        .btn-prev:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-next, .btn-submit {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }
        
        .btn-next:hover, .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.5);
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        }
        
        .btn-next:active, .btn-submit:active {
            transform: translateY(0);
        }
        
        .other-input-container {
            margin-left: 44px;
            margin-top: 8px;
        }
        
        .other-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #2563eb;
            border-radius: 8px;
            font-size: 15px;
        }
        
        textarea.answer-input {
            width: 100%;
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 16px;
            font-family: inherit;
            resize: vertical;
            min-height: 140px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1.6;
        }
        
        textarea.answer-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1), 0 4px 12px rgba(37, 99, 235, 0.15);
            background: #ffffff;
        }
        
        select.answer-select {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23475569' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 48px;
        }
        
        select.answer-select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1), 0 4px 12px rgba(37, 99, 235, 0.15);
            background-color: #ffffff;
        }
        
        select.answer-select:hover {
            border-color: #cbd5e1;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                padding: 12px 16px;
                margin: 12px auto;
            }
            
            .logo-text {
                font-size: 14px;
            }
            
            .nav-menu a {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .test-container {
                padding: 0 16px;
                margin: 20px auto;
            }
            
            .test-header {
                padding: 28px 24px;
                border-radius: 20px;
            }
            
            .test-header h1 {
                font-size: 28px;
            }
            
            .test-header p {
                font-size: 15px;
            }
            
            .progress-bar {
                padding: 20px 24px;
                border-radius: 16px;
            }
            
            .question-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
            
            .question-number {
                font-size: 12px;
                padding: 5px 10px;
            }
            
            .question-text {
                font-size: 20px;
                margin-bottom: 28px;
            }
            
            .option-label {
                padding: 18px 20px;
                gap: 14px;
            }
            
            .option-text {
                font-size: 16px;
            }
            
            .navigation-buttons {
                flex-direction: column;
                padding: 20px 24px;
                gap: 12px;
            }
            
            .nav-btn {
                width: 100%;
                padding: 16px 32px;
            }
            
            .main-footer {
                padding: 32px 0;
                margin-top: 40px;
            }
            
            .footer-container {
                padding: 0 16px;
                gap: 16px;
            }
            
            .footer-text, .footer-links a {
                font-size: 14px;
            }
            
            textarea.answer-input {
                padding: 16px;
                font-size: 15px;
                min-height: 120px;
            }
            
            select.answer-select {
                padding: 16px 18px;
                font-size: 15px;
                padding-right: 44px;
            }
            
            .other-input-container {
                margin-left: 0;
                margin-top: 12px;
            }
            
            .other-input {
                padding: 12px 14px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php
    $homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : '/dashboard';
    ?>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">Shaxsiy kabinet</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="test-container">
            <div class="test-header">
            <h1><?= htmlspecialchars($test['title'] ?? 'Psixologik test', ENT_QUOTES, 'UTF-8') ?></h1>
                <p><?= htmlspecialchars($test['description'] ?? 'Talabalar uchun psixologik so\'rovnoma.', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

            <div class="progress-bar">
                <div class="progress-text">Savol <span id="current-question">1</span> / <span id="total-questions"><?= count($test['questions'] ?? []) ?></span></div>
                <div class="progress-fill" id="progress-fill" style="width: <?= (1 / count($test['questions'] ?? [1])) * 100 ?>%"></div>
                            </div>

            <form method="post" action="/tests/take" id="test-form">
                <input type="hidden" name="test_id" value="<?= htmlspecialchars((string)($test['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                <?php foreach ($test['questions'] as $i => $q): ?>
                    <div class="question-card <?= $i === 0 ? 'active' : '' ?>" data-question-index="<?= $i ?>">
                        <div class="question-number">Savol <?= $i + 1 ?></div>
                        <div class="question-text"><?= htmlspecialchars($q['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>

                            <?php if (($q['type'] ?? 'text') === 'multiple_choice' && !empty($q['options'])): ?>
                            <div class="question-options">
                                    <?php foreach ($q['options'] as $optIdx => $option): ?>
                                        <?php $isOther = !empty($option['is_other'] ?? false); ?>
                                    <label class="option-label">
                                        <input type="radio" name="answers[<?= $i ?>]" value="<?= htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') ?>" class="answer-input" required>
                                        <span class="option-text"><?= htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </label>
                                        <?php if ($isOther): ?>
                                        <div class="other-input-container" style="display: none;">
                                            <input type="text" name="answers[<?= $i ?>]_other" placeholder="Boshqa javobingizni kiriting" class="other-input">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (($q['type'] ?? 'text') === 'multiple_select' && !empty($q['options'])): ?>
                            <div style="margin-bottom: 12px; color: #64748b; font-style: italic;">
                                    (Bir nechta variant tanlash mumkin)
                                </div>
                            <div class="question-options">
                                    <?php foreach ($q['options'] as $optIdx => $option): ?>
                                        <?php $isOther = !empty($option['is_other'] ?? false); ?>
                                    <label class="option-label">
                                        <input type="checkbox" name="answers[<?= $i ?>][]" value="<?= htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') ?>" class="answer-input" <?= $optIdx === 0 ? 'required' : '' ?>>
                                        <span class="option-text"><?= htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </label>
                                        <?php if ($isOther): ?>
                                        <div class="other-input-container" style="display: none;">
                                            <input type="text" name="answers[<?= $i ?>]_other" placeholder="Boshqa javobingizni kiriting" class="other-input">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (($q['type'] ?? 'text') === 'scale'): ?>
                            <div style="margin-bottom: 12px; color: #64748b;">
                                    1 dan (men uchun umuman mos kelmaydi) 5 gacha (menga to'liq mos keladi) shkalada baholang.
                                </div>
                            <select name="answers[<?= $i ?>]" required class="answer-select">
                                    <option value="">Variantni tanlang</option>
                                    <?php for ($v = 1; $v <= 5; $v++): ?>
                                        <option value="<?= $v ?>"><?= $v ?></option>
                                    <?php endfor; ?>
                                </select>
                            <?php else: ?>
                            <textarea name="answers[<?= $i ?>]" rows="5" placeholder="Sizning javobingiz" required class="answer-input"></textarea>
                            <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                <div class="navigation-buttons">
                    <button type="button" class="nav-btn btn-prev" id="prev-btn" disabled>Oldingi</button>
                    <button type="button" class="nav-btn btn-next" id="next-btn">Keyingi</button>
                    <button type="submit" class="nav-btn btn-submit" id="submit-btn" style="display: none;">Yakunlash</button>
                </div>
            </form>
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

    <script>
        const questions = document.querySelectorAll('.question-card');
        const totalQuestions = questions.length;
        let currentQuestionIndex = 0;

        function updateProgress() {
            const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;
            document.getElementById('progress-fill').style.width = progress + '%';
            document.getElementById('current-question').textContent = currentQuestionIndex + 1;
        }

        function showQuestion(index) {
            questions.forEach((q, i) => {
                q.classList.toggle('active', i === index);
            });
            
            currentQuestionIndex = index;
            updateProgress();
            
            // Обновляем кнопки навигации
            document.getElementById('prev-btn').disabled = index === 0;
            
            if (index === totalQuestions - 1) {
                document.getElementById('next-btn').style.display = 'none';
                document.getElementById('submit-btn').style.display = 'block';
            } else {
                document.getElementById('next-btn').style.display = 'block';
                document.getElementById('submit-btn').style.display = 'none';
            }
        }

        function validateAllQuestions() {
            let allValid = true;
            const firstInvalidQuestion = [];

            questions.forEach((questionCard, index) => {
                const questionIndex = questionCard.dataset.questionIndex;
                let questionValid = false;

                // Проверяем radio buttons (multiple_choice)
                const radioButtons = questionCard.querySelectorAll('input[type="radio"]');
                if (radioButtons.length > 0) {
                    const checkedRadio = Array.from(radioButtons).find(rb => rb.checked);
                    if (checkedRadio) {
                        questionValid = true;
                        // Если выбрано "Boshqa", проверяем, что текстовое поле заполнено
                        if (checkedRadio.value === 'Boshqa') {
                            const otherInput = questionCard.querySelector('input[name*="_other"]');
                            if (otherInput && !otherInput.value.trim()) {
                                questionValid = false;
                            }
                        }
                    }
                }
                // Проверяем checkboxes (multiple_select)
                else {
                    const checkboxes = questionCard.querySelectorAll('input[type="checkbox"]');
                    if (checkboxes.length > 0) {
                        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
                        if (checkedBoxes.length > 0) {
                            questionValid = true;
                            // Проверяем, если выбрано "Boshqa", что текстовое поле заполнено
                            const hasBoshqa = checkedBoxes.some(cb => cb.value === 'Boshqa');
                            if (hasBoshqa) {
                                const otherInput = questionCard.querySelector('input[name*="_other"]');
                                if (otherInput && !otherInput.value.trim()) {
                                    questionValid = false;
                                }
                            }
                        }
                    }
                    // Проверяем select (scale)
                    else {
                        const select = questionCard.querySelector('select');
                        if (select) {
                            questionValid = select.value !== '';
                        }
                        // Проверяем textarea
                        else {
                            const textarea = questionCard.querySelector('textarea');
                            if (textarea) {
                                questionValid = textarea.value.trim() !== '';
                            }
                        }
                    }
                }

                if (!questionValid) {
                    allValid = false;
                    if (firstInvalidQuestion.length === 0) {
                        firstInvalidQuestion.push(index);
                    }
                }
            });

            if (!allValid && firstInvalidQuestion.length > 0) {
                // Переходим к первому неотвеченному вопросу
                showQuestion(firstInvalidQuestion[0]);
                alert('Iltimos, barcha savollarga javob bering!');
                return false;
            }

            return allValid;
        }

        document.getElementById('next-btn').addEventListener('click', function(e) {
            e.preventDefault();
            // Проверяем текущий вопрос перед переходом к следующему
            const currentQuestion = questions[currentQuestionIndex];
            const form = document.querySelector('form');
            
            // Проверяем валидность текущего вопроса
            if (!currentQuestion.querySelector('form') && !form.checkValidity()) {
                // Если текущий вопрос не отвечен, показываем ошибку
                const radioButtons = currentQuestion.querySelectorAll('input[type="radio"]');
                const checkboxes = currentQuestion.querySelectorAll('input[type="checkbox"]');
                const select = currentQuestion.querySelector('select');
                const textarea = currentQuestion.querySelector('textarea');
                
                let hasAnswer = false;
                if (radioButtons.length > 0) {
                    hasAnswer = Array.from(radioButtons).some(rb => rb.checked);
                } else if (checkboxes.length > 0) {
                    hasAnswer = Array.from(checkboxes).some(cb => cb.checked);
                } else if (select) {
                    hasAnswer = select.value !== '';
                } else if (textarea) {
                    hasAnswer = textarea.value.trim() !== '';
                }
                
                if (!hasAnswer) {
                    alert('Iltimos, ushbu savolga javob bering!');
                    return;
                }
            }
            
            if (currentQuestionIndex < totalQuestions - 1) {
                showQuestion(currentQuestionIndex + 1);
            } else {
                // Если это последний вопрос, проверяем все вопросы и отправляем форму
                if (validateAllQuestions()) {
                    form.submit();
                }
            }
        });

        document.getElementById('prev-btn').addEventListener('click', function() {
            if (currentQuestionIndex > 0) {
                showQuestion(currentQuestionIndex - 1);
            }
        });

        // Обработка "Boshqa" опций
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionCard = this.closest('.question-card');
                const otherContainer = questionCard.querySelector('.other-input-container');
                if (otherContainer) {
                    otherContainer.style.display = this.value === 'Boshqa' ? 'block' : 'none';
                }
            });
        });

        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const questionCard = this.closest('.question-card');
                const otherContainer = questionCard.querySelector('.other-input-container');
                if (otherContainer) {
                    const boshqaCheckbox = Array.from(questionCard.querySelectorAll('input[type="checkbox"]'))
                        .find(cb => cb.value === 'Boshqa');
                    otherContainer.style.display = (boshqaCheckbox && boshqaCheckbox.checked) ? 'block' : 'none';
                }
            });
        });

        // Инициализация
        updateProgress();
        showQuestion(0); // Инициализируем отображение первого вопроса
        
        // Если вопрос только один, сразу показываем кнопку "Yakunlash" вместо "Keyingi"
        if (totalQuestions === 1) {
            document.getElementById('next-btn').style.display = 'none';
            document.getElementById('submit-btn').style.display = 'block';
        }
        
        // Убеждаемся, что кнопка submit тоже работает
        document.getElementById('submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            // Проверяем все вопросы перед отправкой
            if (validateAllQuestions()) {
            const form = document.querySelector('form');
                form.submit();
            }
        });

        // Также добавляем валидацию при отправке формы напрямую
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateAllQuestions()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
