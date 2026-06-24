<?php
/**
 * Tajovuz holati tashxisi - Natijalar sahifasi
 */
$homeUrl     = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$displayName = !empty($_SESSION['teacher_user'])
    ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
    : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
$logoutUrl   = !empty($_SESSION['teacher_user']) ? '/teachers/logout' : '/hemis/logout';

$scaleScores    = $results['scale_scores'] ?? [];
$ti             = $results['ti'] ?? 0;
$di             = $results['di'] ?? 0;
$tiNorm         = $results['ti_norm'] ?? ['min' => 17, 'max' => 25];
$diNorm         = $results['di_norm'] ?? ['min' => 3.5, 'max' => 9.5];
$tiLevel        = $results['ti_level'] ?? 'me\'yor';
$diLevel        = $results['di_level'] ?? 'me\'yor';
$interpretation = $results['interpretation'] ?? [];
$studentName    = $results['student_name'] ?? $displayName;
$completedAt    = $results['completed_at'] ?? '';

$levelColors = [
    'past'     => ['bg' => 'rgba(16,185,129,0.12)', 'border' => 'rgba(16,185,129,0.3)', 'text' => '#6ee7b7', 'label' => 'Past'],
    "me'yor"   => ['bg' => 'rgba(59,130,246,0.12)', 'border' => 'rgba(59,130,246,0.3)', 'text' => '#93c5fd', 'label' => "Me'yor"],
    'yuqori'   => ['bg' => 'rgba(239,68,68,0.12)',  'border' => 'rgba(239,68,68,0.3)',  'text' => '#fca5a5', 'label' => 'Yuqori'],
];

$tiColor = $levelColors[$tiLevel] ?? $levelColors["me'yor"];
$diColor = $levelColors[$diLevel] ?? $levelColors["me'yor"];

