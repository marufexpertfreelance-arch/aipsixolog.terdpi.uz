<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Psixolog kabineti - Termiz davlat pedagogika instituti</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/admin-sidebar.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="admin-content-inner">
                <!-- Page Header -->
                <div class="admin-page-header">
                    <h1 class="admin-page-title">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px;"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Bosh sahifa
                    </h1>
                    <p class="admin-page-subtitle">
                        Psixologik xizmat statistikasi va umumiy ma'lumotlar
                    </p>
                </div>

                <!-- Flash Messages -->
                <?php if (!empty($flash)): ?>
                    <div class="alert alert-success" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #10b981; color: #065f46; padding: 16px 20px; border-radius: 12px; font-weight: 600; margin-bottom: 24px;">
                        <strong>✓</strong> <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #ef4444; color: #991b1b; padding: 16px 20px; border-radius: 12px; font-weight: 600; margin-bottom: 24px;">
                        <strong>✗</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <!-- Статистика -->
                <div class="stats-grid">
                    <!-- Jami testlar -->
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14l2 2 4-4"/></svg>
                        </div>
                        <div class="stat-card-value"><?= count($tests ?? []) ?></div>
                        <div class="stat-card-label">Jami testlar</div>
                    </div>

                    <!-- Talabalar -->
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M22 16.5l-5 3-5-3"/><path d="M17 10v6.5"/></svg>
                        </div>
                        <div class="stat-card-value"><?= count($allStudents ?? []) ?></div>
                        <div class="stat-card-label">Talabalar</div>
                    </div>

                    <!-- O'qituvchilar -->
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="stat-card-value"><?= count($allTeachers ?? []) ?></div>
                        <div class="stat-card-label">O'qituvchilar</div>
                    </div>

                    <!-- Natijalar -->
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                        </div>
                        <div class="stat-card-value"><?= $totalResults ?? 0 ?></div>
                        <div class="stat-card-label">Jami natijalar</div>
                    </div>
                </div>

                <!-- Быстрые действия -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <!-- Tez harakatlar - Talabalar -->
                    <div class="stat-card">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 2px;"></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M22 16.5l-5 3-5-3"/><path d="M17 10v6.5"/></svg>
                            Talabalar bo'limi
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="/admin/results" class="btn-action btn-action-info" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg> Natijalarni ko'rish</a>
                            <a href="/admin/results/statistics" class="btn-action btn-action-success" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> Statistika</a>
                            <a href="/admin/results/analytics" class="btn-action btn-action-primary" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg> Tahlil</a>
                        </div>
                    </div>

                    <!-- Tez harakatlar - O'qituvchilar -->
                    <div class="stat-card">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 24px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 2px;"></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            O'qituvchilar bo'limi
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="/admin/results/teachers" class="btn-action btn-action-info" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg> Natijalarni ko'rish</a>
                            <a href="/admin/results/teacher-statistics" class="btn-action btn-action-warning" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> Statistika</a>
                            <a href="/admin/teachers" class="btn-action btn-action-primary" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg> Ro'yxat</a>
                        </div>
                    </div>

                    <!-- Tez harakatlar - Testlar -->
                    <div class="stat-card">
                        <h3 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14l2 2 4-4"/></svg>
                            Testlar
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="/admin/tests/create" class="btn-action btn-action-success" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Yangi test yaratish</a>
                            <a href="/admin/tests" class="btn-action btn-action-info" style="display: block;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg> Barcha testlar</a>
                        </div>
                    </div>
                </div>

                <!-- Последние тесты -->
                <div class="admin-table-container">
                    <div style="padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 20px; font-weight: 700; color: #1a202c; margin: 0; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                            So'nggi testlar
                        </h3>
                        <a href="/admin/tests" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 14px;">
                            Barchasini ko'rish →
                        </a>
                    </div>

                    <?php if (empty($tests)): ?>
                        <div style="padding: 60px 20px; text-align: center;">
                            <p style="color: #a0aec0; font-size: 16px; margin: 0;">
                                Hali hech qanday test yo'q. 
                                <a href="/admin/tests/create" style="color: #667eea;">Birinchi testni yarating</a>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nomi</th>
                                    <th>Kategoriya</th>
                                    <th>Savollar</th>
                                    <th>Guruhlar</th>
                                    <th>O'qituvchilar</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $displayTests = array_slice($tests, 0, 5);
                                foreach ($displayTests as $t): 
                                    $allowedGroups = $t['allowed_groups'] ?? [];
                                    if (!is_array($allowedGroups)) $allowedGroups = [];
                                    $allowedGroups = array_filter($allowedGroups, fn($g) => !empty($g) && trim((string)$g) !== '');
                                    
                                    $teacherData = $t['teacher_data'] ?? [];
                                ?>
                                    <tr>
                                        <td style="font-weight: 500; color: #718096;"><?= htmlspecialchars((string)($t['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td style="font-weight: 600; color: #1a202c;"><?= htmlspecialchars($t['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($t['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <span class="badge badge-info"><?= count($t['questions'] ?? []) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($allowedGroups)): ?>
                                                <span class="badge badge-primary"><?= count($allowedGroups) ?> guruh</span>
                                            <?php else: ?>
                                                <span style="color: #a0aec0; font-size: 13px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($teacherData)): ?>
                                                <span class="badge badge-success"><?= count($teacherData) ?> o'qituvchi</span>
                                            <?php else: ?>
                                                <span style="color: #a0aec0; font-size: 13px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="/admin/tests/show?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-info" style="padding: 6px 12px; font-size: 12px;">Ochish</a>
                                                <a href="/admin/tests/edit?id=<?= urlencode((string)($t['id'] ?? '')) ?>" class="btn-action btn-action-primary" style="padding: 6px 12px; font-size: 12px;">Tahrirlash</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Mobile menu toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div class="sidebar-overlay" onclick="toggleMobileMenu()"></div>

    <script>
    function toggleMobileMenu() {
        document.querySelector('.admin-sidebar').classList.toggle('open');
        document.querySelector('.sidebar-overlay').classList.toggle('active');
    }
    </script>
</body>
</html>
