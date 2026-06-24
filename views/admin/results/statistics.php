<?php 
$pageTitle = 'Talabalar statistikasi';
$audience = $audience ?? ($_GET['audience'] ?? 'students');
$audience = in_array($audience, ['students', 'teachers', 'all'], true) ? $audience : 'students';
$audienceLabel = [
    'students' => 'Talabalar',
    'teachers' => 'O\'qituvchilar',
    'all' => 'Barchasi',
][$audience] ?? 'Talabalar';
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function toggleAccordion(header) {
        const accordionItem = header.closest(".accordion-item");
        const isActive = accordionItem.classList.contains("active");
        const toggle = header.querySelector(".accordion-toggle");
        if (isActive) {
            accordionItem.classList.remove("active");
            if (toggle) toggle.textContent = "▶";
        } else {
            accordionItem.classList.add("active");
            if (toggle) toggle.textContent = "▼";
        }
    }
</script>';
$extraStyles = '
        .stat-card {
            padding: 24px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 24px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin: 8px 0;
            color: #111827;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .temperament-section {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 32px;
            margin-top: 24px;
            border: 2px solid #e5e7eb;
        }
        
        /* Аккордеон стили */
        .accordion-item {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border-radius: 12px;
            margin-bottom: 16px;
            border: 2px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .accordion-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .accordion-header {
            padding: 20px 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            transition: all 0.3s ease;
            user-select: none;
        }
        
        .accordion-header:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        
        .accordion-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .accordion-toggle {
            font-size: 18px;
            color: #6b7280;
            transition: all 0.3s ease;
            font-weight: bold;
            display: inline-block;
            width: 20px;
            text-align: center;
        }
        
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 24px;
        }
        
        .accordion-item.active .accordion-content {
            max-height: 5000px;
            padding: 24px;
        }
        
        .pie-chart-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 48px;
            flex-wrap: wrap;
            margin-top: 32px;
        }
        
        .pie-chart-wrapper {
            position: relative;
            width: 280px;
            height: 280px;
        }
        
        .pie-chart {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }
        
        .pie-segment {
            fill: none;
            stroke-width: 60;
            stroke-linecap: round;
            transition: all 0.3s ease;
        }
        
        .pie-segment:hover {
            opacity: 0.8;
        }
        
        .temperament-legend {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        
        .legend-item:hover {
            border-color: currentColor;
            transform: translateX(4px);
        }
        
        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            flex-shrink: 0;
        }
        
        .legend-info {
            flex: 1;
        }
        
        .legend-label {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
        }
        
        .legend-value {
            font-size: 20px;
            font-weight: 700;
            margin-top: 4px;
        }
        
        .temperament-choleric { color: #10b981; }
        .temperament-sanguine { color: #3b82f6; }
        .temperament-phlegmatic { color: #f59e0b; }
        .temperament-melancholic { color: #ef4444; }
        
        .test-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 16px;
            padding: 28px;
            margin-top: 24px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        
        .test-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .test-stat-item {
            padding: 16px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }
        /* Профессиональный фон для личного кабинета психолога */
        body.admin-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        body.admin-dashboard .main-content {
            background: transparent;
            max-width: 100%;
            padding: 0;
        }
        
        /* Навигация на всю ширину для админ панели */
        body.admin-dashboard .nav-container {
            max-width: 100%;
            border-radius: 0;
            margin: 0;
            padding: 20px 40px;
        }
        
        body.admin-dashboard .nav-menu {
            gap: 4px;
        }
        
'; ?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div>
            <h1 class="admin-page-title">📈 <?= htmlspecialchars($audienceLabel, ENT_QUOTES, 'UTF-8') ?> statistikasi</h1>
            <p class="admin-page-subtitle">Test natijalari bo'yicha umumiy statistika</p>
        </div>
        <div style="display: inline-flex; gap: 8px; background: rgba(255,255,255,0.75); border: 1px solid rgba(226,232,240,0.9); padding: 6px; border-radius: 14px;">
            <?php
            $baseParams = $_GET;
            unset($baseParams['audience']);
            $mkUrl = function (string $aud) use ($baseParams): string {
                $params = $baseParams;
                $params['audience'] = $aud;
                return '/admin/results/statistics' . (!empty($params) ? ('?' . http_build_query($params)) : '');
            };
            $btnStyle = function (bool $active, string $color): string {
                if ($active) {
                    return 'padding: 10px 14px; border-radius: 12px; background: ' . $color . '; color: #fff; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;';
                }
                return 'padding: 10px 14px; border-radius: 12px; background: #fff; color: #0f172a; border: 1px solid #e2e8f0; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;';
            };
            ?>
            <a href="<?= htmlspecialchars($mkUrl('students'), ENT_QUOTES, 'UTF-8') ?>" style="<?= $btnStyle($audience === 'students', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)') ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Talabalar
            </a>
            <a href="<?= htmlspecialchars($mkUrl('teachers'), ENT_QUOTES, 'UTF-8') ?>" style="<?= $btnStyle($audience === 'teachers', 'linear-gradient(135deg, #10b981 0%, #059669 100%)') ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                O'qituvchilar
            </a>
            <a href="<?= htmlspecialchars($mkUrl('all'), ENT_QUOTES, 'UTF-8') ?>" style="<?= $btnStyle($audience === 'all', 'linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)') ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Barchasi
            </a>
        </div>
    </div>
</div>

<!-- Общая статистика -->
        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Umumiy statistika
                </h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </div>
                    <div class="stat-label">Jami natijalar</div>
                    <div class="stat-value">
                        <?= htmlspecialchars((string)($overall['total_results'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="stat-label">Jami talabalar</div>
                    <div class="stat-value">
                        <?= htmlspecialchars((string)($overall['total_students'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div class="stat-label">Oddiy testlar</div>
                    <div class="stat-value">
                        <?= htmlspecialchars((string)($overall['test_types']['custom'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="stat-label">Temperament</div>
                    <div class="stat-value">
                        <?= htmlspecialchars((string)($overall['test_types']['eysenck'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    </div>
                    <div class="stat-label">IQ Test</div>
                    <div class="stat-value">
                        <?= htmlspecialchars((string)($overall['test_types']['iq'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика по тестам -->
        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Testlar bo'yicha statistika
                </h2>
            </div>

            <?php if (empty($test_statistics)): ?>
                <p class="muted" style="margin-top: 12px; text-align: center; padding: 40px;">
                    Hozircha test natijalari yo'q.
                </p>
            <?php else: ?>
                <?php foreach ($test_statistics as $testId => $stats): ?>
                    <?php 
                    $isTemperament = ($testId === 'eysenck' || $testId === '0' || $testId === '-1');
                    $isIqTest = ($testId === 'iq');
                    $isLusherTest = ($testId === 'lusher');
                    $hasTemperamentDistribution = isset($stats['temperament_distribution']) && !empty($stats['temperament_distribution']);
                    $hasIqStatistics = $isIqTest && isset($stats['average_iq']);
                    $hasLusherStatistics = $isLusherTest && isset($stats['total_completions']);
                    ?>
                    
                    <?php if ($isIqTest && $hasIqStatistics): ?>
                        <!-- Отдельный красивый блок для IQ Test -->
                        <div class="accordion-item active" data-test-id="<?= htmlspecialchars((string)$testId, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h3>
                                    <span class="accordion-toggle">▼</span>
                                    💡 <?= htmlspecialchars($stats['test']['title'] ?? 'IQ Test', ENT_QUOTES, 'UTF-8') ?>
                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280; margin-left: 8px;">
                                        (<?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?> o'tkazilgan)
                                    </span>
                                </h3>
                            </div>
                            <div class="accordion-content">
                                <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
                                    <a href="/admin/results/test?id=iq" 
                                       style="padding: 10px 20px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease;"
                                       onmouseover="this.style.transform='scale(1.05)'" 
                                       onmouseout="this.style.transform='scale(1)'">
                                        Batafsil ko'rish
                                    </a>
                                </div>
                            
                            <!-- Красивые карточки со статистикой -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                                        📊
                                    </div>
                                    <div class="stat-label">Jami o'tkazilgan</div>
                                    <div class="stat-value" style="font-size: 32px;">
                                        <?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                                        📈
                                    </div>
                                    <div class="stat-label">O'rtacha IQ</div>
                                    <div class="stat-value" style="font-size: 32px;">
                                        <?= htmlspecialchars($stats['average_iq'] !== null ? (string)$stats['average_iq'] : 'N/A', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white;">
                                        ⬇️
                                    </div>
                                    <div class="stat-label">Min IQ</div>
                                    <div class="stat-value" style="font-size: 32px;">
                                        <?= htmlspecialchars($stats['min_iq'] !== null ? (string)$stats['min_iq'] : 'N/A', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                                        ⬆️
                                    </div>
                                    <div class="stat-label">Max IQ</div>
                                    <div class="stat-value" style="font-size: 32px;">
                                        <?= htmlspecialchars($stats['max_iq'] !== null ? (string)$stats['max_iq'] : 'N/A', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($stats['category_distribution'])): ?>
                                <h4 style="margin-top: 32px; margin-bottom: 20px; font-size: 20px; font-weight: 600; color: #111827;">
                                    IQ kategoriyalari bo'yicha taqsimot
                                </h4>
                                
                                <?php
                                // Определяем цвета для категорий IQ
                                $iqCategoryColors = [
                                    'Genial daraja' => ['color' => '#8b5cf6', 'icon' => '👑'],
                                    'Juda yuqori' => ['color' => '#3b82f6', 'icon' => '⭐'],
                                    'Yuqori' => ['color' => '#10b981', 'icon' => '✨'],
                                    'O\'rtadan yuqori' => ['color' => '#14b8a6', 'icon' => '📊'],
                                    'O\'rtacha' => ['color' => '#f59e0b', 'icon' => '📈'],
                                    'O\'rtadan past' => ['color' => '#f97316', 'icon' => '📉'],
                                    'Past' => ['color' => '#ef4444', 'icon' => '⚠️'],
                                    'Juda past' => ['color' => '#dc2626', 'icon' => '🔴'],
                                ];
                                
                                $distribution = $stats['category_distribution'];
                                $total = array_sum($distribution);
                                
                                // Вычисляем проценты и углы для круговой диаграммы
                                $angles = [];
                                $currentAngle = 0;
                                foreach ($distribution as $category => $count) {
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                    $angle = $total > 0 ? ($count / $total) * 360 : 0;
                                    
                                    $angles[$category] = [
                                        'count' => $count,
                                        'percentage' => $percentage,
                                        'angle' => $angle,
                                        'startAngle' => $currentAngle,
                                        'endAngle' => $currentAngle + $angle,
                                    ];
                                    $currentAngle += $angle;
                                }
                                ?>
                                
                                <div class="pie-chart-container">
                                    <div class="pie-chart-wrapper">
                                        <svg class="pie-chart" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                                            <?php
                                            $centerX = 50;
                                            $centerY = 50;
                                            $radius = 30;
                                            $currentAngle = 0;
                                            
                                            // Функция для вычисления точки на окружности
                                            $getPoint = function($angle, $r) use ($centerX, $centerY) {
                                                $rad = deg2rad($angle);
                                                return [
                                                    'x' => $centerX + $r * cos($rad),
                                                    'y' => $centerY + $r * sin($rad)
                                                ];
                                            };
                                            
                                            foreach ($distribution as $category => $count) {
                                                if ($count > 0) {
                                                    $angle = $angles[$category];
                                                    $startAngle = $currentAngle;
                                                    $endAngle = $currentAngle + $angle['angle'];
                                                    
                                                    $largeArc = $angle['angle'] > 180 ? 1 : 0;
                                                    
                                                    $start = $getPoint($startAngle, $radius);
                                                    $end = $getPoint($endAngle, $radius);
                                                    
                                                    $color = $iqCategoryColors[$category]['color'] ?? '#8b5cf6';
                                                    
                                                    $pathData = "M $centerX $centerY L {$start['x']} {$start['y']} A $radius $radius 0 $largeArc 1 {$end['x']} {$end['y']} Z";
                                            ?>
                                                    <path d="<?= $pathData ?>" 
                                                          fill="<?= $color ?>" 
                                                          class="pie-segment"
                                                          data-category="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>"
                                                          style="opacity: 0.9; transition: opacity 0.3s ease;"
                                                          onmouseover="this.style.opacity='1'; this.style.filter='brightness(1.1)'"
                                                          onmouseout="this.style.opacity='0.9'; this.style.filter='brightness(1)'"/>
                                            <?php
                                                    $currentAngle = $endAngle;
                                                }
                                            }
                                            ?>
                                        </svg>
                                    </div>
                                    
                                    <div class="temperament-legend">
                                        <?php foreach ($distribution as $category => $count): ?>
                                            <?php 
                                            $info = $iqCategoryColors[$category] ?? ['color' => '#8b5cf6', 'icon' => '📊'];
                                            $angle = $angles[$category] ?? ['count' => 0, 'percentage' => 0];
                                            ?>
                                            <div class="legend-item" style="border-color: <?= $info['color'] ?>;">
                                                <div class="legend-color" style="background: <?= $info['color'] ?>;"></div>
                                                <div class="legend-info">
                                                    <div class="legend-label" style="color: <?= $info['color'] ?>;">
                                                        <?= htmlspecialchars($info['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                                                    </div>
                                                    <div class="legend-value" style="color: <?= $info['color'] ?>;">
                                                        <?= htmlspecialchars((string)$angle['count'], ENT_QUOTES, 'UTF-8') ?> ta
                                                        <span style="font-size: 14px; opacity: 0.7;">
                                                            (<?= number_format($angle['percentage'], 1) ?>%)
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Chart.js график для IQ категорий -->
                                <div style="margin-top: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                                    <h5 style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: #374151;">Grafik ko'rinishida</h5>
                                    <canvas id="iqCategoryChart-<?= $testId ?>" style="max-height: 300px;"></canvas>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif ($isLusherTest && $hasLusherStatistics): ?>
                        <!-- Отдельный красивый блок для Lyusher Testi -->
                        <div class="accordion-item" data-test-id="<?= htmlspecialchars((string)$testId, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h3>
                                    <span class="accordion-toggle">▶</span>
                                    🎨 <?= htmlspecialchars($stats['test']['title'] ?? 'Lyusher Testi', ENT_QUOTES, 'UTF-8') ?>
                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280; margin-left: 8px;">
                                        (<?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?> o'tkazilgan)
                                    </span>
                                </h3>
                            </div>
                            <div class="accordion-content">
                                <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
                                    <a href="/admin/results/test?id=lusher" 
                                       style="padding: 10px 20px; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease;"
                                       onmouseover="this.style.transform='scale(1.05)'" 
                                       onmouseout="this.style.transform='scale(1)'">
                                        Batafsil ko'rish
                                    </a>
                                </div>
                            
                                <div class="test-stats-grid">
                                    <div class="test-stat-item">
                                        <div class="stat-label">Jami o'tkazilgan</div>
                                        <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                            <?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                    <div class="test-stat-item">
                                        <div class="stat-label">Unikal talabalar</div>
                                        <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                            <?= htmlspecialchars((string)($stats['unique_students'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($isTemperament && $hasTemperamentDistribution): ?>
                        <!-- Отдельный красивый блок для Temperament -->
                        <div class="accordion-item" data-test-id="<?= htmlspecialchars((string)$testId, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h3>
                                    <span class="accordion-toggle">▶</span>
                                    <?= htmlspecialchars($stats['test']['title'] ?? 'Temperament', ENT_QUOTES, 'UTF-8') ?>
                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280; margin-left: 8px;">
                                        (<?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?> o'tkazilgan)
                                    </span>
                                </h3>
                            </div>
                            <div class="accordion-content">
                                <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
                                    <a href="/admin/results/test?id=<?= urlencode((string)$testId) ?>" 
                                       style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s ease;"
                                       onmouseover="this.style.transform='scale(1.05)'" 
                                       onmouseout="this.style.transform='scale(1)'">
                                        Batafsil ko'rish
                                    </a>
                                </div>
                            
                            <div class="test-stats-grid">
                                <div class="test-stat-item">
                                    <div class="stat-label">Jami o'tkazilgan</div>
                                    <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                        <?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div class="test-stat-item">
                                    <div class="stat-label">Nozik talabalar</div>
                                    <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                        <?= htmlspecialchars((string)($stats['unique_students'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <h4 style="margin-top: 32px; margin-bottom: 20px; font-size: 20px; font-weight: 600; color: #111827;">
                                Temperamentlar taqsimoti
                            </h4>
                            
                            <?php
                            $tempNames = [
                                'Choleric' => ['uz' => 'Xolerik', 'color' => '#10b981', 'icon' => '🔥'],
                                'Sanguine' => ['uz' => 'Sangvinik', 'color' => '#3b82f6', 'icon' => '☀️'],
                                'Phlegmatic' => ['uz' => 'Flegmatik', 'color' => '#f59e0b', 'icon' => '🌊'],
                                'Melancholic' => ['uz' => 'Melanxolik', 'color' => '#ef4444', 'icon' => '🌙'],
                            ];
                            
                            $distribution = $stats['temperament_distribution'];
                            $total = array_sum($distribution);
                            
                            // Вычисляем проценты и углы для круговой диаграммы
                            $angles = [];
                            $currentAngle = 0;
                            foreach (['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'] as $type) {
                                $count = $distribution[$type] ?? 0;
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                $angle = $total > 0 ? ($count / $total) * 360 : 0;
                                
                                $angles[$type] = [
                                    'count' => $count,
                                    'percentage' => $percentage,
                                    'angle' => $angle,
                                    'startAngle' => $currentAngle,
                                    'endAngle' => $currentAngle + $angle,
                                ];
                                $currentAngle += $angle;
                            }
                            ?>
                            
                            <div class="pie-chart-container">
                                <div class="pie-chart-wrapper">
                                    <svg class="pie-chart" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                                        <?php
                                        $centerX = 50;
                                        $centerY = 50;
                                        $radius = 30;
                                        $currentAngle = 0;
                                        
                                        // Функция для вычисления точки на окружности
                                        $getPoint = function($angle, $r) use ($centerX, $centerY) {
                                            $rad = deg2rad($angle);
                                            return [
                                                'x' => $centerX + $r * cos($rad),
                                                'y' => $centerY + $r * sin($rad)
                                            ];
                                        };
                                        
                                        foreach (['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'] as $type) {
                                            $angle = $angles[$type];
                                            if ($angle['angle'] > 0) {
                                                $startAngle = $currentAngle;
                                                $endAngle = $currentAngle + $angle['angle'];
                                                
                                                $largeArc = $angle['angle'] > 180 ? 1 : 0;
                                                
                                                $start = $getPoint($startAngle, $radius);
                                                $end = $getPoint($endAngle, $radius);
                                                
                                                $color = $tempNames[$type]['color'];
                                                
                                                $pathData = "M $centerX $centerY L {$start['x']} {$start['y']} A $radius $radius 0 $largeArc 1 {$end['x']} {$end['y']} Z";
                                        ?>
                                                <path d="<?= $pathData ?>" 
                                                      fill="<?= $color ?>" 
                                                      class="pie-segment"
                                                      data-temp="<?= $type ?>"
                                                      style="opacity: 0.9; transition: opacity 0.3s ease;"
                                                      onmouseover="this.style.opacity='1'; this.style.filter='brightness(1.1)'"
                                                      onmouseout="this.style.opacity='0.9'; this.style.filter='brightness(1)'"/>
                                        <?php
                                                $currentAngle = $endAngle;
                                            }
                                        }
                                        ?>
                                    </svg>
                                </div>
                                
                                <div class="temperament-legend">
                                    <?php foreach (['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'] as $type): ?>
                                        <?php $info = $tempNames[$type]; $angle = $angles[$type]; ?>
                                        <div class="legend-item" style="border-color: <?= $info['color'] ?>;">
                                            <div class="legend-color" style="background: <?= $info['color'] ?>;"></div>
                                            <div class="legend-info">
                                                <div class="legend-label" style="color: <?= $info['color'] ?>;">
                                                    <?= $info['icon'] ?> <?= htmlspecialchars($info['uz'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                                <div class="legend-value" style="color: <?= $info['color'] ?>;">
                                                    <?= htmlspecialchars((string)$angle['count'], ENT_QUOTES, 'UTF-8') ?> ta
                                                    <span style="font-size: 14px; opacity: 0.7;">
                                                        (<?= number_format($angle['percentage'], 1) ?>%)
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Chart.js график для темпераментов -->
                            <div style="margin-top: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                                <h5 style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: #374151;">Grafik ko'rinishida</h5>
                                <canvas id="temperamentChart-<?= $testId ?>" style="max-height: 300px;"></canvas>
                            </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Обычные тесты (включая HADS) -->
                        <div class="accordion-item" data-test-id="<?= htmlspecialchars((string)$testId, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <h3>
                                    <span class="accordion-toggle">▶</span>
                                    <?= htmlspecialchars($stats['test']['title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?>
                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280; margin-left: 8px;">
                                        (<?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?> o'tkazilgan)
                                    </span>
                                </h3>
                            </div>
                            <div class="accordion-content">
                                <div class="test-stats-grid">
                                    <div class="test-stat-item">
                                        <div class="stat-label">Jami o'tkazilgan</div>
                                        <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                            <?= htmlspecialchars((string)($stats['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                    <div class="test-stat-item">
                                        <div class="stat-label">Nozik talabalar</div>
                                        <div class="stat-value" style="font-size: 28px; margin-top: 8px;">
                                            <?= htmlspecialchars((string)($stats['unique_students'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 20px;">
                                    <a href="/admin/results/test?id=<?= urlencode((string)$testId) ?>" 
                                       style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-block;">
                                        Batafsil ko'rish
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <script>
        // Графики для темпераментов
        <?php foreach ($test_statistics as $testId => $stats): ?>
            <?php if (($testId === 'eysenck' || $testId === '0' || $testId === '-1') && isset($stats['temperament_distribution']) && !empty($stats['temperament_distribution'])): ?>
                <?php
                $distribution = $stats['temperament_distribution'];
                $total = array_sum($distribution);
                ?>
                (function() {
                    const ctx = document.getElementById('temperamentChart-<?= $testId ?>');
                    if (!ctx) return;
                    
                    const tempNames = {
                        'Choleric': 'Xolerik',
                        'Sanguine': 'Sangvinik',
                        'Phlegmatic': 'Flegmatik',
                        'Melancholic': 'Melanxolik'
                    };
                    
                    const colors = {
                        'Choleric': 'rgba(239, 68, 68, 0.8)',
                        'Sanguine': 'rgba(16, 185, 129, 0.8)',
                        'Phlegmatic': 'rgba(59, 130, 246, 0.8)',
                        'Melancholic': 'rgba(139, 92, 246, 0.8)'
                    };
                    
                    const borderColors = {
                        'Choleric': 'rgba(239, 68, 68, 1)',
                        'Sanguine': 'rgba(16, 185, 129, 1)',
                        'Phlegmatic': 'rgba(59, 130, 246, 1)',
                        'Melancholic': 'rgba(139, 92, 246, 1)'
                    };
                    
                    const labels = ['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'].map(t => tempNames[t]);
                    const data = ['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'].map(t => <?= json_encode($distribution[$t] ?? 0) ?>);
                    const bgColors = ['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'].map(t => colors[t]);
                    const bdColors = ['Choleric', 'Sanguine', 'Phlegmatic', 'Melancholic'].map(t => borderColors[t]);
                    
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: bgColors,
                                borderColor: bdColors,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 14,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const percentage = <?= $total ?> > 0 ? ((value / <?= $total ?>) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                })();
            <?php endif; ?>
            
            <?php if ($isIqTest && isset($stats['category_distribution']) && !empty($stats['category_distribution'])): ?>
                <?php
                $distribution = $stats['category_distribution'];
                $total = array_sum($distribution);
                ?>
                (function() {
                    const ctx = document.getElementById('iqCategoryChart-<?= $testId ?>');
                    if (!ctx) return;
                    
                    const categoryColors = {
                        'Genial daraja': 'rgba(139, 92, 246, 0.8)',
                        'Juda yuqori': 'rgba(59, 130, 246, 0.8)',
                        'Yuqori': 'rgba(16, 185, 129, 0.8)',
                        'O\'rtadan yuqori': 'rgba(20, 184, 166, 0.8)',
                        'O\'rtacha': 'rgba(245, 158, 11, 0.8)',
                        'O\'rtadan past': 'rgba(249, 115, 22, 0.8)',
                        'Past': 'rgba(239, 68, 68, 0.8)',
                        'Juda past': 'rgba(220, 38, 38, 0.8)'
                    };
                    
                    const borderColors = {
                        'Genial daraja': 'rgba(139, 92, 246, 1)',
                        'Juda yuqori': 'rgba(59, 130, 246, 1)',
                        'Yuqori': 'rgba(16, 185, 129, 1)',
                        'O\'rtadan yuqori': 'rgba(20, 184, 166, 1)',
                        'O\'rtacha': 'rgba(245, 158, 11, 1)',
                        'O\'rtadan past': 'rgba(249, 115, 22, 1)',
                        'Past': 'rgba(239, 68, 68, 1)',
                        'Juda past': 'rgba(220, 38, 38, 1)'
                    };
                    
                    const labels = Object.keys(<?= json_encode($distribution, JSON_UNESCAPED_UNICODE) ?>);
                    const data = Object.values(<?= json_encode($distribution, JSON_UNESCAPED_UNICODE) ?>);
                    const bgColors = labels.map(cat => categoryColors[cat] || 'rgba(139, 92, 246, 0.8)');
                    const bdColors = labels.map(cat => borderColors[cat] || 'rgba(139, 92, 246, 1)');
                    
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: bgColors,
                                borderColor: bdColors,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 14,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const percentage = <?= $total ?> > 0 ? ((value / <?= $total ?>) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                })();
            <?php endif; ?>
        <?php endforeach; ?>
        
        // Инициализация: открываем аккордеоны с классом 'active' по умолчанию
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.accordion-item.active').forEach(item => {
                const header = item.querySelector('.accordion-header');
                const toggle = header.querySelector('.accordion-toggle');
                toggle.textContent = '▼';
            });
            // Устанавливаем правильные иконки для закрытых аккордеонов
            document.querySelectorAll('.accordion-item:not(.active)').forEach(item => {
                const header = item.querySelector('.accordion-header');
                const toggle = header.querySelector('.accordion-toggle');
                if (toggle && toggle.textContent !== '▼') {
                    toggle.textContent = '▶';
                }
            });
        });
    </script>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>

