<?php

require_once __DIR__ . '/../../app/test/bootstrap.php';

$app['path.root'] = __DIR__;

$start = microtime(true);
$app->run();
var_dump(microtime(true) - $start);
