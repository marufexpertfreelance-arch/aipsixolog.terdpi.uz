<?php
/**
 * Первый раунд выбора цветов (предпочтения)
 */
$homeUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$isTeacher = !empty($_SESSION['teacher_user']);
$primaryColor = $isTeacher ? '#10b981' : '#6366f1';
$primaryGradient = $isTeacher ? 'linear-gradient(135deg, #047857 0%, #10b981 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)';
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lüscher Testi - 1-bosqich</title>
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
        
        .nav-badge {
            padding: 8px 16px;
            background: #fce7f3;
            color: #be185d;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
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
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        
        .test-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        
        .test-header {
            background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);
            padding: 32px;
            text-align: center;
            color: white;
        }
        
        .test-header h1 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        
        .test-header p {
            font-size: 15px;
            opacity: 0.9;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }
        
        .step {
            width: 40px;
            height: 6px;
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .step.active {
            background: white;
        }
        
        .test-body {
            padding: 32px;
        }
        
        .instruction {
            background: #fdf2f8;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: center;
        }
        
        .instruction p {
            color: #9d174d;
            font-size: 15px;
            font-weight: 500;
        }
        
        .colors-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .color-item {
            aspect-ratio: 1;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s;
            border: 4px solid transparent;
            position: relative;
        }
        
        .color-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .color-item.selected {
            opacity: 0.3;
            transform: scale(0.9);
            pointer-events: none;
        }
        
        .color-item .order {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 28px;
            height: 28px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            opacity: 0;
        }
        
        .color-item.selected .order {
            opacity: 1;
        }
        
        .selected-colors {
            margin-bottom: 32px;
        }
        
        .selected-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 12px;
        }
        
        .selected-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .selected-dot {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .selected-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
        }
        
        .btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 24px;
        }
        
        .btn-reset:hover {
            background: #e2e8f0;
        }
        
        .btn-submit {
            width: 100%;
            padding: 18px 32px;
            background: <?= $primaryGradient ?>;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px <?= $isTeacher ? 'rgba(16,185,129,0.35)' : 'rgba(99,102,241,0.35)' ?>;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px <?= $isTeacher ? 'rgba(16,185,129,0.45)' : 'rgba(99,102,241,0.45)' ?>;
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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
            .colors-grid { grid-template-columns: repeat(4, 1fr); gap: 10px; }
            .test-body { padding: 24px; }
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
            <div class="nav-badge">🎨 Lüscher testi</div>
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-exit">✕ Chiqish</a>
        </div>
    </nav>

    <main class="container">
        <div class="test-card">
            <div class="test-header">
                <h1>1-bosqich: Ranglarni tanlang</h1>
                <p>Sizga eng yoqadigan rangdan boshlang</p>
                <div class="step-indicator">
                    <div class="step active"></div>
                    <div class="step"></div>
                </div>
            </div>
            
            <div class="test-body">
                <div class="instruction">
                    <p>🎯 Ranglarni sizga eng yoqadigan tartibda tanlang (1-chi = eng yoqadi)</p>
                </div>
                
                <form method="POST" action="/lusher/round1" id="colorForm">
                    <div class="colors-grid" id="colorsGrid">
                        <?php 
                        $colors = [
                            ['id' => 'gray', 'hex' => '#6b7280'],
                            ['id' => 'blue', 'hex' => '#3b82f6'],
                            ['id' => 'green', 'hex' => '#22c55e'],
                            ['id' => 'red', 'hex' => '#ef4444'],
                            ['id' => 'yellow', 'hex' => '#eab308'],
                            ['id' => 'violet', 'hex' => '#a855f7'],
                            ['id' => 'brown', 'hex' => '#a16207'],
                            ['id' => 'black', 'hex' => '#1f2937'],
                        ];
                        foreach ($colors as $index => $color): 
                        ?>
                        <div class="color-item" 
                             style="background: <?= $color['hex'] ?>;" 
                             data-color="<?= $color['id'] ?>"
                             onclick="selectColor(this, '<?= $color['id'] ?>')">
                            <span class="order"></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="selected-colors">
                        <div class="selected-label">Tanlangan tartib:</div>
                        <div class="selected-row" id="selectedRow">
                            <?php for ($i = 0; $i < 8; $i++): ?>
                            <div class="selected-placeholder"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-reset" onclick="resetSelection()">
                        🔄 Qaytadan boshlash
                    </button>
                    
                    <input type="hidden" name="colors" id="colorsInput" value="">
                    
                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
                        Keyingi bosqichga o'tish →
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        TerDPI talabalar psixologik xizmati © <?= date('Y') ?>
    </footer>

    <script>
        let selectedColors = [];
        const colorHex = {
            'gray': '#6b7280',
            'blue': '#3b82f6',
            'green': '#22c55e',
            'red': '#ef4444',
            'yellow': '#eab308',
            'violet': '#a855f7',
            'brown': '#a16207',
            'black': '#1f2937'
        };
        
        function selectColor(element, colorId) {
            if (selectedColors.includes(colorId)) return;
            
            selectedColors.push(colorId);
            element.classList.add('selected');
            element.querySelector('.order').textContent = selectedColors.length;
            
            updateSelectedRow();
            updateSubmitButton();
        }
        
        function updateSelectedRow() {
            const row = document.getElementById('selectedRow');
            row.innerHTML = '';
            
            for (let i = 0; i < 8; i++) {
                if (selectedColors[i]) {
                    const dot = document.createElement('div');
                    dot.className = 'selected-dot';
                    dot.style.background = colorHex[selectedColors[i]];
                    row.appendChild(dot);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'selected-placeholder';
                    row.appendChild(placeholder);
                }
            }
        }
        
        function updateSubmitButton() {
            const btn = document.getElementById('submitBtn');
            const input = document.getElementById('colorsInput');
            
            if (selectedColors.length === 8) {
                btn.disabled = false;
                input.value = selectedColors.join(',');
            } else {
                btn.disabled = true;
            }
        }
        
        function resetSelection() {
            selectedColors = [];
            document.querySelectorAll('.color-item').forEach(item => {
                item.classList.remove('selected');
                item.querySelector('.order').textContent = '';
            });
            updateSelectedRow();
            updateSubmitButton();
        }
    </script>
</body>
</html>
