<?php
$pageTitle = 'Talaba natijalari';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">👤 Talaba natijalari</h1>
    <p class="admin-page-subtitle"><?= htmlspecialchars($student['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="admin-table-container" style="padding: 24px;">

        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <div>
                    <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0 0 8px 0;"><?= htmlspecialchars($student['name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?></h2>
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">ID: <?= htmlspecialchars($student['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <a href="/admin/results" class="btn-secondary" style="padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Barcha natijalar</a>
            </div>

            <?php if (empty($results)): ?>
                <p class="muted" style="margin-top: 12px;">
                    Bu talaba hali hech qanday testdan o'tmagan.
                </p>
            <?php else: ?>
                <p class="muted" style="margin-bottom: 16px;">
                    Jami: <?= count($results) ?> test natijasi
                </p>
                <div class="table-container" style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Test</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Turi</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Sana</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Natija</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $index => $result): ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 18px; font-weight: 600; color: #1f2937; font-size: 15px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <strong>Temperament</strong>
                                    <?php else: ?>
                                        <?= htmlspecialchars($result['test_title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 500; color: #0369a1;">Temperament</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f4f8; border-radius: 8px; font-size: 13px; font-weight: 500; color: #475569;">Oddiy test</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px; color: #6b7280; font-size: 13px;">
                                    <?php
                                    $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                                    if ($date) {
                                        echo htmlspecialchars(substr($date, 0, 16), ENT_QUOTES, 'UTF-8');
                                    }
                                    ?>
                                </td>
                                <td style="padding: 18px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <?php
                                        $tempType = $result['temperament']['type'] ?? '';
                                        $tempNames = [
                                            'Choleric' => 'Xolerik',
                                            'Sanguine' => 'Sangvinik',
                                            'Phlegmatic' => 'Flegmatik',
                                            'Melancholic' => 'Melanxolik',
                                        ];
                                        $tempName = $tempNames[$tempType] ?? $tempType;
                                        ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #e0e7ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #4338ca; margin-bottom: 4px;">
                                            <?= htmlspecialchars($tempName, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                            E: <?= htmlspecialchars((string)($result['scores']['E'] ?? 0), ENT_QUOTES, 'UTF-8') ?>,
                                            N: <?= htmlspecialchars((string)($result['scores']['N'] ?? 0), ENT_QUOTES, 'UTF-8') ?>,
                                            L: <?= htmlspecialchars((string)($result['scores']['L'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    <?php else: ?>
                                        <?php
                                        // Проверяем, есть ли расчетные результаты
                                        $calculatedScore = $result['calculated_score'] ?? null;
                                        $interpretation = $result['interpretation'] ?? null;
                                        $scales = $result['scales'] ?? null;
                                        $hasResults = ($calculatedScore !== null || ($interpretation && isset($interpretation['category']))) || ($scales !== null && !empty($scales));
                                        
                                        if ($hasResults):
                                            if ($scales !== null && !empty($scales)):
                                                // Для multi_scale показываем результаты по каждой шкале
                                        ?>
                                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                                    <?php foreach ($scales as $scaleName => $scaleResult): ?>
                                                        <div style="padding: 8px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                            <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                                                <?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?>:
                                                            </div>
                                                            <?php if (isset($scaleResult['score'])): ?>
                                                                <span style="display: inline-block; padding: 4px 8px; background: #f0f9ff; border-radius: 6px; font-size: 12px; font-weight: 700; color: #0369a1; margin-right: 6px;">
                                                                    <?= htmlspecialchars((string)$scaleResult['score'], ENT_QUOTES, 'UTF-8') ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php 
                                                            $scaleInterpretation = $scaleResult['interpretation'] ?? null;
                                                            if ($scaleInterpretation && isset($scaleInterpretation['category'])): 
                                                            ?>
                                                                <span style="display: inline-block; padding: 4px 8px; background: #e0e7ff; border-radius: 6px; font-size: 11px; font-weight: 600; color: #4338ca;">
                                                                    <?= htmlspecialchars($scaleInterpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <!-- Для обычных типов расчета -->
                                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                                    <?php if ($calculatedScore !== null): ?>
                                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 700; color: #0369a1;">
                                                            Ball: <?= htmlspecialchars((string)$calculatedScore, ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($interpretation && isset($interpretation['category'])): ?>
                                                        <span style="display: inline-block; padding: 6px 12px; background: #e0e7ff; border-radius: 8px; font-size: 12px; font-weight: 600; color: #4338ca;">
                                                            <?= htmlspecialchars($interpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                                <?= count($result['answers'] ?? []) ?> javob
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <a href="/admin/results/test?id=eysenck&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; display: inline-block;" onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">Batafsil</a>
                                    <?php else: ?>
                                        <a href="/admin/results/test?id=<?= urlencode((string)($result['test_id'] ?? 0)) ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; display: inline-block;" onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">Batafsil</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

