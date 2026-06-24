<?php
/**
 * Страница выбора цветов для теста Ranglar metodikasi
 */
$colors = $test['colors'] ?? [];
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranglar Metodikasi - Rang tanlash</title>
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
        
        .test-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .colors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .color-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .color-box {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
            border: 3px solid transparent;
        }
        
        .color-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }
        
        .color-box.selected {
            border-color: #059669;
            transform: scale(1.1);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.2);
        }
        
        .color-box.rejected {
            opacity: 0.5;
            border-color: #dc2626;
        }
        
        .color-name {
            text-align: center;
            margin-top: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        
        .btn-submit {
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
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
            margin-top: 32px;
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .instruction {
            background: #f0fdf4;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #10b981;
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
        <div class="test-container">
            <div class="card">
                <h1 class="section-title">🎨 Ranglar Metodikasi</h1>
                
                <form method="post" action="/ranglar/select" id="color-form">
                    <div class="instruction">
                        <p><strong>1. O'zingizga eng yoqadigan rangni tanlang:</strong></p>
                        <p>Quyidagi ranglardan bittasini tanlang.</p>
                    </div>
                    
                    <div class="colors-grid" id="preferred-section">
                        <?php foreach ($colors as $color): ?>
                            <div class="color-item">
                                <div class="color-box" 
                                     data-color-id="<?= $color['id'] ?>"
                                     data-color-name="<?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?>"
                                     style="background-color: <?= htmlspecialchars($color['code'], ENT_QUOTES, 'UTF-8') ?>"
                                     onclick="selectPreferred(<?= $color['id'] ?>)">
                                </div>
                                <div class="color-name"><?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="hidden" name="preferred" id="preferred-color" required>
                    
                    <div class="instruction" style="margin-top: 40px;">
                        <p><strong>2. Qaysi ranglarni yoqtirmasligingizni belgilang (ixtiyoriy):</strong></p>
                        <p>Bir yoki bir nechta rangni tanlang.</p>
                    </div>
                    
                    <div class="colors-grid" id="rejected-section">
                        <?php foreach ($colors as $color): ?>
                            <div class="color-item">
                                <div class="color-box rejected" 
                                     data-color-id="<?= $color['id'] ?>"
                                     data-color-name="<?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?>"
                                     style="background-color: <?= htmlspecialchars($color['code'], ENT_QUOTES, 'UTF-8') ?>"
                                     onclick="toggleRejected(<?= $color['id'] ?>)">
                                </div>
                                <div class="color-name"><?= htmlspecialchars($color['name'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submit-btn" disabled>
                        Natijalarni ko'rish →
                    </button>
                </form>
            </div>
        </div>
    </main>
    
    <script>
        let preferredColorId = null;
        let rejectedColorIds = new Set();
        
        function selectPreferred(colorId) {
            preferredColorId = colorId;
            document.getElementById('preferred-color').value = colorId;
            
            // Remove selection from all
            document.querySelectorAll('#preferred-section .color-box').forEach(box => {
                box.classList.remove('selected');
            });
            
            // Add selection to clicked
            event.target.closest('.color-box').classList.add('selected');
            
            updateSubmitButton();
        }
        
        function toggleRejected(colorId) {
            const box = event.target.closest('.color-box');
            if (rejectedColorIds.has(colorId)) {
                rejectedColorIds.delete(colorId);
                box.classList.remove('rejected');
                box.style.opacity = '1';
            } else {
                rejectedColorIds.add(colorId);
                box.classList.add('rejected');
                box.style.opacity = '0.5';
            }
            
            updateSubmitButton();
        }
        
        function updateSubmitButton() {
            const btn = document.getElementById('submit-btn');
            if (preferredColorId !== null) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }
        
        document.getElementById('color-form').addEventListener('submit', function(e) {
            // Add rejected colors to form
            rejectedColorIds.forEach(colorId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rejected[]';
                input.value = colorId;
                this.appendChild(input);
            });
        });
    </script>
</body>
</html>

