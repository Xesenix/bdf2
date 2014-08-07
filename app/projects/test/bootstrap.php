<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$debug = false;

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => $debug,
	'project_dir' => __DIR__,
	'projectName' => 'Black Dragon Framework 2',
	'date.default_format' => 'd/m/Y',
));

// --- Registering modules ---

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

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
	'resources.assets.publish_mode' => !$debug,
));

$app['resources.assets.resource_dir'] = $app->share($app->extend('resources.assets.resource_dir', function ($paths) {
	$paths[] = __DIR__ . '/../../../vendor/resources';
	$paths[] = __DIR__ . '/../../resources';
	$paths[] = __DIR__ . '/resources';
	
	return $paths;
}));

$app['resources.assets.compositions'] = $app->share($app->extend('resources.assets.compositions', function ($compositions) {
	$compositions['css/page.css'] = array(
		'css/reset.css',
		'css/typography.css',
		'css/layout.css',
	);
	
	$compositions['js/core.js'] = array(
		'js/jquery/jquery-1.11.1.min.js',
		'js/core.js',
	);
	
	return $compositions;
}));

// --- Modules ---

$app->register(new BDF2\Content\Provider\ContentServiceProvider(), array(
	'content.routes.prefix' => '/',
));


