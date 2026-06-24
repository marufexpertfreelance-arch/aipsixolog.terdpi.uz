<?php
/**
 * Профессиональный Sidebar для админ-панели психолога
 * Используется на всех страницах /admin/*
 */

$currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
$currentPath = strtok($currentPath, '?'); // Убираем query string

// Функция для определения активного пункта меню
function isActive($path, $currentPath, $exact = false) {
    if ($exact) {
        return $currentPath === $path ? 'active' : '';
    }
    return strpos($currentPath, $path) === 0 ? 'active' : '';
}

// Функция для определения раскрытого подменю
function isExpanded($paths, $currentPath) {
    foreach ($paths as $path) {
        if (strpos($currentPath, $path) === 0) {
            return 'expanded';
        }
    }
    return '';
}
?>

<aside class="admin-sidebar">
    <!-- Логотип и название -->
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <img src="/images/logo.png" alt="TERDPI" class="sidebar-logo-img">
            <div class="sidebar-logo-text">
                <span class="logo-title">TERDPI</span>
                <span class="logo-subtitle">Psixolog kabineti</span>
            </div>
        </a>
    </div>

    <!-- Навигация -->
    <nav class="sidebar-nav">
        <!-- Bosh sahifa -->
        <a href="/admin" class="sidebar-item <?= isActive('/admin', $currentPath, true) ?>">
            <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></span>
            <span class="sidebar-text">Bosh sahifa</span>
        </a>

        <!-- TESTLAR -->
        <div class="sidebar-group <?= isExpanded(['/admin/tests'], $currentPath) ?>">
            <div class="sidebar-group-header" onclick="toggleSidebarGroup(this)">
                <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14l2 2 4-4"/></svg></span>
                <span class="sidebar-text">Testlar</span>
                <span class="sidebar-arrow">›</span>
            </div>
            <div class="sidebar-submenu">
                <a href="/admin/tests" class="sidebar-subitem <?= isActive('/admin/tests', $currentPath, true) ?>">
                    <span class="sidebar-dot"></span>
                    Barcha testlar
                </a>
                <a href="/admin/tests/create" class="sidebar-subitem <?= isActive('/admin/tests/create', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    + Yangi test yaratish
                </a>
            </div>
        </div>

        <!-- Разделитель -->
        <div class="sidebar-divider"></div>

        <!-- TALABALAR -->
        <div class="sidebar-group <?= isExpanded(['/admin/results', '/students'], $currentPath) && strpos($currentPath, 'teacher') === false ? 'expanded' : '' ?>">
            <div class="sidebar-group-header" onclick="toggleSidebarGroup(this)">
                <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M22 16.5l-5 3-5-3"/><path d="M17 10v6.5"/></svg></span>
                <span class="sidebar-text">Talabalar</span>
                <span class="sidebar-arrow">›</span>
            </div>
            <div class="sidebar-submenu">
                <a href="/admin/results" class="sidebar-subitem <?= isActive('/admin/results', $currentPath, true) ?>">
                    <span class="sidebar-dot"></span>
                    Natijalar
                </a>
                <a href="/admin/results/statistics" class="sidebar-subitem <?= isActive('/admin/results/statistics', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Statistika
                </a>
                <a href="/admin/results/analytics" class="sidebar-subitem <?= isActive('/admin/results/analytics', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Tahlil
                </a>
                <a href="/admin/results/test-groups" class="sidebar-subitem <?= isActive('/admin/results/test-groups', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Guruhlar bo'yicha
                </a>
                <a href="/students" class="sidebar-subitem <?= isActive('/students', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Ro'yxat
                </a>
            </div>
        </div>

        <!-- O'QITUVCHILAR -->
        <div class="sidebar-group <?= isExpanded(['/admin/results/teachers', '/admin/results/teacher', '/admin/teachers'], $currentPath) ?>">
            <div class="sidebar-group-header" onclick="toggleSidebarGroup(this)">
                <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                <span class="sidebar-text">O'qituvchilar</span>
                <span class="sidebar-arrow">›</span>
            </div>
            <div class="sidebar-submenu">
                <a href="/admin/results/teachers" class="sidebar-subitem <?= isActive('/admin/results/teachers', $currentPath, true) ?>">
                    <span class="sidebar-dot"></span>
                    Natijalar
                </a>
                <a href="/admin/results/teacher-statistics" class="sidebar-subitem <?= isActive('/admin/results/teacher-statistics', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Statistika
                </a>
                <a href="/admin/teachers" class="sidebar-subitem <?= isActive('/admin/teachers', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    Ro'yxat
                </a>
            </div>
        </div>

        <!-- Разделитель -->
        <div class="sidebar-divider"></div>

        <!-- Psixologik testlar -->
        <div class="sidebar-group">
            <div class="sidebar-group-header" onclick="toggleSidebarGroup(this)">
                <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"/><path d="M8.5 2h7"/><path d="M7 16.5h10"/></svg></span>
                <span class="sidebar-text">Psixologik testlar</span>
                <span class="sidebar-arrow">›</span>
            </div>
            <div class="sidebar-submenu">
                <a href="/admin/results/test?id=eysenck" class="sidebar-subitem">
                    <span class="sidebar-dot"></span>
                    Temperament (Eysenck)
                </a>
                <a href="/admin/results/test?id=iq" class="sidebar-subitem">
                    <span class="sidebar-dot"></span>
                    IQ Test
                </a>
                <a href="/admin/results/test?id=lusher" class="sidebar-subitem">
                    <span class="sidebar-dot"></span>
                    Lyusher testi
                </a>
                <a href="/admin/results/test?id=aggression" class="sidebar-subitem <?= isActive('/admin/results/test', $currentPath) && ($_GET['id'] ?? '') === 'aggression' ? 'active' : '' ?>">
                    <span class="sidebar-dot"></span>
                    Tajovuz tashxisi
                </a>
            </div>
        </div>

        <!-- Разделитель -->
        <div class="sidebar-divider"></div>

        <!-- SOZLAMALAR -->
        <div class="sidebar-group <?= isExpanded(['/admin/settings'], $currentPath) ?>">
            <div class="sidebar-group-header" onclick="toggleSidebarGroup(this)">
                <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span>
                <span class="sidebar-text">Sozlamalar</span>
                <span class="sidebar-arrow">›</span>
            </div>
            <div class="sidebar-submenu">
                <a href="/admin/settings/sync" class="sidebar-subitem <?= isActive('/admin/settings/sync', $currentPath) ?>">
                    <span class="sidebar-dot"></span>
                    HEMIS sinxronlash
                </a>
            </div>
        </div>
    </nav>

    <!-- Нижняя часть sidebar -->
    <div class="sidebar-footer">
        <a href="/admin/logout" class="sidebar-logout">
            <span class="sidebar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
            <span class="sidebar-text">Chiqish</span>
        </a>
    </div>
</aside>

<script>
function toggleSidebarGroup(header) {
    const group = header.closest('.sidebar-group');
    group.classList.toggle('expanded');
}

// Автоматически раскрываем активную группу при загрузке
document.addEventListener('DOMContentLoaded', function() {
    const activeItems = document.querySelectorAll('.sidebar-subitem.active');
    activeItems.forEach(item => {
        const group = item.closest('.sidebar-group');
        if (group) {
            group.classList.add('expanded');
        }
    });
});
</script>
