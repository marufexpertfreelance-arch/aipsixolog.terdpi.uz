<?php
$title = $title ?? 'O\'qituvchilar natijalari';
$results = $results ?? [];
$tests = $tests ?? [];
$filters = $filters ?? [];

$pageTitle = 'O\'qituvchilar natijalari';
$extraStyles = '
    .filter-form {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        padding: 32px;
        border-radius: 24px;
        margin-bottom: 32px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    .filter-form::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    }
    .filter-form-title {
        font-size: 20px;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .filter-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-input, .filter-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        background: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    .filter-actions {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }
    .btn-filter {
        padding: 12px 24px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-filter-submit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    .btn-filter-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    .btn-filter-clear {
        background: #f1f5f9;
        color: #475569;
        border: 2px solid #e2e8f0;
        text-decoration: none;
    }
    .btn-filter-clear:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }
    .results-container {
        display: grid;
        gap: 24px;
    }
    .result-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 32px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .result-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .result-card::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .result-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 24px 48px rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }
    .result-card:hover::before {
        transform: scaleX(1);
    }
    .result-card:hover::after {
        opacity: 1;
    }
    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
        gap: 16px;
        flex-wrap: wrap;
    }
    .result-teacher-info {
        flex: 1;
        min-width: 0;
    }
    .result-teacher-name {
        font-size: 22px;
        font-weight: 900;
        background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .result-teacher-dept {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 1px solid rgba(102, 126, 234, 0.2);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #667eea;
    }
    .result-test-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    .result-test-title {
        font-size: 20px;
        font-weight: 900;
        background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-align: right;
    }
    .result-test-type {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #059669;
    }
    .result-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .result-meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .result-meta-item:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .result-meta-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        border-radius: 12px;
        font-size: 20px;
        flex-shrink: 0;
        border: 2px solid rgba(102, 126, 234, 0.2);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    .result-meta-content {
        flex: 1;
        min-width: 0;
    }
    .result-meta-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .result-meta-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }
    .result-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }
    .result-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        border: 2px solid;
        white-space: nowrap;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .result-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .result-badge .muted {
        font-weight: 700;
        opacity: 0.7;
    }
    .result-badge--green {
        border-color: rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.08) 100%);
        color: #065f46;
    }
    .result-badge--blue {
        border-color: rgba(59, 130, 246, 0.4);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, rgba(37, 99, 235, 0.08) 100%);
        color: #1d4ed8;
    }
    .result-badge--amber {
        border-color: rgba(245, 158, 11, 0.4);
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.10) 100%);
        color: #92400e;
    }
    .result-badge--red {
        border-color: rgba(239, 68, 68, 0.4);
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(220, 38, 38, 0.08) 100%);
        color: #991b1b;
    }
    .result-badge--gray {
        border-color: rgba(100, 116, 139, 0.4);
        background: linear-gradient(135deg, rgba(100, 116, 139, 0.12) 0%, rgba(71, 85, 105, 0.08) 100%);
        color: #475569;
    }
    .empty-results {
        padding: 80px 20px;
        text-align: center;
        background: white;
        border-radius: 20px;
        border: 2px dashed #e5e7eb;
    }
    .empty-results-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .empty-results h2 {
        font-size: 24px;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 12px;
    }
    .empty-results p {
        color: #94a3b8;
        font-size: 16px;
    }
    .results-summary {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 32px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        margin-top: 32px;
        position: relative;
        overflow: hidden;
    }
    .results-summary::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    }
    .results-summary-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        position: relative;
        z-index: 1;
    }
    .results-summary-value {
        font-size: 48px;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.2));
    }
    .results-summary-label {
        font-size: 14px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 8px;
    }
    @media (max-width: 768px) {
        .result-header {
            flex-direction: column;
        }
        .result-test-info {
            align-items: flex-start;
        }
        .result-test-title {
            text-align: left;
        }
        .result-meta {
            grid-template-columns: 1fr;
        }
    }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<?php
$toText = function ($value): string {
    if (is_string($value)) {
        return $value;
    }
    if ($value === null) {
        return '';
    }
    if (is_bool($value)) {
        return $value ? '1' : '0';
    }
    if (is_scalar($value)) {
        return (string)$value;
    }
    if (is_array($value)) {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE);
        return $json !== false ? $json : '';
    }
    return '';
};
?>

