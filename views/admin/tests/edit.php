<?php
$pageTitle = 'Testni tahrirlash';
$extraStyles = '
        /* Улучшенные стили для form-group */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: 0.01em;
        }
        
        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            color: #1e293b;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .form-group input[type="text"]:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .add-option-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin-top: 8px;
            margin-right: 8px;
            color: #475569;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .add-option-btn:hover {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-color: #94a3b8;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .option-item {
            transition: all 0.2s ease;
        }
        
        .option-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transform: translateX(2px);
        }
        
        .option-text {
            position: relative !important;
            z-index: 10 !important;
            pointer-events: auto !important;
        }
        
        /* Обеспечиваем доступность полей формы */
        .question-item .form-group {
            position: relative !important;
            z-index: 100 !important;
        }
        
        .question-item .form-group input[type="text"],
        .question-item .form-group textarea,
        .question-item .form-group select {
            position: relative !important;
            z-index: 102 !important;
            pointer-events: auto !important;
            cursor: text !important;
            background: white !important;
        }
        
        /* Убеждаемся, что все интерактивные элементы доступны */
        .question-item .form-group input:focus,
        .question-item .form-group textarea:focus,
        .question-item .form-group select:focus {
            z-index: 9999 !important;
            position: relative !important;
            pointer-events: auto !important;
        }
        
        /* Предотвращаем перекрытие абсолютно позиционированными элементами */
        .question-item {
            overflow: visible !important;
        }
        
        /* Гарантируем, что все поля ввода доступны */
        .question-item input[type="text"],
        .question-item textarea {
            position: relative !important;
            z-index: 200 !important;
            pointer-events: auto !important;
            cursor: text !important;
            background: white !important;
            isolation: isolate;
        }
        
        .question-item input[type="text"]:focus,
        .question-item textarea:focus {
            z-index: 10000 !important;
            position: relative !important;
        }
        
        /* Абсолютно позиционированные элементы не должны блокировать поля */
        .question-item .question-number-badge {
            z-index: 1 !important;
        }
        
        .question-item > div[style*="position: absolute"][style*="top: 20px"][style*="right: 20px"] {
            z-index: 2 !important;
        }
        
        /* Убираем pointer-events с декоративных элементов */
        .question-item > div[style*="position: absolute"][style*="left: 0"][style*="width: 4px"] {
            pointer-events: none !important;
            z-index: 0 !important;
        }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">✏️ Testni tahrirlash</h1>
    <p class="admin-page-subtitle">Test savollarini o'zgartirish</p>
</div>

