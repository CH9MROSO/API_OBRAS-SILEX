<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// load configuration
require __DIR__ . '/../app/config/dev.php';
//require __DIR__ . '/../app/config/prod.php';
require __DIR__ . '/../app/app.php';

// run app.
//$app['http_cache']->run();
$app->run();