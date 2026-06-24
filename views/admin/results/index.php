<?php $pageTitle = 'Talabalar natijalari'; ?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="admin-page-title">📊 Talabalar natijalari</h1>
            <p class="admin-page-subtitle">Barcha test natijalarini ko'rish va filtrlash</p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="/admin/results/statistics" class="btn-action btn-action-info" style="padding: 12px 20px;">📈 Statistika</a>
            <a href="/admin/results/export" class="btn-action btn-action-success" style="padding: 12px 20px;">📥 Excel eksport</a>
        </div>
    </div>
</div>

<div class="admin-table-container" style="padding: 24px;">

            <!-- Улучшенные фильтры -->
            <form method="get" action="/admin/results" id="filter-form" style="margin-bottom: 32px; padding: 32px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #374151; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                        <span style="width: 3px; height: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                        Filtrlash parametrlari
                    </h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div class="form-group" style="position: relative;">
                        <label for="type" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Test turi</label>
                        <select id="type" name="type" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 18px; padding-right: 40px;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                            <option value="all" <?= ($filters['type'] ?? '') === 'all' ? 'selected' : '' ?>>Barchasi</option>
                            <option value="custom" <?= ($filters['type'] ?? '') === 'custom' ? 'selected' : '' ?>>Oddiy testlar</option>
                            <option value="eysenck" <?= ($filters['type'] ?? '') === 'eysenck' ? 'selected' : '' ?>>Temperament</option>
                            <option value="iq" <?= ($filters['type'] ?? '') === 'iq' ? 'selected' : '' ?>>IQ Test</option>
                            <option value="lusher" <?= ($filters['type'] ?? '') === 'lusher' ? 'selected' : '' ?>>Lyusher Testi</option>
                        </select>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="test_id" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Test</label>
                        <select id="test_id" name="test_id" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 18px; padding-right: 40px;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                            <option value="0">Barchasi</option>
                            <?php foreach ($tests as $test): ?>
                                <option value="<?= htmlspecialchars((string)($test['id'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= ($filters['test_id'] ?? 0) === ($test['id'] ?? 0) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="student_id" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Talaba</label>
                        <input type="text" id="student_id" name="student_id" list="students-list" 
                            value="<?= htmlspecialchars($filters['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Talaba ID yoki ismini kiriting..."
                            autocomplete="off"
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s;" 
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" 
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <datalist id="students-list">
                            <option value="">Barchasi</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= htmlspecialchars($student['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-label="<?= htmlspecialchars(($student['student_name'] ?? '') . ' (' . ($student['student_id'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(($student['student_name'] ?? '') . ' (' . ($student['student_id'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="group" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Guruh</label>
                        <input type="text" id="group" name="group" list="groups-list" 
                            value="<?= htmlspecialchars($filters['group'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Guruh nomini kiriting..."
                            autocomplete="off"
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s;" 
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" 
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <datalist id="groups-list">
                            <option value="">Barchasi</option>
                            <?php foreach ($groups ?? [] as $group): ?>
                                <option value="<?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="faculty" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Fakultet</label>
                        <input type="text" id="faculty" name="faculty" list="faculties-list" 
                            value="<?= htmlspecialchars($filters['faculty'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Fakultet nomini kiriting..."
                            autocomplete="off"
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s;" 
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" 
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <datalist id="faculties-list">
                            <option value="">Barchasi</option>
                            <?php foreach ($faculties ?? [] as $faculty): ?>
                                <option value="<?= htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="specialty" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Yo'nalish</label>
                        <input type="text" id="specialty" name="specialty" list="specialties-list" 
                            value="<?= htmlspecialchars($filters['specialty'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Yo'nalish nomini kiriting..."
                            autocomplete="off"
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s;" 
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" 
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <datalist id="specialties-list">
                            <option value="">Barchasi</option>
                            <?php foreach ($specialties ?? [] as $specialty): ?>
                                <option value="<?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="category" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Test kategoriyasi</label>
                        <select id="category" name="category" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 18px; padding-right: 40px;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                            <option value="">Barchasi</option>
                            <?php foreach ($categories ?? [] as $category): ?>
                                <option value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= ($filters['category'] ?? '') === $category ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="date_from" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Sanadan</label>
                        <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s; cursor: pointer;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="date_to" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">Sanagacha</label>
                        <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: white; transition: all 0.3s; cursor: pointer;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    </div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; padding-top: 20px; border-top: 2px solid #f0f4f8;">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)';">
                        🔍 Filtrlash
                    </button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='/admin/results';" style="cursor: pointer; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s; border: 2px solid #e5e7eb; background: white;" onmouseover="this.style.borderColor='#667eea'; this.style.color='#667eea';" onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                        🗑️ Tozalash
                    </button>
                    <?php
                    // Формируем URL для экспорта с текущими параметрами фильтрации
                    $exportParams = [];
                    if (!empty($filters['type']) && $filters['type'] !== 'all') {
                        $exportParams[] = 'type=' . urlencode($filters['type']);
                    }
                    if (!empty($filters['test_id']) && $filters['test_id'] > 0) {
                        $exportParams[] = 'test_id=' . urlencode((string)$filters['test_id']);
                    }
                    if (!empty($filters['student_id'])) {
                        $exportParams[] = 'student_id=' . urlencode($filters['student_id']);
                    }
                    if (!empty($filters['group'])) {
                        $exportParams[] = 'group=' . urlencode($filters['group']);
                    }
                    if (!empty($filters['faculty'])) {
                        $exportParams[] = 'faculty=' . urlencode($filters['faculty']);
                    }
                    if (!empty($filters['specialty'])) {
                        $exportParams[] = 'specialty=' . urlencode($filters['specialty']);
                    }
                    if (!empty($filters['date_from'])) {
                        $exportParams[] = 'date_from=' . urlencode($filters['date_from']);
                    }
                    if (!empty($filters['date_to'])) {
                        $exportParams[] = 'date_to=' . urlencode($filters['date_to']);
                    }
                    if (!empty($filters['category'])) {
                        $exportParams[] = 'category=' . urlencode($filters['category']);
                    }
                    $exportUrl = '/admin/results/export' . (!empty($exportParams) ? '?' . implode('&', $exportParams) : '');
                    ?>
                    <a href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-secondary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 15px; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.3)';" onclick="this.style.opacity='0.7'; this.innerHTML='<span>⏳ Yuklanmoqda...</span>';">
                        <span>📥</span>
                        <span>Excel ga eksport qilish</span>
                    </a>
                    <button type="button" class="btn-secondary" onclick="clearAllResults()" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; cursor: pointer; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(239, 68, 68, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(239, 68, 68, 0.3)';">
                        🗑️ Barcha natijalarni o'chirish
                    </button>
                </div>
            </form>

            <?php if (!empty($_GET['show_teachers'])): ?>
            <!-- Список преподавателей -->
            <div style="margin-bottom: 40px; padding: 32px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 12px;">
                        <span style="width: 4px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                        👨‍🏫 O'qituvchilar
                    </h2>
                </div>
                
                <?php if (empty($teachers ?? [])): ?>
                    <div style="text-align: center; padding: 40px 20px; background: #f9fafb; border-radius: 12px; border: 2px dashed #e5e7eb;">
                        <p style="color: #6b7280; font-size: 16px; margin: 0;">Hozircha o'qituvchilar yo'q</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <table style="width: 100%; border-collapse: collapse; background: white;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">ID</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">F.I.SH</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Login</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Kafedra</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Ro'yxatdan o'tgan</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; color: white;">Oxirgi kirish</th>
                                    <th style="padding: 14px 16px; text-align: center; font-size: 14px; font-weight: 600; color: white;">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers ?? [] as $teacher): ?>
                                    <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                        <td style="padding: 14px 16px; font-weight: 600; color: #111827;">
                                            <?= htmlspecialchars((string)($teacher['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td style="padding: 14px 16px; font-weight: 500; color: #1f2937;">
                                            <?= htmlspecialchars($teacher['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td style="padding: 14px 16px;">
                                            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 13px; color: #374151;">
                                                <?= htmlspecialchars($teacher['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </code>
                                        </td>
                                        <td style="padding: 14px 16px; color: #6b7280;">
                                            <?= htmlspecialchars($teacher['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td style="padding: 14px 16px; color: #6b7280; font-size: 13px;">
                                            <?php
                                            $registeredAt = $teacher['registered_at'] ?? '';
                                            if ($registeredAt) {
                                                echo date('d.m.Y H:i', strtotime($registeredAt));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td style="padding: 14px 16px; color: #6b7280; font-size: 13px;">
                                            <?php
                                            $lastLogin = $teacher['last_login'] ?? '';
                                            if ($lastLogin) {
                                                echo date('d.m.Y H:i', strtotime($lastLogin));
                                            } else {
                                                echo 'Hali kirmagan';
                                            }
                                            ?>
                                        </td>
                                        <td style="padding: 14px 16px; text-align: center;">
                                            <form method="POST" action="/admin/teachers/delete" style="display: inline;" onsubmit="return confirm('Haqiqatan ham bu o\'qituvchini o\'chirmoqchimisiz?');">
                                                <input type="hidden" name="teacher_id" value="<?= htmlspecialchars((string)($teacher['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" style="padding: 6px 14px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.background='#dc2626';" onmouseout="this.style.background='#ef4444';">O'chirish</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 16px; padding: 16px; background: #f9fafb; border-radius: 8px;">
                        <p style="color: #6b7280; font-size: 14px; margin: 0;">
                            <strong>Jami o'qituvchilar:</strong> <?= count($teachers ?? []) ?> | 
                            O'qituvchilar <a href="/teachers/register" style="color: #667eea; text-decoration: none; font-weight: 600;">/teachers/register</a> sahifasidan ro'yxatdan o'tishlari mumkin.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($results)): ?>
                <div style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-radius: 16px; border: 2px dashed #e5e7eb;">
                    <div style="font-size: 64px; margin-bottom: 20px;">📊</div>
                    <h3 style="font-size: 24px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Natijalar topilmadi</h3>
                    <p style="color: #6b7280; font-size: 16px; margin: 0; max-width: 500px; margin: 0 auto;">
                        Hozircha natijalar yo'q. Talabalar testlarni to'ldirganda bu yerda ko'rinadi.
                    </p>
                </div>
            <?php else: ?>
                <div class="table-container" style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Test</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Tip</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">F.I.SH</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Guruh/Kafedra</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Fakultet</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Yo'nalish</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Sana</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Natija</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $index => $result): ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 16px; color: #6b7280; font-size: 13px; font-weight: 500;"><?= htmlspecialchars((string)($result['test_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 16px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; color: #667eea;">
                                            🧠 <span>Temperament</span>
                                        </span>
                                    <?php elseif (($result['test_type'] ?? '') === 'iq'): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; color: #ec4899;">
                                            💡 <span>IQ Test</span>
                                        </span>
                                    <?php elseif (($result['test_type'] ?? '') === 'lusher'): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; color: #9333ea;">
                                            🎨 <span>Lyusher Testi</span>
                                        </span>
                                    <?php else: ?>
                                        <span style="font-weight: 600; color: #374151;"><?= htmlspecialchars($result['test_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px;">
                                    <?php $userType = $result['user_type'] ?? 'student'; ?>
                                    <?php if ($userType === 'teacher'): ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 8px; font-size: 13px; font-weight: 600; color: #065f46;">
                                            👨‍🏫 O'qituvchi
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 8px; font-size: 13px; font-weight: 600; color: #1e40af;">
                                            👨‍🎓 Talaba
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px; font-weight: 500; color: #1f2937;">
                                    <?= htmlspecialchars($result['user_name'] ?? $result['student_name'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="padding: 16px;">
                                    <?php if ($userType === 'teacher'): ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #d1fae5; border-radius: 8px; font-size: 13px; font-weight: 500; color: #065f46;">
                                            <?= htmlspecialchars($result['user_department'] ?? $result['student_faculty'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #f0f4f8; border-radius: 8px; font-size: 13px; font-weight: 500; color: #475569;">
                                            <?= htmlspecialchars($result['student_group'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px; color: #6b7280; font-size: 14px;">
                                    <?php if ($userType === 'teacher'): ?>
                                        <span style="color: #9ca3af; font-style: italic;">-</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($result['student_faculty'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px; color: #6b7280; font-size: 14px;">
                                    <?php if ($userType === 'teacher'): ?>
                                        <span style="color: #9ca3af; font-style: italic;">-</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($result['student_specialty'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px; color: #6b7280; font-size: 13px;">
                                    <?php
                                    $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                                    if ($date) {
                                        echo htmlspecialchars(substr($date, 0, 16), ENT_QUOTES, 'UTF-8');
                                    }
                                    ?>
                                </td>
                                <td style="padding: 16px;">
                                    <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                        <?php $temp = $result['temperament']['type'] ?? ''; ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 8px; font-size: 12px; font-weight: 600; color: #4f46e5;">
                                            <?= htmlspecialchars($temp, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php elseif (($result['test_type'] ?? '') === 'iq'): ?>
                                        <?php 
                                        $iqScore = $result['iq_score'] ?? null;
                                        $iqCategory = $result['category']['name'] ?? '';
                                        ?>
                                        <?php if ($iqScore !== null): ?>
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border-radius: 8px; font-size: 13px; font-weight: 700; color: #ec4899;">
                                                    IQ: <?= htmlspecialchars((string)$iqScore, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <?php if ($iqCategory): ?>
                                                    <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 8px; font-size: 12px; font-weight: 600; color: #4338ca;">
                                                        <?= htmlspecialchars($iqCategory, ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #6b7280; font-size: 13px;">Natija mavjud emas</span>
                                        <?php endif; ?>
                                    <?php elseif (($result['test_type'] ?? '') === 'lusher'): ?>
                                        <?php 
                                        $preferred = $result['interpretation']['preferred'] ?? [];
                                        $rejected = $result['interpretation']['rejected'] ?? [];
                                        ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); border-radius: 8px; font-size: 12px; font-weight: 600; color: #7e22ce;">
                                            <?= count($preferred) > 0 ? count($preferred) . ' ta yoqtirilgan' : '' ?> <?= count($rejected) > 0 ? count($rejected) . ' ta rad etilgan' : '' ?>
                                        </span>
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
                                            <span style="display: inline-block; padding: 6px 12px; background: #f0f9ff; border-radius: 8px; font-size: 12px; font-weight: 600; color: #0369a1;">
                                                <?= count($result['answers'] ?? []) ?> javob
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px;">
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <?php if (($result['test_type'] ?? '') === 'eysenck'): ?>
                                            <a href="/admin/results/test?id=eysenck&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Batafsil</a>
                                        <?php elseif (($result['test_type'] ?? '') === 'iq'): ?>
                                            <a href="/admin/results/test?id=iq&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Batafsil</a>
                                        <?php elseif (($result['test_type'] ?? '') === 'lusher'): ?>
                                            <a href="/admin/results/test?id=lusher&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Batafsil</a>
                                        <?php else: ?>
                                            <a href="/admin/results/test?id=<?= urlencode((string)($result['test_id'] ?? 0)) ?>&student_id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-info" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Batafsil</a>
                                        <?php endif; ?>
                                        <?php if (($result['user_type'] ?? 'student') === 'teacher'): ?>
                                            <a href="/admin/teachers" class="btn-action" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">O'qituvchi</a>
                                        <?php else: ?>
                                            <a href="/admin/results/student?id=<?= urlencode($result['student_id'] ?? '') ?>" class="btn-action btn-action-primary" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">Talaba</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>


    <script>
        function clearAllResults() {
            if (!confirm('Haqiqatan ham barcha talabalar natijalarini o\'chirmoqchimisiz? Bu amalni qaytarib bo\'lmaydi!')) {
                return;
            }
            
            // Создаем форму для отправки POST запроса
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/results/clear-students';
            
            // Добавляем CSRF токен, если он есть (опционально)
            // Можно добавить скрытое поле с токеном
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

