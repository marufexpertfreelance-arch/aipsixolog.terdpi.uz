<?php
$pageTitle = 'Batafsil tahlil';
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <h1 class="admin-page-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
        Batafsil tahlil
    </h1>
    <p class="admin-page-subtitle">Kunlik statistika va eng faol talabalar</p>
</div>

<div class="admin-table-container" style="padding: 24px;">

        <!-- Kunlik statistika -->
        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Oxirgi 30 kunlik statistika
                </h2>
            </div>
            <?php if (empty($daily_stats)): ?>
                <p class="muted" style="margin-top: 12px; text-align: center; padding: 40px; color: #6b7280;">
                    Hozircha ma'lumotlar yo'q.
                </p>
            <?php else: ?>
                <div style="margin-top: 20px; padding: 24px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <canvas id="dailyStatsChart" style="max-height: 400px;"></canvas>
                </div>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('dailyStatsChart');
                    if (!ctx) return;
                    
                    const dailyData = <?= json_encode($daily_stats, JSON_THROW_ON_ERROR) ?>;
                    const labels = Object.keys(dailyData);
                    const data = Object.values(dailyData);
                    
                    // Создаем градиент
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
                    gradient.addColorStop(0.5, 'rgba(118, 75, 162, 0.6)');
                    gradient.addColorStop(1, 'rgba(118, 75, 162, 0.1)');
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels.map(date => {
                                const d = new Date(date);
                                return d.toLocaleDateString('uz-UZ', { month: 'short', day: 'numeric' });
                            }),
                            datasets: [{
                                label: 'Testlar soni',
                                data: data,
                                backgroundColor: gradient,
                                borderColor: 'rgb(102, 126, 234)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(102, 126, 234)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointHoverBackgroundColor: 'rgb(118, 75, 162)',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 12,
                                    borderColor: 'rgba(102, 126, 234, 0.5)',
                                    borderWidth: 1,
                                    displayColors: false,
                                    titleFont: {
                                        size: 14,
                                        weight: '600'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    callbacks: {
                                        title: function(context) {
                                            return labels[context[0].dataIndex];
                                        },
                                        label: function(context) {
                                            return 'Testlar: ' + context.parsed.y + ' ta';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6b7280',
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        maxRotation: 45,
                                        minRotation: 45
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    border: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
            <?php endif; ?>
        </div>

        <!-- Топ студентов -->
        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #f0f4f8;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <span style="width: 4px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                    Eng faol talabalar
                </h2>
            </div>
            <?php if (empty($top_students)): ?>
                <p class="muted" style="margin-top: 12px; text-align: center; padding: 40px; color: #6b7280;">
                    Hozircha ma'lumotlar yo'q.
                </p>
            <?php else: ?>
                <div class="table-container" style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                №
                            </th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                Talaba
                            </th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Talaba ID
                            </th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                Testlar soni
                            </th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                Oxirgi test
                            </th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($top_students as $index => $student): ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 18px;">
                                    <?php if ($index < 3): ?>
                                        <?php
                                        $medals = ['🥇', '🥈', '🥉'];
                                        $colors = ['#fbbf24', '#94a3b8', '#cd7f32'];
                                        ?>
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: <?= $colors[$index] ?>; color: white; font-weight: 700; font-size: 14px;">
                                            <?= $index + 1 ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #6b7280; font-size: 14px; font-weight: 500;"><?= $index + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                            <?= strtoupper(substr($student['student_name'] ?? 'N', 0, 1)) ?>
                                        </div>
                                        <span style="font-weight: 600; color: #1f2937; font-size: 15px;">
                                            <?= htmlspecialchars($student['student_name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 18px; color: #6b7280; font-size: 14px; font-family: 'Courier New', monospace;">
                                    <?= htmlspecialchars($student['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding: 18px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 8px; font-size: 14px; font-weight: 600; color: #0369a1; border: 1px solid #bae6fd;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                        <?= htmlspecialchars((string)($student['tests_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td style="padding: 18px;">
                                    <span style="color: #6b7280; font-size: 13px; font-weight: 500;">
                                        <?php
                                        $lastDate = $student['last_test_date'] ?? '';
                                        if ($lastDate) {
                                            $date = new DateTime($lastDate);
                                            echo $date->format('d.m.Y');
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td style="padding: 18px;">
                                    <a href="/admin/results/student?id=<?= urlencode($student['student_id'] ?? '') ?>" 
                                       style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);" 
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)';" 
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Ko'rish
                                    </a>
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

