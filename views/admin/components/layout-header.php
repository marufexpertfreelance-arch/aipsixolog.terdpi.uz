<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Psixolog kabineti', ENT_QUOTES, 'UTF-8') ?> - TERDPI</title>
    <!-- Встроенный SVG favicon - символ психологии Ψ -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'><stop offset='0%25' style='stop-color:%238b5cf6'/><stop offset='100%25' style='stop-color:%234f46e5'/></linearGradient></defs><circle cx='50' cy='50' r='48' fill='url(%23g)'/><text x='50' y='70' font-size='60' text-anchor='middle' fill='white' font-family='serif'>Ψ</text></svg>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/admin-sidebar.css">
    <?php if (!empty($extraStyles)): ?>
        <style><?= $extraStyles ?></style>
    <?php endif; ?>
    <?php if (!empty($extraHead)): ?>
        <?= $extraHead ?>
    <?php endif; ?>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="admin-content-inner">
