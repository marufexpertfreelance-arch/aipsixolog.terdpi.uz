<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис для расчета результатов психологических тестов
 */
final class TestCalculationService
{
    /**
     * Рассчитать результаты теста на основе ответов
     * 
     * @param array<string, mixed> $test Данные теста
     * @param array<int, string|array> $answers Ответы студента (индекс вопроса => ответ)
     * @return array<string, mixed> Результаты расчета
     */
    public function calculateResults(array $test, array $answers): array
    {
        $config = $test['calculation_config'] ?? null;
        
        // Если конфигурация расчета не задана, возвращаем пустой результат
        if (!$config || !isset($config['type'])) {
            return [
                'calculated_score' => null,
                'interpretation' => null,
            ];
        }
        
        $calculationType = $config['type'];
        $questions = $test['questions'] ?? [];
        
        switch ($calculationType) {
            case 'sum':
                return $this->calculateSum($questions, $answers, $config);
            
            case 'average':
                return $this->calculateAverage($questions, $answers, $config);
            
            case 'categories':
                return $this->calculateCategories($questions, $answers, $config);
            
            case 'multi_scale':
                return $this->calculateMultiScale($questions, $answers, $config);
            
            case 'percentage':
                return $this->calculatePercentage($questions, $answers, $config);
            
            default:
                return [
                    'calculated_score' => null,
                    'interpretation' => null,
                ];
        }
    }
    