<div class="admin-table-container" style="padding: 24px;">

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>⚠️</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/tests/update" id="test-form" style="margin-top: 24px;">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string)($test['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="title">Test nomi</label>
                        <input type="text" id="title" name="title" required placeholder="Masalan: Temperament turi testi" value="<?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Kategoriya</label>
                        <input type="text" id="category" name="category" placeholder="Temperament, stress, motivatsiya..." value="<?= htmlspecialchars($test['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Qisqa tavsif</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Bu test nimalarni o'lchaydi, natijalarni qanday talqin qilish kerak."><?= htmlspecialchars($test['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <h3 style="margin-top: 40px; font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Savollar</h3>
                <p class="muted small" style="margin-bottom: 24px; font-size: 14px; color: #6b7280;">
                    Bir yoki bir nechta savol qo'shing. Har bir savol uchun javob variantlarini yarating.
                </p>

                <div id="questions-container">
                    <?php foreach (($test['questions'] ?? []) as $qIdx => $question): ?>
                        <?php 
                        $questionType = $question['type'] ?? 'multiple_choice';
                        if (empty($question['options']) && ($questionType === 'multiple_choice' || $questionType === 'multiple_select')) {
                            $questionType = 'text';
                        }
                        ?>
                        <div class="question-item" data-index="<?= $qIdx ?>" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 28px; padding-left: 90px; padding-right: 120px; border-radius: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: visible;" onmouseover="this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.15)'; this.style.transform='translateY(-2px)'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); z-index: 0; pointer-events: none;"></div>
                            <div class="question-number-badge" style="position: absolute; top: 28px; left: 28px; width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2); z-index: 1; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.2); pointer-events: auto;" onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 8px 28px rgba(102, 126, 234, 0.5), inset 0 1px 0 rgba(255,255,255,0.3)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2)';">
                                <span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);"><?= $qIdx + 1 ?></span>
                            </div>
                            <div style="position: absolute; top: 20px; right: 20px; display: flex; gap: 8px; z-index: 2; pointer-events: auto;">
                                <button type="button" class="duplicate-question-btn" data-question-index="<?= $qIdx ?>" style="padding: 8px 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.3)';" title="Dublikat qilish">📋</button>
                                <button type="button" class="remove-question-btn" data-question-index="<?= $qIdx ?>" style="padding: 8px 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(239, 68, 68, 0.3)';" title="O'chirish">🗑️</button>
                            </div>
                            <div class="form-group">
                                <label for="question-<?= $qIdx ?>-type">Savol turi</label>
                                <select id="question-<?= $qIdx ?>-type" name="questions[<?= $qIdx ?>][type]" class="question-type-select" data-question-index="<?= $qIdx ?>" style="width: 100%; padding: 12px; border: 2px solid var(--border-color); border-radius: var(--radius-md); font-size: 15px;">
                                    <option value="multiple_choice" <?= $questionType === 'multiple_choice' ? 'selected' : '' ?>>Bitta variant tanlash</option>
                                    <option value="multiple_select" <?= $questionType === 'multiple_select' ? 'selected' : '' ?>>Bir nechta variant tanlash</option>
                                    <option value="text" <?= $questionType === 'text' ? 'selected' : '' ?>>Matnli javob</option>
                                    <option value="scale" <?= $questionType === 'scale' ? 'selected' : '' ?>>Shkala (1-5)</option>
                                </select>
                            </div>
                            <div class="form-group" style="position: relative; z-index: 100;">
                                <label for="question-<?= $qIdx ?>-text" style="position: relative; z-index: 101;">Savol matni</label>
                                <input type="text" id="question-<?= $qIdx ?>-text" name="questions[<?= $qIdx ?>][text]"
                                       placeholder="Masalan: «Men odamlar bilan muloqotda tez charchayman»" 
                                       value="<?= htmlspecialchars($question['text'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       style="position: relative; z-index: 102; pointer-events: auto; cursor: text; background: white;"
                                       onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'; this.style.zIndex='999';"
                                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.zIndex='102';">
                            </div>
                            <div class="form-group question-options-group" data-question-index="<?= $qIdx ?>" style="<?= ($questionType === 'text' || $questionType === 'scale') ? 'display: none;' : '' ?>">
                                <label>Javob variantlari</label>
                                <div class="options-container" data-question-index="<?= $qIdx ?>" style="margin-top: 12px;">
                                    <?php if (($questionType === 'multiple_choice' || $questionType === 'multiple_select') && !empty($question['options'])): ?>
                                        <?php foreach ($question['options'] as $optIdx => $option): ?>
                                            <div class="option-item" data-option-index="<?= $optIdx ?>" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                                                <div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>
                                                <input type="color" name="questions[<?= $qIdx ?>][options][<?= $optIdx ?>][color]" value="<?= htmlspecialchars($option['color'] ?? '#667eea', ENT_QUOTES, 'UTF-8') ?>" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">
                                                <input type="text" name="questions[<?= $qIdx ?>][options][<?= $optIdx ?>][text]" placeholder="Variant <?= $optIdx + 1 ?>" value="<?= htmlspecialchars($option['text'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; position: relative; z-index: 10; pointer-events: auto;">
                                                <input type="number" name="questions[<?= $qIdx ?>][options][<?= $optIdx ?>][score]" placeholder="Ball" value="<?= htmlspecialchars((string)($option['score'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" title="Bu variant uchun ball">
                                                <?php if (!empty($option['is_other'])): ?>
                                                    <input type="hidden" name="questions[<?= $qIdx ?>][options][<?= $optIdx ?>][is_other]" value="1">
                                                <?php endif; ?>
                                                <button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="option-item" data-option-index="0" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                                            <div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>
                                            <input type="color" name="questions[<?= $qIdx ?>][options][0][color]" value="#667eea" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">
                                            <input type="text" name="questions[<?= $qIdx ?>][options][0][text]" placeholder="Variant 1" value="Variant 1" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; position: relative; z-index: 10; pointer-events: auto;">
                                            <input type="number" name="questions[<?= $qIdx ?>][options][0][score]" placeholder="Ball" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" title="Bu variant uchun ball">
                                            <button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 8px; margin-top: 8px;">
                                    <button type="button" class="add-option-btn" data-question-index="<?= $qIdx ?>" style="padding: 8px 16px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px; color: #374151;">+ Variant qo'shish</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Кнопка добавления вопроса - всегда внизу списка вопросов -->
                <button type="button" class="btn-secondary" id="add-question-btn" style="margin-top: 20px; padding: 14px 28px; font-weight: 600; border-radius: 12px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border: 1px solid #d1d5db; color: #374151; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">+ Yana savol qo'shish</button>

                <!-- Раздел расчета результатов -->
                <?php
                $calcConfig = $test['calculation_config'] ?? null;
                $calcType = $calcConfig['type'] ?? 'none';
                $categories = $calcConfig['categories'] ?? [];
                ?>
                <div id="calculation-section" style="margin-top: 48px; padding-top: 32px; border-top: 2px solid #e2e8f0;">
                    <h3 style="margin-bottom: 16px; font-size: 24px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 12px;">
                        <span style="width: 4px; height: 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></span>
                        📊 Natijalarni hisoblash
                    </h3>
                    <p class="muted small" style="margin-bottom: 24px; color: #6b7280;">
                        Test natijalarini avtomatik hisoblash va kategoriyalarga ajratish. Agar kerak bo'lmasa, bu bo'limni o'tkazib yuborishingiz mumkin.
                    </p>

                    <div class="form-group">
                        <label for="calculation_type">Hisoblash turi</label>
                        <select id="calculation_type" name="calculation_type" class="question-type-select" style="max-width: 400px;">
                            <option value="none" <?= $calcType === 'none' ? 'selected' : '' ?>>Hisoblash yo'q</option>
                            <option value="sum" <?= $calcType === 'sum' ? 'selected' : '' ?>>Summa (barcha shkala savollarining yig'indisi)</option>
                            <option value="average" <?= $calcType === 'average' ? 'selected' : '' ?>>O'rtacha qiymat (shkala savollarining o'rtachasi)</option>
                            <option value="categories" <?= $calcType === 'categories' ? 'selected' : '' ?>>Kategoriyalar (summa asosida kategoriya aniqlash)</option>
                            <option value="multi_scale" <?= $calcType === 'multi_scale' ? 'selected' : '' ?>>Bir nechta shkala (masalan: HADS - Tashvish va Depressiya)</option>
                            <option value="percentage" <?= $calcType === 'percentage' ? 'selected' : '' ?>>Foiz (javoblar foizini hisoblash)</option>
                        </select>
                    </div>

                    <!-- Настройки для sum, average, categories -->
                    <div id="calculation-categories-config" style="display: <?= in_array($calcType, ['sum', 'average', 'categories']) ? 'block' : 'none' ?>; margin-top: 24px; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <h4 style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: #374151;">Kategoriyalar</h4>
                        <p class="muted small" style="margin-bottom: 16px; color: #6b7280;">
                            Ballar diapazoniga qarab kategoriyalarni belgilang. Masalan: Past (0-10), O'rtacha (11-20), Yuqori (21-30)
                        </p>
                        <div id="categories-container">
                            <?php if (empty($categories)): ?>
                            <div class="category-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;">
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>
                                    <input type="text" name="category_names[]" placeholder="Masalan: Past" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>
                                    <input type="number" name="category_mins[]" placeholder="0" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>
                                    <input type="number" name="category_maxs[]" placeholder="10" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif (ixtiyoriy)</label>
                                    <input type="text" name="category_descriptions[]" placeholder="Tavsif" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <button type="button" class="remove-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                </div>
                            </div>
                            <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                            <div class="category-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;">
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>
                                    <input type="text" name="category_names[]" value="<?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Masalan: Past" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>
                                    <input type="number" name="category_mins[]" value="<?= htmlspecialchars($cat['min'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>
                                    <input type="number" name="category_maxs[]" value="<?= htmlspecialchars($cat['max'] ?? '10', ENT_QUOTES, 'UTF-8') ?>" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif (ixtiyoriy)</label>
                                    <input type="text" name="category_descriptions[]" value="<?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Tavsif" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <button type="button" class="remove-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-category-btn" class="add-option-btn" style="margin-top: 12px;">+ Kategoriya qo'shish</button>
                    </div>

                    <!-- Настройки для multi_scale -->
                    <div id="calculation-multi-scale-config" style="display: <?= $calcType === 'multi_scale' ? 'block' : 'none' ?>; margin-top: 24px; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <h4 style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: #374151;">Shkalalar</h4>
                        <p class="muted small" style="margin-bottom: 16px; color: #6b7280;">
                            Bir nechta shkala yarating. Har bir shkala uchun savollar va kategoriyalarni belgilang. Masalan: HADS testida "Tashvish" va "Depressiya" shkalalari.
                        </p>
                        <div id="scales-container">
                            <?php 
                            $scales = $calcConfig['scales'] ?? [];
                            $questions = $test['questions'] ?? [];
                            
                            if (!empty($scales)): 
                                // Предзаполняем существующие шкалы
                                foreach ($scales as $scaleIndex => $scale):
                                    $scaleName = $scale['name'] ?? '';
                                    $questionIndices = $scale['question_indices'] ?? [];
                                    $categories = $scale['categories'] ?? [];
                            ?>
                                <div class="scale-item" data-scale-index="<?= $scaleIndex ?>" style="margin-bottom: 32px; padding: 20px; background: white; border-radius: 12px; border: 2px solid #e2e8f0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0;">
                                        <h5 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">Shkala <?= $scaleIndex + 1 ?></h5>
                                        <button type="button" class="remove-scale-btn" data-scale-index="<?= $scaleIndex ?>" style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">🗑️ O'chirish</button>
                                    </div>
                                    <div style="margin-bottom: 16px;">
                                        <label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Shkala nomi</label>
                                        <input type="text" name="scales[scale_<?= $scaleIndex ?>][name]" value="<?= htmlspecialchars($scaleName, ENT_QUOTES, 'UTF-8') ?>" placeholder="Masalan: Tashvish" required style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                    </div>
                                    <div style="margin-bottom: 16px;">
                                        <label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Savollar (tanlang)</label>
                                        <div style="max-height: 200px; overflow-y: auto; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <?php foreach ($questions as $qIndex => $question): ?>
                                                <?php $questionText = $question['text'] ?? ''; ?>
                                                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer;">
                                                    <input type="checkbox" name="scales[scale_<?= $scaleIndex ?>][question_indices][]" value="<?= $qIndex ?>" <?= in_array($qIndex, $questionIndices) ? 'checked' : '' ?> style="width: 18px; height: 18px; cursor: pointer;">
                                                    <span style="font-size: 14px; color: #374151;">Savol <?= $qIndex + 1 ?>: <?= htmlspecialchars(mb_substr($questionText, 0, 50), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($questionText) > 50 ? '...' : '' ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Kategoriyalar</label>
                                        <div class="scale-categories-container" data-scale-index="<?= $scaleIndex ?>" style="margin-top: 12px;">
                                            <?php if (empty($categories)): ?>
                                                <div class="scale-category-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;">
                                                    <div>
                                                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>
                                                        <input type="text" name="scales[scale_<?= $scaleIndex ?>][categories][0][name]" placeholder="Masalan: Norma" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                    </div>
                                                    <div>
                                                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>
                                                        <input type="number" name="scales[scale_<?= $scaleIndex ?>][categories][0][min]" placeholder="0" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                    </div>
                                                    <div>
                                                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>
                                                        <input type="number" name="scales[scale_<?= $scaleIndex ?>][categories][0][max]" placeholder="7" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                    </div>
                                                    <div>
                                                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif</label>
                                                        <input type="text" name="scales[scale_<?= $scaleIndex ?>][categories][0][description]" placeholder="Tavsif" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                    </div>
                                                    <div>
                                                        <button type="button" class="remove-scale-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($categories as $catIndex => $category): ?>
                                                    <div class="scale-category-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;">
                                                        <div>
                                                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>
                                                            <input type="text" name="scales[scale_<?= $scaleIndex ?>][categories][<?= $catIndex ?>][name]" value="<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Masalan: Norma" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                        </div>
                                                        <div>
                                                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>
                                                            <input type="number" name="scales[scale_<?= $scaleIndex ?>][categories][<?= $catIndex ?>][min]" value="<?= htmlspecialchars($category['min'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                        </div>
                                                        <div>
                                                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>
                                                            <input type="number" name="scales[scale_<?= $scaleIndex ?>][categories][<?= $catIndex ?>][max]" value="<?= htmlspecialchars($category['max'] ?? '7', ENT_QUOTES, 'UTF-8') ?>" step="0.01" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                        </div>
                                                        <div>
                                                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif</label>
                                                            <input type="text" name="scales[scale_<?= $scaleIndex ?>][categories][<?= $catIndex ?>][description]" value="<?= htmlspecialchars($category['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Tavsif" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                                        </div>
                                                        <div>
                                                            <button type="button" class="remove-scale-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="add-scale-category-btn" data-scale-index="<?= $scaleIndex ?>" style="margin-top: 12px; padding: 8px 16px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px; color: #374151;">+ Kategoriya qo'shish</button>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </div>
                        <button type="button" id="add-scale-btn" class="add-option-btn" style="margin-top: 12px;">+ Shkala qo'shish</button>
                    </div>

                    <!-- Настройки для percentage -->
                    <div id="calculation-percentage-config" style="display: <?= $calcType === 'percentage' ? 'block' : 'none' ?>; margin-top: 24px; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <h4 style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: #374151;">Javoblar talqini</h4>
                        <p class="muted small" style="margin-bottom: 16px; color: #6b7280;">
                            Har bir javob variantini kategoriyaga bog'lang
                        </p>
                        <div id="answer-interpretations-container">
                            <?php
                            $answerInterpretations = $calcConfig['answer_interpretations'] ?? [];
                            if (empty($answerInterpretations)):
                            ?>
                            <div class="answer-interpretation-item" style="display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 12px; margin-bottom: 12px; align-items: end;">
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Javob matni</label>
                                    <input type="text" name="answer_keys[]" placeholder="Masalan: Ha" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya</label>
                                    <input type="text" name="answer_categories[]" placeholder="Masalan: Ijobiy" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <button type="button" class="remove-answer-interpretation-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                </div>
                            </div>
                            <?php else: ?>
                            <?php foreach ($answerInterpretations as $key => $interpretation): ?>
                            <div class="answer-interpretation-item" style="display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 12px; margin-bottom: 12px; align-items: end;">
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Javob matni</label>
                                    <input type="text" name="answer_keys[]" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" placeholder="Masalan: Ha" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya</label>
                                    <input type="text" name="answer_categories[]" value="<?= htmlspecialchars($interpretation['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Masalan: Ijobiy" class="form-group input[type='text']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">
                                </div>
                                <div>
                                    <button type="button" class="remove-answer-interpretation-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-answer-interpretation-btn" class="add-option-btn" style="margin-top: 12px;">+ Javob qo'shish</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Кнопки действий внизу страницы -->
        <div style="background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); padding: 32px 40px; margin-top: 0; border-top: 2px solid #e2e8f0;">
            <div style="max-width: 1400px; margin: 0 auto; display: flex; gap: 16px; flex-wrap: wrap; justify-content: flex-end;">
                <a href="/admin/tests" class="btn-secondary btn-large" style="padding: 14px 32px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; background: white; border: 2px solid #cbd5e1; color: #475569; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='#94a3b8';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='#cbd5e1';">❌ Bekor qilish</a>
                <button type="submit" form="test-form" class="btn btn-large" style="padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.2s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); display: inline-flex; align-items: center; gap: 8px; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)';">💾 O'zgarishlarni saqlash</button>
            </div>
        </div>
    </main>


    <script>
        (function () {
            let questionIndex = <?= count($test['questions'] ?? []) ?>;
            const container = document.getElementById('questions-container');
            const addQuestionBtn = document.getElementById('add-question-btn');

            // Функция для обновления отображения в зависимости от типа вопроса
            function updateQuestionType(questionIndex) {
                const questionItem = document.querySelector(`.question-item[data-index="${questionIndex}"]`);
                if (!questionItem) return;
                
                const typeSelect = questionItem.querySelector('.question-type-select');
                const optionsGroup = questionItem.querySelector('.question-options-group');
                const questionType = typeSelect ? typeSelect.value : 'multiple_choice';
                
                // Показываем/скрываем блок с вариантами ответов
                if (questionType === 'text' || questionType === 'scale') {
                    // Для текстовых ответов и шкалы скрываем блок с вариантами
                    if (optionsGroup) {
                        optionsGroup.style.display = 'none';
                    }
                } else {
                    // Для multiple_choice и multiple_select ВСЕГДА показываем блок с вариантами
                    if (optionsGroup) {
                        optionsGroup.style.display = 'block';
                        optionsGroup.style.visibility = 'visible';
                        // Убеждаемся, что поля ввода доступны и не заблокированы
                        const textInputs = optionsGroup.querySelectorAll('.option-text');
                        textInputs.forEach(input => {
                            input.disabled = false;
                            input.readOnly = false;
                            input.removeAttribute('readonly');
                            input.removeAttribute('disabled');
                            input.style.pointerEvents = 'auto';
                            input.style.opacity = '1';
                            input.style.position = 'relative';
                            input.style.zIndex = '10';
                        });
                        
                        // Убеждаемся, что кнопки добавления вариантов доступны
                        const addButtons = optionsGroup.querySelectorAll('.add-option-btn');
                        addButtons.forEach(btn => {
                            btn.disabled = false;
                            btn.style.pointerEvents = 'auto';
                            btn.style.opacity = '1';
                        });
                    }
                    // Убеждаемся, что все текстовые поля доступны
                    const textInputs = questionItem.querySelectorAll('.option-text');
                    textInputs.forEach(input => {
                        input.style.pointerEvents = 'auto';
                        input.style.position = 'relative';
                        input.style.zIndex = '10';
                    });
                }
            }

            // Инициализация для всех существующих вопросов
            // Сначала убеждаемся, что все блоки видны для типов с вариантами
            <?php foreach (($test['questions'] ?? []) as $qIdx => $question): ?>
            <?php 
            $qType = $question['type'] ?? 'multiple_choice';
            if ($qType !== 'text' && $qType !== 'scale'): ?>
            const optionsGroup<?= $qIdx ?> = document.querySelector('.question-item[data-index="<?= $qIdx ?>"] .question-options-group');
            if (optionsGroup<?= $qIdx ?>) {
                optionsGroup<?= $qIdx ?>.style.display = 'block';
            }
            <?php endif; ?>
            updateQuestionType(<?= $qIdx ?>);
            <?php endforeach; ?>
            
            // Функция для перемещения всех вопросов из контейнера перед кнопкой добавления
            function moveAllQuestionsBeforeCalculation() {
                const container = document.getElementById('questions-container');
                const addQuestionBtn = document.getElementById('add-question-btn');
                
                if (!container || !addQuestionBtn) return;
                
                // Перемещаем все вопросы из контейнера перед кнопкой добавления
                const questions = Array.from(container.querySelectorAll('.question-item'));
                questions.forEach(question => {
                    addQuestionBtn.parentElement.insertBefore(question, addQuestionBtn);
                });
            }
            
            // Функция для гарантированной разблокировки всех полей
            function ensureInputsAreEditable() {
                // Разблокируем все текстовые поля (включая поле текста вопроса)
                const allTextInputs = document.querySelectorAll('.question-item input[type="text"], .question-item textarea');
                allTextInputs.forEach(input => {
                    input.disabled = false;
                    input.readOnly = false;
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    input.style.pointerEvents = 'auto';
                    input.style.cursor = 'text';
                    input.style.opacity = '1';
                    input.style.position = 'relative';
                    input.style.zIndex = '200';
                    input.style.background = 'white';
                    input.style.isolation = 'isolate';
                });
                
                // Разблокируем поля вариантов ответов
                const allOptionInputs = document.querySelectorAll('.option-text');
                allOptionInputs.forEach(input => {
                    input.disabled = false;
                    input.readOnly = false;
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    input.style.pointerEvents = 'auto';
                    input.style.cursor = 'text';
                    input.style.opacity = '1';
                    input.style.position = 'relative';
                    input.style.zIndex = '200';
                });
                
                // Убеждаемся, что абсолютно позиционированные элементы не блокируют поля
                const questionItems = document.querySelectorAll('.question-item');
                questionItems.forEach(item => {
                    item.style.overflow = 'visible';
                    
                    // Декоративные элементы не должны перехватывать события
                    const decorativeElements = item.querySelectorAll('div[style*="position: absolute"]');
                    decorativeElements.forEach(el => {
                        const style = el.getAttribute('style') || '';
                        if (style.includes('left: 0') && style.includes('width: 4px')) {
                            el.style.pointerEvents = 'none';
                            el.style.zIndex = '0';
                        }
                    });
                });
            }
            
            // Принудительно делаем все поля доступными при клике
            document.addEventListener('click', function(e) {
                const target = e.target;
                if (target.matches('.question-item input[type="text"], .question-item textarea')) {
                    target.style.zIndex = '10000';
                    target.style.position = 'relative';
                    target.focus();
                }
            }, true);
            
            // Перемещаем все вопросы перед разделом расчета при загрузке
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    moveAllQuestionsBeforeCalculation();
                });
            } else {
                moveAllQuestionsBeforeCalculation();
            }
            
            // Убеждаемся, что все поля ввода доступны при загрузке
            ensureInputsAreEditable();
            
            // Дополнительная проверка после небольшой задержки
            setTimeout(ensureInputsAreEditable, 100);
            setTimeout(ensureInputsAreEditable, 500);
            
            // Обработчик клика для гарантированной доступности
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('option-text')) {
                    ensureInputsAreEditable();
                }
            });

            // Обработчик изменения типа вопроса
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('question-type-select')) {
                    const questionIndex = e.target.dataset.questionIndex;
                    updateQuestionType(questionIndex);
                }
            });

            // Функция для обновления нумерации вопросов
            function updateQuestionNumbers() {
                const questionItems = container.querySelectorAll('.question-item');
                questionItems.forEach((item, index) => {
                    const numberBadge = item.querySelector('.question-number-badge');
                    const numberSpan = numberBadge ? numberBadge.querySelector('span') : null;
                    if (numberSpan) {
                        numberSpan.textContent = index + 1;
                    } else if (numberBadge) {
                        numberBadge.innerHTML = '<span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">' + (index + 1) + '</span>';
                    } else {
                        // Если бейджа нет, создаем его
                        const badge = document.createElement('div');
                        badge.className = 'question-number-badge';
                        badge.style.cssText = 'position: absolute; top: 28px; left: 28px; width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2); z-index: 2; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.2);';
                        badge.innerHTML = '<span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">' + (index + 1) + '</span>';
                        item.style.position = 'relative';
                        item.style.paddingLeft = '90px';
                        item.style.overflow = 'visible';
                        
                        // Добавляем вертикальную полоску
                        const accentBar = document.createElement('div');
                        accentBar.style.cssText = 'position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); z-index: 1;';
                        item.insertBefore(accentBar, item.firstChild);
                        item.insertBefore(badge, item.firstChild);
                    }
                });
            }

            // Добавление нового вопроса
            addQuestionBtn.addEventListener('click', function () {
                const wrapper = document.createElement('div');
                wrapper.className = 'question-item';
                wrapper.dataset.index = String(questionIndex);
                wrapper.style.cssText = 'background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 28px; padding-left: 90px; padding-right: 120px; border-radius: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: visible;';
                
                // Добавляем обработчики hover
                wrapper.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 8px 24px rgba(102, 126, 234, 0.15)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.borderColor = '#cbd5e1';
                });
                wrapper.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 4px 16px rgba(0,0,0,0.06)';
                    this.style.transform = 'translateY(0)';
                    this.style.borderColor = '#e2e8f0';
                });
                
                const optionIndex = 0;
                const questionNumber = container.querySelectorAll('.question-item').length + 1;
                wrapper.innerHTML =
                    '<div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); z-index: 1;"></div>' +
                    '<div class="question-number-badge" style="position: absolute; top: 28px; left: 28px; width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2); z-index: 2; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.2);" onmouseover="this.style.transform=\'scale(1.1)\'; this.style.boxShadow=\'0 8px 28px rgba(102, 126, 234, 0.5), inset 0 1px 0 rgba(255,255,255,0.3)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2)\';"><span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">' + questionNumber + '</span></div>' +
                    '<div style="position: absolute; top: 20px; right: 20px; display: flex; gap: 8px; z-index: 3;">' +
                    '<button type="button" class="duplicate-question-btn" data-question-index="' + questionIndex + '" style="padding: 8px 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 12px rgba(16, 185, 129, 0.4)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 2px 8px rgba(16, 185, 129, 0.3)\';" title="Dublikat qilish">📋</button>' +
                    '<button type="button" class="remove-question-btn" data-question-index="' + questionIndex + '" style="padding: 8px 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 12px rgba(239, 68, 68, 0.4)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 2px 8px rgba(239, 68, 68, 0.3)\';" title="O\'chirish">🗑️</button>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="question-' + questionIndex + '-type">Savol turi</label>' +
                    '<select id="question-' + questionIndex + '-type" name="questions[' + questionIndex + '][type]" class="question-type-select" data-question-index="' + questionIndex + '" style="width: 100%; padding: 12px; border: 2px solid var(--border-color); border-radius: var(--radius-md); font-size: 15px;">' +
                    '<option value="multiple_choice">Bitta variant tanlash</option>' +
                    '<option value="multiple_select">Bir nechta variant tanlash</option>' +
                    '<option value="text">Matnli javob</option>' +
                    '<option value="scale">Shkala (1-5)</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="question-' + questionIndex + '-text">Savol matni</label>' +
                    '<input type="text" id="question-' + questionIndex + '-text" name="questions[' + questionIndex + '][text]" placeholder="Savol matni">' +
                    '</div>' +
                    '<div class="form-group question-options-group" data-question-index="' + questionIndex + '">' +
                    '<label>Javob variantlari</label>' +
                    '<div class="options-container" data-question-index="' + questionIndex + '" style="margin-top: 12px;">' +
                    '<div class="option-item" data-option-index="' + optionIndex + '" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">' +
                    '<div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>' +
                    '<input type="color" name="questions[' + questionIndex + '][options][' + optionIndex + '][color]" value="#667eea" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">' +
                    '<input type="text" name="questions[' + questionIndex + '][options][' + optionIndex + '][text]" placeholder="Variant 1" value="Variant 1" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; position: relative; z-index: 10; pointer-events: auto;">' +
                    '<input type="number" name="questions[' + questionIndex + '][options][' + optionIndex + '][score]" placeholder="Ball" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" title="Bu variant uchun ball">' +
                    '<button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>' +
                    '</div>' +
                    '</div>' +
                    '<div style="display: flex; gap: 8px; margin-top: 8px;">' +
                    '<button type="button" class="add-option-btn" data-question-index="' + questionIndex + '" style="padding: 8px 16px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px; color: #374151;">+ Variant qo\'shish</button>' +
                    '</div>' +
                    '</div>';
                
                // Добавляем вопрос перед кнопкой "+ Yana savol qo'shish"
                const addQuestionBtn = document.getElementById('add-question-btn');
                
                if (addQuestionBtn) {
                    // Вставляем перед кнопкой, чтобы кнопка всегда оставалась внизу
                    addQuestionBtn.parentElement.insertBefore(wrapper, addQuestionBtn);
                } else {
                    // Fallback: добавляем в контейнер
                    container.appendChild(wrapper);
                }
                
                updateQuestionType(questionIndex);
                updateQuestionNumbers();
                
                questionIndex++;
            });

            // Функция для получения типа вопроса
            function getQuestionType(questionIndex) {
                const questionItem = document.querySelector(`.question-item[data-index="${questionIndex}"]`);
                if (!questionItem) return 'multiple_choice';
                const typeSelect = questionItem.querySelector('.question-type-select');
                return typeSelect ? typeSelect.value : 'multiple_choice';
            }

            // Делегирование событий для добавления вариантов
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-option-btn')) {
                    const questionIdx = e.target.dataset.questionIndex;
                    const optionsContainer = e.target.closest('.form-group').querySelector('.options-container');
                    const optionIndex = optionsContainer.children.length;
                    const questionType = getQuestionType(questionIdx);
                    
                    const optionItem = document.createElement('div');
                    optionItem.className = 'option-item';
                    optionItem.dataset.optionIndex = optionIndex;
                    optionItem.style.cssText = 'display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;';
                    optionItem.innerHTML =
                        '<div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>' +
                        '<input type="color" name="questions[' + questionIdx + '][options][' + optionIndex + '][color]" value="#667eea" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">' +
                        '<input type="text" name="questions[' + questionIdx + '][options][' + optionIndex + '][text]" placeholder="Variant ' + (optionIndex + 1) + '" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; position: relative; z-index: 10; pointer-events: auto;">' +
                        '<input type="number" name="questions[' + questionIdx + '][options][' + optionIndex + '][score]" placeholder="Ball" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" title="Bu variant uchun ball">' +
                        '<button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>';
                    
                    optionsContainer.appendChild(optionItem);
                }
                
                if (e.target.classList.contains('remove-option')) {
                    const optionItem = e.target.closest('.option-item');
                    if (optionItem && optionItem.parentElement.children.length > 1) {
                        optionItem.remove();
                    } else {
                        alert('Kamida bitta variant bo\'lishi kerak!');
                    }
                }
                
                // Дублирование вопроса
                if (e.target.classList.contains('duplicate-question-btn')) {
                    const sourceQuestionIndex = parseInt(e.target.dataset.questionIndex);
                    duplicateQuestion(sourceQuestionIndex);
                }
                
                // Удаление вопроса
                if (e.target.classList.contains('remove-question-btn')) {
                    const questionIndex = parseInt(e.target.dataset.questionIndex);
                    const questionItem = document.querySelector(`.question-item[data-index="${questionIndex}"]`);
                    if (questionItem) {
                        const totalQuestions = container.querySelectorAll('.question-item').length;
                        if (totalQuestions > 1) {
                            if (confirm('Bu savolni o\'chirishni xohlaysizmi?')) {
                                questionItem.remove();
                                updateQuestionNumbers();
                                reindexQuestions();
                            }
                        } else {
                            alert('Kamida bitta savol bo\'lishi kerak!');
                        }
                    }
                }
            });
            
            // Функция дублирования вопроса
            function duplicateQuestion(sourceIndex) {
                const sourceQuestion = document.querySelector(`.question-item[data-index="${sourceIndex}"]`);
                if (!sourceQuestion) return;
                
                // Получаем данные исходного вопроса
                const sourceTypeSelect = sourceQuestion.querySelector('.question-type-select');
                const sourceTextInput = sourceQuestion.querySelector('input[name*="[text]"]');
                const sourceOptions = sourceQuestion.querySelectorAll('.option-item');
                
                const sourceType = sourceTypeSelect ? sourceTypeSelect.value : 'multiple_choice';
                const sourceText = sourceTextInput ? sourceTextInput.value : '';
                const sourceOptionsData = [];
                const sourceOptionsScores = [];
                
                const sourceOptionsColors = [];
                sourceOptions.forEach(option => {
                    const optionInput = option.querySelector('.option-text');
                    const optionScoreInput = option.querySelector('.option-score');
                    const optionColorInput = option.querySelector('.option-color');
                    if (optionInput) {
                        sourceOptionsData.push(optionInput.value);
                        sourceOptionsScores.push(optionScoreInput ? optionScoreInput.value : '0');
                        sourceOptionsColors.push(optionColorInput ? optionColorInput.value : '#667eea');
                    }
                });
                
                // Создаем новый вопрос
                const wrapper = document.createElement('div');
                wrapper.className = 'question-item';
                wrapper.dataset.index = String(questionIndex);
                wrapper.style.cssText = 'background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 28px; padding-left: 90px; padding-right: 120px; border-radius: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: visible;';
                
                wrapper.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 8px 24px rgba(102, 126, 234, 0.15)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.borderColor = '#cbd5e1';
                });
                wrapper.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 4px 16px rgba(0,0,0,0.06)';
                    this.style.transform = 'translateY(0)';
                    this.style.borderColor = '#e2e8f0';
                });
                
                const questionNumber = container.querySelectorAll('.question-item').length + 1;
                
                // Строим HTML для вариантов ответов
                let optionsHTML = '';
                if (sourceOptionsData.length > 0) {
                    sourceOptionsData.forEach((optText, optIdx) => {
                        const questionType = getQuestionType(String(questionIndex));
                        const optScore = sourceOptionsScores[optIdx] || '0';
                        optionsHTML += 
                            '<div class="option-item" data-option-index="' + optIdx + '" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">' +
                            '<div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>' +
                            '<input type="color" name="questions[' + questionIndex + '][options][' + optIdx + '][color]" value="' + (sourceOptionsColors && sourceOptionsColors[optIdx] ? sourceOptionsColors[optIdx] : '#667eea') + '" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">' +
                            '<input type="text" name="questions[' + questionIndex + '][options][' + optIdx + '][text]" placeholder="Variant ' + (optIdx + 1) + '" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; position: relative; z-index: 10; pointer-events: auto;" value="' + (optText ? optText.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '') + '">' +
                            '<input type="number" name="questions[' + questionIndex + '][options][' + optIdx + '][score]" placeholder="Ball" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" value="' + optScore + '" title="Bu variant uchun ball">' +
                            '<button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>' +
                            '</div>';
                    });
                } else {
                    const questionType = getQuestionType(String(questionIndex));
                    optionsHTML = 
                        '<div class="option-item" data-option-index="0" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">' +
                        '<div class="drag-handle" style="cursor: move; color: #9ca3af; font-size: 18px;">⋮⋮</div>' +
                        '<input type="color" name="questions[' + questionIndex + '][options][0][color]" value="#667eea" class="option-color" style="width: 50px; height: 40px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; flex-shrink: 0;" title="Rangni tanlang">' +
                        '<input type="text" name="questions[' + questionIndex + '][options][0][text]" placeholder="Variant 1" class="option-text" style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;">' +
                        '<input type="number" name="questions[' + questionIndex + '][options][0][score]" placeholder="Ball" class="option-score" style="width: 80px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px;" min="0" step="1" title="Bu variant uchun ball">' +
                        '<button type="button" class="remove-option" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px 8px;">×</button>' +
                        '</div>';
                }
                
                wrapper.innerHTML =
                    '<div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); z-index: 1;"></div>' +
                    '<div class="question-number-badge" style="position: absolute; top: 28px; left: 28px; width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2); z-index: 2; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.2);" onmouseover="this.style.transform=\'scale(1.1)\'; this.style.boxShadow=\'0 8px 28px rgba(102, 126, 234, 0.5), inset 0 1px 0 rgba(255,255,255,0.3)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 6px 20px rgba(102, 126, 234, 0.4), inset 0 1px 0 rgba(255,255,255,0.2)\';"><span style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">' + questionNumber + '</span></div>' +
                    '<div style="position: absolute; top: 20px; right: 20px; display: flex; gap: 8px; z-index: 3;">' +
                    '<button type="button" class="duplicate-question-btn" data-question-index="' + questionIndex + '" style="padding: 8px 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 12px rgba(16, 185, 129, 0.4)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 2px 8px rgba(16, 185, 129, 0.3)\';" title="Dublikat qilish">📋</button>' +
                    '<button type="button" class="remove-question-btn" data-question-index="' + questionIndex + '" style="padding: 8px 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 12px rgba(239, 68, 68, 0.4)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 2px 8px rgba(239, 68, 68, 0.3)\';" title="O\'chirish">🗑️</button>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="question-' + questionIndex + '-type">Savol turi</label>' +
                    '<select id="question-' + questionIndex + '-type" name="questions[' + questionIndex + '][type]" class="question-type-select" data-question-index="' + questionIndex + '" style="width: 100%; padding: 12px; border: 2px solid var(--border-color); border-radius: var(--radius-md); font-size: 15px;">' +
                    '<option value="multiple_choice"' + (sourceType === 'multiple_choice' ? ' selected' : '') + '>Bitta variant tanlash</option>' +
                    '<option value="multiple_select"' + (sourceType === 'multiple_select' ? ' selected' : '') + '>Bir nechta variant tanlash</option>' +
                    '<option value="text"' + (sourceType === 'text' ? ' selected' : '') + '>Matnli javob</option>' +
                    '<option value="scale"' + (sourceType === 'scale' ? ' selected' : '') + '>Shkala (1-5)</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="question-' + questionIndex + '-text">Savol matni</label>' +
                    '<input type="text" id="question-' + questionIndex + '-text" name="questions[' + questionIndex + '][text]" placeholder="Masalan: «Men odamlar bilan muloqotda tez charchayman»" value="' + (sourceText ? sourceText.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '') + '">' +
                    '</div>' +
                    '<div class="form-group question-options-group" data-question-index="' + questionIndex + '">' +
                    '<label>Javob variantlari</label>' +
                    '<div class="options-container" data-question-index="' + questionIndex + '">' +
                    optionsHTML +
                    '</div>' +
                    '<div style="display: flex; gap: 8px; margin-top: 8px;">' +
                    '<button type="button" class="add-option-btn" data-question-index="' + questionIndex + '" style="padding: 8px 16px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px; color: #374151;">+ Variant qo\'shish</button>' +
                    '</div>' +
                    '</div>';
                
                // Вставляем новый вопрос перед кнопкой "+ Yana savol qo'shish"
                const addQuestionBtn = document.getElementById('add-question-btn');
                
                if (addQuestionBtn) {
                    // Вставляем перед кнопкой, чтобы кнопка всегда оставалась внизу
                    addQuestionBtn.parentElement.insertBefore(wrapper, addQuestionBtn);
                } else {
                    // Fallback: вставляем после исходного
                    sourceQuestion.insertAdjacentElement('afterend', wrapper);
                }
                
                // Инициализируем новый вопрос
                handleQuestionTypeChange(questionIndex);
                ensureInputsAreEditable();
                updateQuestionNumbers();
                reindexQuestions();
                
                questionIndex++;
            }
            
            // Функция для переиндексации всех вопросов
            function reindexQuestions() {
                const questionItems = container.querySelectorAll('.question-item');
                questionItems.forEach((item, index) => {
                    const oldIndex = item.dataset.index;
                    item.dataset.index = String(index);
                    
                    // Обновляем все атрибуты data-question-index
                    const allElements = item.querySelectorAll('[data-question-index]');
                    allElements.forEach(el => {
                        el.dataset.questionIndex = String(index);
                    });
                    
                    // Обновляем name атрибуты
                    const nameElements = item.querySelectorAll('[name*="questions[' + oldIndex + ']"]');
                    nameElements.forEach(el => {
                        if (el.name) {
                            el.name = el.name.replace(/questions\[\d+\]/, 'questions[' + index + ']');
                        }
                        if (el.id) {
                            el.id = el.id.replace(/question-\d+/, 'question-' + index);
                        }
                    });
                    
                    // Обновляем for атрибуты в label
                    const labels = item.querySelectorAll('label[for]');
                    labels.forEach(label => {
                        if (label.getAttribute('for')) {
                            label.setAttribute('for', label.getAttribute('for').replace(/question-\d+/, 'question-' + index));
                        }
                    });
                });
            }

            // Простая реализация drag and drop
            let draggedElement = null;
            document.addEventListener('mousedown', function(e) {
                if (e.target.classList.contains('drag-handle')) {
                    draggedElement = e.target.closest('.option-item');
                    draggedElement.style.opacity = '0.5';
                }
            });

            document.addEventListener('mousemove', function(e) {
                if (draggedElement) {
                    e.preventDefault();
                }
            });

            document.addEventListener('mouseup', function(e) {
                if (draggedElement) {
                    draggedElement.style.opacity = '1';
                    const optionsContainer = draggedElement.parentElement;
                    const afterElement = getDragAfterElement(optionsContainer, e.clientY);
                    if (afterElement == null) {
                        optionsContainer.appendChild(draggedElement);
                    } else {
                        optionsContainer.insertBefore(draggedElement, afterElement);
                    }
                    draggedElement = null;
                }
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.option-item:not(.dragging)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }
        })();

        // Управление конфигурацией расчета результатов
        (function() {
            const calculationTypeSelect = document.getElementById('calculation_type');
            const categoriesConfig = document.getElementById('calculation-categories-config');
            const multiScaleConfig = document.getElementById('calculation-multi-scale-config');
            const percentageConfig = document.getElementById('calculation-percentage-config');
            const categoriesContainer = document.getElementById('categories-container');
            const scalesContainer = document.getElementById('scales-container');
            const answerInterpretationsContainer = document.getElementById('answer-interpretations-container');
            // Инициализируем scaleIndex с учетом существующих шкал
            let scaleIndex = scalesContainer ? scalesContainer.querySelectorAll('.scale-item').length : 0;

            function updateCalculationConfig() {
                const type = calculationTypeSelect.value;
                
                // Скрываем все конфигурации
                categoriesConfig.style.display = 'none';
                multiScaleConfig.style.display = 'none';
                percentageConfig.style.display = 'none';
                
                // Показываем нужную конфигурацию
                if (type === 'sum' || type === 'average' || type === 'categories') {
                    categoriesConfig.style.display = 'block';
                } else if (type === 'multi_scale') {
                    multiScaleConfig.style.display = 'block';
                } else if (type === 'percentage') {
                    percentageConfig.style.display = 'block';
                }
            }
            
            // Функция для получения списка всех вопросов
            function getAllQuestions() {
                const questions = [];
                const questionItems = document.querySelectorAll('.question-item');
                questionItems.forEach((item, index) => {
                    const questionText = item.querySelector('input[name*="[text]"]')?.value || '';
                    if (questionText.trim()) {
                        questions.push({
                            index: index,
                            text: questionText.trim()
                        });
                    }
                });
                return questions;
            }
            
            // Функция для добавления новой шкалы
            function addScale() {
                const scaleId = 'scale_' + scaleIndex++;
                const questions = getAllQuestions();
                
                const scaleDiv = document.createElement('div');
                scaleDiv.className = 'scale-item';
                scaleDiv.dataset.scaleIndex = scaleId;
                scaleDiv.style.cssText = 'margin-bottom: 32px; padding: 20px; background: white; border-radius: 12px; border: 2px solid #e2e8f0;';
                
                let questionsCheckboxes = '';
                questions.forEach(q => {
                    questionsCheckboxes += 
                        '<label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer;">' +
                        '<input type="checkbox" name="scales[' + scaleId + '][question_indices][]" value="' + q.index + '" style="width: 18px; height: 18px; cursor: pointer;">' +
                        '<span style="font-size: 14px; color: #374151;">Savol ' + (q.index + 1) + ': ' + (q.text.length > 50 ? q.text.substring(0, 50) + '...' : q.text) + '</span>' +
                        '</label>';
                });
                
                scaleDiv.innerHTML = 
                    '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0;">' +
                    '<h5 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">Shkala ' + (scalesContainer.children.length + 1) + '</h5>' +
                    '<button type="button" class="remove-scale-btn" data-scale-id="' + scaleId + '" style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">🗑️ O\'chirish</button>' +
                    '</div>' +
                    '<div style="margin-bottom: 16px;">' +
                    '<label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Shkala nomi</label>' +
                    '<input type="text" name="scales[' + scaleId + '][name]" placeholder="Masalan: Tashvish" required style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div style="margin-bottom: 16px;">' +
                    '<label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Savollar (tanlang)</label>' +
                    '<div style="max-height: 200px; overflow-y: auto; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">' +
                    questionsCheckboxes +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Kategoriyalar</label>' +
                    '<div class="scale-categories-container" data-scale-id="' + scaleId + '" style="margin-top: 12px;">' +
                    '<div class="scale-category-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;">' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>' +
                    '<input type="text" name="scales[' + scaleId + '][categories][0][name]" placeholder="Masalan: Norma" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>' +
                    '<input type="number" name="scales[' + scaleId + '][categories][0][min]" placeholder="0" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>' +
                    '<input type="number" name="scales[' + scaleId + '][categories][0][max]" placeholder="7" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif</label>' +
                    '<input type="text" name="scales[' + scaleId + '][categories][0][description]" placeholder="Tavsif" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<button type="button" class="remove-scale-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<button type="button" class="add-scale-category-btn" data-scale-id="' + scaleId + '" style="margin-top: 12px; padding: 8px 16px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px; color: #374151;">+ Kategoriya qo\'shish</button>' +
                    '</div>';
                
                scalesContainer.appendChild(scaleDiv);
            }
            
            // Обработчик добавления категории к шкале
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-scale-category-btn')) {
                    const scaleIndex = e.target.dataset.scaleIndex;
                    const scaleId = 'scale_' + scaleIndex;
                    const categoriesContainer = e.target.previousElementSibling;
                    const categoryIndex = categoriesContainer.children.length;
                    
                    const categoryItem = document.createElement('div');
                    categoryItem.className = 'scale-category-item';
                    categoryItem.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;';
                    // Определяем правильный scaleId (может быть scale_0, scale_1 или scale_0, scale_1 и т.д.)
                    const scaleItem = e.target.closest('.scale-item');
                    const scaleNameInput = scaleItem?.querySelector('input[name*="[name]"]');
                    let actualScaleId = scaleId;
                    if (scaleNameInput) {
                        const nameAttr = scaleNameInput.getAttribute('name');
                        const match = nameAttr.match(/scales\[([^\]]+)\]/);
                        if (match) {
                            actualScaleId = match[1];
                        }
                    }
                    
                    categoryItem.innerHTML = 
                        '<div>' +
                        '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>' +
                        '<input type="text" name="scales[' + actualScaleId + '][categories][' + categoryIndex + '][name]" placeholder="Masalan: Past" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                        '</div>' +
                        '<div>' +
                        '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>' +
                        '<input type="number" name="scales[' + actualScaleId + '][categories][' + categoryIndex + '][min]" placeholder="0" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                        '</div>' +
                        '<div>' +
                        '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>' +
                        '<input type="number" name="scales[' + actualScaleId + '][categories][' + categoryIndex + '][max]" placeholder="10" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                        '</div>' +
                        '<div>' +
                        '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif</label>' +
                        '<input type="text" name="scales[' + actualScaleId + '][categories][' + categoryIndex + '][description]" placeholder="Tavsif" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                        '</div>' +
                        '<div>' +
                        '<button type="button" class="remove-scale-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>' +
                        '</div>';
                    
                    categoriesContainer.appendChild(categoryItem);
                }
            });
            
            // Обработчик удаления категории из шкалы
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-scale-category-btn')) {
                    const categoryItem = e.target.closest('.scale-category-item');
                    const categoriesContainer = categoryItem?.parentElement;
                    if (categoryItem && categoriesContainer && categoriesContainer.children.length > 1) {
                        categoryItem.remove();
                    } else if (categoryItem) {
                        alert('Kamida bitta kategoriya bo\'lishi kerak!');
                    }
                }
            });
            
            // Обработчик удаления шкалы
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-scale-btn') || e.target.closest('.remove-scale-btn')) {
                    const btn = e.target.classList.contains('remove-scale-btn') ? e.target : e.target.closest('.remove-scale-btn');
                    const scaleItem = btn.closest('.scale-item');
                    if (scaleItem && scalesContainer.children.length > 1) {
                        scaleItem.remove();
                        // Обновляем номера шкал
                        Array.from(scalesContainer.children).forEach((item, index) => {
                            const h5 = item.querySelector('h5');
                            if (h5) h5.textContent = 'Shkala ' + (index + 1);
                        });
                    } else if (scaleItem) {
                        alert('Kamida bitta shkala bo\'lishi kerak!');
                    }
                }
            });
            
            // Кнопка добавления шкалы
            document.getElementById('add-scale-btn')?.addEventListener('click', addScale);
            
            // Инициализация при загрузке
            updateCalculationConfig();
            
            // Если выбран multi_scale и нет шкал, добавляем первую шкалу
            // (существующие шкалы уже загружены через PHP)
            if (calculationTypeSelect.value === 'multi_scale' && scalesContainer && scalesContainer.children.length === 0) {
                addScale();
            }

            calculationTypeSelect?.addEventListener('change', updateCalculationConfig);

            // Добавление категории
            document.getElementById('add-category-btn')?.addEventListener('click', function() {
                const categoryItem = document.createElement('div');
                categoryItem.className = 'category-item';
                categoryItem.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; margin-bottom: 12px; align-items: end;';
                categoryItem.innerHTML = 
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya nomi</label>' +
                    '<input type="text" name="category_names[]" placeholder="Masalan: Past" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Min</label>' +
                    '<input type="number" name="category_mins[]" placeholder="0" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Max</label>' +
                    '<input type="number" name="category_maxs[]" placeholder="10" step="0.01" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Tavsif (ixtiyoriy)</label>' +
                    '<input type="text" name="category_descriptions[]" placeholder="Tavsif" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<button type="button" class="remove-category-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>' +
                    '</div>';
                
                categoriesContainer.appendChild(categoryItem);
            });

            // Удаление категории
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-category-btn')) {
                    const categoryItem = e.target.closest('.category-item');
                    if (categoryItem && categoriesContainer.children.length > 1) {
                        categoryItem.remove();
                    } else if (categoryItem) {
                        alert('Kamida bitta kategoriya bo\'lishi kerak!');
                    }
                }
            });

            // Добавление интерпретации ответа
            document.getElementById('add-answer-interpretation-btn')?.addEventListener('click', function() {
                const interpretationItem = document.createElement('div');
                interpretationItem.className = 'answer-interpretation-item';
                interpretationItem.style.cssText = 'display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 12px; margin-bottom: 12px; align-items: end;';
                interpretationItem.innerHTML = 
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Javob matni</label>' +
                    '<input type="text" name="answer_keys[]" placeholder="Masalan: Ha" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Kategoriya</label>' +
                    '<input type="text" name="answer_categories[]" placeholder="Masalan: Ijobiy" class="form-group input[type=\'text\']" style="width: 100%; padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px;">' +
                    '</div>' +
                    '<div>' +
                    '<button type="button" class="remove-answer-interpretation-btn" style="padding: 10px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px;">×</button>' +
                    '</div>';
                
                answerInterpretationsContainer.appendChild(interpretationItem);
            });

            // Удаление интерпретации ответа
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-answer-interpretation-btn')) {
                    const interpretationItem = e.target.closest('.answer-interpretation-item');
                    if (interpretationItem && answerInterpretationsContainer.children.length > 1) {
                        interpretationItem.remove();
                    } else if (interpretationItem) {
                        alert('Kamida bitta javob bo\'lishi kerak!');
                    }
                }
            });

            // Инициализация при загрузке
            if (calculationTypeSelect) {
                updateCalculationConfig();
            }
        })();
    </script>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