<!-- Page Header -->
<div class="admin-page-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 24px; padding: 32px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; z-index: 0;"></div>
    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.08) 100%); border-radius: 50%; z-index: 0;"></div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px; position: relative; z-index: 1;">
        <div>
            <h1 class="admin-page-title" style="font-size: 32px; font-weight: 900; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 40px; filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3));">👨‍🏫</span>
                <span>O'qituvchilar natijalari</span>
            </h1>
            <p class="admin-page-subtitle" style="font-size: 16px; color: #64748b; font-weight: 600; margin: 0;">Barcha o'qituvchilar test natijalarini ko'rish va tahlil qilish</p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="/admin/results/teacher-statistics" class="btn-action btn-action-info" style="padding: 14px 24px; text-decoration: none; border-radius: 14px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(59, 130, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)'">
                📈 Statistika
            </a>
            <?php
            $exportParams = $_GET;
            $exportUrl = '/admin/results/export-teachers' . (!empty($exportParams) ? ('?' . http_build_query($exportParams)) : '');
            ?>
            <a href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-action btn-action-success" style="padding: 14px 24px; text-decoration: none; border-radius: 14px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                📥 Excel eksport
            </a>
            <button type="button" onclick="clearTeacherResults()" style="padding: 14px 24px; border: none; cursor: pointer; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; border-radius: 14px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(239, 68, 68, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)'">
                🗑️ O'chirish
            </button>
        </div>
    </div>
</div>

