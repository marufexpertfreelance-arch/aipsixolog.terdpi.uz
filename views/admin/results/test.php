<?php
$pageTitle = ($test['title'] ?? 'Test') . ' - Natijalar';
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">📊 <?= htmlspecialchars($test['title'] ?? 'Test natijalari', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="admin-page-subtitle">Test natijalari va statistika</p>
</div>

<div class="admin-table-container" style="padding: 24px;">

        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Test: <?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <div style="display: flex; gap: 12px;">
                    <?php if (isset($test['id']) && $test['id'] !== 'eysenck' && $test['id'] !== 'lusher'): ?>
                        <a href="/admin/results/test-students?test_id=<?= urlencode((string)$test['id']) ?>" class="btn-secondary" style="padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Talabalar</a>
                        <a href="/admin/results/export?type=custom&test_id=<?= urlencode((string)$test['id']) ?>" class="btn-secondary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.3)';" onclick="this.style.opacity='0.7'; this.innerHTML='⏳ Yuklanmoqda...';">📥 Excel ga eksport</a>
                    <?php endif; ?>
                    <?php if (isset($test['id']) && $test['id'] === 'lusher'): ?>
                        <a href="/admin/results/export?type=lusher" class="btn-secondary" style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(147, 51, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(147, 51, 234, 0.3)';" onclick="this.style.opacity='0.7'; this.innerHTML='⏳ Yuklanmoqda...';">📥 Excel ga eksport</a>
                    <?php endif; ?>
                    <a href="/admin/results" class="btn-secondary" style="padding: 12px 24px; border-radius: 10px; font-weight: 500; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Barcha natijalar</a>
                </div>
            </div>

            <!-- Статистика -->
            <?php if (!empty($statistics)): ?>
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
                            <?php if (isset($statistics['total_students_in_groups']) && $statistics['total_students_in_groups'] > 0): ?>
                                <?php 
                                $totalStudents = $statistics['total_students_in_groups'];
                                $passedStudents = $statistics['unique_students'] ?? 0;
                                $notPassedStudents = $totalStudents - $passedStudents;
                                $passRate = $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100) : 0;
                                ?>
                                <!-- Jami talabalar -->
                                <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(102,126,234,0.15)'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)'; this.style.borderColor='#e5e7eb';">
                                    <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                                    <div style="position: relative; z-index: 1;">
                                        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(102,126,234,0.4);">
                                            <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">👥</span>
                                        </div>
                                        <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Jami talabalar</p>
                                        <p style="font-size: 42px; font-weight: 800; margin: 0; color: #1f2937; line-height: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                            <?= htmlspecialchars((string)$totalStudents, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <p style="font-size: 12px; color: #9ca3af; margin: 8px 0 0 0;">Guruhda</p>
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
                                            <?= htmlspecialchars((string)$passedStudents, ENT_QUOTES, 'UTF-8') ?>
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
                                            <?= htmlspecialchars((string)$notPassedStudents, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <div style="margin-top: 12px; width: 100%; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                            <div style="width: <?= 100 - $passRate ?>%; height: 100%; background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); border-radius: 3px; transition: width 0.5s ease; box-shadow: 0 2px 8px rgba(239,68,68,0.4);"></div>
                                        </div>
                                        <p style="font-size: 12px; color: #dc2626; margin: 6px 0 0 0; font-weight: 600;"><?= 100 - $passRate ?>%</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Jami o'tkazilgan -->
                            <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(245,158,11,0.15)'; this.style.borderColor='#fde68a';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)'; this.style.borderColor='#e5e7eb';">
                                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, rgba(217,119,6,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                                <div style="position: relative; z-index: 1;">
                                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(245,158,11,0.4);">
                                        <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">📝</span>
                                    </div>
                                    <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Jami o'tkazilgan</p>
                                    <p style="font-size: 42px; font-weight: 800; margin: 0; color: #d97706; line-height: 1;">
                                        <?= htmlspecialchars((string)($statistics['total_completions'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p style="font-size: 12px; color: #9ca3af; margin: 8px 0 0 0;">Testlar soni</p>
                                </div>
                            </div>
                            
                            <?php if (!empty($is_iq_test)): ?>
                                <!-- IQ Test карточки статистики -->
                                <?php if (isset($statistics['average_iq']) && $statistics['average_iq'] !== null): ?>
                                    <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border-radius: 16px; border: 2px solid #bae6fd; box-shadow: 0 4px 16px rgba(59,130,246,0.15); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(59,130,246,0.25)'; this.style.borderColor='#93c5fd';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(59,130,246,0.15)'; this.style.borderColor='#bae6fd';">
                                        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(59,130,246,0.15) 0%, rgba(37,99,235,0.15) 100%); border-radius: 50%; z-index: 0;"></div>
                                        <div style="position: relative; z-index: 1;">
                                            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(59,130,246,0.4);">
                                                <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">📈</span>
                                            </div>
                                            <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">O'rtacha IQ</p>
                                            <p style="font-size: 42px; font-weight: 800; margin: 0; color: #2563eb; line-height: 1;">
                                                <?= htmlspecialchars((string)$statistics['average_iq'], ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($statistics['min_iq']) && $statistics['min_iq'] !== null): ?>
                                    <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%); border-radius: 16px; border: 2px solid #fecaca; box-shadow: 0 4px 16px rgba(236,72,153,0.15); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(236,72,153,0.25)'; this.style.borderColor='#fda4af';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(236,72,153,0.15)'; this.style.borderColor='#fecaca';">
                                        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(236,72,153,0.15) 0%, rgba(219,39,119,0.15) 100%); border-radius: 50%; z-index: 0;"></div>
                                        <div style="position: relative; z-index: 1;">
                                            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(236,72,153,0.4);">
                                                <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">⬇️</span>
                                            </div>
                                            <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Min IQ</p>
                                            <p style="font-size: 42px; font-weight: 800; margin: 0; color: #db2777; line-height: 1;">
                                                <?= htmlspecialchars((string)$statistics['min_iq'], ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($statistics['max_iq']) && $statistics['max_iq'] !== null): ?>
                                    <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #fff7ed 100%); border-radius: 16px; border: 2px solid #fed7aa; box-shadow: 0 4px 16px rgba(249,115,22,0.15); transition: all 0.3s ease; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(249,115,22,0.25)'; this.style.borderColor='#fdba74';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(249,115,22,0.15)'; this.style.borderColor='#fed7aa';">
                                        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(249,115,22,0.15) 0%, rgba(234,88,12,0.15) 100%); border-radius: 50%; z-index: 0;"></div>
                                        <div style="position: relative; z-index: 1;">
                                            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(249,115,22,0.4);">
                                                <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">⬆️</span>
                                            </div>
                                            <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Max IQ</p>
                                            <p style="font-size: 42px; font-weight: 800; margin: 0; color: #ea580c; line-height: 1;">
                                                <?= htmlspecialchars((string)$statistics['max_iq'], ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (!isset($statistics['total_students_in_groups']) || $statistics['total_students_in_groups'] == 0): ?>
                                <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';">
                                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(102,126,234,0.4);">
                                        <span style="font-size: 28px;">👤</span>
                                    </div>
                                    <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Nozik talabalar</p>
                                    <p style="font-size: 42px; font-weight: 800; margin: 0; color: #1f2937; line-height: 1;">
                                        <?= htmlspecialchars((string)($statistics['unique_students'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($statistics['date_range']['first'])): ?>
                                <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';">
                                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(59,130,246,0.4);">
                                        <span style="font-size: 28px;">📅</span>
                                    </div>
                                    <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Birinchi test</p>
                                    <p style="font-size: 20px; font-weight: 700; margin: 0; color: #1f2937;">
                                        <?= htmlspecialchars(substr($statistics['date_range']['first'], 0, 10), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($statistics['date_range']['last'])): ?>
                                <div style="padding: 28px; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';">
                                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(139,92,246,0.4);">
                                        <span style="font-size: 28px;">📅</span>
                                    </div>
                                    <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Oxirgi test</p>
                                    <p style="font-size: 20px; font-weight: 700; margin: 0; color: #1f2937;">
                                        <?= htmlspecialchars(substr($statistics['date_range']['last'], 0, 10), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                    <?php if (isset($statistics['temperament_distribution']) && !empty($statistics['temperament_distribution'])): ?>
                        <div style="margin-top: 32px; padding-top: 32px; border-top: 2px solid #e5e7eb;">
                            <p style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 20px;">🎭</span>
                                Temperamentlar taqsimoti
                            </p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                <?php
                                $tempNames = [
                                    'Choleric' => 'Xolerik',
                                    'Sanguine' => 'Sangvinik',
                                    'Phlegmatic' => 'Flegmatik',
                                    'Melancholic' => 'Melanxolik',
                                ];
                                $tempColors = [
                                    'Choleric' => ['bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'text' => '#059669'],
                                    'Sanguine' => ['bg' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 'text' => '#2563eb'],
                                    'Phlegmatic' => ['bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'text' => '#d97706'],
                                    'Melancholic' => ['bg' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'text' => '#dc2626'],
                                ];
                                foreach ($statistics['temperament_distribution'] as $type => $count):
                                    $color = $tempColors[$type] ?? ['bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text' => '#667eea'];
                                ?>
                                    <div style="padding: 20px; background: white; border-radius: 12px; border: 2px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.3s ease; display: flex; align-items: center; gap: 16px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';">
                                        <div style="width: 48px; height: 48px; background: <?= $color['bg'] ?>; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); flex-shrink: 0;">
                                            <span style="font-size: 24px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">●</span>
                                        </div>
                                        <div style="flex: 1;">
                                            <p style="font-size: 13px; color: #6b7280; font-weight: 600; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($tempNames[$type] ?? $type, ENT_QUOTES, 'UTF-8') ?></p>
                                            <p style="font-size: 28px; font-weight: 800; margin: 0; color: <?= $color['text'] ?>; line-height: 1;">
                                                <?= htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- IQ Test категории и характеристики -->
            <?php if (!empty($is_iq_test) && !empty($statistics)): ?>
                <?php
                // Определяем категории IQ и их описания (соответствуют IqTestController)
                $iqCategoryNames = [
                    'Genial daraja' => 'Genial daraja',
                    'Juda yuqori' => 'Juda yuqori',
                    'Yuqori' => 'Yuqori',
                    'O\'rtadan yuqori' => 'O\'rtadan yuqori',
                    'O\'rtacha' => 'O\'rtacha',
                    'O\'rtadan past' => 'O\'rtadan past',
                    'Past' => 'Past',
                    'Juda past' => 'Juda past',
                ];
                
                $iqCategoryDescriptions = [
                    'Genial daraja' => 'Bu talabalar genial darajadagi intellektual qobiliyatga ega. Bu juda kam uchraydigan daraja va ular murakkab muammolarni hal qilishda ajoyib qobiliyatlarga ega.',
                    'Juda yuqori' => 'Bu talabalar juda yuqori darajadagi intellektual qobiliyatga ega. Ular murakkab muammolarni hal qilishda ajoyib qobiliyatlarga ega va yuqori intellektual faoliyatga qodir.',
                    'Yuqori' => 'Bu talabalar yuqori darajadagi intellektual qobiliyatga ega. Ular mantiqiy fikrlash va muammolarni hal qilishda yaxshi qobiliyatlarga ega.',
                    'O\'rtadan yuqori' => 'Bu talabalar o\'rtadan yuqori darajadagi intellektual qobiliyatga ega. Ular yaxshi mantiqiy fikrlash qobiliyatiga ega va murakkab vazifalarni bajarishga qodir.',
                    'O\'rtacha' => 'Bu talabalar o\'rtacha intellektual qobiliyatga ega. Bu normal va sog\'lom daraja hisoblanadi. Ko\'pchilik talabalar shu darajada bo\'ladi.',
                    'O\'rtadan past' => 'Bu talabalar o\'rtadan past darajadagi intellektual qobiliyatga ega. Ammo bu qobiliyatlarni rivojlantirish imkoniyatini ko\'rsatadi va qo\'shimcha mashqlar bilan yaxshilash mumkin.',
                    'Past' => 'Bu talabalar past darajadagi intellektual qobiliyatga ega. Ammo bu test natijasi faqat bir ko\'rsatkichdir va boshqa omillar ham muhimdir. Qo\'shimcha yordam va mashqlar bilan yaxshilash mumkin.',
                    'Juda past' => 'Bu talabalar juda past darajadagi intellektual qobiliyatga ega. Bu test natijasi faqat bir ko\'rsatkichdir va boshqa omillar ham muhimdir. Mutaxassislar bilan maslahatlashish tavsiya etiladi.',
                ];
                
                $categoryColors = [
                    'Genial daraja' => ['bg' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'text' => '#7c3aed'],
                    'Juda yuqori' => ['bg' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'text' => '#4f46e5'],
                    'Yuqori' => ['bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'text' => '#059669'],
                    'O\'rtadan yuqori' => ['bg' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 'text' => '#2563eb'],
                    'O\'rtacha' => ['bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'text' => '#d97706'],
                    'O\'rtadan past' => ['bg' => 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', 'text' => '#ea580c'],
                    'Past' => ['bg' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'text' => '#dc2626'],
                    'Juda past' => ['bg' => 'linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%)', 'text' => '#7f1d1d'],
                ];
                
                $categoryDistribution = $statistics['category_distribution'] ?? [];
                ?>
                <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(124,58,237,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(124,58,237,0.08) 0%, rgba(139,92,246,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                            <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(139,92,246,0.4);"></div>
                            <span style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 32px;">💡</span>
                                IQ kategoriyalari va xarakteristikalari
                            </span>
                        </h3>
                        
                        <?php if (!empty($categoryDistribution)): ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
                                <?php 
                                $totalIqResults = array_sum($categoryDistribution);
                                foreach ($categoryDistribution as $category => $count): 
                                    $percentage = $totalIqResults > 0 ? round(($count / $totalIqResults) * 100, 1) : 0;
                                    $color = $categoryColors[$category] ?? ['bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text' => '#667eea'];
                                    $description = $iqCategoryDescriptions[$category] ?? '';
                                ?>
                                    <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';">
                                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                                            <div style="width: 56px; height: 56px; background: <?= $color['bg'] ?>; border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 20px rgba(0,0,0,0.15); flex-shrink: 0;">
                                                <span style="font-size: 28px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">💡</span>
                                            </div>
                                            <div style="flex: 1;">
                                                <p style="font-size: 16px; font-weight: 700; margin: 0 0 4px 0; color: <?= $color['text'] ?>;">
                                                    <?= htmlspecialchars($iqCategoryNames[$category] ?? $category, ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                                <p style="font-size: 24px; font-weight: 800; margin: 0; color: #1f2937; line-height: 1;">
                                                    <?= htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8') ?> ta
                                                    <span style="font-size: 14px; color: #6b7280; font-weight: 600;">(<?= $percentage ?>%)</span>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if (!empty($description)): ?>
                                            <p style="font-size: 14px; line-height: 1.6; color: #6b7280; margin: 0; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                                                <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Графики статистики -->
            <?php if (!empty($category_statistics)): ?>
                <?php 
                // Проверяем, является ли тест процентным с цветами
                $calcConfig = $test['calculation_config'] ?? null;
                $calcType = $calcConfig['type'] ?? null;
                $isPercentageType = ($calcType === 'percentage');
                $hasColors = false;
                $colorMap = [];
                
                // Проверяем наличие цветов в вариантах ответов
                if ($isPercentageType && !empty($test['questions'])) {
                    foreach ($test['questions'] as $question) {
                        $options = $question['options'] ?? [];
                        foreach ($options as $option) {
                            $optionText = $option['text'] ?? '';
                            $optionColor = $option['color'] ?? null;
                            if (!empty($optionColor) && !empty($optionText)) {
                                $hasColors = true;
                                $colorMap[$optionText] = $optionColor;
                            }
                        }
                    }
                }
                ?>
                
                <?php if (!empty($category_statistics['is_multi_scale']) && !empty($category_statistics['scales'])): ?>
                    <!-- Графики для multi_scale (по каждой шкале отдельно) -->
                    <?php foreach ($category_statistics['scales'] as $scaleName => $scaleStats): ?>
                        <?php if (!empty($scaleStats['categories'])): ?>
                            <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                                <!-- Декоративный фон -->
                                <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; background: linear-gradient(135deg, rgba(102,126,234,0.08) 0%, rgba(118,75,162,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                                <div style="position: relative; z-index: 1;">
                                    <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                                        <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(102,126,234,0.4);"></div>
                                        <span style="display: flex; align-items: center; gap: 12px;">
                                            <span style="font-size: 32px;">📈</span>
                                            <?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?> - Kategoriyalar bo'yicha taqsimot
                                        </span>
                                    </h3>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 40px;">
                                        <!-- Pie Chart -->
                                        <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                            <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Pie Chart</h4>
                                            <canvas id="categoryPieChart_<?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?>" style="max-height: 400px;"></canvas>
                                        </div>
                                        <!-- Bar Chart -->
                                        <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                            <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Bar Chart</h4>
                                            <canvas id="categoryBarChart_<?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?>" style="max-height: 400px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php elseif (!empty($category_statistics['categories']) && ($category_statistics['is_percentage_type'] ?? false) && $hasColors): ?>
                    <!-- Статистика для процентных тестов с цветами -->
                    <?php
                    $categories = $category_statistics['categories'] ?? [];
                    $percentages = $category_statistics['percentages'] ?? [];
                    $totalCompletions = $category_statistics['total'] ?? count($results);
                    $answerInterpretations = $calcConfig['answer_interpretations'] ?? [];
                    ?>
                    <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(147,51,234,0.1) 0%, rgba(124,58,237,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(124,58,237,0.08) 0%, rgba(147,51,234,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                        
                        <div style="position: relative; z-index: 1;">
                            <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                                <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(147,51,234,0.4);"></div>
                                <span style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 32px;">🌈</span>
                                    Ranglar bo'yicha taqsimot
                                </span>
                            </h3>
                            
                            <!-- Статистика по цветам -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                                <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 24px;">📊</span>
                                        Tanlangan ranglar
                                    </h4>
                                    <?php if (!empty($categories)): ?>
                                        <div style="display: flex; flex-direction: column; gap: 12px;">
                                            <?php 
                                            // Сортируем по количеству выборов (по убыванию)
                                            arsort($categories);
                                            foreach ($categories as $answerText => $count): 
                                                $percentage = $percentages[$answerText] ?? 0;
                                                $color = $colorMap[$answerText] ?? '#667eea';
                                                $interpretation = $answerInterpretations[$answerText] ?? null;
                                                $categoryName = $interpretation['category'] ?? '';
                                                $description = $interpretation['description'] ?? '';
                                            ?>
                                                <div style="padding: 12px; background: #f9fafb; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#f3f4f6'; this.style.transform='translateX(4px)';" onmouseout="this.style.background='#f9fafb'; this.style.transform='translateX(0)';">
                                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                                        <div style="width: 40px; height: 40px; background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>; border-radius: 8px; border: 2px solid #e5e7eb; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                                                        <div style="flex: 1;">
                                                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: #1f2937;">
                                                                <?= htmlspecialchars($answerText, ENT_QUOTES, 'UTF-8') ?>
                                                            </p>
                                                            <?php if (!empty($categoryName)): ?>
                                                                <p style="font-size: 12px; color: #667eea; font-weight: 600; margin: 2px 0 0 0;">
                                                                    <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div style="width: 60px; text-align: right;">
                                                            <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                                                <div style="width: <?= min(100, max(0, (float)$percentage)) ?>%; height: 100%; background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>; border-radius: 4px; transition: width 0.5s ease;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                                                        <p style="font-size: 12px; color: #6b7280; margin: 0;">
                                                            <?= htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8') ?> marta (<?= number_format((float)$percentage, 1) ?>%)
                                                        </p>
                                                        <?php if (!empty($description)): ?>
                                                            <p style="font-size: 11px; color: #9ca3af; margin: 0; font-style: italic;">
                                                                <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p style="color: #6b7280; font-size: 14px;">Ma'lumotlar yo'q</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Графики распределения цветов -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 40px; margin-top: 32px;">
                                <!-- Pie Chart -->
                                <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Grafik ko'rinishida (Pie Chart)</h4>
                                    <canvas id="ranglarPieChart" style="max-height: 400px;"></canvas>
                                </div>
                                <!-- Bar Chart -->
                                <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Grafik ko'rinishida (Bar Chart)</h4>
                                    <canvas id="ranglarBarChart" style="max-height: 400px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (!empty($category_statistics['categories'])): ?>
                    <!-- Графики для обычных типов расчета -->
                    <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                        <!-- Декоративный фон -->
                        <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; background: linear-gradient(135deg, rgba(102,126,234,0.08) 0%, rgba(118,75,162,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                        <div style="position: relative; z-index: 1;">
                            <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                                <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(102,126,234,0.4);"></div>
                                <span style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 32px;">📈</span>
                                    Kategoriyalar bo'yicha taqsimot
                                </span>
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 40px;">
                                <!-- Pie Chart -->
                                <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Pie Chart</h4>
                                    <canvas id="categoryPieChart" style="max-height: 400px;"></canvas>
                                </div>
                                <!-- Bar Chart -->
                                <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                    <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; text-align: center;">Bar Chart</h4>
                                    <canvas id="categoryBarChart" style="max-height: 400px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Lüscher Test статистика -->
            <?php if (!empty($is_lusher_test) && !empty($statistics)): ?>
                <?php
                // Загружаем данные теста для получения цветов
                $testDataPath = dirname(__DIR__, 3) . '/data/lusher-test.json';
                $lusherTestData = null;
                $colorMap = [];
                if (file_exists($testDataPath)) {
                    $content = file_get_contents($testDataPath);
                    $lusherTestData = json_decode($content, true);
                    if ($lusherTestData && isset($lusherTestData['colors'])) {
                        foreach ($lusherTestData['colors'] as $color) {
                            $colorMap[$color['id']] = $color;
                        }
                    }
                }
                
                $preferredColors = $statistics['preferred_colors_distribution'] ?? [];
                $rejectedColors = $statistics['rejected_colors_distribution'] ?? [];
                $totalCompletions = $statistics['total_completions'] ?? 0;
                ?>
                <div style="padding: 40px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 20px; margin-bottom: 32px; border: 2px solid #e5e7eb; box-shadow: 0 8px 32px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(147,51,234,0.1) 0%, rgba(124,58,237,0.1) 100%); border-radius: 50%; z-index: 0;"></div>
                    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, rgba(124,58,237,0.08) 0%, rgba(147,51,234,0.08) 100%); border-radius: 50%; z-index: 0;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 800; color: #1f2937; display: flex; align-items: center; gap: 16px;">
                            <div style="width: 6px; height: 36px; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); border-radius: 3px; box-shadow: 0 4px 12px rgba(147,51,234,0.4);"></div>
                            <span style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 32px;">🌈</span>
                                Lyusher testi statistika
                            </span>
                        </h3>
                        
                        <!-- Статистика по цветам -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 32px;">
                            <!-- Предпочитаемые цвета -->
                            <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 24px;">✅</span>
                                    Yoqtirilgan ranglar
                                </h4>
                                <?php if (!empty($preferredColors)): ?>
                                    <div style="display: flex; flex-direction: column; gap: 12px;">
                                        <?php 
                                        arsort($preferredColors);
                                        foreach ($preferredColors as $colorId => $count): 
                                            $color = $colorMap[$colorId] ?? null;
                                            if (!$color) continue;
                                            $percentage = $totalCompletions > 0 ? round(($count / $totalCompletions) * 100, 1) : 0;
                                        ?>
                                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9fafb; border-radius: 10px;">
                                                <div style="width: 40px; height: 40px; background: <?= htmlspecialchars($color['code'] ?? '#FFFFFF', ENT_QUOTES, 'UTF-8') ?>; border-radius: 8px; border: 2px solid #e5e7eb; flex-shrink: 0;"></div>
                                                <div style="flex: 1;">
                                                    <p style="font-size: 14px; font-weight: 600; margin: 0; color: #1f2937;">
                                                        <?= htmlspecialchars($color['name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                    </p>
                                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">
                                                        <?= htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8') ?> marta (<?= $percentage ?>%)
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #6b7280; font-size: 14px;">Ma'lumotlar yo'q</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Отвергаемые цвета -->
                            <div style="padding: 24px; background: white; border-radius: 16px; border: 2px solid #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                                <h4 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 24px;">❌</span>
                                    Rad etilgan ranglar
                                </h4>
                                <?php if (!empty($rejectedColors)): ?>
                                    <div style="display: flex; flex-direction: column; gap: 12px;">
                                        <?php 
                                        arsort($rejectedColors);
                                        foreach ($rejectedColors as $colorId => $count): 
                                            $color = $colorMap[$colorId] ?? null;
                                            if (!$color) continue;
                                            $percentage = $totalCompletions > 0 ? round(($count / $totalCompletions) * 100, 1) : 0;
                                        ?>
                                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #fef2f2; border-radius: 10px;">
                                                <div style="width: 40px; height: 40px; background: <?= htmlspecialchars($color['code'] ?? '#FFFFFF', ENT_QUOTES, 'UTF-8') ?>; border-radius: 8px; border: 2px solid #e5e7eb; flex-shrink: 0;"></div>
                                                <div style="flex: 1;">
                                                    <p style="font-size: 14px; font-weight: 600; margin: 0; color: #1f2937;">
                                                        <?= htmlspecialchars($color['name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                    </p>
                                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">
                                                        <?= htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8') ?> marta (<?= $percentage ?>%)
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #6b7280; font-size: 14px;">Ma'lumotlar yo'q</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($results)): ?>
                <p class="muted" style="margin-top: 12px;">
                    Bu test uchun hali natijalar yo'q.
                </p>
            <?php else: ?>
                <div class="table-container" style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Talaba</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Talaba ID</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Sana</th>
                            <?php if (($test['type'] ?? '') === 'eysenck'): ?>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">E</th>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">N</th>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">L</th>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Temperament</th>
                            <?php elseif (!empty($is_aggression_test)): ?>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Umumiy tajovuz (TI)</th>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">To'g'ridan-to'g'ri (DI)</th>
                            <?php else: ?>
                                <?php
                                $hasCalculation = isset($test['calculation_config']) && !empty($test['calculation_config']);
                                $calcConfig = $test['calculation_config'] ?? null;
                                $calcType = $calcConfig['type'] ?? null;
                                $isMultiScale = ($calcType === 'multi_scale');
                                
                                if ($hasCalculation):
                                    if ($isMultiScale):
                                        // Для multi_scale добавляем колонки для каждой шкалы
                                        $scales = $calcConfig['scales'] ?? [];
                                        foreach ($scales as $scale):
                                            $scaleName = $scale['name'] ?? 'Noma\'lum';
                                        ?>
                                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?> - Ball</th>
                                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?> - Kategoriya</th>
                                        <?php
                                        endforeach;
                                    else:
                                        // Для обычных типов расчета - одна колонка
                                    ?>
                                        <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Ball</th>
                                        <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Kategoriya</th>
                                    <?php
                                    endif;
                                endif;
                                ?>
                                <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Javoblar</th>
                            <?php endif; ?>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $index => $result): ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 18px; font-weight: 600; color: #1f2937; font-size: 15px;"><?= htmlspecialchars($result['student_name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 14px;"><?= htmlspecialchars($result['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 13px;">
                                    <?php
                                    $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                                    if ($date) {
                                        echo htmlspecialchars(substr($date, 0, 16), ENT_QUOTES, 'UTF-8');
                                    }
                                    ?>
                                </td>
                                <?php if (($test['type'] ?? '') === 'eysenck'): ?>
                                    <td style="padding: 18px; text-align: center;">
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                            <?= htmlspecialchars((string)($result['scores']['E'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td style="padding: 18px; text-align: center;">
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                            <?= htmlspecialchars((string)($result['scores']['N'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td style="padding: 18px; text-align: center;">
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                            <?= htmlspecialchars((string)($result['scores']['L'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td style="padding: 18px;">
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
                                        <span style="display: inline-block; padding: 6px 12px; background: #e0e7ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #4338ca;">
                                            <?= htmlspecialchars($tempName, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                <?php elseif (!empty($is_aggression_test)): ?>
                                    <td style="padding: 18px;">
                                        <?php if (isset($result['indexes']['TI'])): ?>
                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 14px; font-weight: 700; color: #0369a1; text-align: center;">
                                                    <?= htmlspecialchars((string)($result['indexes']['TI']['score'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span style="font-size: 12px; color: #4b5563; text-align: center; font-weight: 500;">
                                                    <?= htmlspecialchars($result['indexes']['TI']['category'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 18px;">
                                        <?php if (isset($result['indexes']['DI'])): ?>
                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                <span style="display: inline-block; padding: 6px 12px; background: #fdf4ff; border-radius: 8px; font-size: 14px; font-weight: 700; color: #86198f; text-align: center;">
                                                    <?= htmlspecialchars((string)($result['indexes']['DI']['score'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span style="font-size: 12px; color: #4b5563; text-align: center; font-weight: 500;">
                                                    <?= htmlspecialchars($result['indexes']['DI']['category'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php else: ?>
                                    <?php
                                    $hasCalculation = isset($test['calculation_config']) && !empty($test['calculation_config']);
                                    $calcConfig = $test['calculation_config'] ?? null;
                                    $calcType = $calcConfig['type'] ?? null;
                                    $isMultiScale = ($calcType === 'multi_scale');
                                    
                                    if ($hasCalculation):
                                        if ($isMultiScale):
                                            // Для multi_scale показываем результаты по каждой шкале
                                            $scales = $calcConfig['scales'] ?? [];
                                            $resultScales = $result['scales'] ?? [];
                                            
                                            foreach ($scales as $scale):
                                                $scaleName = $scale['name'] ?? 'Noma\'lum';
                                                $scaleResult = $resultScales[$scaleName] ?? null;
                                            ?>
                                                <td style="padding: 18px; text-align: center;">
                                                    <?php if ($scaleResult && isset($scaleResult['score'])): ?>
                                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 15px; font-weight: 700; color: #0369a1;">
                                                            <?= htmlspecialchars((string)$scaleResult['score'], ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #9ca3af;">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 18px;">
                                                    <?php 
                                                    $scaleInterpretation = $scaleResult['interpretation'] ?? null;
                                                    if ($scaleInterpretation && isset($scaleInterpretation['category'])): 
                                                    ?>
                                                        <span style="display: inline-block; padding: 6px 12px; background: #e0e7ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #4338ca;">
                                                            <?= htmlspecialchars($scaleInterpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #9ca3af;">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php
                                            endforeach;
                                        else:
                                            // Для обычных типов расчета
                                            $calculatedScore = $result['calculated_score'] ?? null;
                                            $interpretation = $result['interpretation'] ?? null;
                                        ?>
                                            <td style="padding: 18px; text-align: center;">
                                                <?php if ($calculatedScore !== null): ?>
                                                    <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 15px; font-weight: 700; color: #0369a1;">
                                                        <?= htmlspecialchars((string)$calculatedScore, ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #9ca3af;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 18px;">
                                                <?php if ($interpretation && isset($interpretation['category'])): ?>
                                                    <span style="display: inline-block; padding: 6px 12px; background: #e0e7ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #4338ca;">
                                                        <?= htmlspecialchars($interpretation['category'], ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #9ca3af;">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                    <td style="padding: 18px;">
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                            <?= count($result['answers'] ?? []) ?> javob
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td style="padding: 18px;">
                                    <a href="/admin/results/test?id=<?= urlencode((string)($test['id'] ?? 0)) ?>&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; display: inline-block;" onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">Batafsil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>


    <?php if (!empty($category_statistics)): ?>
    <script>
        // Улучшенные цвета для категорий с градиентами
        const colorPalettes = {
            tashvish: [
                { bg: 'rgba(59, 130, 246, 0.85)', border: 'rgba(59, 130, 246, 1)', gradient: ['rgba(59, 130, 246, 0.9)', 'rgba(37, 99, 235, 0.9)'] }, // Синий - Norma
                { bg: 'rgba(16, 185, 129, 0.85)', border: 'rgba(16, 185, 129, 1)', gradient: ['rgba(16, 185, 129, 0.9)', 'rgba(5, 150, 105, 0.9)'] }, // Зеленый - Yengil
                { bg: 'rgba(245, 158, 11, 0.85)', border: 'rgba(245, 158, 11, 1)', gradient: ['rgba(245, 158, 11, 0.9)', 'rgba(217, 119, 6, 0.9)'] }, // Оранжевый - O'rtacha
                { bg: 'rgba(239, 68, 68, 0.85)', border: 'rgba(239, 68, 68, 1)', gradient: ['rgba(239, 68, 68, 0.9)', 'rgba(220, 38, 38, 0.9)'] }, // Красный - Og'ir
            ],
            depressiya: [
                { bg: 'rgba(139, 92, 246, 0.85)', border: 'rgba(139, 92, 246, 1)', gradient: ['rgba(139, 92, 246, 0.9)', 'rgba(124, 58, 237, 0.9)'] }, // Фиолетовый - Norma
                { bg: 'rgba(16, 185, 129, 0.85)', border: 'rgba(16, 185, 129, 1)', gradient: ['rgba(16, 185, 129, 0.9)', 'rgba(5, 150, 105, 0.9)'] }, // Зеленый - Yengil
                { bg: 'rgba(245, 158, 11, 0.85)', border: 'rgba(245, 158, 11, 1)', gradient: ['rgba(245, 158, 11, 0.9)', 'rgba(217, 119, 6, 0.9)'] }, // Оранжевый - O'rtacha
                { bg: 'rgba(239, 68, 68, 0.85)', border: 'rgba(239, 68, 68, 1)', gradient: ['rgba(239, 68, 68, 0.9)', 'rgba(220, 38, 38, 0.9)'] }, // Красный - Og'ir
            ],
            default: [
                { bg: 'rgba(102, 126, 234, 0.85)', border: 'rgba(102, 126, 234, 1)', gradient: ['rgba(102, 126, 234, 0.9)', 'rgba(118, 75, 162, 0.9)'] },
                { bg: 'rgba(118, 75, 162, 0.85)', border: 'rgba(118, 75, 162, 1)', gradient: ['rgba(118, 75, 162, 0.9)', 'rgba(102, 126, 234, 0.9)'] },
                { bg: 'rgba(240, 147, 251, 0.85)', border: 'rgba(240, 147, 251, 1)', gradient: ['rgba(240, 147, 251, 0.9)', 'rgba(79, 172, 254, 0.9)'] },
                { bg: 'rgba(79, 172, 254, 0.85)', border: 'rgba(79, 172, 254, 1)', gradient: ['rgba(79, 172, 254, 0.9)', 'rgba(0, 242, 254, 0.9)'] },
                { bg: 'rgba(0, 242, 254, 0.85)', border: 'rgba(0, 242, 254, 1)', gradient: ['rgba(0, 242, 254, 0.9)', 'rgba(16, 185, 129, 0.9)'] },
                { bg: 'rgba(16, 185, 129, 0.85)', border: 'rgba(16, 185, 129, 1)', gradient: ['rgba(16, 185, 129, 0.9)', 'rgba(245, 158, 11, 0.9)'] },
                { bg: 'rgba(245, 158, 11, 0.85)', border: 'rgba(245, 158, 11, 1)', gradient: ['rgba(245, 158, 11, 0.9)', 'rgba(239, 68, 68, 0.9)'] },
                { bg: 'rgba(239, 68, 68, 0.85)', border: 'rgba(239, 68, 68, 1)', gradient: ['rgba(239, 68, 68, 0.9)', 'rgba(220, 38, 38, 0.9)'] },
            ]
        };
        
        // Функция для получения цветов по имени шкалы
        function getColorsForScale(scaleName) {
            const name = scaleName.toLowerCase();
            if (name.includes('tashvish') || name.includes('тревог')) {
                return colorPalettes.tashvish;
            } else if (name.includes('depressiya') || name.includes('депресс')) {
                return colorPalettes.depressiya;
            }
            return colorPalettes.default;
        }
        
        <?php if (!empty($category_statistics['is_multi_scale']) && !empty($category_statistics['scales'])): ?>
            // Графики для multi_scale (по каждой шкале отдельно)
            const scaleStatistics = <?= json_encode($category_statistics['scales'], JSON_UNESCAPED_UNICODE) ?>;
            
            Object.keys(scaleStatistics).forEach(function(scaleName) {
                const scaleStats = scaleStatistics[scaleName];
                const categoryData = scaleStats.categories || {};
                const categoryPercentages = scaleStats.percentages || {};
                
                const labels = Object.keys(categoryData);
                const values = Object.values(categoryData);
                const scaleColors = getColorsForScale(scaleName);
                const bgColors = scaleColors.slice(0, labels.length).map(c => c.bg);
                const borderColors = scaleColors.slice(0, labels.length).map(c => c.border);
                
                // Pie Chart
                const pieCtx = document.getElementById('categoryPieChart_' + scaleName);
                if (pieCtx) {
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: bgColors,
                                borderColor: borderColors,
                                borderWidth: 3,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 14,
                                            weight: '700',
                                            family: "'Inter', sans-serif"
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        color: '#374151'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 16,
                                    titleFont: {
                                        size: 16,
                                        weight: '700'
                                    },
                                    bodyFont: {
                                        size: 14,
                                        weight: '600'
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 12,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const percentage = categoryPercentages[context.label] || 0;
                                            return label + ': ' + value + ' talaba (' + percentage + '%)';
                                        },
                                        title: function(context) {
                                            return 'Kategoriya: ' + context[0].label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Bar Chart
                const barCtx = document.getElementById('categoryBarChart_' + scaleName);
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Talabalar soni',
                                data: values,
                                backgroundColor: bgColors,
                                borderColor: borderColors,
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 16,
                                    titleFont: {
                                        size: 16,
                                        weight: '700'
                                    },
                                    bodyFont: {
                                        size: 14,
                                        weight: '600'
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 12,
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y || 0;
                                            const percentage = categoryPercentages[context.label] || 0;
                                            return value + ' talaba (' + percentage + '%)';
                                        },
                                        title: function(context) {
                                            return 'Kategoriya: ' + context[0].label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        color: '#6b7280'
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        color: '#6b7280'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            });
        <?php elseif (!empty($category_statistics['categories']) && ($category_statistics['is_percentage_type'] ?? false) && $hasColors): ?>
            // Графики для процентных тестов с цветами
            const ranglarCategoryData = <?= json_encode($category_statistics['categories'], JSON_UNESCAPED_UNICODE) ?>;
            const ranglarPercentages = <?= json_encode($category_statistics['percentages'], JSON_UNESCAPED_UNICODE) ?>;
            const ranglarColorMap = <?= json_encode($colorMap, JSON_UNESCAPED_UNICODE) ?>;
            
            const ranglarLabels = Object.keys(ranglarCategoryData);
            const ranglarValues = Object.values(ranglarCategoryData);
            const ranglarBgColors = ranglarLabels.map(label => ranglarColorMap[label] || '#667eea');
            const ranglarBorderColors = ranglarLabels.map(label => ranglarColorMap[label] || '#667eea');
            
            // Pie Chart для цветов
            const ranglarPieCtx = document.getElementById('ranglarPieChart');
            if (ranglarPieCtx) {
                new Chart(ranglarPieCtx, {
                    type: 'pie',
                    data: {
                        labels: ranglarLabels,
                        datasets: [{
                            data: ranglarValues,
                            backgroundColor: ranglarBgColors,
                            borderColor: ranglarBorderColors,
                            borderWidth: 3,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 14,
                                        weight: '700',
                                        family: "'Inter', sans-serif"
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#374151',
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                const percentage = ranglarPercentages[label] || 0;
                                                const color = ranglarBgColors[i];
                                                return {
                                                    text: label + ' (' + percentage.toFixed(1) + '%)',
                                                    fillStyle: color,
                                                    strokeStyle: color,
                                                    lineWidth: 3,
                                                    hidden: false,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 16,
                                titleFont: {
                                    size: 16,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const percentage = ranglarPercentages[label] || 0;
                                        return label + ': ' + value + ' marta (' + percentage.toFixed(1) + '%)';
                                    },
                                    title: function(context) {
                                        return 'Rang: ' + context[0].label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Bar Chart для цветов
            const ranglarBarCtx = document.getElementById('ranglarBarChart');
            if (ranglarBarCtx) {
                new Chart(ranglarBarCtx, {
                    type: 'bar',
                    data: {
                        labels: ranglarLabels,
                        datasets: [{
                            label: 'Tanlangan soni',
                            data: ranglarValues,
                            backgroundColor: ranglarBgColors,
                            borderColor: ranglarBorderColors,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 16,
                                titleFont: {
                                    size: 16,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y || 0;
                                        const percentage = ranglarPercentages[context.label] || 0;
                                        return value + ' marta (' + percentage.toFixed(1) + '%)';
                                    },
                                    title: function(context) {
                                        return 'Rang: ' + context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#6b7280'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#6b7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        <?php elseif (!empty($category_statistics['categories'])): ?>
            // Графики для обычных типов расчета
            const categoryData = <?= json_encode($category_statistics['categories'], JSON_UNESCAPED_UNICODE) ?>;
            const categoryPercentages = <?= json_encode($category_statistics['percentages'], JSON_UNESCAPED_UNICODE) ?>;
            
            const labels = Object.keys(categoryData);
            const values = Object.values(categoryData);
            const defaultColors = colorPalettes.default;
            const bgColors = defaultColors.slice(0, labels.length).map(c => c.bg);
            const borderColors = defaultColors.slice(0, labels.length).map(c => c.border);
            
            // Pie Chart
            const pieCtx = document.getElementById('categoryPieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: bgColors,
                            borderColor: borderColors,
                            borderWidth: 3,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 14,
                                        weight: '700',
                                        family: "'Inter', sans-serif"
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#374151'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 16,
                                titleFont: {
                                    size: 16,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const percentage = categoryPercentages[context.label] || 0;
                                        return label + ': ' + value + ' talaba (' + percentage + '%)';
                                    },
                                    title: function(context) {
                                        return 'Kategoriya: ' + context[0].label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Bar Chart
            const barCtx = document.getElementById('categoryBarChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Talabalar soni',
                            data: values,
                            backgroundColor: bgColors,
                            borderColor: borderColors,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 16,
                                titleFont: {
                                    size: 16,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y || 0;
                                        const percentage = categoryPercentages[context.label] || 0;
                                        return value + ' talaba (' + percentage + '%)';
                                    },
                                    title: function(context) {
                                        return 'Kategoriya: ' + context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#6b7280'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#6b7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        <?php endif; ?>
    </script>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

