#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Commands\CountCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new CountCommand());
$app->run();