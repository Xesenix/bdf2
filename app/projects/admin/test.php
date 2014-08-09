<?php
use BDF2\Content\Entity\Article;
use BDF2\Module\Entity\Module;

require_once 'bootstrap.php';

$em = $app['orm.em'];

$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
$article = $em->find('BDF2\Content\Entity\Article', 5 /*article id*/);
$logs = $repo->getLogEntries($article);

var_dump($logs);