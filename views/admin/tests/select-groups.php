<?php
$pageTitle = 'Guruhlarni tanlash';
$extraStyles = '
        .cascade-container {
            margin-top: 24px;
        }
        
        .faculty-item {
            margin-bottom: 16px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        
        .faculty-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }
        
        .faculty-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            user-select: none;
        }
        
        .faculty-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .specialties-container {
            margin-left: 32px;
            margin-top: 12px;
            padding-left: 16px;
            border-left: 2px solid #e5e7eb;
            display: none;
        }
        
        .specialties-container.active {
            display: block;
        }
        
        .specialty-item {
            margin-bottom: 12px;
            padding: 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .specialty-header {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
            user-select: none;
        }
        
        .specialty-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #764ba2;
        }
        
        .groups-container {
            margin-left: 28px;
            margin-top: 10px;
            padding-left: 14px;
            border-left: 2px solid #d1d5db;
            display: none;
        }
        
        .groups-container.active {
            display: block;
        }
        
        .groups-loading {
            color: #6b7280;
            font-size: 14px;
            padding: 12px;
            font-style: italic;
        }
        
        .groups-list {
            display: none !important;
        }
        
        .groups-list.active {
            display: block !important;
        }
        
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
            margin-top: 12px;
        }
        
        /* Для больших экранов - еще более компактное отображение */
        @media (min-width: 1400px) {
            .groups-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 6px;
            }
        }
        
        .group-card {
            position: relative;
            padding: 10px 8px;
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(10px);
        }
        
        .group-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25), 0 4px 10px rgba(102, 126, 234, 0.15);
            transform: translateY(-4px) scale(1.02);
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            border-width: 2.5px;
        }
        
        .group-card.selected {
            background: #ffffff;
            border-color: #667eea;
            border-width: 3px;
            color: #1f2937;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3), 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: scale(1.03);
        }
        
        .group-card.selected:hover {
            transform: scale(1.05) translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.35), 0 5px 15px rgba(102, 126, 234, 0.25);
        }
        
        .group-card.selected .group-name {
            color: #667eea;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        
        .group-card input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .group-name {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            line-height: 1.4;
            word-break: break-word;
            letter-spacing: -0.01em;
            transition: color 0.2s ease, font-weight 0.2s ease;
        }
        
        .group-card:hover .group-name {
            color: #111827;
            font-weight: 700;
        }
        
        .group-check-icon {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 24px;
            height: 24px;
            background: #667eea;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(102, 126, 234, 0.4), 0 1px 3px rgba(0, 0, 0, 0.2);
            font-weight: bold;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .group-card.selected .group-check-icon {
            display: flex;
            animation: checkAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .group-card.selected .group-check-icon:hover {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5), 0 2px 6px rgba(0, 0, 0, 0.25);
            background: #5568d3;
        }
        
        @keyframes checkAppear {
            0% {
                opacity: 0;
                transform: scale(0.3) rotate(-180deg);
            }
            40% {
                opacity: 0.8;
                transform: scale(1.2) rotate(10deg);
            }
            60% {
                transform: scale(0.95) rotate(-5deg);
            }
            80% {
                transform: scale(1.05) rotate(2deg);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }
        
        /* Плавное появление карточек при загрузке */
        .group-card.card-enter {
            animation: cardFadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .select-all-btn {
            margin-top: 8px;
            padding: 6px 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s ease;
        }
        
        .select-all-btn:hover {
            background: #5568d3;
        }
        
        .icon {
            font-size: 18px;
            display: inline-block;
            margin-right: 4px;
        }
        
        .empty-message {
            color: #6b7280;
            font-size: 14px;
            padding: 12px;
            font-style: italic;
        }
        
        /* Стили для поискового поля */
        .groups-search-container {
            position: relative;
            margin-bottom: 12px;
            display: none;
        }
        
        .groups-search-container.active {
            display: block;
        }
        
        .groups-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .groups-search-input {
            width: 100%;
            padding: 12px 40px 12px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .groups-search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .groups-search-icon {
            position: absolute;
            left: 14px;
            font-size: 18px;
            color: #9ca3af;
            pointer-events: none;
        }
        
        .groups-search-clear {
            position: absolute;
            right: 12px;
            width: 24px;
            height: 24px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #9ca3af;
            font-size: 18px;
            transition: all 0.2s ease;
        }
        
        .groups-search-clear:hover {
            background: #f3f4f6;
            color: #374151;
        }
        
        .groups-search-clear.visible {
            display: flex;
        }
        
        .groups-search-results {
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
        }
        
        .group-card.hidden {
            display: none !important;
        }
        
        /* Плавное появление карточек при загрузке */
        .group-card {
            animation: cardAppear 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .remove-group-btn:hover {
            background: #dc2626 !important;
            transform: scale(1.1);
        }

        .selected-group-tag {
            transition: all 0.2s ease;
        }

        .selected-group-tag:hover {
            border-color: #2563eb;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }
        
        /* Улучшенные пропорции для карточек */
        .group-card {
            aspect-ratio: 1.2 / 1;
            max-width: 100%;
        }
        
        .groups-no-results {
            display: none;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            font-style: italic;
        }
        
        .groups-no-results.visible {
            display: block;
        }
'; ?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">👥 Guruhlarni tanlash</h1>
    <p class="admin-page-subtitle">Test uchun guruhlarni tanlash</p>
</div>

<div class="admin-table-container" style="padding: 24px;">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>⚠️</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 24px; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h2 style="margin: 0 0 8px 0; font-size: 20px;"><?= htmlspecialchars($test['title'] ?? 'Test', ENT_QUOTES, 'UTF-8') ?></h2>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">
                    ID: <?= htmlspecialchars((string)($test['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    <?php if (!empty($test['category'])): ?>
                        | Kategoriya: <?= htmlspecialchars($test['category'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </p>
            </div>

            <h3 style="margin-bottom: 16px;">🏛️ Fakultet → 📚 Yo'nalish → 👥 Guruh</h3>
            <p class="muted small" style="margin-bottom: 20px;">
                Testni qaysi guruhlarga ochishni tanlang. Avval fakultetni, keyin yo'nalishni, so'ng guruhlarni tanlang.
                <br><small style="color: #10b981; font-size: 12px;">ℹ️ Ma'lumotlar avtomatik ravishda talabalar tizimga kirganda yangilanadi.</small>
            </p>

            <?php 
            $allowedGroups = $test['allowed_groups'] ?? [];
            $allowedFaculties = $test['allowed_faculties'] ?? [];
            
            // Получаем информацию о выбранных группах для отображения
            $selectedGroupsInfo = [];
            if (!empty($allowedGroups)) {
                $studentStorage = new \App\Services\StudentStorage();
                $allStudents = $studentStorage->getAll();
                
                foreach ($allowedGroups as $groupName) {
                    foreach ($allStudents as $student) {
                        $studentGroup = $student['group'] ?? null;
                        if ($studentGroup) {
                            // Нормализация для сравнения
                            $normalize = function($str) {
                                return mb_strtolower(trim((string)$str));
                            };
                            if ($normalize($studentGroup) === $normalize($groupName)) {
                                $faculty = $student['faculty'] ?? null;
                                $specialty = $student['specialty'] ?? null;
                                if ($faculty && $specialty) {
                                    if (!isset($selectedGroupsInfo[$faculty])) {
                                        $selectedGroupsInfo[$faculty] = [];
                                    }
                                    if (!isset($selectedGroupsInfo[$faculty][$specialty])) {
                                        $selectedGroupsInfo[$faculty][$specialty] = [];
                                    }
                                    if (!in_array($groupName, $selectedGroupsInfo[$faculty][$specialty], true)) {
                                        $selectedGroupsInfo[$faculty][$specialty][] = $groupName;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            ?>

            <?php if (!empty($allowedGroups)): ?>
                <div id="selected-groups-display" style="margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 12px; border: 2px solid #3b82f6;">
                    <h4 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                        <span>✅</span>
                        <span>Tanlangan guruhlar (<span id="selected-groups-count"><?= count($allowedGroups) ?></span>)</span>
                    </h4>
                    <div id="selected-groups-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($allowedGroups as $groupName): ?>
                            <span class="selected-group-tag" data-group-name="<?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px 8px 16px; background: white; border: 2px solid #3b82f6; border-radius: 8px; font-size: 14px; font-weight: 600; color: #1e40af;">
                                <span><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></span>
                                <button type="button" class="remove-group-btn" data-group-name="<?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>" style="display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; padding: 0; border: none; background: #ef4444; color: white; border-radius: 50%; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.2s ease; flex-shrink: 0;" title="Guruhni o'chirish">
                                    ×
                                </button>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($selectedGroupsInfo)): ?>
                        <p style="margin: 12px 0 0 0; font-size: 13px; color: #475569;">
                            <strong>ℹ️ Eslatma:</strong> Guruhlar quyidagi fakultet va yo'nalishlarga tegishli. 
                            Guruhlarni ko'rish uchun ularning fakultet va yo'nalishlarini tanlang.
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/tests/update-groups" id="groups-form">
                <input type="hidden" name="test_id" value="<?= htmlspecialchars((string)($test['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                <div style="margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; border: 2px solid #10b981;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 16px; font-weight: 600; color: #065f46;">
                        <input type="checkbox" id="open-for-all" name="open_for_all" value="1" style="width: 20px; height: 20px; cursor: pointer; accent-color: #10b981;" <?= ($test['open_for_all'] ?? (empty($allowedGroups) && empty($allowedFaculties))) ? 'checked' : '' ?>>
                        <span>🌐 Testni barcha talabalar uchun ochiq qilish</span>
                    </label>
                    <p style="margin: 8px 0 0 32px; font-size: 13px; color: #047857;">
                        Agar belgilansa, test barcha guruhlar va fakultetlar uchun ochiq bo'ladi. Guruhlarni tanlash shart emas.
                    </p>
                </div>

                <div id="cascade-selects" class="cascade-container" style="margin-bottom: 18px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                        <div>
                            <label style="display:block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">🏛️ Fakultet</label>
                            <select id="faculty-select" style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;">
                                <option value="">Fakultetni tanlang</option>
                                <?php if (!empty($faculties ?? [])): ?>
                                    <?php foreach ($faculties as $faculty): ?>
                                        <?php
                                        $facultyName = $faculty['name'] ?? '';
                                        $facultyId = $faculty['id'] ?? '';
                                        ?>
                                        <option value="<?= htmlspecialchars((string)$facultyId, ENT_QUOTES, 'UTF-8') ?>" data-faculty-name="<?= htmlspecialchars((string)$facultyName, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars((string)$facultyName, ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label style="display:block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">📚 Yo'nalish</label>
                            <select id="specialty-select" disabled style="width:100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;">
                                <option value="">Avval fakultetni tanlang</option>
                            </select>
                        </div>
                    </div>

                    <div id="cascade-help" class="muted small" style="margin-top: 10px; color: #64748b;">
                        Fakultetni tanlang, so'ng yo'nalishni tanlang — guruhlar pastda chiqadi.
                    </div>
                </div>

                <div id="selected-groups-inputs"></div>

                <div id="groups-panel" class="groups-container" style="display: block; margin-left: 0; padding-left: 0; border-left: none;">
                    <div id="groups-loading" class="groups-loading" style="display: none;">Yuklanmoqda...</div>
                    <div id="groups-empty" class="empty-message" style="display: none; color:#6b7280; padding: 12px;">Avval fakultet va yo'nalishni tanlang.</div>
                    <div id="groups-list" class="groups-list active"></div>
                </div>

                <div style="margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-large">💾 Saqlash</button>
                    <a href="/admin/tests" class="btn-secondary btn-large">❌ Bekor qilish</a>
                    <a href="/admin/tests/show?id=<?= urlencode((string)($test['id'] ?? '')) ?>" class="btn-secondary btn-large">← Testga qaytish</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        (function() {
            const allowedGroups = <?= json_encode($allowedGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
            const facultySelect = document.getElementById('faculty-select');
            const specialtySelect = document.getElementById('specialty-select');
            const groupsLoading = document.getElementById('groups-loading');
            const groupsEmpty = document.getElementById('groups-empty');
            const groupsList = document.getElementById('groups-list');
            const selectedInputs = document.getElementById('selected-groups-inputs');

            if (!facultySelect || !specialtySelect || !groupsLoading || !groupsEmpty || !groupsList || !selectedInputs) {
                return;
            }

            const selected = new Set(Array.isArray(allowedGroups) ? allowedGroups : []);

            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function syncHiddenInputs() {
                selectedInputs.innerHTML = '';
                Array.from(selected).forEach(groupName => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'allowed_groups[]';
                    input.value = groupName;
                    selectedInputs.appendChild(input);
                });
            }

            function updateSelectedGroupsDisplay() {
                const selectedGroupsDisplay = document.getElementById('selected-groups-display');
                const selectedGroupsList = document.getElementById('selected-groups-list');
                const selectedGroupsCount = document.getElementById('selected-groups-count');
                
                if (!selectedGroupsDisplay || !selectedGroupsList || !selectedGroupsCount) {
                    return;
                }

                // Обновляем счетчик
                selectedGroupsCount.textContent = selected.size;

                // Обновляем список выбранных групп
                selectedGroupsList.innerHTML = '';
                
                if (selected.size === 0) {
                    // Если нет выбранных групп, скрываем блок
                    selectedGroupsDisplay.style.display = 'none';
                    return;
                }

                // Показываем блок
                selectedGroupsDisplay.style.display = 'block';

                // Создаем теги для каждой выбранной группы
                Array.from(selected).forEach(groupName => {
                    const tag = document.createElement('span');
                    tag.className = 'selected-group-tag';
                    tag.setAttribute('data-group-name', groupName);
                    tag.style.cssText = 'display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px 8px 16px; background: white; border: 2px solid #3b82f6; border-radius: 8px; font-size: 14px; font-weight: 600; color: #1e40af;';
                    
                    const nameSpan = document.createElement('span');
                    nameSpan.textContent = groupName;
                    tag.appendChild(nameSpan);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-group-btn';
                    removeBtn.setAttribute('data-group-name', groupName);
                    removeBtn.title = 'Guruhni o\'chirish';
                    removeBtn.style.cssText = 'display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; padding: 0; border: none; background: #ef4444; color: white; border-radius: 50%; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.2s ease; flex-shrink: 0;';
                    removeBtn.textContent = '×';
                    
                    removeBtn.addEventListener('mouseenter', function() {
                        this.style.background = '#dc2626';
                        this.style.transform = 'scale(1.1)';
                    });
                    
                    removeBtn.addEventListener('mouseleave', function() {
                        this.style.background = '#ef4444';
                        this.style.transform = 'scale(1)';
                    });

                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const groupNameToRemove = this.getAttribute('data-group-name');
                        if (groupNameToRemove) {
                            selected.delete(groupNameToRemove);
                            syncHiddenInputs();
                            updateSelectedGroupsDisplay();
                            
                            // Также обновляем состояние чекбокса в списке групп, если он виден
                            const checkbox = document.querySelector(`.group-checkbox[data-group-name="${escapeHtml(groupNameToRemove)}"]`);
                            if (checkbox) {
                                checkbox.checked = false;
                                const card = checkbox.closest('.group-card');
                                if (card) {
                                    card.classList.remove('selected');
                                }
                            }
                            
                            // Показываем краткое уведомление
                            showTemporaryMessage('Guruh o\'chirildi. O\'zgarishlarni saqlash uchun "Saqlash" tugmasini bosing.', 'info');
                        }
                    });
                    
                    tag.appendChild(removeBtn);
                    selectedGroupsList.appendChild(tag);
                });
            }

            function showMessage(type, text) {
                groupsLoading.style.display = 'none';
                groupsList.innerHTML = '';
                groupsEmpty.style.display = 'block';
                if (type === 'error') {
                    groupsEmpty.style.color = '#ef4444';
                } else if (type === 'success') {
                    groupsEmpty.style.color = '#10b981';
                } else {
                    groupsEmpty.style.color = '#6b7280';
                }
                groupsEmpty.textContent = text;
            }

            function showTemporaryMessage(text, type = 'info') {
                // Создаем элемент уведомления, если его еще нет
                let notification = document.getElementById('temp-notification');
                if (!notification) {
                    notification = document.createElement('div');
                    notification.id = 'temp-notification';
                    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; z-index: 10000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease; max-width: 400px;';
                    document.body.appendChild(notification);
                }

                // Устанавливаем цвет в зависимости от типа
                if (type === 'error') {
                    notification.style.background = '#fee2e2';
                    notification.style.color = '#991b1b';
                    notification.style.border = '2px solid #ef4444';
                } else if (type === 'success') {
                    notification.style.background = '#d1fae5';
                    notification.style.color = '#065f46';
                    notification.style.border = '2px solid #10b981';
                } else {
                    notification.style.background = '#dbeafe';
                    notification.style.color = '#1e40af';
                    notification.style.border = '2px solid #3b82f6';
                }

                notification.textContent = text;
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                notification.style.display = 'block';

                // Плавное появление
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateY(0)';
                }, 10);

                // Автоматически скрываем через 3 секунды
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 300);
                }, 3000);
            }

            async function loadSpecialties(facultyId, facultyName) {
                specialtySelect.disabled = true;
                specialtySelect.innerHTML = '<option value="">Yuklanmoqda...</option>';
                showMessage('info', 'Avval yo\'nalishni tanlang.');

                const url = `/admin/api/specialties?faculty_id=${encodeURIComponent(facultyId)}&faculty_name=${encodeURIComponent(facultyName)}`;
                const res = await fetch(url);
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                const data = await res.json();
                if (!data.success || !Array.isArray(data.data)) {
                    throw new Error(data.error || 'Yo\'nalishlar yuklanmadi');
                }

                specialtySelect.innerHTML = '<option value="">Yo\'nalishni tanlang</option>';
                data.data.forEach(item => {
                    const name = item.name || item.id || '';
                    if (!name) return;
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    specialtySelect.appendChild(opt);
                });
                specialtySelect.disabled = false;
            }

            async function loadGroups(facultyId, facultyName, specialtyName) {
                groupsEmpty.style.display = 'none';
                groupsLoading.style.display = 'block';
                groupsList.innerHTML = '';

                const url = `/admin/api/groups?specialty=${encodeURIComponent(specialtyName)}&faculty_id=${encodeURIComponent(facultyId)}&faculty_name=${encodeURIComponent(facultyName)}&per_page=200&page=1`;
                const res = await fetch(url);
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                const data = await res.json();
                if (!data.success || !Array.isArray(data.data)) {
                    throw new Error(data.error || 'Guruhlar yuklanmadi');
                }

                groupsLoading.style.display = 'none';

                if (data.data.length === 0) {
                    showMessage('info', 'Guruhlar topilmadi.');
                    return;
                }

                const grid = document.createElement('div');
                grid.className = 'groups-grid';

                data.data.forEach(group => {
                    const groupName = group.name || group.id || '';
                    if (!groupName) return;

                    const isSelected = selected.has(groupName);
                    const card = document.createElement('label');
                    card.className = 'group-card' + (isSelected ? ' selected' : '');
                    card.innerHTML = `
                        <input type="checkbox" class="group-checkbox" data-group-name="${escapeHtml(groupName)}" ${isSelected ? 'checked' : ''}>
                        <div class="group-check-icon">✓</div>
                        <div class="group-name">${escapeHtml(groupName)}</div>
                    `;

                    card.addEventListener('click', function(e) {
                        if (e.target && e.target.classList && e.target.classList.contains('group-checkbox')) {
                            return;
                        }
                        const checkbox = card.querySelector('.group-checkbox');
                        checkbox.checked = !checkbox.checked;
                        const checked = checkbox.checked;
                        card.classList.toggle('selected', checked);
                        if (checked) {
                            selected.add(groupName);
                        } else {
                            selected.delete(groupName);
                        }
                        syncHiddenInputs();
                        updateSelectedGroupsDisplay();
                    });

                    grid.appendChild(card);
                });

                groupsList.appendChild(grid);
            }

            facultySelect.addEventListener('change', async function() {
                const facultyId = facultySelect.value;
                const selectedOption = facultySelect.options[facultySelect.selectedIndex];
                const facultyName = (selectedOption && selectedOption.dataset) ? (selectedOption.dataset.facultyName || '') : '';

                specialtySelect.disabled = true;
                specialtySelect.innerHTML = '<option value="">Avval fakultetni tanlang</option>';

                if (!facultyId) {
                    showMessage('info', 'Avval fakultet va yo\'nalishni tanlang.');
                    return;
                }

                try {
                    await loadSpecialties(facultyId, facultyName);
                } catch (err) {
                    console.error(err);
                    specialtySelect.disabled = true;
                    specialtySelect.innerHTML = '<option value="">Xatolik</option>';
                    showMessage('error', 'Yo\'nalishlar yuklanmadi: ' + (err && err.message ? err.message : 'Xatolik'));
                }
            });

            specialtySelect.addEventListener('change', async function() {
                const facultyId = facultySelect.value;
                const selectedOption = facultySelect.options[facultySelect.selectedIndex];
                const facultyName = (selectedOption && selectedOption.dataset) ? (selectedOption.dataset.facultyName || '') : '';
                const specialtyName = specialtySelect.value;

                if (!facultyId || !specialtyName) {
                    showMessage('info', 'Avval yo\'nalishni tanlang.');
                    return;
                }

                try {
                    await loadGroups(facultyId, facultyName, specialtyName);
                } catch (err) {
                    console.error(err);
                    showMessage('error', 'Guruhlar yuklanmadi: ' + (err && err.message ? err.message : 'Xatolik'));
                }
            });

            syncHiddenInputs();
            updateSelectedGroupsDisplay();
            showMessage('info', 'Avval fakultet va yo\'nalishni tanlang.');

            // Обработка чекбокса "Открыть для всех"
            const openForAllCheckbox = document.getElementById('open-for-all');
            const cascadeSelects = document.getElementById('cascade-selects');
            const groupsPanel = document.getElementById('groups-panel');

            function toggleGroupsSelection() {
                if (openForAllCheckbox.checked) {
                    // Очищаем все выбранные группы
                    selected.clear();
                    syncHiddenInputs();
                    updateSelectedGroupsDisplay();
                    // Отключаем выбор групп
                    cascadeSelects.style.opacity = '0.5';
                    cascadeSelects.style.pointerEvents = 'none';
                    groupsPanel.style.opacity = '0.5';
                    groupsPanel.style.pointerEvents = 'none';
                    showMessage('success', 'Test barcha talabalar uchun ochiq bo\'ladi.');
                } else {
                    // Включаем выбор групп
                    cascadeSelects.style.opacity = '1';
                    cascadeSelects.style.pointerEvents = 'auto';
                    groupsPanel.style.opacity = '1';
                    groupsPanel.style.pointerEvents = 'auto';
                    showMessage('info', 'Avval fakultet va yo\'nalishni tanlang.');
                }
            }

            openForAllCheckbox.addEventListener('change', toggleGroupsSelection);
            
            // Инициализация при загрузке
            toggleGroupsSelection();
        })();
    </script>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>
