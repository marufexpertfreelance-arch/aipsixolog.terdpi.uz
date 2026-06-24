<?php
$pageTitle = 'Talabalar ro\'yxati';
?>
<?php include __DIR__ . '/../admin/components/layout-header.php'; ?>

<!-- Page Header -->
<div class="admin-page-header">
    <h1 class="admin-page-title">👥 Talabalar ro'yxati</h1>
    <p class="admin-page-subtitle">Talabalar ma'lumotlarini ko'rish uchun</p>
</div>

<div class="admin-table-container" style="padding: 24px;">
        <div style="position: relative; z-index: 2; max-width: 1400px; margin: 0 auto;">
            </div>
            <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%); border-radius: 50%; z-index: 1;"></div>
            <div style="position: absolute; bottom: -80px; left: -80px; width: 300px; height: 300px; background: linear-gradient(135deg, rgba(118,75,162,0.08) 0%, rgba(102,126,234,0.08) 100%); border-radius: 50%; z-index: 1;"></div>
        </div>

        <div class="card" style="border-radius: 0; margin: 0; box-shadow: 0 0 0; background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
            <!-- Блок фильтров -->
            <div style="margin-bottom: 24px; padding: 20px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h3 style="margin: 0 0 16px 0; font-size: 18px; color: #374151;">🔍 Filtrlash</h3>
                <form method="get" action="/students" id="filter-form" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 14px; color: #6b7280; font-weight: 500;">
                            🏛️ Fakultet
                        </label>
                        <select name="faculty" id="faculty-filter" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            <option value="">Barcha fakultetlar</option>
                            <?php foreach ($faculties ?? [] as $faculty): ?>
                                <?php $facultyName = $faculty['name'] ?? ''; ?>
                                <option value="<?= htmlspecialchars($facultyName, ENT_QUOTES, 'UTF-8') ?>" 
                                        <?= (isset($_GET['faculty']) && $_GET['faculty'] === $facultyName) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($facultyName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 14px; color: #6b7280; font-weight: 500;">
                            📚 Yo'nalish
                        </label>
                        <select name="specialty" id="specialty-filter" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" 
                                <?= empty($selectedFaculty ?? null) ? 'disabled' : '' ?>>
                            <option value="">Barcha yo'nalishlar</option>
                            <?php foreach ($specialties ?? [] as $specialty): ?>
                                <?php $specialtyName = $specialty['name'] ?? ''; ?>
                                <option value="<?= htmlspecialchars($specialtyName, ENT_QUOTES, 'UTF-8') ?>" 
                                        <?= (isset($_GET['specialty']) && $_GET['specialty'] === $specialtyName) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($specialtyName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 14px; color: #6b7280; font-weight: 500;">
                            👥 Guruh
                        </label>
                        <select name="group" id="group-filter" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" 
                                <?= (empty($selectedFaculty ?? null) || empty($selectedSpecialty ?? null)) ? 'disabled' : '' ?>>
                            <option value="">Barcha guruhlar</option>
                            <?php foreach ($groups ?? [] as $group): ?>
                                <?php $groupName = $group['name'] ?? ''; ?>
                                <option value="<?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>" 
                                        <?= (isset($_GET['group']) && $_GET['group'] === $groupName) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">
                            Qidirish
                        </button>
                        <a href="/students" style="padding: 10px 20px; background: white; color: #6b7280; border: 1px solid #d1d5db; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-block;">
                            Tozalash
                        </a>
                    </div>
                </form>
            </div>

            <?php if (empty($students)): ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📋</div>
                    <p class="muted" style="font-size: 16px;">
                        <?php if (!empty($selectedFaculty) || !empty($selectedSpecialty) || !empty($selectedGroup)): ?>
                            Tanlangan filtrlarga mos talabalar topilmadi.
                        <?php else: ?>
                            Talabalar ro'yxati bo'sh.
                            <?php if (isset($_ENV['HEMIS_USE_MOCK']) && $_ENV['HEMIS_USE_MOCK'] === 'false'): ?>
                                <br>HEMIS API ga ulanishda xatolik bo'lishi mumkin. .env faylidagi sozlamalarni tekshiring.
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <p style="margin: 0; color: #6b7280; font-size: 14px;">
                        <?php if (!empty($selectedFaculty) || !empty($selectedSpecialty) || !empty($selectedGroup)): ?>
                            Topilgan talabalar: <strong><?= count($students) ?></strong>
                        <?php else: ?>
                            Jami talabalar: <strong><?= count($students) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <div style="overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">F.I.O</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Guruh</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Yo'nalish</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Fakultet</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Holat</th>
                            <th style="padding: 18px; text-align: left; color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Psixologik modul</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $index => $s): ?>
                            <tr style="border-bottom: 1px solid #f0f4f8; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 18px; color: #6b7280; font-size: 14px; font-weight: 500;"><?= htmlspecialchars((string)($s['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px;">
                                    <a href="/students/show?id=<?= urlencode((string)($s['id'] ?? '')) ?>" style="color: #667eea; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.color='#764ba2'; this.style.textDecoration='underline';" onmouseout="this.style.color='#667eea'; this.style.textDecoration='none';">
                                        <?= htmlspecialchars($s['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td style="padding: 18px; color: #6b7280; font-size: 14px;"><?= htmlspecialchars($s['group'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 14px;"><?= htmlspecialchars($s['specialty'] ?? 'Noma\'lum', ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="padding: 18px; color: #6b7280; font-size: 14px;"><?= htmlspecialchars($s['faculty'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php $status = $s['status'] ?? ''; ?>
                                    <?php
                                    $statusClass = 'badge';
                                    if (str_contains(mb_strtolower($status), 'отпуск') || str_contains(mb_strtolower($status), 'ta\'til')) {
                                        $statusClass .= ' warning';
                                    } elseif (str_contains(mb_strtolower($status), 'отчис') || str_contains(mb_strtolower($status), 'chetlat')) {
                                        $statusClass .= ' danger';
                                    } else {
                                        $statusClass .= ' success';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="muted small">
                                    Bu yerda test natijalari, maslahatga tashrif buyurish,
                                    individual hamrohlik rejasi va boshqalarni ko'rsatish mumkin.
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <a class="btn-secondary" href="/">← Bosh sahifaga</a>
            </div>
        </div>
    <script>
        (function() {
            const facultyFilter = document.getElementById('faculty-filter');
            const specialtyFilter = document.getElementById('specialty-filter');
            const groupFilter = document.getElementById('group-filter');
            
            if (!facultyFilter) return;
            
            // При изменении факультета загружаем направления
            facultyFilter.addEventListener('change', function() {
                const facultyName = this.value;
                const specialtySelect = document.getElementById('specialty-filter');
                const groupSelect = document.getElementById('group-filter');
                
                // Очищаем и отключаем направление и группу
                specialtySelect.innerHTML = '<option value="">Barcha yo\'nalishlar</option>';
                specialtySelect.disabled = !facultyName;
                
                groupSelect.innerHTML = '<option value="">Barcha guruhlar</option>';
                groupSelect.disabled = true;
                
                if (!facultyName) {
                    return;
                }
                
                // Загружаем направления
                specialtySelect.disabled = true;
                fetch(`/admin/api/specialties?faculty_name=${encodeURIComponent(facultyName)}`)
                    .then(response => response.json())
                    .then(data => {
                        specialtySelect.innerHTML = '<option value="">Barcha yo\'nalishlar</option>';
                        if (data.success && data.data && data.data.length > 0) {
                            data.data.forEach(specialty => {
                                const option = document.createElement('option');
                                option.value = specialty.name || specialty.id || '';
                                option.textContent = specialty.name || specialty.id || '';
                                specialtySelect.appendChild(option);
                            });
                        }
                        specialtySelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading specialties:', error);
                        specialtySelect.disabled = false;
                    });
            });
            
            // При изменении направления загружаем группы
            specialtyFilter.addEventListener('change', function() {
                const specialtyName = this.value;
                const facultyName = facultyFilter.value;
                const groupSelect = document.getElementById('group-filter');
                
                // Очищаем и отключаем группу
                groupSelect.innerHTML = '<option value="">Barcha guruhlar</option>';
                groupSelect.disabled = !specialtyName || !facultyName;
                
                if (!specialtyName || !facultyName) {
                    return;
                }
                
                // Загружаем группы
                groupSelect.disabled = true;
                fetch(`/admin/api/groups?specialty=${encodeURIComponent(specialtyName)}&faculty_name=${encodeURIComponent(facultyName)}`)
                    .then(response => response.json())
                    .then(data => {
                        groupSelect.innerHTML = '<option value="">Barcha guruhlar</option>';
                        if (data.success && data.data && data.data.length > 0) {
                            data.data.forEach(group => {
                                const option = document.createElement('option');
                                option.value = group.name || group.id || '';
                                option.textContent = group.name || group.id || '';
                                groupSelect.appendChild(option);
                            });
                        }
                        groupSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading groups:', error);
                        groupSelect.disabled = false;
                    });
            });
        })();
    </script>
</div>

<?php include __DIR__ . '/../admin/components/layout-footer.php'; ?>
