<?php
$pageTitle = 'Test ko\'rish';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">📋 <?= htmlspecialchars($test['title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="admin-page-subtitle">Test ma'lumotlari va savollar</p>
</div>

<div class="admin-table-container" style="padding: 24px;">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-success">
                    <strong>✓</strong> <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0 0 16px 0; display: flex; align-items: center; gap: 12px;">
                <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                <?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <?php if (!empty($test['description'])): ?>
                <p class="muted" style="margin-top: 12px; font-size: 16px; line-height: 1.6;"><?= nl2br(htmlspecialchars($test['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>

            <div style="margin-top: 24px; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <p class="small" style="margin: 4px 0;">
                    <strong>Kategoriya:</strong>
                    <?= htmlspecialchars($test['category'] ?? 'ko\'rsatilmagan', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="small" style="margin: 4px 0;">
                    <strong>Yaratilgan:</strong>
                    <?= htmlspecialchars($test['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <h3 style="margin-top: 32px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 12px;">Talabalar uchun havola</h3>
            <p class="muted small" style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
                Bu havolani talabalar bilan ulashishingiz mumkin (Telegram, HEMIS orqali va boshqalar).
            </p>
            <?php $relativeUrl = '/tests/take?id=' . urlencode((string)($test['id'] ?? '')); ?>
            <input type="text"
                   readonly
                   value="<?= htmlspecialchars($relativeUrl, ENT_QUOTES, 'UTF-8') ?>"
                   style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-family: monospace; background: #ffffff; font-size: 14px; transition: all 0.2s;"
                   onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';"
                   onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                   onclick="this.select(); document.execCommand('copy'); alert('Havola nusxalandi!');">

            <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Test savollari</h3>
            <?php if (empty($test['questions'])): ?>
                <p class="muted small" style="padding: 20px; background: #f9fafb; border-radius: 12px; color: #6b7280;">Hali hech qanday savol yo'q.</p>
            <?php else: ?>
                <div style="margin-top: 16px;">
                    <?php foreach ($test['questions'] as $qIdx => $q): ?>
                        <div style="margin-bottom: 24px; padding: 28px; padding-left: 90px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-2px)'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); z-index: 1;"></div>
                            <div style="position: absolute; top: 28px; left: 28px; width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2); z-index: 2; border: 2px solid rgba(255,255,255,0.2);">
                                <span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);"><?= $qIdx + 1 ?></span>
                            </div>
                            <div style="font-weight: 600; margin-bottom: 8px; font-size: 16px; color: #1e293b; line-height: 1.5;">
                                <?= htmlspecialchars($q['text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="muted small" style="margin-top: 12px; color: #64748b; font-size: 14px;">
                                <?php if (($q['type'] ?? 'text') === 'multiple_choice' && !empty($q['options'])): ?>
                                    <strong style="color: #475569;">Javob variantlari:</strong>
                                    <ul style="margin: 8px 0 0 20px; padding: 0; list-style: disc;">
                                        <?php foreach ($q['options'] as $option): ?>
                                            <li style="margin-bottom: 6px; color: #64748b;">
                                                <?= htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') ?>
                                                <?php if (!empty($option['is_other'])): ?>
                                                    <span style="color: #667eea; font-size: 12px; font-weight: 600;">(Boshqa)</span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php elseif (($q['type'] ?? 'text') === 'scale'): ?>
                                    <strong style="color: #475569;">Turi:</strong> <span style="padding: 4px 10px; background: #f0f9ff; border-radius: 6px; color: #0369a1; font-weight: 600;">Shkala 1–5</span>
                                <?php else: ?>
                                    <strong style="color: #475569;">Turi:</strong> <span style="padding: 4px 10px; background: #f0fdf4; border-radius: 6px; color: #059669; font-weight: 600;">Erkin javob</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($allowedGroups) && !empty($allowedGroups)): ?>
                <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Tanlangan guruhlar</h3>
                <div style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <p style="margin: 0; color: #1f2937; font-size: 15px; font-weight: 500;">
                        <strong style="color: #667eea;">Guruhlar:</strong> <?= htmlspecialchars(implode(', ', $allowedGroups), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            <?php else: ?>
                <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Guruhlar</h3>
                <div style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <p style="margin: 0; color: #6b7280; font-size: 15px;">
                        Test barcha talabalar uchun ochiq (guruhlar tanlanmagan).
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($allowedTeachers)): ?>
                <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Tanlangan o'qituvchilar</h3>
                <div style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <?php
                    // Получаем имена преподавателей
                    $teacherNames = [];
                    foreach ($allowedTeachers as $teacherId) {
                        foreach ($allTeachers as $teacher) {
                            if ((int)($teacher['id'] ?? 0) === $teacherId) {
                                $teacherNames[] = $teacher['full_name'] ?? 'Noma\'lum';
                                break;
                            }
                        }
                    }
                    ?>
                    <p style="margin: 0; color: #1f2937; font-size: 15px; font-weight: 500;">
                        <strong style="color: #10b981;">O'qituvchilar:</strong> <?= htmlspecialchars(implode(', ', $teacherNames), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            <?php else: ?>
                <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">O'qituvchilar</h3>
                <div style="margin-bottom: 20px; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <p style="margin: 0; color: #6b7280; font-size: 15px;">
                        Test o'qituvchilarga tayinlanmagan.
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($totalStudents) && $totalStudents > 0): ?>
                <h3 style="margin-top: 32px; margin-bottom: 20px;">📊 Umumiy statistika</h3>
                <div style="margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 20px;">
                        <div style="padding: 20px; background: white; border-radius: 10px; border: 2px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    👥
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #111827; line-height: 1;">
                                        <?= $totalStudents ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Jami o'tkazilgan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 20px; background: white; border-radius: 10px; border: 2px solid #10b981; box-shadow: 0 2px 4px rgba(16,185,129,0.1); transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    ✓
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #10b981; line-height: 1;">
                                        <?= $passedCount ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Testdan o'tgan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 20px; background: white; border-radius: 10px; border: 2px solid #ef4444; box-shadow: 0 2px 4px rgba(239,68,68,0.1); transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    ✗
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #ef4444; line-height: 1;">
                                        <?= $totalStudents - $passedCount ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Testdan o'tmagan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 20px; background: white; border-radius: 10px; border: 2px solid #667eea; box-shadow: 0 2px 4px rgba(102,126,234,0.1); transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    %
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #667eea; line-height: 1;">
                                        <?= $totalStudents > 0 ? round(($passedCount / $totalStudents) * 100, 1) : 0 ?>%
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Foiz
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($notPassedStudents)): ?>
                        <div style="margin-top: 24px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                            <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #111827; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 32px; height: 32px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">⚠️</span>
                                Testdan o'tmagan talabalar (<?= count($notPassedStudents) ?>)
                            </h4>
                            <div style="background: white; border-radius: 10px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">F.I.O</th>
                                                <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Guruh</th>
                                                <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Fakultet</th>
                                                <th style="padding: 14px 16px; text-align: center; font-size: 14px; font-weight: 600; color: white;">Amallar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($notPassedStudents as $index => $student): ?>
                                                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s ease;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                                    <td style="padding: 12px 16px; font-size: 14px; color: #374151; font-weight: 500;">
                                                        <?= htmlspecialchars($student['name'] ?? $student['full_name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                    </td>
                                                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">
                                                        <span style="display: inline-block; padding: 4px 10px; background: #f3f4f6; border-radius: 6px; font-weight: 500;">
                                                            <?= htmlspecialchars($student['group'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">
                                                        <?= htmlspecialchars($student['faculty'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                    </td>
                                                    <td style="padding: 12px 16px; text-align: center;">
                                                        <a href="/students/show?id=<?= urlencode((string)($student['id'] ?? $student['student_id'] ?? '')) ?>" 
                                                           style="display: inline-block; padding: 6px 14px; background: #667eea; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s ease;"
                                                           onmouseover="this.style.background='#5568d3'" 
                                                           onmouseout="this.style.background='#667eea'">
                                                            Talaba
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($allowedTeachers) && $totalTeachers > 0): ?>
                <!-- Статистика по преподавателям -->
                <div style="margin-top: 40px; padding-top: 32px; border-top: 2px solid #e5e7eb;">
                    <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 24px; color: #111827; display: flex; align-items: center; gap: 12px;">
                        <span style="width: 4px; height: 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 2px;"></span>
                        👨‍🏫 O'qituvchilar statistika
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
                        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: 16px; padding: 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    👥
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #10b981; line-height: 1;">
                                        <?= $totalTeachers ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Jami o'qituvchilar
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: 16px; padding: 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    ✓
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #10b981; line-height: 1;">
                                        <?= $passedTeachersCount ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Testdan o'tgan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #fbbf24; border-radius: 16px; padding: 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    ✗
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #d97706; line-height: 1;">
                                        <?= count($notPassedTeachers) ?>
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Testdan o'tmagan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: 16px; padding: 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                    %
                                </div>
                                <div>
                                    <div style="font-size: 28px; font-weight: 700; color: #10b981; line-height: 1;">
                                        <?= $totalTeachers > 0 ? round(($passedTeachersCount / $totalTeachers) * 100, 1) : 0 ?>%
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280; margin-top: 4px; font-weight: 500;">
                                        Foiz
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($notPassedTeachers)): ?>
                        <div style="margin-top: 24px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                            <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #111827; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 32px; height: 32px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">⚠️</span>
                                Testdan o'tmagan o'qituvchilar (<?= count($notPassedTeachers) ?>)
                            </h4>
                            <div style="background: white; border-radius: 10px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">F.I.SH</th>
                                                <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Kafedra</th>
                                                <th style="padding: 14px 16px; text-align: center; font-size: 14px; font-weight: 600; color: white;">Amallar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($notPassedTeachers as $teacher): ?>
                                                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s ease;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                                    <td style="padding: 12px 16px; font-size: 14px; color: #374151; font-weight: 500;">
                                                        <?= htmlspecialchars($teacher['full_name'], ENT_QUOTES, 'UTF-8') ?>
                                                    </td>
                                                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">
                                                        <span style="display: inline-block; padding: 4px 10px; background: #f3f4f6; border-radius: 6px; font-weight: 500;">
                                                            <?= htmlspecialchars($teacher['department'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px 16px; text-align: center;">
                                                        <a href="/admin/teachers" 
                                                           style="display: inline-block; padding: 6px 14px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s ease;"
                                                           onmouseover="this.style.background='#059669'" 
                                                           onmouseout="this.style.background='#10b981'">
                                                            O'qituvchi
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="margin-top: 40px; padding-top: 32px; border-top: 2px solid #f0f4f8; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="/admin/tests" class="btn-secondary" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">← Barcha testlarga</a>
                <a href="/admin/tests/edit?id=<?= urlencode((string)($test['id'] ?? '')) ?>" class="btn-action btn-action-primary" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)';">✏️ Tahrirlash</a>
                <a href="/admin/tests/select-teachers?id=<?= urlencode((string)($test['id'] ?? '')) ?>" class="btn-action btn-action-success" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.3)';">👨‍🏫 O'qituvchilarga tayinlash</a>
                <a href="/admin/tests/delete?id=<?= urlencode((string)($test['id'] ?? '')) ?>" class="btn-action btn-action-danger" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(239, 68, 68, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(239, 68, 68, 0.3)';" onclick="return confirm('Testni o\'chirishni xohlaysizmi? Bu amalni qaytarib bo\'lmaydi.');">🗑️ O'chirish</a>
                <a href="/admin/tests/create" class="btn-secondary" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Yana bittasini yaratish</a>
            </div>
        </div>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>
