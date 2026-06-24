<?php
declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Сервис для экспорта результатов тестов в Excel
 */
final class ExcelExportService
{
    /**
     * Экспорт результатов теста Айзенка
     * @param array<int, array<string, mixed>> $results
     * @param array<string, string> $studentGroups Массив соответствия student_id => group
     * @return void
     */
    public function exportEysenckResults(array $results, array $studentGroups = []): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Temperament natijalari');

        // Заголовки
        $headers = [
            'A1' => '№',
            'B1' => 'Talaba ID',
            'C1' => 'F.I.O.',
            'D1' => 'Guruh',
            'E1' => 'Sana',
            'F1' => 'Ekstraversiya (E)',
            'G1' => 'Neyrotizm (N)',
            'H1' => 'Yolg\'on (L)',
            'I1' => 'Temperament',
            'J1' => 'Tavsif',
            'K1' => 'Natija to\'g\'ri'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Стили для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '667eea'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Заполнение данных
        $row = 2;
        foreach ($results as $index => $result) {
            $studentId = (string)($result['student_id'] ?? '');
            $studentGroup = $studentGroups[$studentId] ?? '';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $studentId);
            $sheet->setCellValue('C' . $row, $result['student_name'] ?? '');
            $sheet->setCellValue('D' . $row, $studentGroup);
            
            $date = $result['completed_at'] ?? $result['submitted_at'] ?? '';
            if ($date) {
                $sheet->setCellValue('E' . $row, substr($date, 0, 16));
            }

            $scores = $result['scores'] ?? [];
            $sheet->setCellValue('F' . $row, $scores['E'] ?? 0);
            $sheet->setCellValue('G' . $row, $scores['N'] ?? 0);
            $sheet->setCellValue('H' . $row, $scores['L'] ?? 0);

            $temperament = $result['temperament'] ?? [];
            $sheet->setCellValue('I' . $row, $temperament['type'] ?? '');
            $sheet->setCellValue('J' . $row, $temperament['description'] ?? '');
            $sheet->setCellValue('K' . $row, ($temperament['is_valid'] ?? false) ? 'Ha' : 'Yo\'q');

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray($dataStyle);

            $row++;
        }

        // Автоподбор ширины колонок
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Фиксируем первую строку
        $sheet->freezePane('A2');

