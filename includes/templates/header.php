<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __("title") ?></title>
    <link rel="stylesheet" href="./includes/style/style.css?v=<?= file_exists(__DIR__ . '/../style/style.css') ? filemtime(__DIR__ . '/../style/style.css') : time() ?>">
    <link rel="stylesheet" href="./includes/lib/codemirror/codemirror.min.css">
    <script src="./includes/lib/codemirror/codemirror.min.js"></script>
    <script src="./includes/lib/codemirror/yaml.min.js"></script>
    <script src="./includes/lib/codemirror/properties.min.js"></script>
</head>
<body>