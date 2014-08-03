<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$debug = true;

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => $debug,
	'path.project' => __DIR__,
	'projectName' => 'Black Dragon Admin - administracja stroną testową',
	'date.default_format' => 'd/m/Y',
));

// --- Registering modules ---

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

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

$app->register(new BDF2\Form\Provider\FormServiceProvider());

// --- View ---

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => $app->share(function() {
		return array(
			__DIR__ . '/views',
			__DIR__ . '/../../views',
		);
	}),
	'twig.options' => array(
		'cache' => __DIR__ . '/cache/twig',
	),
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new BDF2\Resources\Provider\ResourceServiceProvider(), array(
	'resources.dev_mode' => $debug,
));

$app['resources.paths'] = $app->share($app->extend('resources.paths', function ($paths) {
	$paths[] = __DIR__ . '/../../resources';
	$paths[] = __DIR__ . '/resources';
	
	return $paths;
}));

// --- Modules ---
$app->register(new BDF2\Module\Provider\AdminModuleServiceProvider(), array(
	'module.routes.admin_prefix' => '/',
));
$app->register(new BDF2\Module\Provider\ModuleServiceProvider());

$app->register(new BDF2\Content\Provider\AdminContentServiceProvider());
$app->register(new BDF2\JQuery\Provider\JQueryServiceProvider());
$app->register(new BDF2\TinyMCE\Provider\TinyMceServiceProvider());
