<?php
declare(strict_types=1);

$app = require_once __DIR__ . '/../Bootstrap/App.php';

require_once __DIR__ . '/../Routes/Web.php';

$app->run();
