<?php
/**
 * Tajovuz holati tashxisi - Savol sahifasi
 */
$homeUrl     = !empty($_SESSION['teacher_user']) ? '/teacher/dashboard' : (!empty($_SESSION['hemis_user']) ? '/dashboard' : '/');
$displayName = !empty($_SESSION['teacher_user'])
    ? (string)($_SESSION['teacher_user']['full_name'] ?? 'O\'qituvchi')
    : (string)($_SESSION['hemis_user']['name'] ?? 'Foydalanuvchi');
$logoutUrl   = !empty($_SESSION['teacher_user']) ? '/teachers/logout' : '/hemis/logout';

$progress = $totalQuestions > 0 ? round(($currentQuestion / $totalQuestions) * 100) : 0;
$prevQ    = $currentQuestion > 1 ? $currentQuestion - 1 : null;
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savol <?= $currentQuestion ?>/<?= $totalQuestions ?> – Tajovuz testi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);min-height:100vh;color:#fff}

        /* Navbar */
        .navbar{background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.08);padding:12px 32px;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
        .nav-brand img{width:32px;height:32px;border-radius:8px}
        .nav-brand span{font-weight:700;font-size:14px;color:rgba(255,255,255,0.8)}
        .nav-info{display:flex;align-items:center;gap:16px}
        .nav-user{font-size:13px;color:rgba(255,255,255,0.5)}
        .nav-exit{font-size:13px;color:#f87171;text-decoration:none;font-weight:500}

        /* Progress bar */
        .progress-wrap{padding:0 32px;padding-top:20px;max-width:760px;margin:0 auto}
        .progress-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .progress-label{font-size:13px;color:rgba(255,255,255,0.5);font-weight:500}
        .progress-count{font-size:22px;font-weight:800;color:#fff}
        .progress-count span{font-size:14px;font-weight:500;color:rgba(255,255,255,0.4)}
        .progress-bar{height:6px;background:rgba(255,255,255,0.08);border-radius:6px;overflow:hidden}
        .progress-fill{height:100%;background:linear-gradient(90deg,#dc2626,#ef4444);border-radius:6px;transition:width .4s ease}

        /* Main */
        .main{max-width:760px;margin:0 auto;padding:28px 32px 60px}

        /* Question card */
        .q-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:24px;padding:40px;margin-bottom:28px}
        .q-number{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(239,68,68,0.7);margin-bottom:18px}
        .q-text{font-size:20px;font-weight:600;line-height:1.6;color:#fff}

        /* Answer options */
        .answers{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .answer-btn{position:relative;display:flex;align-items:center;gap:14px;padding:18px 20px;background:rgba(255,255,255,0.04);border:2px solid rgba(255,255,255,0.08);border-radius:16px;cursor:pointer;transition:all .25s;text-align:left;font-family:inherit}
        .answer-btn:hover{background:rgba(255,255,255,0.09);border-color:rgba(255,255,255,0.18);transform:translateY(-2px)}
        .answer-btn.yes-strong:hover,.answer-btn.yes-strong.selected{background:rgba(16,185,129,0.12);border-color:rgba(16,185,129,0.4)}
        .answer-btn.yes-weak:hover,.answer-btn.yes-weak.selected{background:rgba(16,185,129,0.07);border-color:rgba(16,185,129,0.25)}
        .answer-btn.no-weak:hover,.answer-btn.no-weak.selected{background:rgba(239,68,68,0.07);border-color:rgba(239,68,68,0.25)}
        .answer-btn.no-strong:hover,.answer-btn.no-strong.selected{background:rgba(239,68,68,0.12);border-color:rgba(239,68,68,0.4)}

        .answer-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
        .yes-strong .answer-icon{background:rgba(16,185,129,0.15)}
        .yes-weak .answer-icon{background:rgba(16,185,129,0.08)}
        .no-weak .answer-icon{background:rgba(239,68,68,0.08)}
        .no-strong .answer-icon{background:rgba(239,68,68,0.15)}

        .answer-text{flex:1}
        .answer-title{font-size:15px;font-weight:700;display:block;margin-bottom:3px}
        .yes-strong .answer-title,.yes-weak .answer-title{color:#6ee7b7}
        .no-weak .answer-title,.no-strong .answer-title{color:#fca5a5}
        .answer-desc{font-size:12px;color:rgba(255,255,255,0.4)}
        .answer-check{width:22px;height:22px;border:2px solid rgba(255,255,255,0.15);border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .25s}
        .answer-btn.selected .answer-check{background:#ef4444;border-color:#ef4444}
        .yes-strong.selected .answer-check,.yes-weak.selected .answer-check{background:#10b981;border-color:#10b981}
        .answer-check::after{content:'✓';font-size:13px;color:#fff;display:none}
        .answer-btn.selected .answer-check::after{display:block}

        /* Nav buttons */
        .nav-btns{display:flex;gap:14px;align-items:center;margin-top:28px}
        .btn-prev{display:flex;align-items:center;gap:8px;padding:14px 24px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:rgba(255,255,255,0.7);text-decoration:none;font-size:15px;font-weight:600;transition:all .25s}
        .btn-prev:hover{background:rgba(255,255,255,0.1);color:#fff}
        .btn-next{flex:1;display:flex;align-items:center;justify-content:center;gap:10px;padding:16px 24px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;border-radius:14px;font-size:16px;font-weight:700;font-family:inherit;cursor:pointer;transition:all .3s;box-shadow:0 4px 20px rgba(239,68,68,0.3)}
        .btn-next:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none}
        .btn-next:not(:disabled):hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(239,68,68,0.45)}

        /* Mini dots */
        .progress-dots{display:flex;flex-wrap:wrap;gap:4px;margin-top:8px}
        .dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,0.1);transition:background .3s}
        .dot.done{background:#ef4444}
        .dot.current{background:#fbbf24;width:20px;border-radius:4px}

        @media(max-width:640px){
            .navbar{padding:10px 16px}
            .progress-wrap,.main{padding-left:16px;padding-right:16px}
            .answers{grid-template-columns:1fr}
            .q-text{font-size:17px}
            .q-card{padding:24px 20px}
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-brand">
        <img src="/images/logo.png" alt="TerDPI">
        <span>Tajovuz testi</span>
    </a>
    <div class="nav-info">
        <span class="nav-user">👤 <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></span>
        <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" class="nav-exit">Chiqish</a>
    </div>
</nav>

<div class="progress-wrap">
    <div class="progress-header">
        <span class="progress-label">Savol <?= $currentQuestion ?> dan <?= $totalQuestions ?> tasidan</span>
        <span class="progress-count"><?= $currentQuestion ?><span>/<?= $totalQuestions ?></span></span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" style="width:<?= $progress ?>%"></div>
    </div>
    <div class="progress-dots" id="dots"></div>
</div>

<div class="main">
    <form method="POST" action="/aggression/answer" id="answerForm">
        <input type="hidden" name="question_id" value="<?= (int)$question['id'] ?>">
        <input type="hidden" name="answer" id="answerInput" value="">

        <div class="q-card">
            <div class="q-number">Savol №<?= $currentQuestion ?></div>
            <p class="q-text"><?= htmlspecialchars($question['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div class="answers">
            <button type="button" class="answer-btn yes-strong <?= ($currentAnswer === 'yes') ? 'selected' : '' ?>" onclick="selectAnswer('yes', this)">
                <div class="answer-icon">✅</div>
                <div class="answer-text">
                    <span class="answer-title">Ha</span>
                    <span class="answer-desc">Menga to'liq mos keladi</span>
                </div>
                <div class="answer-check"></div>
            </button>

            <button type="button" class="answer-btn yes-weak <?= ($currentAnswer === 'yes') ? 'selected' : '' ?>" onclick="selectAnswer('yes', this)">
                <div class="answer-icon">🤔</div>
                <div class="answer-text">
                    <span class="answer-title">Ha shekilli</span>
                    <span class="answer-desc">Ko'pincha mos keladi</span>
                </div>
                <div class="answer-check"></div>
            </button>

            <button type="button" class="answer-btn no-weak <?= ($currentAnswer === 'no') ? 'selected' : '' ?>" onclick="selectAnswer('no', this)">
                <div class="answer-icon">🙁</div>
                <div class="answer-text">
                    <span class="answer-title">Yo'q shekilli</span>
                    <span class="answer-desc">Kamdan-kam mos keladi</span>
                </div>
                <div class="answer-check"></div>
            </button>

            <button type="button" class="answer-btn no-strong <?= ($currentAnswer === 'no') ? 'selected' : '' ?>" onclick="selectAnswer('no', this)">
                <div class="answer-icon">❌</div>
                <div class="answer-text">
                    <span class="answer-title">Yo'q</span>
                    <span class="answer-desc">Menga mos kelmaydi</span>
                </div>
                <div class="answer-check"></div>
            </button>
        </div>

        <div class="nav-btns">
            <?php if ($prevQ): ?>
            <a href="/aggression/question?q=<?= $prevQ ?>" class="btn-prev">← Orqaga</a>
            <?php endif; ?>
            <button type="submit" class="btn-next" id="nextBtn" disabled>
                <?= $currentQuestion < $totalQuestions ? 'Keyingi savol →' : '✅ Testni yakunlash' ?>
            </button>
        </div>
    </form>
</div>

<script>
const totalQ = <?= $totalQuestions ?>;
const currentQ = <?= $currentQuestion ?>;
const hasAnswer = <?= $currentAnswer !== null ? 'true' : 'false' ?>;

// Progress dots
(function() {
    const dots = document.getElementById('dots');
    if (totalQ <= 75) {
        for (let i = 1; i <= totalQ; i++) {
            const d = document.createElement('div');
            d.className = 'dot' + (i < currentQ ? ' done' : '') + (i === currentQ ? ' current' : '');
            dots.appendChild(d);
        }
    }
})();

// If already answered, enable next
if (hasAnswer) {
    document.getElementById('nextBtn').disabled = false;
}

function selectAnswer(val, el) {
    document.querySelectorAll('.answer-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('answerInput').value = val;
    document.getElementById('nextBtn').disabled = false;
}

// Auto-submit with slight delay for UX
document.querySelectorAll('.answer-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        setTimeout(() => {
            if (document.getElementById('answerInput').value) {
                document.getElementById('answerForm').submit();
            }
        }, 350);
    });
});
</script>
</body>
</html>
