<?php
use BDF2\Content\Entity\Article;
use BDF2\Module\Entity\Module;

require_once 'bootstrap.php';

$entityManager = $app['orm.em'];

$article = new Article();
$article
	->setSlug('wstep')
	->setTitle('Wstęp')
	->setContent('<p>Nazywam się Paweł Kapalla, często występuje pod nickiem Xesenix, jestem grafikiem i programistą, a Ty trafiłeś do mojego portfolio. Znajdziesz tu listę prowadzonych przez ze mnie projektów, jak również galerię moich grafik oraz kilka przydatnych informacji.</p><p>Zapraszam do zwiedzania.</p>')
	->setAuthor('Xesenix')
	->setDate(new DateTime('2012-01-12'));

$entityManager->persist($article);

$module = new Module();
$module->name = 'Test sidebar';
$module->position = 'sidebar';
$module->content = 'TEST SIDEBAR';

$entityManager->persist($module);

$entityManager->flush();
