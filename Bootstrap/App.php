<?php
declare(strict_types=1);

use App\Core\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

// Load environment variables
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

// Load config array
$config = require $root . '/Config/config.php';

// Error reporting per env/debug
if (($config['app']['env'] ?? 'development') === 'development' && ($config['app']['debug'] ?? true)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Jakarta');

// Build the App and return it
$app = new App($root, $config);
return $app;
