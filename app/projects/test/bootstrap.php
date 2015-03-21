<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$debug = true;

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => $debug,
	'project_dir' => __DIR__,
	'vendor_dir' => __DIR__ . '/../../../vendor',
	'projectName' => 'Black Dragon Framework 2',
	'date.default_format' => 'd/m/Y',
));

// --- Registering modules ---

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// --- DB ---

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.default_options' => include "config/db.php",
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

// for mixing with AngularJs templates
/*
$app['twig'] = $app->share($app->extend('twig', function($twig) {
	$twig->setLexer(new \Twig_Lexer($twig, array(
		'tag_comment'     => array('<#', '#>'),
		'tag_block'       => array('<%', '%>'),
		'tag_variable'    => array('<=', '=>'),
		'whitespace_trim' => '-',
		'interpolation'   => array('#<', '>'),
	)));
	
	return $twig;
}));
*/

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new BDF2\Resources\Provider\ResourceServiceProvider(), array(
	'resources.assets.publish_mode' => !$debug,
));

$app['resources.assets.resource_dir'] = $app->share($app->extend('resources.assets.resource_dir', function ($paths) {
	$paths[] = __DIR__ . '/../../../vendor/resources/jquery';
	$paths[] = __DIR__ . '/../../../vendor/resources/jquery-ui';
	$paths[] = __DIR__ . '/../../../vendor/resources/bootstrap';
	$paths[] = __DIR__ . '/../../../vendor/resources/vis';
	$paths[] = __DIR__ . '/../../../vendor/resources/require';
	$paths[] = __DIR__ . '/../../../vendor/resources/angularjs';
	$paths[] = __DIR__ . '/../../resources';
	$paths[] = __DIR__ . '/resources';
	
	return $paths;
}));

$app['resources.assets.compositions'] = $app->share($app->extend('resources.assets.compositions', function ($compositions) {
	$compositions['css/page.css'] = array(
		'css/bootstrap.min.css',
		'css/bootstrap-theme.min.css',
		'css/layout.css',
	);
	
	$compositions['js/core.js'] = array(
		'js/jquery/jquery-1.11.1.min.js',
		'js/bootstrap.min.js',
		'js/core.js',
	);
	
	// can be used as alias to file in resources path
	$compositions['js/vis.js'] = array(
		'dist/vis.min.js',
	);
	
	// can be used as alias to file in resources path
	$compositions['css/vis.css'] = array(
		'dist/vis.css',
	);
	
	return $compositions;
}));

// --- Modules ---

$app->register(new BDF2\Content\Provider\ContentServiceProvider(), array(
	'content.routes.prefix' => '/',
));

// --- Simple Controller Add

// define asset composition to use
$app['resources.assets.compositions'] = $app->share($app->extend('resources.assets.compositions', function ($compositions) {
	$compositions['css/rpg.css'] = array(
		'css/bootstrap.min.css',
		'css/bootstrap-theme.min.css',
		'css/layout.css',
		'css/rpg.css',
	);

	$compositions['js/rpg/core.js'] = array(
		//'js/require.js',
		'js/jquery/jquery-1.11.1.min.js',
		'js/bootstrap.min.js',
		'js/angular.min.js',
		'js/rpg/core.js',
	);

	return $compositions;
}));

$app['rpg.controllers.game_controller'] = $app->share(function() use ($app) {
	return new RPG\RpgController($app);
});

$app->get('/rpg', 'rpg.controllers.game_controller:profil')->bind('rpg');
$app->get('/rpg/profil', 'rpg.controllers.game_controller:profil')->bind('rpg:profil');
$app->get('/rpg/inwentarz', 'rpg.controllers.game_controller:inventory')->bind('rpg:inventory');
$app->get('/rpg/umiejetnosci', 'rpg.controllers.game_controller:skills')->bind('rpg:skills');

// adding additional parameters to twig via prerender event
// global event
/*
$app->on('twig:render', function() use($app) {

	if ($app['request']->get('_route') == 'article')
	{
		$app['twig']->addGlobal('activItem', 'articles');
	}
});
*/
// specific action event
$app->on('article:render', function() use($app) {
	$app['twig']->addGlobal('activItem', 'articles');
});