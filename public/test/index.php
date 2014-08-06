<?php

require_once __DIR__ . '/../../app/projects/test/bootstrap.php';

$app['public_dir'] = __DIR__;

$start = microtime(true);
$app->run();
var_dump(microtime(true) - $start);
