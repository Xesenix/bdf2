<?php

namespace RPG;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;

class RpgController extends AbstractController {
	public function game(Application $app)
	{
		return $this->render('rpg/game.html', array(
			'pageTitle' => 'Magiczna opowieść',
		));
	}
	
	public function inventory(Application $app)
	{
		return array(
			'Notes',
			'Łuk',
			'Strzały',
		);
	}
	
	public function skills(Application $app)
	{
		return array(
			'Tropienie',
			'Czytanie',
			'Pisanie',
		);
	}
	
	public function gameTabs(Application $app)
	{
		return array(
			array(
				'route' => '/profil',
				'view' => 'part/profil.html',
				'label' => 'Profil',
			),
			array(
				'route' => '/inventory',
				'view' => 'part/inventory.html',
				'label' => 'Inwentarz',
			),
			array(
				'route' => '/skills',
				'view' => 'part/skills.html',
				'label' => 'Umiejętności',
			),
			array(
				'route' => '/map',
				'view' => 'part/map.html',
				'label' => 'Mapa',
			),
		);
	}
}