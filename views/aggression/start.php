<?php
/**
 * Tajovuz holati tashxisi (Buss-Darki) - Boshlash sahifasi
 */
$homeUrl       = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$displayName   = !empty($_SESSION['teacher_user'])
    ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
    : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
$logoutUrl     = !empty($_SESSION['teacher_user']) ? '/teachers/logout' : '/hemis/logout';
$isTeacher     = !empty($_SESSION['teacher_user']);
$scales        = $test['scales'] ?? [];
$questionCount = count($test['questions'] ?? []);
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tajovuz Holati Tashxisi – TerDPI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);min-height:100vh;color:#fff}

        /* navbar */
        .navbar{background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.08);padding:14px 40px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
        .nav-brand{display:flex;align-items:center;gap:12px;text-decoration:none}
        .nav-brand img{width:36px;height:36px;border-radius:10px}
        .nav-brand span{font-weight:700;font-size:15px;color:#fff}
        .nav-links{display:flex;align-items:center;gap:20px}
        .nav-links a{color:rgba(255,255,255,0.7);text-decoration:none;font-size:14px;font-weight:500;transition:color .2s}
        .nav-links a:hover{color:#fff}
        .nav-user{display:flex;align-items:center;gap:8px;padding:7px 14px;background:rgba(255,255,255,0.1);border-radius:20px;font-size:13px;color:rgba(255,255,255,0.9)}

        /* hero */
        .hero{padding:60px 24px 40px;text-align:center;max-width:760px;margin:0 auto}
        .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:30px;padding:8px 18px;font-size:13px;color:#fca5a5;margin-bottom:24px;font-weight:500}
        .hero-badge svg{width:16px;height:16px}
        .hero-title{font-size:42px;font-weight:900;line-height:1.1;margin-bottom:10px;background:linear-gradient(135deg,#fff 0%,#fca5a5 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .hero-sub{font-size:17px;color:rgba(255,255,255,0.6);margin-bottom:16px}
        .hero-meta{display:flex;align-items:center;justify-content:center;gap:24px;margin-bottom:0}
        .hero-meta span{display:flex;align-items:center;gap:6px;font-size:14px;color:rgba(255,255,255,0.5)}
        .hero-meta svg{width:16px;height:16px;stroke:rgba(255,255,255,0.4)}

        /* main card */
        .main{max-width:900px;margin:0 auto;padding:0 24px 60px}

        /* scales grid */
        .section-label{font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:16px;margin-top:40px}
        .scales-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:8px}
        .scale-chip{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:12px;transition:all .25s}
        .scale-chip:hover{background:rgba(255,255,255,0.09);transform:translateY(-2px)}
        .scale-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
        .scale-name{font-size:14px;font-weight:500;color:rgba(255,255,255,0.85)}

        /* instructions */
        .instr-card{background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:20px;padding:28px 32px;margin-top:32px}
        .instr-title{font-size:15px;font-weight:700;color:#fbbf24;margin-bottom:14px;display:flex;align-items:center;gap:8px}
        .instr-title svg{width:18px;height:18px}
        .instr-text{font-size:15px;color:rgba(255,255,255,0.7);line-height:1.75}
        .answer-opts{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px}
        .answer-opt{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:30px;font-size:13px;font-weight:600}
        .opt-yes{background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#6ee7b7}
        .opt-maybe-yes{background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.18);color:#a7f3d0}
        .opt-maybe-no{background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);color:#fca5a5}
        .opt-no{background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#f87171}

        /* stats */
        .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:32px}
        .stat-box{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:22px;text-align:center}
        .stat-val{font-size:32px;font-weight:800;color:#ef4444}
        .stat-lbl{font-size:13px;color:rgba(255,255,255,0.45);margin-top:4px}

        /* start btn */
        .btn-start{display:flex;align-items:center;justify-content:center;gap:12px;width:100%;padding:20px 32px;background:linear-gradient(135deg,#dc2626 0%,#ef4444 100%);color:#fff;border:none;border-radius:16px;font-size:18px;font-weight:700;font-family:inherit;cursor:pointer;text-decoration:none;transition:all .3s;box-shadow:0 8px 32px rgba(239,68,68,0.35);margin-top:32px}
        .btn-start:hover{transform:translateY(-3px);box-shadow:0 12px 40px rgba(239,68,68,0.5)}
        .btn-start svg{width:22px;height:22px}

        .footer{text-align:center;padding:30px 0;color:rgba(255,255,255,0.2);font-size:13px}

        @media(max-width:640px){
            .hero-title{font-size:28px}
            .navbar{padding:12px 16px}
            .hero{padding:40px 16px 24px}
            .stats-row{grid-template-columns:1fr;gap:10px}
            .scales-grid{grid-template-columns:1fr 1fr}
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-brand">
        <img src="/images/logo.png" alt="TerDPI">
        <span>TerDPI</span>
    </a>
    <div class="nav-links">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">← Orqaga</a>
        <div class="nav-user">👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></div>
        <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" style="color:#f87171">Chiqish</a>
    </div>
</nav>

<div class="hero">
    <div class="hero-badge">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Basss-Darki diagnostik metodikasi
    </div>
    <h1 class="hero-title">Tajovuz Holati Tashxisi</h1>
    <p class="hero-sub">Shaxsning tajovuzkorlik va dushmanlik indekslarini aniqlash</p>
    <div class="hero-meta">
        <span>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <?= $questionCount ?> ta savol
        </span>
        <span>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            ~20 daqiqa
        </span>
        <span>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            8 shkala
        </span>
    </div>
</div>

<div class="main">
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-val"><?= $questionCount ?></div>
            <div class="stat-lbl">Savollar soni</div>
        </div>
        <div class="stat-box">
            <div class="stat-val">8</div>
            <div class="stat-lbl">Tajovuz shkalasi</div>
        </div>
        <div class="stat-box">
            <div class="stat-val">2</div>
            <div class="stat-lbl">Integral ko'rsatkich</div>
        </div>
    </div>

    <div class="section-label">O'lchanadigan shkalalar</div>
    <div class="scales-grid">
        <?php foreach ($scales as $scale): ?>
        <div class="scale-chip">
            <div class="scale-dot" style="background:<?= htmlspecialchars($scale['color'], ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="scale-name"><?= htmlspecialchars($scale['name'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="instr-card">
        <div class="instr-title">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ko'rsatmalar
        </div>
        <p class="instr-text">
            Quyidagi hukmlarni o'qib, ularni <strong style="color:#fff">o'z xulq-atvoringiz</strong> bilan solishtiring va unga mos yoki mos emasligini bildirish uchun 4 ta javob variantidan birini tanlang:
        </p>
        <div class="answer-opts">
            <span class="answer-opt opt-yes">✓ Ha</span>
            <span class="answer-opt opt-maybe-yes">~ Ha shekilli</span>
            <span class="answer-opt opt-maybe-no">~ Yo'q shekilli</span>
            <span class="answer-opt opt-no">✕ Yo'q</span>
        </div>
        <p class="instr-text" style="margin-top:14px;font-size:13px;color:rgba(255,255,255,0.45)">
            «Ha» va «Ha shekilli» — «HA» javobiga; «Yo'q» va «Yo'q shekilli» — «YO'Q» javobiga teng hisoblanadi.
        </p>
    </div>

    <a href="/aggression/question?q=1" class="btn-start">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Testni boshlash
    </a>
</div>

<footer class="footer">TerDPI Psixologik xizmat © <?= date('Y') ?></footer>
</body>
</html>
