<?php
$pageTitle = 'Test talabalari';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">👥 Test talabalari</h1>
    <p class="admin-page-subtitle"><?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="admin-table-container" style="padding: 24px;">

        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Test: <?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <a href="/admin/results/test?id=<?= urlencode((string)($test['id'] ?? 0)) ?>" class="btn-secondary" style="padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(102,126,234,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102,126,234,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102,126,234,0.3)';">Test natijalari</a>
            </div>

            <!-- Статистика -->
            <?php 
            $passRate = $total_students > 0 ? round(($passed_count / $total_students) * 100) : 0;
            $notPassRate = $total_students > 0 ? round(($not_passed_count / $total_students) * 100) : 0;
            ?>
            <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                <!-- Декоративный фон -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(118,75,162,0.08) 0%, rgba(102,126,234,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                        <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(102,126,234,0.4);"></div>
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 32px;">📊</span>
                            Umumiy statistika
                        </span>
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                        <!-- Jami talabalar -->
                        <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(102,126,234,0.15)'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)'; this.style.borderColor='#e5e7eb';">
                            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(102,126,234,0.4);">
                                    <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">👥</span>
                                </div>
                                <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Jami talabalar</p>
                                <p style="font-size: 42px; font-weight: 800; margin: 0; color: #1f2937; line-height: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                    <?= htmlspecialchars((string)$total_students, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Test topshirgan -->
                        <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-radius: 16px; border: 2px solid #86efac; box-shadow: 0 4px 16px rgba(16,185,129,0.15); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(16,185,129,0.25)'; this.style.borderColor='#4ade80';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(16,185,129,0.15)'; this.style.borderColor='#86efac';">
                            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(5,150,105,0.15) 100%); border-radius: 50%; z-index: 0;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(16,185,129,0.4);">
                                    <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">✅</span>
                                </div>
                                <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Test topshirgan</p>
                                <p style="font-size: 42px; font-weight: 800; margin: 0; color: #059669; line-height: 1;">
                                    <?= htmlspecialchars((string)$passed_count, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <div style="margin-top: 12px; width: 100%; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                    <div style="width: <?= $passRate ?>%; height: 100%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); border-radius: 3px; transition: width 0.5s ease; box-shadow: 0 2px 8px rgba(16,185,129,0.4);"></div>
                                </div>
                                <p style="font-size: 12px; color: #059669; margin: 6px 0 0 0; font-weight: 600;"><?= $passRate ?>%</p>
                            </div>
                        </div>
                        
                        <!-- Test topshirmagan -->
                        <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%); border-radius: 16px; border: 2px solid #fca5a5; box-shadow: 0 4px 16px rgba(239,68,68,0.15); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(239,68,68,0.25)'; this.style.borderColor='#f87171';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(239,68,68,0.15)'; this.style.borderColor='#fca5a5';">
                            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(239,68,68,0.15) 0%, rgba(220,38,38,0.15) 100%); border-radius: 50%; z-index: 0;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(239,68,68,0.4);">
                                    <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">❌</span>
                                </div>
                                <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Test topshirmagan</p>
                                <p style="font-size: 42px; font-weight: 800; margin: 0; color: #dc2626; line-height: 1;">
                                    <?= htmlspecialchars((string)$not_passed_count, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <div style="margin-top: 12px; width: 100%; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                    <div style="width: <?= $notPassRate ?>%; height: 100%; background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); border-radius: 3px; transition: width 0.5s ease; box-shadow: 0 2px 8px rgba(239,68,68,0.4);"></div>
                                </div>
                                <p style="font-size: 12px; color: #dc2626; margin: 6px 0 0 0; font-weight: 600;"><?= $notPassRate ?>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($students)): ?>
                <div style="padding: 60px 40px; text-align: center; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-radius: 16px; border: 2px dashed #e5e7eb;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📭</div>
                    <p style="font-size: 18px; color: #6b7280; margin: 0; font-weight: 500;">
                        Bu test uchun guruhlar tanlanmagan yoki talabalar topilmadi.
                    </p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">№</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">F.I.O.</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Talaba ID</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Guruh</th>
                            <th style="padding: 18px; text-align: center; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Sana</th>
                            <th style="padding: 18px; text-align: center; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $index => $item): ?>
                            <?php 
                            $student = $item['student'];
                            $result = $item['result'];
                            $hasPassed = $item['has_passed'];
                            $studentName = $student['name'] ?? $student['full_name'] ?? 'Noma\'lum';
                            $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                            $studentGroup = $student['group'] ?? '';
                            ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='white';">
                                <td style="padding: 18px; color: #6b7280; font-weight: 600;"><?= htmlspecialchars((string)($index + 1), ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #1f2937; font-weight: 600;"><?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 13px;"><?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 13px;"><?= htmlspecialchars($studentGroup, ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; text-align: center;">
                                    <?php if ($hasPassed): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border-radius: 20px; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(16,185,129,0.2);">
                                            <span style="font-size: 16px;">✅</span>
                                            Test topshirgan
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border-radius: 20px; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(239,68,68,0.2);">
                                            <span style="font-size: 16px;">❌</span>
                                            Test topshirmagan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px; color: #6b7280; font-size: 13px;">
                                    <?php if ($hasPassed && $result): ?>
                                        <?php
                                        $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                                        if ($date) {
                                            echo htmlspecialchars(substr($date, 0, 16), ENT_QUOTES, 'UTF-8');
                                        } else {
                                            echo '<span style="color: #9ca3af;">-</span>';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span style="color: #9ca3af;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px; text-align: center;">
                                    <?php if ($hasPassed && $result): ?>
                                        <a href="/admin/results/test?id=<?= urlencode((string)($test['id'] ?? 0)) ?>&student_id=<?= urlencode($studentId) ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; box-shadow: 0 2px 8px rgba(59,130,246,0.3);" onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.4)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 8px rgba(59,130,246,0.3)';">Batafsil</a>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 13px;">-</span>
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

