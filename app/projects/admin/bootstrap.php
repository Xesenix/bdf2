<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

$debug = true;

$app = new Silex\Application(array(
	'locale' => 'pl',
	'debug' => $debug,
	'project_dir' => __DIR__,
	'vendor_dir' => __DIR__ . '/../../../vendor',
	'projectName' => 'Black Dragon Admin - administracja stroną testową',
	'date.default_format' => 'd/m/Y',
	'date_time.default_format' => 'H:s:i d/m/Y',
));

// --- Registering modules ---

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// -- Session --

$app->register(new Silex\Provider\SessionServiceProvider());

// -- DB ---

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.default_options' => include "config/db.php",
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
	'twig.path' => $app->share(function() {
		return array(
			__DIR__ . '/views',
			__DIR__ . '/../../views',
		);
	}),
	'twig.options' => array(
		'cache' => __DIR__ . '/cache/twig',
	),
	'twig.form.templates' => array(
		'form_div_layout.html',
	),
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new BDF2\Resources\Provider\ResourceServiceProvider(), array(
	'resources.assets.publish_mode' => !$debug,
));

$app['resources.assets.resource_dir'] = $app->share($app->extend('resources.assets.resource_dir', function ($paths) {
	$paths[] = __DIR__ . '/../../../vendor/resources/jquery';
	$paths[] = __DIR__ . '/../../../vendor/resources/jquery-ui';
	$paths[] = __DIR__ . '/../../../vendor/resources/tinymce';
	$paths[] = __DIR__ . '/../../../vendor/resources/bootstrap';
	$paths[] = __DIR__ . '/../../../vendor/resources/vis';
	$paths[] = __DIR__ . '/../../resources';
	$paths[] = __DIR__ . '/resources';
	
	return $paths;
}));

$app['resources.assets.compositions'] = $app->share($app->extend('resources.assets.compositions', function ($compositions) {
	$compositions['css/admin.css'] = array(
		//'css/reset.css',
		//'css/typography.css',
		'css/bootstrap.min.css',
		'css/bootstrap-theme.min.css',
		'css/bootstrap.addons.css',
		'css/layout.css',
	);
	// tinymce needs its composition file to be located in tinymce folder or rewrite root for its plugins
	$compositions['js/tinymce/tinymce.min.js'] = array(
		'js/tinymce/tinymce.min.js',
		'js/tinymce/jquery.tinymce.min.js',
	);
	
	$compositions['js/jquery/jquery.js'] = array(
		'js/jquery/jquery-1.11.1.min.js',
	);
	
	$compositions['js/bootstrap.js'] = array(
		'js/bootstrap.min.js',
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

$app->register(new BDF2\Form\Provider\FormServiceProvider());

// -- Security --

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
	'security.firewalls' => array(
		'login' => array(
			'pattern' => '^/(login|resources.*)$',
			'anonymous' => true,
		),
		'admin' => array(
			'pattern' => '^.+$',
			'form' => array(
				'login_path' => '/login',
				'check_path' => '/admin/login_check',
			),
			'users' => include 'config/users.php',
			'logout' => array(
				'logout_path' => '/logout',
				'target_url' => '/login',
			),
		),
	),
));

$app->get('/login', function(Symfony\Component\HttpFoundation\Request $request) use ($app) {
	return $app['twig']->render('login.html', array(
		'error' => $app['security.last_error']($request),
		'lastUsername' => $app['session']->get('_security.last_username'),
		'pageTitle' => 'Login',
	));
})
->bind('login');

// --- Modules ---

$app->register(new BDF2\Widget\Provider\AdminWidgetServiceProvider(), array(
	'widgets.routes.admin_prefix' => '/',
));

$app->register(new BDF2\Navigation\Provider\AdminNavigationServiceProvider());

$app->register(new BDF2\Content\Provider\AdminContentServiceProvider());

$app->register(new BDF2\Widget\Provider\WidgetServiceProvider());

$app->register(new BDF2\Avatar\Provider\AvatarServiceProvider());

// Adding gravatar as avatar provider
$app['helpers.gravatar'] = $app->share(function() use ($app) {
	return new GetNinja\Gravatar\Gravatar();
});

$app['helpers.avatar.provider'] = $app->share($app->extend('helpers.avatar.provider', function ($provider) use($app) {
	$provider->register(new BDF2\Avatar\Provider\AvatarCallbackProvider(function ($email, $size) use($app) {
		$errors = $app['validator']->validateValue($email, new Symfony\Component\Validator\Constraints\Email());
		
		if (count($errors) == 0) 
		{
			return $app['helpers.gravatar']->getGravatar($email, $size);
		}
		
		return null;
	}, 0));
	
	return $provider;
}));

//$app['twig']->addGlobal('message', 'Test');
