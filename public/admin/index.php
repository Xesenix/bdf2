<?php

require_once __DIR__ . '/../../app/admin/bootstrap.php';

$start = microtime(true);
$app->run();
var_dump(microtime(true) - $start);
