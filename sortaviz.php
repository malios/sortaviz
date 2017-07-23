<?php
// set to run indefinitely if needed
set_time_limit(0);

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Malios\Sortaviz\Console\VisualizeCommand;

$app = new Application();
$app->add(new VisualizeCommand());
$app->run();
