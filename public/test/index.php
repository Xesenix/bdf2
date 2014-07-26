<?php

require_once __DIR__ . '/../../app/test/bootstrap.php';

$start = microtime(true);
$app->run();
var_dump(microtime(true) - $start);