        // Отправка файла
        $this->sendFile($spreadsheet, 'Temperament_natijalari_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Экспорт результатов IQ теста
     * @param array<int, array<string, mixed>> $results
     * @param array<string, string> $studentGroups Массив соответствия student_id => group
     * @return void
     */
    public function exportIqResults(array $results, array $studentGroups = []): void
    {
        // Загружаем данные теста для получения вопросов
        $testDataPath = dirname(__DIR__, 2) . '/data/iq-test.json';
        $testData = null;
        if (file_exists($testDataPath)) {
            $content = file_get_contents($testDataPath);
            $testData = json_decode($content, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IQ Test natijalari');

        // Определяем количество вопросов для заголовков
        $questionsCount = $testData ? count($testData['questions'] ?? []) : 40;
        
        // Формируем заголовки
        $headers = [
            'A1' => '№',
            'B1' => 'Talaba ID',
            'C1' => 'F.I.O.',
            'D1' => 'Guruh',
            'E1' => 'Sana',
            'F1' => 'To\'g\'ri javoblar',
            'G1' => 'Jami savollar',
            'H1' => 'Foiz',
            'I1' => 'IQ ko\'rsatkichi',
            'J1' => 'Kategoriya',
        ];

        // Добавляем заголовки для каждого вопроса
        $col = 'K';
        if ($testData) {
            foreach ($testData['questions'] as $question) {
                $qId = $question['id'] ?? 0;
                $headers[$col . '1'] = 'Savol ' . $qId;
                $col++;
            }
        }

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Стили для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8b5cf6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $lastCol = $col;
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Заполнение данных
        $row = 2;
        foreach ($results as $index => $result) {
            $studentId = (string)($result['student_id'] ?? '');
            $studentGroup = $studentGroups[$studentId] ?? '';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $studentId);
            $sheet->setCellValue('C' . $row, $result['student_name'] ?? '');
            $sheet->setCellValue('D' . $row, $studentGroup);
            
            $date = $result['completed_at'] ?? $result['submitted_at'] ?? '';
            if ($date) {
                $sheet->setCellValue('E' . $row, substr($date, 0, 16));
            }

            $sheet->setCellValue('F' . $row, $result['correct_answers'] ?? 0);
            $sheet->setCellValue('G' . $row, $result['total_questions'] ?? 0);
            $sheet->setCellValue('H' . $row, ($result['percentage'] ?? 0) . '%');
            $sheet->setCellValue('I' . $row, $result['iq_score'] ?? 0);
            $sheet->setCellValue('J' . $row, $result['category']['name'] ?? '');

            // Записываем ответы на вопросы
            $answers = $result['answers'] ?? [];
            $col = 'K';
            if ($testData) {
                foreach ($testData['questions'] as $question) {
                    $qId = $question['id'] ?? 0;
                    $userAnswer = $answers[$qId] ?? '';
                    $correctAnswer = $question['correct_answer'] ?? -1;
                    
                    // Определяем текст ответа
                    $answerText = '';
                    if ($userAnswer !== '' && isset($question['options'][$userAnswer])) {
                        $answerText = $question['options'][$userAnswer];
                        if ($userAnswer == $correctAnswer) {
                            $answerText .= ' ✓';
                        } else {
                            $answerText .= ' ✗';
                        }
                    } else {
                        $answerText = 'Javob yo\'q';
                    }
                    
                    $sheet->setCellValue($col . $row, $answerText);
                    $col++;
                }
            }

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ];
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($dataStyle);

            $row++;
        }

        // Автоподбор ширины колонок
        $startColIndex = Coordinate::columnIndexFromString('A');
        $endColIndex = Coordinate::columnIndexFromString($lastCol);
        for ($colIndex = $startColIndex; $colIndex <= $endColIndex; $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Фиксируем первую строку
        $sheet->freezePane('A2');

        // Отправка файла
        $this->sendFile($spreadsheet, 'IQ_Test_natijalari_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Экспорт результатов теста Люшера
     * @param array<int, array<string, mixed>> $results
     * @param array<string, string> $studentGroups Массив соответствия student_id => group
     * @return void
     */
    public function exportLusherResults(array $results, array $studentGroups = []): void
    {
        // Загружаем данные теста для получения цветов
        $testDataPath = dirname(__DIR__, 2) . '/data/lusher-test.json';
        $testData = null;
        $colors = [];
        if (file_exists($testDataPath)) {
            $content = file_get_contents($testDataPath);
            $testData = json_decode($content, true);
            $colors = $testData['colors'] ?? [];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lyusher Testi natijalari');

        // Формируем заголовки
        $headers = [
            'A1' => '№',
            'B1' => 'Talaba ID',
            'C1' => 'F.I.O.',
            'D1' => 'Guruh',
            'E1' => 'Sana',
        ];

        // Добавляем заголовки для первого раунда (8 позиций)
        $col = 'F';
        for ($i = 1; $i <= 8; $i++) {
            $headers[$col . '1'] = '1-bosqich ' . $i;
            $col++;
        }

        // Добавляем заголовки для второго раунда (8 позиций)
        for ($i = 1; $i <= 8; $i++) {
            $headers[$col . '1'] = '2-bosqich ' . $i;
            $col++;
        }

        // Добавляем дополнительные колонки
        $headers[$col . '1'] = 'Yoqtirilgan ranglar';
        $col++;
        $headers[$col . '1'] = 'Rad etilgan ranglar';
        $col++;
        $headers[$col . '1'] = 'Stress omillari';

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Стили для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9333ea'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $lastCol = $col;
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Заполнение данных
        $row = 2;
        foreach ($results as $index => $result) {
            $studentId = (string)($result['student_id'] ?? '');
            $studentGroup = $studentGroups[$studentId] ?? '';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $studentId);
            $sheet->setCellValue('C' . $row, $result['student_name'] ?? '');
            $sheet->setCellValue('D' . $row, $studentGroup);
            
            $date = $result['completed_at'] ?? $result['submitted_at'] ?? '';
            if ($date) {
                $sheet->setCellValue('E' . $row, substr($date, 0, 16));
            }

            // Записываем первый раунд
            $round1 = $result['round1_selection'] ?? [];
            $col = 'F';
            for ($i = 0; $i < 8; $i++) {
                $colorId = $round1[$i] ?? null;
                if ($colorId !== null && isset($colors[$colorId])) {
                    $color = $colors[$colorId];
                    $colorName = $color['name'] ?? '';
                    $colorCode = $color['code'] ?? '#FFFFFF';
                    
                    $sheet->setCellValue($col . $row, $colorName);
                    
                    // Устанавливаем цвет фона ячейки
                    $rgb = $this->hexToRgb($colorCode);
                    if ($rgb) {
                        $sheet->getStyle($col . $row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($rgb);
                        
                        // Устанавливаем цвет текста в зависимости от яркости
                        $textColor = $this->getContrastColor($rgb);
                        $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB($textColor);
                    }
                } else {
                    $sheet->setCellValue($col . $row, '-');
                }
                $col++;
            }

            // Записываем второй раунд
            $round2 = $result['round2_selection'] ?? [];
            for ($i = 0; $i < 8; $i++) {
                $colorId = $round2[$i] ?? null;
                if ($colorId !== null && isset($colors[$colorId])) {
                    $color = $colors[$colorId];
                    $colorName = $color['name'] ?? '';
                    $colorCode = $color['code'] ?? '#FFFFFF';
                    
                    $sheet->setCellValue($col . $row, $colorName);
                    
                    // Устанавливаем цвет фона ячейки
                    $rgb = $this->hexToRgb($colorCode);
                    if ($rgb) {
                        $sheet->getStyle($col . $row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($rgb);
                        
                        // Устанавливаем цвет текста в зависимости от яркости
                        $textColor = $this->getContrastColor($rgb);
                        $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB($textColor);
                    }
                } else {
                    $sheet->setCellValue($col . $row, '-');
                }
                $col++;
            }

            // Записываем предпочитаемые цвета (первые 2 позиции из round1)
            $round1Preferred = [];
            for ($i = 0; $i < min(2, count($round1)); $i++) {
                $colorId = $round1[$i] ?? null;
                if ($colorId !== null && isset($colors[$colorId])) {
                    $round1Preferred[] = $colors[$colorId]['name'] ?? '';
                }
            }
            $sheet->setCellValue($col . $row, implode(', ', array_filter($round1Preferred)));
            $col++;

            // Записываем отвергаемые цвета (последние 2 позиции из round1)
            $round1Rejected = [];
            for ($i = max(0, count($round1) - 2); $i < count($round1); $i++) {
                $colorId = $round1[$i] ?? null;
                if ($colorId !== null && isset($colors[$colorId])) {
                    $round1Rejected[] = $colors[$colorId]['name'] ?? '';
                }
            }
            $sheet->setCellValue($col . $row, implode(', ', array_filter($round1Rejected)));
            $col++;

            // Записываем стресс-факторы (последние 2 позиции из round1, которые также в конце round2)
            $stressColors = [];
            $lastTwoRound1 = [];
            for ($i = max(0, count($round1) - 2); $i < count($round1); $i++) {
                $lastTwoRound1[] = $round1[$i] ?? null;
            }
            $lastTwoRound2 = [];
            for ($i = max(0, count($round2) - 2); $i < count($round2); $i++) {
                $lastTwoRound2[] = $round2[$i] ?? null;
            }
            foreach ($lastTwoRound1 as $colorId) {
                if ($colorId !== null && in_array($colorId, $lastTwoRound2, true) && isset($colors[$colorId])) {
                    $stressColors[] = $colors[$colorId]['name'] ?? '';
                }
            }
            $sheet->setCellValue($col . $row, implode(', ', array_filter($stressColors)));

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ];
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($dataStyle);

            $row++;
        }

        // Автоподбор ширины колонок
        $startColIndex = Coordinate::columnIndexFromString('A');
        $endColIndex = Coordinate::columnIndexFromString($lastCol);
        for ($colIndex = $startColIndex; $colIndex <= $endColIndex; $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Фиксируем первую строку
        $sheet->freezePane('A2');

        // Отправка файла
        $this->sendFile($spreadsheet, 'Lyusher_Testi_natijalari_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Преобразует HEX цвет в RGB
     * @param string $hex
     * @return string|null
     */
    private function hexToRgb(string $hex): ?string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 6) {
            return strtoupper($hex);
        }
        return null;
    }

    /**
     * Определяет контрастный цвет текста для фона
     * @param string $rgb
     * @return string
     */
    private function getContrastColor(string $rgb): string
    {
        // Простое определение яркости по RGB
        $r = hexdec(substr($rgb, 0, 2));
        $g = hexdec(substr($rgb, 2, 2));
        $b = hexdec(substr($rgb, 4, 2));
        
        // Вычисляем яркость (luminance)
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Если фон темный, возвращаем белый, иначе черный
        return $brightness < 128 ? 'FFFFFF' : '000000';
    }

    /**
     * Экспорт результатов обычных тестов с включением всех студентов из allowed_groups
     * @param array<int, array<string, mixed>> $results
     * @param array<string, mixed>|null $test Данные теста
     * @param array<string, string> $studentGroups Массив соответствия student_id => group
     * @param array<int, array<string, mixed>> $allStudentsInGroups Все студенты из allowed_groups (включая не сдавших)
     * @return void
     */
    public function exportCustomTestResults(array $results, ?array $test = null, array $studentGroups = [], array $allStudentsInGroups = []): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $testTitle = $test['title'] ?? 'Test natijalari';
        $sheet->setTitle('Test natijalari');

        // Метаданные
        $sheet->setCellValue('A1', 'Test:');
        $sheet->setCellValue('B1', $testTitle);
        $sheet->setCellValue('A2', 'Eksport qilingan sana:');
        $sheet->setCellValue('B2', date('Y-m-d H:i:s'));
        $sheet->setCellValue('A3', 'Jami natijalar:');
        $sheet->setCellValue('B3', count($results));

        // Заголовки
        $headers = ['№', 'Talaba ID', 'F.I.O.', 'Guruh', 'Fakultet', 'Yo\'nalish', 'Status', 'Sana'];
        
        // Добавляем колонки для расчетных результатов, если есть конфигурация расчета
        $hasCalculation = false;
        $calcConfig = null;
        $calcType = null;
        $isMultiScale = false;
        
        if ($test !== null && isset($test['calculation_config']) && !empty($test['calculation_config'])) {
            $hasCalculation = true;
            $calcConfig = $test['calculation_config'];
            $calcType = $calcConfig['type'] ?? null;
            $isMultiScale = ($calcType === 'multi_scale');
        }
        
        if ($hasCalculation) {
            if ($isMultiScale) {
                // Для multi_scale добавляем колонки для каждой шкалы
                $scales = $calcConfig['scales'] ?? [];
                if (!empty($scales)) {
                    foreach ($scales as $scale) {
                        $scaleName = $scale['name'] ?? 'Noma\'lum';
                        $headers[] = $scaleName . ' - Ball';
                        $headers[] = $scaleName . ' - Kategoriya';
                        $headers[] = $scaleName . ' - Tavsif';
                    }
                }
            } else {
                // Для обычных типов расчета - одна колонка
                $headers[] = 'Hisoblangan ball';
                $headers[] = 'Kategoriya';
                $headers[] = 'Tavsif';
            }
        }
        
        // Добавляем колонки для вопросов, если есть данные теста
        $questions = $test['questions'] ?? [];
        $maxQuestions = 0;
        if (!empty($questions)) {
            foreach ($questions as $qIndex => $question) {
                $headers[] = 'Savol ' . ($qIndex + 1) . ': ' . mb_substr($question['text'] ?? '', 0, 30);
                $maxQuestions = max($maxQuestions, $qIndex + 1);
            }
        } else {
            // Если нет данных теста, определяем максимальное количество вопросов из результатов
            foreach ($results as $result) {
                $answers = $result['answers'] ?? [];
                $maxQuestions = max($maxQuestions, count($answers));
            }
            for ($i = 1; $i <= $maxQuestions; $i++) {
                $headers[] = 'Savol ' . $i;
            }
        }
        $headers[] = 'Javoblar soni';

        // Функция для преобразования номера колонки в букву Excel (1 = A, 27 = AA, и т.д.)
        $getColumnLetter = function($columnNumber) {
            $columnLetter = '';
            while ($columnNumber > 0) {
                $columnNumber--;
                $columnLetter = chr(65 + ($columnNumber % 26)) . $columnLetter;
                $columnNumber = intval($columnNumber / 26);
            }
            return $columnLetter;
        };
        
        // Записываем заголовки
        $colIndex = 0;
        $headerRow = 5;
        foreach ($headers as $header) {
            $colLetter = $getColumnLetter($colIndex + 1);
            $sheet->setCellValue($colLetter . $headerRow, $header);
            $colIndex++;
        }

        // Стили для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '667eea'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $lastCol = $getColumnLetter(count($headers));
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        // Создаем массив результатов по student_id для быстрого поиска
        $resultsByStudentId = [];
        foreach ($results as $result) {
            $studentId = (string)($result['student_id'] ?? '');
            if (!empty($studentId)) {
                $resultsByStudentId[$studentId] = $result;
            }
        }

        // Определяем, использовать ли всех студентов из групп или только тех, кто сдал
        $studentsToExport = [];
        if (!empty($allStudentsInGroups)) {
            // Используем всех студентов из allowed_groups
            foreach ($allStudentsInGroups as $student) {
                $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                // Проверяем, является ли это преподавателем
                $isTeacher = (strpos($studentId, 'teacher_') === 0);
                
                if ($isTeacher) {
                    // Для преподавателей используем данные из результата
                    $result = $resultsByStudentId[$studentId] ?? null;
                    if ($result) {
                        $teacherData = $result['teacher_data'] ?? null;
                        $studentsToExport[] = [
                            'student' => [
                                'student_id' => $studentId,
                                'full_name' => $teacherData['full_name'] ?? $result['student_name'] ?? 'Noma\'lum o\'qituvchi',
                                'group' => '',
                                'faculty' => $teacherData['department'] ?? '',
                                'specialty' => '',
                            ],
                            'result' => $result,
                            'has_passed' => true,
                        ];
                    }
                } else {
                    // Для студентов - исключаем учителей (их student_id начинается с "teacher_")
                    // (эта проверка уже не нужна, так как мы проверили выше, но оставляем для ясности)
                    $result = $resultsByStudentId[$studentId] ?? null;
                    $studentsToExport[] = [
                        'student' => $student,
                        'result' => $result,
                        'has_passed' => $result !== null,
                    ];
                }
            }
        } else {
            // Используем только тех, кто сдал тест (старая логика)
            foreach ($results as $result) {
                $studentId = (string)($result['student_id'] ?? '');
                // Проверяем, является ли это преподавателем
                $isTeacher = (strpos($studentId, 'teacher_') === 0);
                
                // Если это преподаватель, используем данные из teacher_data
                if ($isTeacher) {
                    $teacherData = $result['teacher_data'] ?? null;
                    if ($teacherData) {
                        $studentsToExport[] = [
                            'student' => [
                                'student_id' => $studentId,
                                'full_name' => $teacherData['full_name'] ?? 'Noma\'lum o\'qituvchi',
                                'group' => '', // У преподавателей нет группы
                                'faculty' => $teacherData['department'] ?? '',
                                'specialty' => '',
                            ],
                            'result' => $result,
                            'has_passed' => true,
                        ];
                    } else {
                        // Если нет teacher_data, используем данные из результата
                        $studentsToExport[] = [
                            'student' => [
                                'student_id' => $studentId,
                                'full_name' => $result['student_name'] ?? 'Noma\'lum o\'qituvchi',
                                'group' => '',
                                'faculty' => '',
                                'specialty' => '',
                            ],
                            'result' => $result,
                            'has_passed' => true,
                        ];
                    }
                } else {
                    // Для студентов - исключаем учителей (их student_id начинается с "teacher_")
                    // (эта проверка уже не нужна, так как мы проверили выше, но оставляем для ясности)
                    $studentsToExport[] = [
                        'student' => null,
                        'result' => $result,
                        'has_passed' => true,
                    ];
                }
            }
        }

        // Заполнение данных
        $row = $headerRow + 1;
        foreach ($studentsToExport as $index => $item) {
            $student = $item['student'];
            $result = $item['result'];
            $hasPassed = $item['has_passed'];

            $colIndex = 0;
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $index + 1);
            
            if ($student) {
                $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                $studentName = $student['name'] ?? $student['full_name'] ?? 'Noma\'lum';
                $studentGroup = $student['group'] ?? $studentGroups[$studentId] ?? '';
            } else {
                // Старая логика - данные из результата
                $studentId = (string)($result['student_id'] ?? '');
                // Для преподавателей проверяем teacher_data
                $isTeacher = (strpos($studentId, 'teacher_') === 0);
                if ($isTeacher && isset($result['teacher_data'])) {
                    $studentName = $result['teacher_data']['full_name'] ?? $result['student_name'] ?? 'Noma\'lum o\'qituvchi';
                } else {
                    $studentName = $result['student_name'] ?? 'Noma\'lum';
                }
                $studentGroup = $studentGroups[$studentId] ?? '';
            }
            
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $studentId);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $studentName);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $studentGroup);
            
            // Факультет и направление
            if ($student) {
                $studentFaculty = $student['faculty'] ?? '';
                $studentSpecialty = $student['specialty'] ?? '';
            } else {
                // Старая логика - пытаемся получить из результата
                // Для преподавателей проверяем teacher_data
                $isTeacher = (strpos((string)($result['student_id'] ?? ''), 'teacher_') === 0);
                if ($isTeacher && isset($result['teacher_data'])) {
                    $studentFaculty = $result['teacher_data']['department'] ?? '';
                    $studentSpecialty = '';
                } else {
                    $studentFaculty = $result['student_faculty'] ?? '';
                    $studentSpecialty = $result['student_specialty'] ?? '';
                }
            }
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $studentFaculty);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $studentSpecialty);
            
            // Статус
            if ($hasPassed) {
                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, 'Test topshirgan');
            } else {
                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, 'Test topshirmagan');
            }
            
            $date = '';
            $answers = [];
            $answerCount = 0;
            
            if ($hasPassed && $result) {
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                $answers = $result['answers'] ?? [];
            }
            
            if ($date) {
                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, substr($date, 0, 16));
            } else {
                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
            }

            // Записываем расчетные результаты, если есть
            if ($hasCalculation) {
                if ($isMultiScale) {
                    // Для multi_scale записываем результаты по каждой шкале
                    $scales = $calcConfig['scales'] ?? [];
                    $resultScales = $result['scales'] ?? [];
                    
                    foreach ($scales as $scale) {
                        $scaleName = $scale['name'] ?? 'Noma\'lum';
                        $scaleResult = $resultScales[$scaleName] ?? null;
                        
                        if ($hasPassed && $scaleResult) {
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $scaleResult['score'] ?? '-');
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $scaleResult['interpretation']['category'] ?? '-');
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $scaleResult['interpretation']['description'] ?? '-');
                        } else {
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                        }
                    }
                } else {
                    // Для обычных типов расчета
                    if ($hasPassed && $result) {
                        $calculatedScore = $result['calculated_score'] ?? null;
                        $interpretation = $result['interpretation'] ?? null;
                        
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $calculatedScore !== null ? $calculatedScore : '-');
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $interpretation['category'] ?? '-');
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $interpretation['description'] ?? '-');
                    } else {
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                        $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                    }
                }
            }

            // Определяем количество вопросов из заголовков
            // Базовые колонки: №, Talaba ID, F.I.O., Guruh, Fakultet, Yo'nalish, Status, Sana (8)
            // + расчетные колонки (если есть): для multi_scale - по 3 колонки на каждую шкалу, иначе 3 колонки
            // + Javoblar soni (1)
            $baseColumns = 8;
            if ($hasCalculation) {
                if ($isMultiScale) {
                    $scales = $calcConfig['scales'] ?? [];
                    $calculationColumns = count($scales) * 3;
                } else {
                    $calculationColumns = 3;
                }
            } else {
                $calculationColumns = 0;
            }
            $questionCount = count($headers) - $baseColumns - $calculationColumns - 1; // -1 для "Javoblar soni"
            
            // Записываем ответы
            for ($qIndex = 0; $qIndex < $questionCount; $qIndex++) {
                if ($hasPassed && !empty($answers)) {
                    $answer = $answers[$qIndex] ?? '';
                    if (is_array($answer)) {
                        $answerText = implode(', ', array_filter($answer, function($a) { return !empty(trim((string)$a)); }));
                    } else {
                        $answerText = (string)$answer;
                    }
                    
                    if (!empty(trim($answerText))) {
                        $answerCount++;
                    }
                    
                    $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $answerText);
                } else {
                    // Для не сдавших - пустая ячейка
                    $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, '-');
                }
            }
            
            // Записываем количество ответов
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $hasPassed ? $answerCount : 0);

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ];
            $lastCol = $getColumnLetter(count($headers));
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($dataStyle);

            $row++;
        }

        // Автоподбор ширины колонок
        for ($i = 1; $i <= count($headers); $i++) {
            $col = $getColumnLetter($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getColumnDimension($col)->setWidth(min($sheet->getColumnDimension($col)->getWidth(), 50));
        }

        // Фиксируем строку заголовков
        $sheet->freezePane('A' . ($headerRow + 1));

        // Отправка файла
        $fileName = 'Test_natijalari_' . date('Y-m-d') . '.xlsx';
        if ($testTitle) {
            $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $testTitle) . '_' . date('Y-m-d') . '.xlsx';
        }
        $this->sendFile($spreadsheet, $fileName);
    }

    /**
     * Отправка файла пользователю
     * @param Spreadsheet $spreadsheet
     * @param string $fileName
     * @return void
     */
    private function sendFile(Spreadsheet $spreadsheet, string $fileName): void
    {
        // Очищаем буфер вывода
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Устанавливаем заголовки для скачивания файла
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Создаем writer и отправляем файл
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Экспорт результатов преподавателей
     * @param array<int, array<string, mixed>> $results
     * @param array<int, array<string, mixed>> $testsById
     * @return void
     */
    public function exportTeacherResults(array $results, array $testsById = []): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('O\'qituvchilar natijalari');

        $getColumnLetter = function (int $columnNumber): string {
            $columnLetter = '';
            while ($columnNumber > 0) {
                $columnNumber--;
                $columnLetter = chr(65 + ($columnNumber % 26)) . $columnLetter;
                $columnNumber = (int)($columnNumber / 26);
            }
            return $columnLetter;
        };

        // Базовые заголовки
        $headers = [
            '№',
            'O\'qituvchi ID',
            'F.I.O.',
            'Kafedra',
            'Test nomi',
            'Test turi',
            'Sana',
        ];

        // Динамические колонки по шкалам (multi_scale) для custom тестов
        $scaleColumns = [];
        foreach ($results as $result) {
            if (($result['test_type'] ?? '') !== 'custom') {
                continue;
            }

            $testId = (int)($result['test_id'] ?? 0);
            $test = $testsById[$testId] ?? null;
            $calcConfig = is_array($test) ? ($test['calculation_config'] ?? null) : null;
            if (!is_array($calcConfig)) {
                continue;
            }

            if (($calcConfig['type'] ?? null) === 'multi_scale') {
                foreach (($calcConfig['scales'] ?? []) as $scale) {
                    $scaleName = (string)($scale['name'] ?? '');
                    if ($scaleName !== '') {
                        $scaleColumns[$scaleName] = true;
                    }
                }
            }
        }

        if (!empty($scaleColumns)) {
            foreach (array_keys($scaleColumns) as $scaleName) {
                $headers[] = $scaleName . ' - Ball';
                $headers[] = $scaleName . ' - Kategoriya';
            }
        } else {
            // Legacy колонки (если нет multi_scale)
            $headers[] = 'Ball';
            $headers[] = 'Kategoriya';
        }

        $headers[] = 'Tavsiya';

        // Записываем заголовки
        foreach ($headers as $i => $value) {
            $sheet->setCellValue($getColumnLetter($i + 1) . '1', $value);
        }

        // Стили для заголовков
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10b981']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ];

        $lastCol = $getColumnLetter(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

        // Автоширина для всех колонок
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension($getColumnLetter($i))->setAutoSize(true);
        }

        // Заполняем данные
        $row = 2;
        $num = 1;

        foreach ($results as $result) {
            $teacherData = $result['teacher_data'] ?? null;
            $teacherFullName = $teacherData['full_name'] ?? 'Noma\'lum';
            $teacherDepartment = $teacherData['department'] ?? '-';
            $teacherId = str_replace('teacher_', '', $result['student_id'] ?? '');

            $testTitle = $result['test_title'] ?? 'Noma\'lum test';
            $testType = $result['test_type'] ?? '';
            $testTypeLabel = $this->getTestTypeLabel($testType);

            $submittedAt = $result['submitted_at'] ?? $result['completed_at'] ?? '';
            if ($submittedAt) {
                $submittedAt = date('d.m.Y H:i', strtotime($submittedAt));
            }

            $colIndex = 0;
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $num);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $teacherId);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $teacherFullName);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $teacherDepartment);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $testTitle);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $testTypeLabel);
            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $submittedAt);

            // Рекомендация
            $recommendation = '-';
            if (isset($result['interpretation']['recommendation'])) {
                $recommendation = $result['interpretation']['recommendation'];
            } elseif (isset($result['interpretation'])) {
                $recommendation = $result['interpretation'];
            }

            // Если есть multi_scale колонки — записываем по шкалам
            if (!empty($scaleColumns)) {
                $resultScales = $result['scales'] ?? [];
                foreach (array_keys($scaleColumns) as $scaleName) {
                    $scaleResult = is_array($resultScales) ? ($resultScales[$scaleName] ?? null) : null;
                    $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, is_array($scaleResult) ? ($scaleResult['score'] ?? '-') : '-');
                    $sheet->setCellValue(
                        $getColumnLetter(++$colIndex) . $row,
                        (is_array($scaleResult) && isset($scaleResult['interpretation']['category'])) ? (string)$scaleResult['interpretation']['category'] : '-'
                    );
                }
            } else {
                // Legacy колонки (Ball + Kategoriya)
                $score = '-';
                if (isset($result['calculated_score'])) {
                    $score = (string)$result['calculated_score'];
                } elseif (isset($result['extraversion_score'])) {
                    $score = 'E:' . $result['extraversion_score'] . ', N:' . $result['neuroticism_score'];
                } elseif (isset($result['correct_answers'])) {
                    $score = $result['correct_answers'] . '/' . ($result['total_questions'] ?? 0);
                }

                $category = '-';
                if (isset($result['interpretation']['category'])) {
                    $category = $result['interpretation']['category'];
                } elseif (isset($result['temperament'])) {
                    $category = $result['temperament'];
                } elseif (isset($result['iq_category'])) {
                    $category = $result['iq_category'];
                }

                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $score);
                $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $category);
            }

            $sheet->setCellValue($getColumnLetter(++$colIndex) . $row, $recommendation);

            // Применяем стили для строки
            $rowStyle = [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                ]
            ];
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($rowStyle);

            $row++;
            $num++;
        }

        // Устанавливаем высоту строк
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Генерируем имя файла
        $fileName = 'oqituvchilar_natijalari_' . date('Y-m-d') . '.xlsx';

        // Отправляем заголовки для скачивания файла
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Создаем writer и отправляем файл
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Получить название типа теста на узбекском
     */
    private function getTestTypeLabel(string $type): string
    {
        $labels = [
            'custom' => 'Oddiy test',
            'eysenck' => 'Eysenck temperament testi',
            'iq' => 'IQ testi',
            'lusher' => 'Lüscher rang testi',
        ];

        return $labels[$type] ?? $type;
    }
}

