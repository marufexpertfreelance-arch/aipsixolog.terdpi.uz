<?php
$title = $title ?? 'O\'qituvchi uchun kirish';
$error = $error ?? null;
$success = $success ?? null;
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f8fafc;
        }
        
        /* Left Side - Branding */
        .brand-side {
            flex: 1;
            background: linear-gradient(135deg, #047857 0%, #059669 25%, #10b981 50%, #34d399 75%, #047857 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }<?php
$title = $title ?? 'O\'qituvchi uchun kirish';
$error = $error ?? null;
$success = $success ?? null;
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f8fafc;
        }
        
        /* Left Side - Branding */
        .brand-side {
            flex: 1;
            background: linear-gradient(135deg, #047857 0%, #059669 25%, #10b981 50%, #34d399 75%, #047857 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .brand-side::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .brand-side::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        
        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 400px;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            padding: 12px;
            margin: 0 auto 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .brand-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        
        .brand-subtitle {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 48px;
        }
        
        .brand-features {
            text-align: left;
        }
        
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .brand-feature:last-child {
            border-bottom: none;
        }
        
        .brand-feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .brand-feature-text {
            font-size: 14px;
            opacity: 0.95;
        }
        
        /* Right Side - Login Form */
        .login-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            background: white;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            margin-bottom: 40px;
        }
        
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            color: #047857;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .login-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .login-desc {
            color: #64748b;
            font-size: 15px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            background: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        /* Buttons */
        .btn {
            width: 100%;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(5,150,105,0.35);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5,150,105,0.45);
        }
        
        .btn-hemis {
            background: white;
            color: #0f172a;
            border: 2px solid #e5e7eb;
            margin-top: 12px;
        }
        
        .btn-hemis:hover {
            border-color: #10b981;
            background: #ecfdf5;
        }
        
        .btn-hemis img {
            width: 20px;
            height: 20px;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        /* Footer Link */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
        }
        
        .login-footer p {
            color: #64748b;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Back to Home */
        .back-home {
            position: absolute;
            top: 32px;
            left: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .back-home:hover {
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            body {
                flex-direction: column;
            }
            
            .brand-side {
                padding: 40px 24px;
                min-height: auto;
            }
            
            .brand-features {
                display: none;
            }
            
            .brand-subtitle {
                margin-bottom: 0;
            }
            
            .login-side {
                padding: 40px 24px;
            }
            
            .back-home {
                top: 16px;
                left: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Left Side - Branding -->
    <div class="brand-side">
        <a href="/" class="back-home">
            ← Bosh sahifa
        </a>
        
        <div class="brand-content">
            <div class="brand-logo">
                <img src="/images/logo.png" alt="TerDPI">
            </div>
            
            <h1 class="brand-title">Termiz davlat pedagogika instituti</h1>
            <p class="brand-subtitle">O'qituvchilar uchun psixologik xizmat platformasi</p>
            
            <div class="brand-features">
                <div class="brand-feature">
                    <div class="brand-feature-icon">🎭</div>
                    <div class="brand-feature-text">Temperament va shaxsiyat testlari</div>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">🧠</div>
                    <div class="brand-feature-text">IQ va intellektual qobiliyatlar</div>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">📊</div>
                    <div class="brand-feature-text">Batafsil natijalar va tahlil</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-side">
        <div class="login-container">
            <div class="login-header">
                <div class="login-badge">
                    👨‍🏫 O'qituvchi paneli
                </div>
                <h2 class="login-title">Xush kelibsiz!</h2>
                <p class="login-desc">Shaxsiy kabinetingizga kirish uchun ma'lumotlaringizni kiriting</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/teachers/login">
                <div class="form-group">
                    <label class="form-label">Login</label>
                    <input type="text" name="login" class="form-input" placeholder="Loginingizni kiriting" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Parol</label>
                    <input type="password" name="password" class="form-input" placeholder="Parolingizni kiriting" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Kirish
                </button>
            </form>

            <div class="divider">yoki</div>

            <a href="/teachers/hemis/login" class="btn btn-hemis">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                HEMIS orqali kirish
            </a>

            <div class="login-footer">
                <p>Hali ro'yxatdan o'tmaganmisiz? <a href="/teachers/register">Ro'yxatdan o'tish</a></p>
            </div>
        </div>
    </div>
</body>
</html>

        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .brand-side::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .brand-side::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        
        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 400px;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            padding: 12px;
            margin: 0 auto 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .brand-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        
        .brand-subtitle {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 48px;
        }
        
        .brand-features {
            text-align: left;
        }
        
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .brand-feature:last-child {
            border-bottom: none;
        }
        
        .brand-feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .brand-feature-text {
            font-size: 14px;
            opacity: 0.95;
        }
        
        /* Right Side - Login Form */
        .login-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            background: white;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            margin-bottom: 40px;
        }
        
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            color: #047857;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .login-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .login-desc {
            color: #64748b;
            font-size: 15px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            background: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        /* Buttons */
        .btn {
            width: 100%;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 14px rgba(5,150,105,0.35);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5,150,105,0.45);
        }
        
        .btn-hemis {
            background: white;
            color: #0f172a;
            border: 2px solid #e5e7eb;
            margin-top: 12px;
        }
        
        .btn-hemis:hover {
            border-color: #10b981;
            background: #ecfdf5;
        }
        
        .btn-hemis img {
            width: 20px;
            height: 20px;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        /* Footer Link */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
        }
        
        .login-footer p {
            color: #64748b;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Back to Home */
        .back-home {
            position: absolute;
            top: 32px;
            left: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .back-home:hover {
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            body {
                flex-direction: column;
            }
            
            .brand-side {
                padding: 40px 24px;
                min-height: auto;
            }
            
            .brand-features {
                display: none;
            }
            
            .brand-subtitle {
                margin-bottom: 0;
            }
            
            .login-side {
                padding: 40px 24px;
            }
            
            .back-home {
                top: 16px;
                left: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Left Side - Branding -->
    <div class="brand-side">
        <a href="/" class="back-home">
            ← Bosh sahifa
        </a>
        
        <div class="brand-content">
            <div class="brand-logo">
                <img src="https://terdpi.uz/images/xterdpi.png.pagespeed.ic.LOw8Pat0Gb.webp" alt="TerDPI">
            </div>
            
            <h1 class="brand-title">Termiz davlat pedagogika instituti</h1>
            <p class="brand-subtitle">O'qituvchilar uchun psixologik xizmat platformasi</p>
            
            <div class="brand-features">
                <div class="brand-feature">
                    <div class="brand-feature-icon">🎭</div>
                    <div class="brand-feature-text">Temperament va shaxsiyat testlari</div>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">🧠</div>
                    <div class="brand-feature-text">IQ va intellektual qobiliyatlar</div>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">📊</div>
                    <div class="brand-feature-text">Batafsil natijalar va tahlil</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-side">
        <div class="login-container">
            <div class="login-header">
                <div class="login-badge">
                    👨‍🏫 O'qituvchi paneli
                </div>
                <h2 class="login-title">Xush kelibsiz!</h2>
                <p class="login-desc">Shaxsiy kabinetingizga kirish uchun ma'lumotlaringizni kiriting</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/teachers/login">
                <div class="form-group">
                    <label class="form-label">Login</label>
                    <input type="text" name="login" class="form-input" placeholder="Loginingizni kiriting" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Parol</label>
                    <input type="password" name="password" class="form-input" placeholder="Parolingizni kiriting" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Kirish
                </button>
            </form>

            <div class="divider">yoki</div>

            <a href="/teachers/hemis/login" class="btn btn-hemis">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                HEMIS orqali kirish
            </a>

            <div class="login-footer">
                <p>Hali ro'yxatdan o'tmaganmisiz? <a href="/teachers/register">Ro'yxatdan o'tish</a></p>
            </div>
        </div>
    </div>
</body>
</html>
