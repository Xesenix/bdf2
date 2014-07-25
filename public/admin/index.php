<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;
$app['projectName'] = 'Black dragon framework 2';

$app['articles'] = array(
	'wstep' => new BDF2\Content\Article(
		'Wstęp',
		'<p>Nazywam się Paweł Kapalla, często występuje pod nickiem Xesenix, jestem grafikiem i programistą, a Ty trafiłeś do mojego portfolio. Znajdziesz tu listę prowadzonych przez ze mnie projektów, jak również galerię moich grafik oraz kilka przydatnych informacji.</p><p>Zapraszam do zwiedzania.</p>',
		'Xesenix',
		'2012-01-12'),
);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


$app->get('/', 'BDF2\\Controllers\ArticleController::listAction')->bind('articles');
$app->get('/{slug}', 'BDF2\\Controllers\ArticleController::articleAction')->bind('article');

// ---
$private = $app['controllers_factory'];
$private->get('/profil', 'RPG\\RpgController::profil')->bind('profil');
$private->get('/mapa', 'RPG\\RpgController::mapa')->bind('mapa');
// ---

$app->mount('/private', $private);


$app->run();
