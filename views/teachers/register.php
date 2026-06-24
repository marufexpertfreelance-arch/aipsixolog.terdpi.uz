<?php
$title = $title ?? 'O\'qituvchi sifatida ro\'yxatdan o\'tish';
$error = $error ?? null;
$success = $success ?? null;
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Встроенный SVG favicon - символ психологии Ψ -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'><stop offset='0%25' style='stop-color:%238b5cf6'/><stop offset='100%25' style='stop-color:%234f46e5'/></linearGradient></defs><circle cx='50' cy='50' r='48' fill='url(%23g)'/><text x='50' y='70' font-size='60' text-anchor='middle' fill='white' font-family='serif'>Ψ</text></svg>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body.teacher-page {
            background: linear-gradient(135deg, #10b981 0%, #059669 25%, #047857 50%, #065f46 75%, #064e3b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        body.teacher-page .main-content {
            background: transparent;
            max-width: 100%;
            padding: 0;
        }
        
        body.teacher-page .nav-container {
            max-width: 100%;
            border-radius: 0;
            margin: 0;
            padding: 20px 40px;
        }
    </style>
</head>
<body class="teacher-page">
    <?php
    $logoUrl = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : '/';
    $homeUrl = '/';
    ?>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TERDPI" class="logo-img">
                <span class="logo-text">Termiz davlat pedagogika instituti</span>
            </a>
            <ul class="nav-menu">
                <li><a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">Bosh sahifa</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <!-- Улучшенный заголовок -->
        <div style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%); border-radius: 0; padding: 50px 40px; margin-bottom: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2; max-width: 1400px; margin: 0 auto;">
                <h1 style="font-size: 48px; font-weight: 800; background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0 0 16px 0; letter-spacing: -1px;">
                    👨‍🏫 O'qituvchi sifatida ro'yxatdan o'tish
                </h1>
                <p style="font-size: 20px; color: #6b7280; margin: 0; font-weight: 400;">
                    Tizimga kirish uchun ro'yxatdan o'ting
                </p>
            </div>
            <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(5,150,105,0.1) 100%); border-radius: 50%; z-index: 1;"></div>
            <div style="position: absolute; bottom: -80px; left: -80px; width: 300px; height: 300px; background: linear-gradient(135deg, rgba(5,150,105,0.08) 0%, rgba(16,185,129,0.08) 100%); border-radius: 50%; z-index: 1;"></div>
        </div>

        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); padding: 40px;">
            <div style="max-width: 600px; margin: 0 auto;">

            <?php if (!empty($error)): ?>
                <div style="padding: 16px 20px; background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; margin-bottom: 24px;">
                    <strong style="color: #dc2626;">⚠️</strong>
                    <span style="color: #991b1b; margin-left: 8px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="/teachers/register">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                        To'liq F.I.SH *
                    </label>
                    <input type="text" name="full_name" required
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                        Login *
                    </label>
                    <input type="text" name="login" required
                           value="<?= htmlspecialchars($_POST['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Email</label>
                    <input type="text" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Telefon</label>
                    <input type="tel" name="phone"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Kafedra</label>
                    <input type="text" name="department"
                           value="<?= htmlspecialchars($_POST['department'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                        Parol *
                    </label>
                    <input type="password" name="password" required
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 32px;"></div>

                <button type="submit" style="width: 100%; padding: 16px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                    Ro'yxatdan o'tish
                </button>
            </form>

            <div style="margin-top: 24px; text-align: center;">
                <p style="color: #6b7280; font-size: 13px;">
                    Allaqachon akkountingiz bormi? 
                    <a href="/teachers/login" style="color: #10b981; text-decoration: none; font-weight: 600;">Kirish</a>
                </p>
            </div>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 30px 20px; color: rgba(255,255,255,0.9); margin-top: 0;">
        <p style="margin: 0; font-weight: 500;">TerDPI talabalar psixologik xizmati © <?= date('Y') ?></p>
    </footer>
</body>
</html>