<div class="admin-table-container" style="padding: 24px;">
    <!-- Фильтры -->
    <form method="GET" action="/admin/results/teachers" class="filter-form">
        <div class="filter-form-title">
            <span style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.3));">🔍</span>
            <span>Filtrlash</span>
        </div>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Test turi</label>
                <select name="type" class="filter-select">
                    <option value="all" <?= $filters['type'] === 'all' ? 'selected' : '' ?>>Barcha testlar</option>
                    <option value="custom" <?= $filters['type'] === 'custom' ? 'selected' : '' ?>>Oddiy testlar</option>
                    <option value="eysenck" <?= $filters['type'] === 'eysenck' ? 'selected' : '' ?>>Eysenck temperament</option>
                    <option value="iq" <?= $filters['type'] === 'iq' ? 'selected' : '' ?>>IQ testi</option>
                    <option value="lusher" <?= $filters['type'] === 'lusher' ? 'selected' : '' ?>>Lüscher rang testi</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Aniq test</label>
                <select name="test_id" class="filter-select">
                    <option value="0">Tanlang...</option>
                    <?php foreach ($tests as $test): ?>
                        <option value="<?= $test['id'] ?>" <?= $filters['test_id'] == $test['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Sanadan</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'], ENT_QUOTES, 'UTF-8') ?>" class="filter-input">
            </div>

            <div class="filter-group">
                <label class="filter-label">Sanagacha</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'], ENT_QUOTES, 'UTF-8') ?>" class="filter-input">
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter btn-filter-submit">
                ✅ Filtrlash
            </button>
            <a href="/admin/results/teachers" class="btn-filter btn-filter-clear">
                🔄 Tozalash
            </a>
        </div>
    </form>

    <!-- Результаты -->
    <div class="results-container">
        <?php if (empty($results)): ?>
            <div class="empty-results">
                <div class="empty-results-icon">📊</div>
                <h2>Natijalar topilmadi</h2>
                <p>O'qituvchilar hali testlardan o'tmagan yoki filtrlar natijalarni ko'rsatmayapti.</p>
            </div>
        <?php else: ?>
            <?php foreach ($results as $result): ?>
                <?php
                $teacherData = $result['teacher_data'] ?? null;
                
                // Если teacher_data нет, пытаемся получить из student_name
                if ($teacherData === null) {
                    $studentName = $result['student_name'] ?? '';
                    if (!empty($studentName)) {
                        $teacherData = [
                            'full_name' => $studentName,
                            'department' => '-',
                        ];
                    }
                }
                
                $teacherName = $teacherData['full_name'] ?? ($result['student_name'] ?? 'Noma\'lum o\'qituvchi');
                $teacherDepartment = $teacherData['department'] ?? '-';
                
                $testTitle = $result['test_title'] ?? 'Noma\'lum test';
                $testType = $result['test_type'] ?? '';
                $testTypeLabels = [
                    'custom' => 'Oddiy test',
                    'eysenck' => 'Eysenck',
                    'iq' => 'IQ',
                    'lusher' => 'Lüscher',
                ];
                $testTypeLabel = $testTypeLabels[$testType] ?? $testType;
                
                $submittedAt = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                $submittedAtFormatted = '';
                $submittedAtDate = '';
                if ($submittedAt) {
                    $submittedAtFormatted = date('d.m.Y H:i', strtotime($submittedAt));
                    $submittedAtDate = date('d.m.Y', strtotime($submittedAt));
                }
                
                // Определяем результат
                $resultText = '-';
                $scales = $result['scales'] ?? null;
                $hasScales = is_array($scales) && !empty($scales);
                if (!$hasScales) {
                    if (isset($result['interpretation']['category'])) {
                        $resultText = $toText($result['interpretation']['category']);
                    } elseif (isset($result['temperament'])) {
                        $tempType = is_array($result['temperament']) ? ($result['temperament']['type'] ?? '') : $result['temperament'];
                        $tempNames = [
                            'Choleric' => 'Xolerik',
                            'Sanguine' => 'Sangvinik',
                            'Phlegmatic' => 'Flegmatik',
                            'Melancholic' => 'Melanxolik',
                        ];
                        $resultText = $tempNames[$tempType] ?? $tempType;
                    } elseif (isset($result['iq_category'])) {
                        $resultText = $toText($result['iq_category']);
                    } elseif (isset($result['calculated_score'])) {
                        $resultText = 'Ball: ' . $toText($result['calculated_score']);
                    }
                }
                ?>
                <div class="result-card">
                    <div class="result-header">
                        <div class="result-teacher-info">
                            <div class="result-teacher-name">
                                <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.3));">👨‍🏫</span>
                                <span><?= htmlspecialchars($teacherName, ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="result-teacher-dept">
                                🏢 <?= htmlspecialchars($teacherDepartment, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <div class="result-test-info">
                            <div class="result-test-title">
                                <?= htmlspecialchars($testTitle, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="result-test-type">
                                <?= htmlspecialchars($testTypeLabel, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>

                    <div class="result-meta">
                        <div class="result-meta-item">
                            <div class="result-meta-icon">📅</div>
                            <div class="result-meta-content">
                                <div class="result-meta-label">Sana</div>
                                <div class="result-meta-value"><?= htmlspecialchars($submittedAtDate, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                        <div class="result-meta-item">
                            <div class="result-meta-icon">🕐</div>
                            <div class="result-meta-content">
                                <div class="result-meta-label">Vaqt</div>
                                <div class="result-meta-value"><?= htmlspecialchars($submittedAtFormatted, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="result-badges">
                        <?php if ($hasScales): ?>
                            <?php
                            $scaleColors = [
                                'HOLERIK' => 'result-badge--red',
                                'SANGVINIK' => 'result-badge--green',
                                'FLEGMATIK' => 'result-badge--amber',
                                'MELANHOLIK' => 'result-badge--blue',
                            ];
                            foreach ($scales as $scaleName => $scaleResult):
                                $score = is_array($scaleResult) ? ($scaleResult['score'] ?? '-') : '-';
                                $cat = (is_array($scaleResult) && isset($scaleResult['interpretation']['category'])) ? (string)$scaleResult['interpretation']['category'] : '-';
                                $colorClass = $scaleColors[$scaleName] ?? 'result-badge--gray';
                            ?>
                                <span class="result-badge <?= $colorClass ?>">
                                    <?= htmlspecialchars((string)$scaleName, ENT_QUOTES, 'UTF-8') ?>
                                    <span class="muted">•</span>
                                    <?= htmlspecialchars((string)$score, ENT_QUOTES, 'UTF-8') ?>
                                    <span class="muted">•</span>
                                    <?= htmlspecialchars((string)$cat, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="result-badge result-badge--gray">
                                <?= htmlspecialchars($toText($resultText), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($results)): ?>
        <div class="results-summary">
            <div class="results-summary-content">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; border: 2px solid rgba(102, 126, 234, 0.2); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);">
                        📊
                    </div>
                    <div>
                        <div class="results-summary-value"><?= count($results) ?></div>
                        <div class="results-summary-label">Jami natijalar</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/layout-footer.php'; ?>

<script>
    function clearTeacherResults() {
        if (!confirm('Haqiqatan ham barcha o\'qituvchilar natijalarini o\'chirmoqchimisiz? Bu amalni qaytarib bo\'lmaydi!')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/results/clear-teachers';
        document.body.appendChild(form);
        form.submit();
    }
</script>

