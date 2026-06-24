<?php
use App\Helpers\Lang;
Lang::init();
$lang = Lang::current();
$t = fn(string $key, string $fb = '') => htmlspecialchars(Lang::get($key, $fb), ENT_QUOTES, 'UTF-8');
$languages = Lang::LANGUAGES;
?>
<!doctype html>
<html lang="<?= $lang ?>" class="lang-<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t('site_title') ?></title>
    <link rel="icon" type="image/png" href="/images/logo.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png?v=2">
    <link rel="preload" as="image" href="/images/hero-poster.jpg">
    <link rel="preload" as="video" href="/videos/hero-bg-720p.mp4" type="video/mp4">
    <link rel="stylesheet" href="/main-styles.css?nocache=<?= time() ?>">
    <style>
        /* Critical Navigation Styles - встроены для обхода PageSpeed */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: transparent;
            color: #1f2937;
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .main-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 16px 24px;
            transition: all 0.4s ease;
        }
        
        .main-nav.scrolled {
            padding: 0;
        }
        
        .nav-container {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 16px;
            padding: 14px 24px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 32px;
            position: relative;
            overflow: visible;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }
        
        .main-nav.scrolled .nav-container {
            border-radius: 0;
            background: rgba(15, 23, 42, 0.92);
            border: none;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            z-index: 2;
        }
        
        .logo-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            background: transparent;
            padding: 0;
            border: none;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        .logo-text {
            font-size: 16px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 8px;
            margin: 0;
            padding: 0;
            justify-content: center;
            z-index: 2;
        }
        
        .nav-menu li {
            margin: 0;
        }
        
        .nav-menu a {
            display: block;
            padding: 10px 18px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
        }
        
        .btn-nav {
            padding: 10px 20px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            margin-left: auto;
            z-index: 10;
        }
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 20px;
            overflow-y: auto;
        }
        .mobile-menu.active { display: block; }
        .mobile-menu-close {
            position: absolute;
            top: 20px; right: 20px;
            background: none; border: none;
            color: white; font-size: 32px; cursor: pointer;
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
        }
        .mobile-menu-nav {
            margin-top: 60px;
            display: flex; flex-direction: column; gap: 12px;
        }
        .mobile-menu-nav a {
            display: block;
            padding: 16px 20px;
            color: white; text-decoration: none;
            border-radius: 8px; font-size: 16px; font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
            text-align: center;
        }
        .mobile-menu-nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* ── Language dropdown ── */
        .lang-dropdown {
            position: relative;
            z-index: 1001;
        }
        .lang-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all .25s ease;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .lang-dropdown-btn:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            color: #fff;
        }
        .lang-dropdown-btn .arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            transition: transform .25s ease;
            opacity: .7;
        }
        .lang-dropdown-btn .arrow svg {
            width: 12px;
            height: 12px;
            fill: currentColor;
        }
        .lang-dropdown.open .lang-dropdown-btn .arrow {
            transform: rotate(180deg);
        }
        .lang-dropdown.open .lang-dropdown-btn {
            background: rgba(255,255,255,0.1);
            border-color: rgba(56,189,248,0.3);
        }
        .lang-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 120px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 10002;
            animation: langDropIn .2s ease;
        }
        .lang-dropdown.open .lang-dropdown-menu { display: block; }
        @keyframes langDropIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .lang-dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            color: #1f2937;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 7px;
            transition: all .15s ease;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .lang-dropdown-menu a:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        .lang-dropdown-menu a.active {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
            font-weight: 600;
        }
        .lang-dropdown-menu a .flag { font-size: 20px; line-height: 1; }
        .lang-dropdown-menu a .check {
            margin-left: auto;
            font-size: 12px;
            opacity: 0;
        }
        .lang-dropdown-menu a.active .check { opacity: 1; }

        /* Mobile language switcher */
        .mobile-lang-list {
            display: flex; gap: 8px; justify-content: center;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            margin-bottom: 10px;
        }
        .mobile-lang-list a {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 16px !important;
            font-size: 14px !important;
            border-radius: 10px !important;
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.1);
            transition: background .2s, border-color .2s;
        }
        .mobile-lang-list a.active {
            background: rgba(99,102,241,0.25) !important;
            border-color: rgba(99,102,241,0.5);
        }
        .mobile-lang-list a .flag { font-size: 18px; }

        /* Phone number in navigation */
        .nav-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 10px;
            color: #10b981;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .nav-phone:hover {
            background: rgba(16, 185, 129, 0.25);
            border-color: rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
        }
        .nav-phone svg {
            width: 18px;
            height: 18px;
            stroke: #10b981;
        }

        @media (max-width: 768px) {
            .nav-container { padding: 12px 16px !important; flex-wrap: wrap; }
            .logo-text { font-size: 14px !important; }
            .nav-menu { display: none !important; }
            .nav-right { display: none !important; }
            .nav-phone { display: none !important; }
            .mobile-menu-toggle { display: block; }
            .hero-content { grid-template-columns: 1fr !important; padding: 80px 16px 40px 16px !important; gap: 24px !important; }
            .hero-text { order: 1; }
            .hero-subtitle { font-size: 18px !important; line-height: 1.4 !important; text-align: center; }
            .footer-container { flex-direction: column !important; gap: 8px !important; text-align: center !important; padding: 20px 16px !important; }
            .footer-developer, .footer-copyright { font-size: 12px !important; }
        }
        @media (max-width: 480px) {
            .hero-subtitle { font-size: 16px !important; }
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-container-bg">
                <video class="nav-mirror-video" autoplay loop muted playsinline preload="none">
                    <source src="/videos/hero-bg-720p.mp4" type="video/mp4">
                </video>
            </div>
            <a href="/" class="nav-logo">
                <img src="/images/logo.png" alt="TERDPI" class="logo-img">
                <span class="logo-text"><?= $t('site_title') ?></span>
            </a>
            <ul class="nav-menu">
                <li><a href="/"><?= $t('home') ?></a></li>
                <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                    <li><a href="/students"><?= $t('students') ?></a></li>
                    <li><a href="/admin"><?= $t('cabinet') ?></a></li>
                    <li><a href="/admin/logout"><?= $t('logout') ?></a></li>
                <?php else: ?>
                    <li><a href="/admin/login"><?= $t('psychologist_login') ?></a></li>
                <?php endif; ?>
                <?php if (!empty($_SESSION['hemis_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">
                            👤 <?= htmlspecialchars($_SESSION['hemis_user']['name'] ?? Lang::get('user'), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                    <li><a href="/hemis/logout"><?= $t('logout') ?></a></li>
                <?php endif; ?>
                <?php if (!empty($_SESSION['teacher_user'])): ?>
                    <li class="nav-user">
                        <span class="user-badge">
                            👨‍🏫 <?= htmlspecialchars($_SESSION['teacher_user']['full_name'] ?? Lang::get('teacher'), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                    <li><a href="/teacher/dashboard"><?= $t('cabinet') ?></a></li>
                    <li><a href="/teachers/logout"><?= $t('logout') ?></a></li>
                <?php elseif (empty($_SESSION['hemis_user']) && empty($_SESSION['admin_logged_in'])): ?>
                    <li><a href="/teachers/login"><?= $t('teacher_login') ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-right">
                <?php if (empty($_SESSION['hemis_user'])): ?>
                    <a href="/hemis/login" class="btn-nav"><?= $t('hemis_login') ?></a>
                <?php endif; ?>
                <!-- Phone number -->
                <a href="tel:+998944652728" class="nav-phone">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>+998 94 465 27 28</span>
                </a>
                <!-- Языки в конце навигации -->
                <div class="lang-dropdown" id="langDropdown">
                    <button class="lang-dropdown-btn" onclick="toggleLangDropdown()" type="button">
                        <span><?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="arrow" aria-hidden="true">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 14l5-5 5 5H7z"/></svg>
                        </span>
                    </button>
                    <div class="lang-dropdown-menu">
                        <?php foreach ($languages as $code => $info): ?>
                        <a href="/locale?lang=<?= $code ?>" class="<?= $code === $lang ? 'active' : '' ?>">
                            <span><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="check">✓</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">☰</button>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-menu-close" onclick="toggleMobileMenu()" aria-label="Close menu">×</button>
        <nav class="mobile-menu-nav">
            <div class="mobile-lang-list">
                <?php foreach ($languages as $code => $info): ?>
                <a href="/locale?lang=<?= $code ?>" class="<?= $code === $lang ? 'active' : '' ?>" onclick="toggleMobileMenu()">
                    <span><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <a href="tel:+998944652728" onclick="toggleMobileMenu()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; gap: 10px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                +998 94 465 27 28
            </a>
            <a href="/" onclick="toggleMobileMenu()"><?= $t('home') ?></a>
            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                <a href="/students" onclick="toggleMobileMenu()"><?= $t('students') ?></a>
                <a href="/admin" onclick="toggleMobileMenu()"><?= $t('cabinet') ?></a>
                <a href="/admin/logout" onclick="toggleMobileMenu()"><?= $t('logout') ?></a>
            <?php else: ?>
                <a href="/admin/login" onclick="toggleMobileMenu()"><?= $t('psychologist_login') ?></a>
            <?php endif; ?>
            <?php if (!empty($_SESSION['hemis_user'])): ?>
                <a href="/hemis/logout" onclick="toggleMobileMenu()"><?= $t('logout') ?></a>
            <?php elseif (empty($_SESSION['admin_logged_in'])): ?>
                <a href="/hemis/login" onclick="toggleMobileMenu()" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);"><?= $t('hemis_login') ?></a>
            <?php endif; ?>
            <?php if (!empty($_SESSION['teacher_user'])): ?>
                <a href="/teacher/dashboard" onclick="toggleMobileMenu()"><?= $t('cabinet') ?></a>
                <a href="/teachers/logout" onclick="toggleMobileMenu()"><?= $t('logout') ?></a>
            <?php elseif (empty($_SESSION['hemis_user']) && empty($_SESSION['admin_logged_in'])): ?>
                <a href="/teachers/login" onclick="toggleMobileMenu()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><?= $t('teacher_login') ?></a>
            <?php endif; ?>
        </nav>
    </div>

    <script>
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
        }
        function toggleLangDropdown() {
            document.getElementById('langDropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            var dd = document.getElementById('langDropdown');
            if (dd && !dd.contains(e.target)) dd.classList.remove('open');
        });

        function ensureVideoPlays(video) {
            if (!video) return;
            try {
                var p = video.play();
                if (p && typeof p.catch === 'function') p.catch(function(){});
            } catch (e) {}
        }

        var lastScroll = 0;
        var nav = document.querySelector('.main-nav');
        window.addEventListener('scroll', function() {
            var cur = window.pageYOffset;
            if (cur > 100) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
            lastScroll = cur;
        });

        class ParticleSystem {
            constructor(canvas) {
                this.canvas = canvas;
                this.ctx = canvas.getContext('2d');
                this.particles = [];
                this.mouse = { x: null, y: null, radius: 150 };
                this.init();
                this.animate();
                window.addEventListener('mousemove', (e) => { this.mouse.x = e.clientX; this.mouse.y = e.clientY; });
                window.addEventListener('resize', () => this.init());
            }
            init() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
                this.particles = [];
                var n = Math.floor((this.canvas.width * this.canvas.height) / 15000);
                for (var i = 0; i < n; i++) {
                    this.particles.push({
                        x: Math.random() * this.canvas.width,
                        y: Math.random() * this.canvas.height,
                        size: Math.random() * 2 + 1,
                        speedX: (Math.random() - 0.5) * 0.5,
                        speedY: (Math.random() - 0.5) * 0.5,
                        color: 'rgba(14, 165, 233, ' + (Math.random() * 0.5 + 0.3) + ')'
                    });
                }
            }
            animate() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                for (var p of this.particles) {
                    p.x += p.speedX; p.y += p.speedY;
                    if (p.x < 0 || p.x > this.canvas.width) p.speedX *= -1;
                    if (p.y < 0 || p.y > this.canvas.height) p.speedY *= -1;
                    if (this.mouse.x && this.mouse.y) {
                        var dx = this.mouse.x - p.x, dy = this.mouse.y - p.y, d = Math.sqrt(dx*dx + dy*dy);
                        if (d < this.mouse.radius) { p.x -= dx/d*2; p.y -= dy/d*2; }
                    }
                    this.ctx.beginPath(); this.ctx.arc(p.x, p.y, p.size, 0, Math.PI*2);
                    this.ctx.fillStyle = p.color; this.ctx.fill();
                }
                for (var i = 0; i < this.particles.length; i++) {
                    for (var j = i+1; j < this.particles.length; j++) {
                        var dx = this.particles[i].x - this.particles[j].x;
                        var dy = this.particles[i].y - this.particles[j].y;
                        var d = Math.sqrt(dx*dx + dy*dy);
                        if (d < 120) {
                            this.ctx.beginPath();
                            this.ctx.strokeStyle = 'rgba(6, 182, 212, ' + (0.2*(1 - d/120)) + ')';
                            this.ctx.lineWidth = 0.5;
                            this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                            this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                            this.ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(() => this.animate());
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            ensureVideoPlays(document.querySelector('.hero-video'));
            ensureVideoPlays(document.querySelector('.nav-mirror-video'));
            ensureVideoPlays(document.querySelector('.hero-mirror-video'));
            var heroV = document.querySelector('.hero-video');
            var navV = document.querySelector('.nav-mirror-video');
            var heroMirrorV = document.querySelector('.hero-mirror-video');
            if (heroV) heroV.addEventListener('canplay', function() { ensureVideoPlays(heroV); }, { once: true });
            if (navV) navV.addEventListener('canplay', function() { ensureVideoPlays(navV); }, { once: true });
            if (heroMirrorV) heroMirrorV.addEventListener('canplay', function() { ensureVideoPlays(heroMirrorV); }, { once: true });
            animateCounters();
            initScrollReveal();
            var pc = document.getElementById('particles-canvas');
            if (pc) new ParticleSystem(pc);
        });

        function initScrollReveal() {
            var els = document.querySelectorAll('.reveal');
            if (!els.length) return;
            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        var nums = e.target.querySelectorAll('.step-number');
                        nums.forEach(function(n) { n.classList.add('pulse'); });
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            els.forEach(function(el) { obs.observe(el); });
        }

        function animateCounters() {
            var counters = document.querySelectorAll('.stat-number');
            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                        var c = entry.target, t = parseInt(c.getAttribute('data-target') || '0', 10);
                        animateValue(c, 0, t, 2000);
                        c.classList.add('animated');
                        obs.unobserve(c);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(function(c) { obs.observe(c); });
        }
        function animateValue(el, start, end, dur) {
            var ts = null;
            function step(t) {
                if (!ts) ts = t;
                var p = Math.min((t - ts) / dur, 1);
                el.textContent = Math.floor(p * (end - start) + start);
                if (p < 1) requestAnimationFrame(step);
                else el.textContent = end;
            }
            requestAnimationFrame(step);
        }

        function switchStepsTab(btn) {
            var tabs = document.querySelectorAll('.steps-tab');
            var panels = document.querySelectorAll('.steps-panel');
            tabs.forEach(function(t) { t.classList.remove('active'); });
            panels.forEach(function(p) { p.classList.remove('active'); });
            btn.classList.add('active');
            var target = document.getElementById(btn.getAttribute('data-target'));
            if (target) target.classList.add('active');
        }
    </script>

    <main class="hero-section">
        <div class="hero-video-container">
            <video class="hero-video" autoplay loop muted playsinline preload="auto" poster="/images/hero-poster.jpg">
                <source src="/videos/hero-bg-720p.mp4" type="video/mp4">
            </video>
            <div class="hero-overlay"></div>
            <canvas id="particles-canvas" class="particles-canvas"></canvas>
        </div>
        <div class="hero-content">
            <div class="hero-text">
                <div class="ai-badge">
                    <svg class="ai-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <span class="ai-text gradient-text"><?= $t('ai_psychology') ?></span>
                </div>
                <p class="hero-subtitle"><?= $t('hero_subtitle') ?></p>
            </div>
        </div>
    </main>

    <!-- Stats strip -->
    <section class="stats-strip">
        <div class="container">
            <div class="stats-strip-inner reveal">
                <div class="strip-item">
                    <span class="strip-number stat-number" data-target="<?= htmlspecialchars((string)($statistics['students_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">0</span>
                    <span class="strip-label"><?= $t('stats_counter_students') ?></span>
                </div>
                <div class="strip-divider"></div>
                <div class="strip-item">
                    <span class="strip-number stat-number" data-target="<?= htmlspecialchars((string)($statistics['tests_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">0</span>
                    <span class="strip-label"><?= $t('stats_counter_tests') ?></span>
                </div>
                <div class="strip-divider"></div>
                <div class="strip-item">
                    <span class="strip-number stat-number" data-target="<?= htmlspecialchars((string)($statistics['results_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">0</span>
                    <span class="strip-label"><?= $t('stats_counter_results') ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tests showcase -->
    <section class="tests-showcase">
        <div class="container">
            <h2 class="section-title reveal"><?= $t('tests_title') ?></h2>
            <p class="section-subtitle reveal"><?= $t('tests_subtitle') ?></p>
            <div class="tests-grid">
                <div class="test-card reveal reveal-delay-1">
                    <div class="test-card-visual eysenck-visual">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        <div class="test-card-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a7 7 0 0 0-7 7c0 3 1.5 5.5 4 6.7V18h6v-2.3c2.5-1.2 4-3.7 4-6.7a7 7 0 0 0-7-7z"/><path d="M12 18v3"/><path d="M8 22h8"/></svg></div>
                    </div>
                    <div class="test-card-body">
                        <h3 class="test-card-title"><?= $t('test_eysenck_title') ?></h3>
                        <p class="test-card-desc"><?= $t('test_eysenck_desc') ?></p>
                        <div class="test-card-meta">
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> <?= $t('test_eysenck_questions') ?></span>
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?= $t('test_eysenck_time') ?></span>
                        </div>
                        <a href="/hemis/login" class="test-card-btn"><?= $t('test_btn_details') ?> →</a>
                    </div>
                </div>

                <div class="test-card reveal reveal-delay-2">
                    <div class="test-card-visual iq-visual">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <div class="test-card-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
                    </div>
                    <div class="test-card-body">
                        <h3 class="test-card-title"><?= $t('test_iq_title') ?></h3>
                        <p class="test-card-desc"><?= $t('test_iq_desc') ?></p>
                        <div class="test-card-meta">
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> <?= $t('test_iq_questions') ?></span>
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?= $t('test_iq_time') ?></span>
                        </div>
                        <a href="/hemis/login" class="test-card-btn"><?= $t('test_btn_details') ?> →</a>
                    </div>
                </div>

                <div class="test-card reveal reveal-delay-3">
                    <div class="test-card-visual luscher-visual">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        <div class="test-card-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="0.5" fill="currentColor"/><circle cx="17.5" cy="10.5" r="0.5" fill="currentColor"/><circle cx="8.5" cy="7.5" r="0.5" fill="currentColor"/><circle cx="6.5" cy="12.5" r="0.5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg></div>
                    </div>
                    <div class="test-card-body">
                        <h3 class="test-card-title"><?= $t('test_luscher_title') ?></h3>
                        <p class="test-card-desc"><?= $t('test_luscher_desc') ?></p>
                        <div class="test-card-meta">
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> <?= $t('test_luscher_questions') ?></span>
                            <span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?= $t('test_luscher_time') ?></span>
                        </div>
                        <a href="/hemis/login" class="test-card-btn"><?= $t('test_btn_details') ?> →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Psychologist Section -->
    <section class="psychologist-section">
        <div class="container">
            <div class="psychologist-content reveal">
                <div class="psychologist-image-wrapper reveal from-left">
                    <div class="psychologist-image-frame">
                        <img src="/images/psychologist.jpg" alt="Xudoyorova Marvarid" class="psychologist-image" onerror="this.src='/images/psychologist-placeholder.svg'">
                        <div class="psychologist-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="psychologist-info reveal from-right">
                    <div class="psychologist-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span><?= $lang === 'uz' ? 'Psixolog' : ($lang === 'ru' ? 'Психолог' : 'Psychologist') ?></span>
                    </div>
                    <h2 class="psychologist-name">Xudoyorova Marvarid</h2>
                    <p class="psychologist-title"><?= $lang === 'uz' ? 'Termiz davlat pedagogika instituti psixologi' : ($lang === 'ru' ? 'Психолог Термезского государственного педагогического института' : 'Psychologist at Termez State Pedagogical Institute') ?></p>
                    <div class="psychologist-description">
                        <p><?= $lang === 'uz' ? 'Talabalar psixologik salomatligi va shaxsiy rivojlanishi bo\'yicha professional yordam ko\'rsataman. Zamonaviy psixologik testlar va konsultatsiyalar orqali har bir talabaning o\'ziga xos ehtiyojlarini hisobga olgan holda qo\'llab-quvvatlayman.' : ($lang === 'ru' ? 'Оказываю профессиональную помощь по психологическому здоровью и личностному развитию студентов. Поддерживаю каждого студента с учетом его индивидуальных потребностей через современные психологические тесты и консультации.' : 'I provide professional assistance in psychological health and personal development of students. I support each student taking into account their individual needs through modern psychological tests and consultations.') ?></p>
                    </div>
                    <div class="psychologist-stats">
                        <div class="psy-stat">
                            <div class="psy-stat-number stat-number" data-target="<?= htmlspecialchars((string)($statistics['students_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">0</div>
                            <div class="psy-stat-label"><?= $lang === 'uz' ? 'Talabalar' : ($lang === 'ru' ? 'Студентов' : 'Students') ?></div>
                        </div>
                        <div class="psy-stat">
                            <div class="psy-stat-number stat-number" data-target="<?= htmlspecialchars((string)($statistics['results_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">0</div>
                            <div class="psy-stat-label"><?= $lang === 'uz' ? 'Testlar' : ($lang === 'ru' ? 'Тестов' : 'Tests') ?></div>
                        </div>
                        <div class="psy-stat">
                            <div class="psy-stat-number">5+</div>
                            <div class="psy-stat-label"><?= $lang === 'uz' ? 'Yillik tajriba' : ($lang === 'ru' ? 'Лет опыта' : 'Years Experience') ?></div>
                        </div>
                    </div>
                    <div class="psychologist-contact">
                        <a href="/hemis/login" class="btn-psychologist-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?= $lang === 'uz' ? 'Konsultatsiya olish' : ($lang === 'ru' ? 'Получить консультацию' : 'Get Consultation') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- For whom -->
    <section class="for-whom-section">
        <div class="container">
            <h2 class="section-title reveal"><?= $t('for_whom_title') ?></h2>
            <p class="section-subtitle reveal"><?= $t('for_whom_subtitle') ?></p>
            <div class="for-whom-grid">
                <div class="whom-card whom-student reveal reveal-delay-1">
                    <div class="whom-card-accent"></div>
                    <div class="whom-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M22 16.5l-5 3-5-3"/><path d="M17 10v6.5"/></svg>
                    </div>
                    <h3 class="whom-title"><?= $t('for_students_title') ?></h3>
                    <ul class="whom-features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_students_f1') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_students_f2') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_students_f3') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_students_f4') ?></li>
                    </ul>
                    <a href="/hemis/login" class="whom-btn whom-btn-student"><?= $t('for_students_btn') ?></a>
                </div>
                <div class="whom-card whom-teacher reveal reveal-delay-2">
                    <div class="whom-card-accent"></div>
                    <div class="whom-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="whom-title"><?= $t('for_teachers_title') ?></h3>
                    <ul class="whom-features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_teachers_f1') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_teachers_f2') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_teachers_f3') ?></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <?= $t('for_teachers_f4') ?></li>
                    </ul>
                    <a href="/teachers/login" class="whom-btn whom-btn-teacher"><?= $t('for_teachers_btn') ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="how-it-works-section">
        <div class="container">
            <h2 class="section-title reveal"><?= $t('how_title') ?></h2>
            <p class="section-subtitle reveal"><?= $t('how_subtitle') ?></p>
            <div class="steps-tabs reveal">
                <button class="steps-tab active" data-target="student-steps" onclick="switchStepsTab(this)"><?= $t('tab_students') ?></button>
                <button class="steps-tab" data-target="teacher-steps" onclick="switchStepsTab(this)"><?= $t('tab_teachers') ?></button>
            </div>
            <div class="steps-panels">
                <div class="steps-panel active" id="student-steps">
                    <div class="steps-grid">
                        <div class="step-card reveal from-left reveal-delay-1">
                            <div class="step-number">1</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg></div>
                            <h3 class="step-title"><?= $t('s_step1_title') ?></h3>
                            <p class="step-description"><?= $t('s_step1_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-2">
                            <div class="step-number">2</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg></div>
                            <h3 class="step-title"><?= $t('s_step2_title') ?></h3>
                            <p class="step-description"><?= $t('s_step2_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-3">
                            <div class="step-number">3</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
                            <h3 class="step-title"><?= $t('s_step3_title') ?></h3>
                            <p class="step-description"><?= $t('s_step3_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-4">
                            <div class="step-number">4</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></div>
                            <h3 class="step-title"><?= $t('s_step4_title') ?></h3>
                            <p class="step-description"><?= $t('s_step4_desc') ?></p>
                        </div>
                    </div>
                </div>
                <div class="steps-panel" id="teacher-steps">
                    <div class="steps-grid">
                        <div class="step-card reveal from-left reveal-delay-1">
                            <div class="step-number">1</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg></div>
                            <h3 class="step-title"><?= $t('t_step1_title') ?></h3>
                            <p class="step-description"><?= $t('t_step1_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-2">
                            <div class="step-number">2</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></div>
                            <h3 class="step-title"><?= $t('t_step2_title') ?></h3>
                            <p class="step-description"><?= $t('t_step2_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-3">
                            <div class="step-number">3</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M22 16.5l-5 3-5-3"/><path d="M17 10v6.5"/></svg></div>
                            <h3 class="step-title"><?= $t('t_step3_title') ?></h3>
                            <p class="step-description"><?= $t('t_step3_desc') ?></p>
                        </div>
                        <div class="step-card reveal from-left reveal-delay-4">
                            <div class="step-number">4</div>
                            <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                            <h3 class="step-title"><?= $t('t_step4_title') ?></h3>
                            <p class="step-description"><?= $t('t_step4_desc') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-shapes"><span></span><span></span><span></span><span></span><span></span></div>
        <div class="container">
            <div class="cta-content reveal zoom-in">
                <h2 class="cta-title"><?= $t('cta_title') ?></h2>
                <p class="cta-description"><?= $t('cta_desc') ?></p>
                <div class="cta-buttons">
                    <a href="/hemis/login" class="btn-cta-primary"><?= $t('hemis_login') ?></a>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer reveal">
        <div class="footer-container" style="text-align: center;">
            <p class="footer-copyright" style="margin: 0;">
                &copy; <?= date('Y') ?> <?= $t('footer') ?>
            </p>
        </div>
    </footer>
</body>
</html>
