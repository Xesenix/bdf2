<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => true,
	'projectName' => 'Black Dragon Framework 2',
	'date.default_format' => 'd/m/Y',
));

// --- Registering modules ---

// --- DB ---

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.default_options' => array(
		'driver'   => 'pdo_mysql',
	    'user'     => 'xesenix',
	    'password' => '***REMOVED***',
	    'dbname'   => 'test',
    ),
));

$app->register(new BDF2\ORM\Provider\ORMServiceProvider());

// --- View ---

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => array(
		__DIR__ . '/views',
		__DIR__ . '/../views',
	),
	'twig.options' => array(
		'cache' => __DIR__ . '/cache/twig',
	),
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// --- Modules ---

$app->register(new BDF2\Content\Provider\ContentServiceProvider(), array(
	'routes.content' => '/',
));


