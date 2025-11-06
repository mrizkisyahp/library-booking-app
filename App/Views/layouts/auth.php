<?php use App\Core\App; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(App::$app->getTitle()) ?></title>
    <link href="<?= $basePath === '' ? '' : $basePath ?>/css/output.css" rel="stylesheet">
</head>
<body class="min-h-dvh bg-primary">
    {{content}}
</body>
</html>
