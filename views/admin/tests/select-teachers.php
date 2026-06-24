<?php
$title = $title ?? 'O\'qituvchilarni tanlash';
$test = $test ?? [];
$allTeachers = $allTeachers ?? [];
$allowedTeachers = $allowedTeachers ?? [];

$pageTitle = 'O\'qituvchilarni tanlash';
$extraStyles = '
    .teacher-checkbox {
        padding: 16px;
        background: #f9fafb;
        border-radius: 12px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .teacher-checkbox:hover { background: #f3f4f6; }
    .teacher-checkbox.selected { background: #dcfce7; border-color: #10b981; }
    .teacher-checkbox label { display: flex; align-items: center; cursor: pointer; }
    .teacher-checkbox input[type="checkbox"] { width: 20px; height: 20px; margin-right: 12px; cursor: pointer; }
    .teacher-info { flex: 1; }
    .teacher-name { font-weight: 600; color: #111827; margin-bottom: 4px; }
    .teacher-meta { font-size: 13px; color: #6b7280; }
';
?>
<?php include __DIR__ . '/../components/layout-header.php'; ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">👨‍🏫 O'qituvchilarni tanlash</h1>
    <p class="admin-page-subtitle">Test: <?= htmlspecialchars($test['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="admin-table-container" style="padding: 24px;">

            <div style="background: #fef3c7; border: 2px solid #fbbf24; border-radius: 12px; padding: 16px 20px; margin-bottom: 30px;">
                <p style="color: #92400e; margin: 0;">
                    <strong>⚠️ Eslatma:</strong> Faqat tanlangan o'qituvchilar ushbu testni o'z kabinet sahifasida ko'rishlari va o'tishlari mumkin.
                </p>
            </div>

            <form method="POST" action="/admin/tests/update-teachers">
                <input type="hidden" name="test_id" value="<?= $test['id'] ?>">

                <?php if (empty($allTeachers)): ?>
                    <div style="background: white; border-radius: 16px; padding: 40px; text-align: center;">
                        <p style="color: #6b7280; font-size: 16px; margin-bottom: 16px;">
                            Hozircha ro'yxatdan o'tgan o'qituvchilar yo'q.
                        </p>
                        <a href="/admin/teachers" class="btn btn-primary">
                            O'qituvchilar ro'yxatiga o'tish
                        </a>
                    </div>
                <?php else: ?>
                    <div style="background: white; border-radius: 16px; padding: 30px; margin-bottom: 24px;">
                        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="font-size: 20px; font-weight: 700; color: #111827;">
                                O'qituvchilarni tanlang
                            </h2>
                            <div>
                                <button type="button" onclick="selectAll()" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; margin-right: 8px; font-weight: 600;">
                                    Barchasini tanlash
                                </button>
                                <button type="button" onclick="deselectAll()" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                    Barchasini bekor qilish
                                </button>
                            </div>
                        </div>

                        <div id="teachers-list">
                            <?php foreach ($allTeachers as $teacher): ?>
                                <?php
                                $teacherId = (int)($teacher['id'] ?? 0);
                                $isChecked = in_array($teacherId, $allowedTeachers);
                                ?>
                                <div class="teacher-checkbox <?= $isChecked ? 'selected' : '' ?>" data-teacher-id="<?= $teacherId ?>">
                                    <label>
                                        <input type="checkbox" 
                                               name="teachers[]" 
                                               value="<?= $teacherId ?>" 
                                               <?= $isChecked ? 'checked' : '' ?>
                                               onchange="updateCheckboxStyle(this)">
                                        <div class="teacher-info">
                                            <div class="teacher-name">
                                                <?= htmlspecialchars($teacher['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="teacher-meta">
                                                Login: <strong><?= htmlspecialchars($teacher['login'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php if (!empty($teacher['department'])): ?>
                                                    | Kafedra: <?= htmlspecialchars($teacher['department'], ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f3f4f6;">
                            <p style="color: #6b7280; margin-bottom: 16px;">
                                <strong>Tanlangan:</strong> <span id="selected-count"><?= count($allowedTeachers) ?></span> ta o'qituvchi
                            </p>
                            <button type="submit" style="width: 100%; padding: 16px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                                Saqlash
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
            </div>
        </div>
    <script>
        function updateCheckboxStyle(checkbox) {
            const container = checkbox.closest('.teacher-checkbox');
            if (checkbox.checked) {
                container.classList.add('selected');
            } else {
                container.classList.remove('selected');
            }
            updateCount();
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="teachers[]"]');
            checkboxes.forEach(cb => {
                cb.checked = true;
                updateCheckboxStyle(cb);
            });
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="teachers[]"]');
            checkboxes.forEach(cb => {
                cb.checked = false;
                updateCheckboxStyle(cb);
            });
        }

        function updateCount() {
            const checked = document.querySelectorAll('input[type="checkbox"][name="teachers[]"]:checked').length;
            document.getElementById('selected-count').textContent = checked;
        }
    </script>
</div>
<?php include __DIR__ . '/../components/layout-footer.php'; ?>

