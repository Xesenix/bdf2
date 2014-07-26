<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => true,
	'projectName' => 'Black Dragon Admin',
));

// --- Registering modules ---

// -- DB ---

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.default_options' => array(
		'driver'   => 'pdo_mysql',
	    'user'     => 'xesenix',
	    'password' => '***REMOVED***',
	    'dbname'   => 'test',
    ),
));

$app->register(new BDF2\ORM\Provider\ORMServiceProvider());

// --- Forms ---

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

// --- View ---

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => array(
		__DIR__ . '/views',
		__DIR__ . '/../views',
	)
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// --- Modules ---

$app->register(new BDF2\Content\Provider\AdminContentServiceProvider());

