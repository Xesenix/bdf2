<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = new Silex\Application();

// --- Config ---

$app['debug'] = true;
$app['projectName'] = 'Black dragon framework 2';

$app['db.config'] = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'xesenix',
    'password' => '***REMOVED***',
    'dbname'   => 'test',
);

$app['em.paths'] = array(
    __DIR__ . '/../../lib/BDF2/Content/Entity',
);

$app['em'] = function() use ($app) {
	$config = Setup::createAnnotationMetadataConfiguration($app['em.paths'], $app['debug']);
	$entityManager = EntityManager::create($app['db.config'], $config);
	
	return $entityManager;
};


$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => array(
		__DIR__ . '/views',
		__DIR__ . '/../views',
	)
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// --- Routing ---
$app->get('/', 'BDF2\\Controllers\ArticleController::listAction')->bind('articles');
$app->get('/{slug}', 'BDF2\\Controllers\ArticleController::articleAction')->bind('article');

// ---
$private = $app['controllers_factory'];
$private->get('/profil', 'RPG\\RpgController::profil')->bind('profil');
$private->get('/mapa', 'RPG\\RpgController::mapa')->bind('mapa');
// ---

$app->mount('/private', $private);