$scales = $test['scales'] ?? [];
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test natijalari – Tajovuz testi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f0c29 0%,#302b63 55%,#24243e 100%);min-height:100vh;color:#fff}

        .navbar{background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.08);padding:12px 32px;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
        .nav-brand img{width:32px;height:32px;border-radius:8px}
        .nav-brand span{font-weight:700;font-size:14px;color:rgba(255,255,255,0.8)}
        .nav-info{display:flex;align-items:center;gap:16px}
        .nav-user{font-size:13px;color:rgba(255,255,255,0.5)}
        .nav-exit{font-size:13px;color:#f87171;text-decoration:none;font-weight:500}

        main{max-width:900px;margin:0 auto;padding:40px 24px 70px}

        /* Hero result */
        .result-hero{text-align:center;padding:40px 0 32px}
        .result-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:30px;padding:8px 18px;font-size:13px;color:#fca5a5;margin-bottom:20px;font-weight:600}
        .result-name{font-size:28px;font-weight:800;margin-bottom:6px}
        .result-date{font-size:13px;color:rgba(255,255,255,0.35)}

        /* Integral indexes */
        .indexes-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px}
        .index-card{border-radius:24px;padding:32px 28px;text-align:center;transition:transform .25s}
        .index-card:hover{transform:translateY(-4px)}
        .index-abbr{font-size:13px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px;opacity:.7}
        .index-val{font-size:52px;font-weight:900;line-height:1;margin-bottom:8px}
        .index-name{font-size:15px;font-weight:600;margin-bottom:12px}
        .index-norm{font-size:12px;opacity:.55}
        .index-level{display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-top:10px}

        /* Scales */
        .section-title{font-size:18px;font-weight:700;margin-bottom:18px;display:flex;align-items:center;gap:10px}
        .section-title svg{width:20px;height:20px;stroke:rgba(255,255,255,0.4)}

        .scale-list{display:flex;flex-direction:column;gap:14px;margin-bottom:36px}
        .scale-row{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:18px;padding:20px 24px;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:18px;transition:all .25s}
        .scale-row:hover{background:rgba(255,255,255,0.08)}
        .scale-left{display:flex;align-items:center;gap:14px}
        .scale-num{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0}
        .scale-info{}
        .scale-name{font-size:15px;font-weight:600;color:#fff;margin-bottom:3px}
        .scale-k{font-size:12px;color:rgba(255,255,255,0.35)}
        .scale-bar-wrap{flex:1;min-width:100px}
        .scale-bar-bg{height:8px;background:rgba(255,255,255,0.08);border-radius:8px;overflow:hidden}
        .scale-bar-fill{height:100%;border-radius:8px;transition:width .6s ease}
        .scale-right{text-align:right;min-width:60px}
        .scale-score{font-size:22px;font-weight:800}
        .scale-max{font-size:12px;color:rgba(255,255,255,0.35)}
        .scale-lbl{display:inline-block;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;margin-top:4px}

        /* Interpretation */
        .interp-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:36px}
        .interp-card{border-radius:18px;padding:20px 22px}
        .interp-name{font-size:13px;font-weight:600;margin-bottom:6px}
        .interp-level{font-size:22px;font-weight:800}
        .interp-icon{font-size:28px;margin-bottom:10px}

        /* Actions */
        .actions{display:flex;gap:14px;flex-wrap:wrap}
        .btn-home{display:inline-flex;align-items:center;gap:8px;padding:14px 24px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:14px;color:rgba(255,255,255,0.8);text-decoration:none;font-size:15px;font-weight:600;transition:all .25s}
        .btn-home:hover{background:rgba(255,255,255,0.12);color:#fff}
        .btn-retry{display:inline-flex;align-items:center;gap:8px;padding:14px 24px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;text-decoration:none;border-radius:14px;font-size:15px;font-weight:600;box-shadow:0 4px 20px rgba(239,68,68,0.3);transition:all .25s}
        .btn-retry:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(239,68,68,0.45)}

        @media(max-width:640px){
            .indexes-row,.interp-grid{grid-template-columns:1fr}
            .scale-row{grid-template-columns:auto 1fr;gap:12px}
            .scale-bar-wrap{display:none}
            main{padding:24px 16px 50px}
            .navbar{padding:10px 16px}
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-brand">
        <img src="/images/logo.png" alt="TerDPI">
        <span>Tajovuz testi – Natijalar</span>
    </a>
    <div class="nav-info">
        <span class="nav-user">👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></span>
        <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-exit">Chiqish</a>
    </div>
</nav>

<main>
    <div class="result-hero">
        <div class="result-badge">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            Test muvaffaqiyatli yakunlandi
        </div>
        <div class="result-name">👤 <?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?></div>
        <?php if ($completedAt): ?>
        <div class="result-date"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($completedAt)), ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>

    <!-- Integral indexes -->
    <div class="indexes-row">
        <div class="index-card" style="background:<?= htmlspecialchars($tiColor['bg'], ENT_QUOTES, 'UTF-8') ?>;border:1px solid <?= htmlspecialchars($tiColor['border'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="index-abbr" style="color:<?= htmlspecialchars($tiColor['text'], ENT_QUOTES, 'UTF-8') ?>">TI – Tajovuzkorlik indeksi</div>
            <div class="index-val" style="color:<?= htmlspecialchars($tiColor['text'], ENT_QUOTES, 'UTF-8') ?>"><?= number_format($ti, 1) ?></div>
            <div class="index-name">= (Jismoniy + Verbal + Bilvosita) ÷ 3</div>
            <div class="index-norm">Me'yor: <?= $tiNorm['min'] ?> – <?= $tiNorm['max'] ?> (21 ±4)</div>
            <div class="index-level" style="background:<?= htmlspecialchars($tiColor['border'], ENT_QUOTES, 'UTF-8') ?>;color:<?= htmlspecialchars($tiColor['text'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tiColor['label'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="index-card" style="background:<?= htmlspecialchars($diColor['bg'], ENT_QUOTES, 'UTF-8') ?>;border:1px solid <?= htmlspecialchars($diColor['border'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="index-abbr" style="color:<?= htmlspecialchars($diColor['text'], ENT_QUOTES, 'UTF-8') ?>">DI – Dushmanlik indeksi</div>
            <div class="index-val" style="color:<?= htmlspecialchars($diColor['text'], ENT_QUOTES, 'UTF-8') ?>"><?= number_format($di, 1) ?></div>
            <div class="index-name">= (Shubhalanuvchilik + Hafagarchililik) ÷ 2</div>
            <div class="index-norm">Me'yor: <?= $diNorm['min'] ?> – <?= $diNorm['max'] ?> (6,5 ±3)</div>
            <div class="index-level" style="background:<?= htmlspecialchars($diColor['border'], ENT_QUOTES, 'UTF-8') ?>;color:<?= htmlspecialchars($diColor['text'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($diColor['label'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>

    <!-- Scale scores -->
    <div class="section-title">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Shkala bo'yicha natijalar
    </div>
    <div class="scale-list">
        <?php foreach ($scales as $scale):
            $key   = $scale['key'];
            $ss    = $scaleScores[$key] ?? [];
            $score = $ss['score'] ?? 0;
            $k     = $ss['k'] ?? $scale['k'];
            $color = $scale['color'];
            $pct   = $k > 0 ? min(100, round(($score / $k) * 100)) : 0;
            $interp = $interpretation[$key] ?? [];
            $isHigh = $interp['high'] ?? false;
            $lblColor = $isHigh ? ['bg' => 'rgba(239,68,68,0.2)', 'text' => '#fca5a5', 'label' => 'Yuqori']
                                : ['bg' => 'rgba(16,185,129,0.2)', 'text' => '#6ee7b7', 'label' => "Me'yor"];
        ?>
        <div class="scale-row">
            <div class="scale-left">
                <div class="scale-num" style="background:<?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>">
                    <?= (int)$scale['id'] ?>
                </div>
                <div class="scale-info">
                    <div class="scale-name"><?= htmlspecialchars($scale['name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="scale-k">k = <?= $k ?></div>
                </div>
            </div>
            <div class="scale-bar-wrap">
                <div class="scale-bar-bg">
                    <div class="scale-bar-fill" style="width:<?= $pct ?>%;background:<?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>"></div>
                </div>
            </div>
            <div class="scale-right">
                <div class="scale-score" style="color:<?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>"><?= number_format($score, 1) ?></div>
                <div class="scale-max">/ <?= $k ?></div>
                <div class="scale-lbl" style="background:<?= htmlspecialchars($lblColor['bg'], ENT_QUOTES, 'UTF-8') ?>;color:<?= htmlspecialchars($lblColor['text'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($lblColor['label'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-home">← Kabinetga qaytish</a>
        <a href="/aggression/start" class="btn-retry">🔄 Qayta topshirish</a>
    </div>
</main>
</body>
</html>
