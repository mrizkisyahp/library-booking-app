<?php
use App\Core\App;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(App::$app->response->resolveTitle(App::$app->controller)) ?></title>
    <base href="<?= App::getBaseUrl() ?>/" />
    <link rel="stylesheet" href="css/output.css">
</head>

<body class="min-h-dvh bg-primary font-sans">
    {{content}}
    <script src="src/script.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</body>

</html>