    /**
     * Расчет суммы баллов
     */
    private function calculateSum(array $questions, array $answers, array $config): array
    {
        $score = 0;
        $scaleQuestions = $config['scale_questions'] ?? [];
        
        // Если указаны конкретные вопросы, считаем только их
        if (!empty($scaleQuestions)) {
            foreach ($scaleQuestions as $qIndex) {
                if (!isset($questions[$qIndex])) continue;
                
                $question = $questions[$qIndex];
                $answer = $answers[$qIndex] ?? null;
                
                if ($answer === null) continue;
                
                // Для вопросов типа scale - ответ уже число
                if (($question['type'] ?? '') === 'scale' && is_numeric($answer)) {
                    $score += (int)$answer;
                }
                // Для вопросов с вариантами ответов - ищем балл в варианте
                elseif (in_array($question['type'] ?? '', ['multiple_choice', 'multiple_select']) && !empty($question['options'])) {
                    $answerText = is_array($answer) ? $answer : [$answer];
                    foreach ($answerText as $ans) {
                        foreach ($question['options'] as $option) {
                            if (($option['text'] ?? '') === (string)$ans) {
                                $optionScore = isset($option['score']) ? (int)$option['score'] : 0;
                                $score += $optionScore;
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            // Иначе считаем все вопросы
            foreach ($questions as $qIndex => $question) {
                $answer = $answers[$qIndex] ?? null;
                if ($answer === null) continue;
                
                // Для вопросов типа scale - ответ уже число
                if (($question['type'] ?? '') === 'scale' && is_numeric($answer)) {
                    $score += (int)$answer;
                }
                // Для вопросов с вариантами ответов - ищем балл в варианте
                elseif (in_array($question['type'] ?? '', ['multiple_choice', 'multiple_select']) && !empty($question['options'])) {
                    $answerText = is_array($answer) ? $answer : [$answer];
                    foreach ($answerText as $ans) {
                        foreach ($question['options'] as $option) {
                            if (($option['text'] ?? '') === (string)$ans) {
                                $optionScore = isset($option['score']) ? (int)$option['score'] : 0;
                                $score += $optionScore;
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        // Определяем категорию, если заданы диапазоны
        $interpretation = $this->interpretByCategories($score, $config);
        
        return [
            'calculated_score' => $score,
            'interpretation' => $interpretation,
        ];
    }
    
    /**
     * Расчет среднего значения
     */
    private function calculateAverage(array $questions, array $answers, array $config): array
    {
        $sum = 0;
        $count = 0;
        $scaleQuestions = $config['scale_questions'] ?? [];
        
        if (!empty($scaleQuestions)) {
            foreach ($scaleQuestions as $qIndex) {
                $answer = $answers[$qIndex] ?? null;
                if ($answer !== null && is_numeric($answer)) {
                    $sum += (int)$answer;
                    $count++;
                }
            }
        } else {
            foreach ($questions as $qIndex => $question) {
                if (($question['type'] ?? '') === 'scale') {
                    $answer = $answers[$qIndex] ?? null;
                    if ($answer !== null && is_numeric($answer)) {
                        $sum += (int)$answer;
                        $count++;
                    }
                }
            }
        }
        
        $average = $count > 0 ? round($sum / $count, 2) : 0;
        
        // Определяем категорию
        $interpretation = $this->interpretByCategories($average, $config);
        
        return [
            'calculated_score' => $average,
            'interpretation' => $interpretation,
        ];
    }
    
    /**
     * Расчет по категориям (сумма баллов с определением категории)
     */
    private function calculateCategories(array $questions, array $answers, array $config): array
    {
        // Используем ту же логику, что и для суммы
        $result = $this->calculateSum($questions, $answers, $config);
        
        // Категория уже определена в interpretByCategories
        return $result;
    }
    
    /**
     * Расчет для нескольких шкал (например, HADS - Tashvish и Depressiya)
     */
    private function calculateMultiScale(array $questions, array $answers, array $config): array
    {
        $scales = $config['scales'] ?? [];
        
        if (empty($scales)) {
            return [
                'calculated_score' => null,
                'interpretation' => null,
                'scales' => [],
            ];
        }
        
        $scaleResults = [];
        
        // Рассчитываем результат для каждой шкалы
        foreach ($scales as $scale) {
            $scaleName = $scale['name'] ?? 'Noma\'lum';
            $questionIndices = $scale['question_indices'] ?? [];
            $categories = $scale['categories'] ?? [];
            
            if (empty($questionIndices) || empty($categories)) {
                continue;
            }
            
            // Рассчитываем сумму баллов для этой шкалы
            $score = 0;
            // Получаем список обратных вопросов (где "Йўқ" дает балл вместо "Ҳа")
            $reverseQuestionIndices = $scale['reverse_question_indices'] ?? [];
            
            foreach ($questionIndices as $qIndex) {
                if (!isset($questions[$qIndex])) {
                    continue;
                }
                
                $question = $questions[$qIndex];
                $answer = $answers[$qIndex] ?? null;
                
                if ($answer === null) {
                    continue;
                }
                
                $isReverse = in_array($qIndex, $reverseQuestionIndices, true);
                
                // Для вопросов типа scale - ответ уже число
                if (($question['type'] ?? '') === 'scale' && is_numeric($answer)) {
                    $optionScore = (int)$answer;
                    // Для обратных вопросов инвертируем: если был 1, становится 0, если был 0, становится 1
                    if ($isReverse) {
                        $optionScore = $optionScore === 1 ? 0 : 1;
                    }
                    $score += $optionScore;
                }
                // Для вопросов с вариантами ответов - ищем балл в варианте
                elseif (in_array($question['type'] ?? '', ['multiple_choice', 'multiple_select']) && !empty($question['options'])) {
                    $answerText = is_array($answer) ? $answer : [$answer];
                    foreach ($answerText as $ans) {
                        foreach ($question['options'] as $option) {
                            if (($option['text'] ?? '') === (string)$ans) {
                                $optionScore = isset($option['score']) ? (int)$option['score'] : 0;
                                // Для обратных вопросов инвертируем: если был 1, становится 0, если был 0, становится 1
                                if ($isReverse) {
                                    $optionScore = $optionScore === 1 ? 0 : 1;
                                }
                                $score += $optionScore;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Определяем категорию для этой шкалы
            $interpretation = null;
            foreach ($categories as $category) {
                $min = (float)($category['min'] ?? 0);
                $max = (float)($category['max'] ?? PHP_FLOAT_MAX);
                
                if ($score >= $min && $score <= $max) {
                    $interpretation = [
                        'category' => $category['name'] ?? 'Noma\'lum',
                        'description' => $category['description'] ?? '',
                        'score' => $score,
                    ];
                    break;
                }
            }
            
            // Если не попал ни в одну категорию, используем первую
            if ($interpretation === null && !empty($categories)) {
                $firstCategory = $categories[0];
                $interpretation = [
                    'category' => $firstCategory['name'] ?? 'Noma\'lum',
                    'description' => $firstCategory['description'] ?? '',
                    'score' => $score,
                ];
            }
            
            $scaleResults[$scaleName] = [
                'score' => $score,
                'interpretation' => $interpretation,
            ];
        }
        
        return [
            'calculated_score' => null, // Для multi_scale нет общего балла
            'interpretation' => null,   // Интерпретация по каждой шкале отдельно
            'scales' => $scaleResults,
        ];
    }
    
    /**
     * Расчет процентного соотношения ответов
     */
    private function calculatePercentage(array $questions, array $answers, array $config): array
    {
        $totalSelections = 0; // Общее количество выбранных вариантов
        $answerCounts = []; // Количество выборов каждого варианта
        
        // Подсчитываем ответы по типам
        foreach ($questions as $qIndex => $question) {
            $answer = $answers[$qIndex] ?? null;
            if ($answer !== null && $answer !== '') {
                // Для multiple_select ответ может быть строкой с разделителями или массивом
                if (is_array($answer)) {
                    foreach ($answer as $singleAnswer) {
                        $answerKey = trim((string)$singleAnswer);
                        if (!empty($answerKey)) {
                            $answerCounts[$answerKey] = ($answerCounts[$answerKey] ?? 0) + 1;
                            $totalSelections++;
                        }
                    }
                } elseif (is_string($answer) && strpos($answer, ',') !== false) {
                    // Ответ сохранен как строка с запятыми и пробелами (multiple_select: "Ko'k, Yashil, Qizil")
                    // Используем preg_split для более надежного разбиения
                    $answerParts = preg_split('/,\s*/', $answer);
                    foreach ($answerParts as $part) {
                        $answerKey = trim($part);
                        if (!empty($answerKey)) {
                            $answerCounts[$answerKey] = ($answerCounts[$answerKey] ?? 0) + 1;
                            $totalSelections++;
                        }
                    }
                } else {
                    // Одиночный ответ
                    $answerKey = trim((string)$answer);
                    if (!empty($answerKey)) {
                        $answerCounts[$answerKey] = ($answerCounts[$answerKey] ?? 0) + 1;
                        $totalSelections++;
                    }
                }
            }
        }
        
        // Рассчитываем проценты от общего количества выбранных вариантов
        $percentages = [];
        foreach ($answerCounts as $answerKey => $count) {
            $percentages[$answerKey] = $totalSelections > 0 ? round(($count / $totalSelections) * 100, 2) : 0;
        }
        
        // Определяем преобладающий ответ
        $dominantAnswer = null;
        $maxPercentage = 0;
        foreach ($percentages as $answerKey => $percentage) {
            if ($percentage > $maxPercentage) {
                $maxPercentage = $percentage;
                $dominantAnswer = $answerKey;
            }
        }
        
        // Интерпретация на основе преобладающего ответа
        $interpretation = null;
        if ($dominantAnswer !== null) {
            $interpretation = $this->interpretByAnswer($dominantAnswer, $config);
        }
        
        return [
            'calculated_score' => $maxPercentage,
            'percentages' => $percentages,
            'dominant_answer' => $dominantAnswer,
            'interpretation' => $interpretation,
        ];
    }
    
    /**
     * Интерпретация результата по категориям (диапазонам)
     */
    private function interpretByCategories(float $score, array $config): ?array
    {
        $categories = $config['categories'] ?? [];
        
        if (empty($categories)) {
            return null;
        }
        
        // Ищем категорию, в которую попадает балл
        foreach ($categories as $category) {
            $min = (float)($category['min'] ?? 0);
            $max = (float)($category['max'] ?? PHP_FLOAT_MAX);
            
            if ($score >= $min && $score <= $max) {
                return [
                    'category' => $category['name'] ?? 'Noma\'lum',
                    'description' => $category['description'] ?? '',
                    'score' => $score,
                ];
            }
        }
        
        // Если не попал ни в одну категорию, возвращаем первую или последнюю
        if (!empty($categories)) {
            $firstCategory = $categories[0];
            return [
                'category' => $firstCategory['name'] ?? 'Noma\'lum',
                'description' => $firstCategory['description'] ?? '',
                'score' => $score,
            ];
        }
        
        return null;
    }
    
    /**
     * Интерпретация результата по типу ответа
     */
    private function interpretByAnswer(string $answer, array $config): ?array
    {
        $answerInterpretations = $config['answer_interpretations'] ?? [];
        
        if (isset($answerInterpretations[$answer])) {
            $interpretation = $answerInterpretations[$answer];
            return [
                'category' => $interpretation['category'] ?? $answer,
                'description' => $interpretation['description'] ?? '',
                'answer' => $answer,
            ];
        }
        
        return [
            'category' => $answer,
            'description' => '',
            'answer' => $answer,
        ];
    }
    
    /**
     * Получить статистику распределения результатов по категориям
     * 
     * @param array<int, array<string, mixed>> $results Все результаты теста
     * @param array<string, mixed>|null $test Данные теста (для определения типа расчета)
     * @return array<string, mixed> Статистика
     */
    public function getCategoryStatistics(array $results, ?array $test = null): array
    {
        $calcConfig = $test['calculation_config'] ?? null;
        $calcType = $calcConfig['type'] ?? null;
        $isMultiScale = ($calcType === 'multi_scale');
        
        if ($isMultiScale) {
            // Для multi_scale собираем статистику по каждой шкале отдельно
            $scales = $calcConfig['scales'] ?? [];
            $scaleStatistics = [];
            
            foreach ($scales as $scale) {
                $scaleName = $scale['name'] ?? 'Noma\'lum';
                $categoryCounts = [];
                $totalResults = 0;
                
                foreach ($results as $result) {
                    $resultScales = $result['scales'] ?? null;
                    if ($resultScales && isset($resultScales[$scaleName])) {
                        $scaleResult = $resultScales[$scaleName];
                        $interpretation = $scaleResult['interpretation'] ?? null;
                        if ($interpretation && isset($interpretation['category'])) {
                            $category = $interpretation['category'];
                            $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                            $totalResults++;
                        }
                    }
                }
                
                // Рассчитываем проценты
                $percentages = [];
                foreach ($categoryCounts as $category => $count) {
                    $percentages[$category] = $totalResults > 0 ? round(($count / $totalResults) * 100, 2) : 0;
                }
                
                $scaleStatistics[$scaleName] = [
                    'categories' => $categoryCounts,
                    'percentages' => $percentages,
                    'total' => $totalResults,
                ];
            }
            
            return [
                'is_multi_scale' => true,
                'scales' => $scaleStatistics,
            ];
        } else {
            $isPercentageType = ($calcType === 'percentage');
            
            if ($isPercentageType) {
                // Для процентных тестов собираем статистику по исходным ответам (цветам), а не по категориям
                $answerCounts = []; // Количество выборов каждого ответа (цвета)
                $totalSelections = 0; // Общее количество выборов всех ответов
                
                foreach ($results as $result) {
                    $answers = $result['answers'] ?? [];
                    
                    // Обрабатываем ответы - они могут быть в разных форматах
                    foreach ($answers as $qIndex => $answer) {
                        if ($answer !== null && $answer !== '') {
                            $answerArray = [];
                            
                            // Разбираем ответ в массив
                            if (is_array($answer)) {
                                $answerArray = $answer;
                            } elseif (is_string($answer) && strpos($answer, ',') !== false) {
                                // Ответ сохранен как строка с разделителями (multiple_select)
                                $answerArray = preg_split('/,\s*/', $answer);
                            } else {
                                // Одиночный ответ
                                $answerArray = [trim((string)$answer)];
                            }
                            
                            // Подсчитываем каждый ответ
                            foreach ($answerArray as $singleAnswer) {
                                $answerKey = trim((string)$singleAnswer);
                                if (!empty($answerKey)) {
                                    $answerCounts[$answerKey] = ($answerCounts[$answerKey] ?? 0) + 1;
                                    $totalSelections++;
                                }
                            }
                        }
                    }
                }
                
                // Рассчитываем проценты от общего количества выборов
                $percentages = [];
                foreach ($answerCounts as $answerKey => $count) {
                    $percentages[$answerKey] = $totalSelections > 0 ? round(($count / $totalSelections) * 100, 2) : 0;
                }
                
                return [
                    'is_multi_scale' => false,
                    'is_percentage_type' => true,
                    'categories' => $answerCounts, // Используем 'categories' для совместимости, но храним ответы
                    'percentages' => $percentages,
                    'total' => $totalSelections,
                ];
            } else {
                // Для обычных типов расчета - одна статистика по категориям
                $categoryCounts = [];
                $totalResults = 0;
                
                foreach ($results as $result) {
                    $interpretation = $result['interpretation'] ?? null;
                    if ($interpretation && isset($interpretation['category'])) {
                        $category = $interpretation['category'];
                        $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                        $totalResults++;
                    }
                }
                
                // Рассчитываем проценты
                $percentages = [];
                foreach ($categoryCounts as $category => $count) {
                    $percentages[$category] = $totalResults > 0 ? round(($count / $totalResults) * 100, 2) : 0;
                }
                
                return [
                    'is_multi_scale' => false,
                    'is_percentage_type' => false,
                    'categories' => $categoryCounts,
                    'percentages' => $percentages,
                    'total' => $totalResults,
                ];
            }
        }
    }
    
    /**
     * Получить средний балл по результатам
     * 
     * @param array<int, array<string, mixed>> $results Все результаты теста
     * @return float|null Средний балл или null, если нет результатов
     */
    public function getAverageScore(array $results): ?float
    {
        $scores = [];
        
        foreach ($results as $result) {
            $score = $result['calculated_score'] ?? null;
            if ($score !== null && is_numeric($score)) {
                $scores[] = (float)$score;
            }
        }
        
        if (empty($scores)) {
            return null;
        }
        
        return round(array_sum($scores) / count($scores), 2);
    }
}

