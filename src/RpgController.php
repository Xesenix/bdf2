<?php

namespace RPG
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use BDF2\Controllers\AbstractController;
	
	class RpgController extends AbstractController {
		public function profil(Application $app)
		{
			return $this->render('rpg/profil.html', array(
				'pageTitle' => 'Profil postaci',
			));
		}
		
		public function inventory(Application $app)
		{
			return $this->render('rpg/profil.html', array(
				'pageTitle' => 'Inwentarz postaci',
			));
		}
		
		public function skills(Application $app)
		{
			return $this->render('rpg/profil.html', array(
				'pageTitle' => 'Drzewko umiejętności postaci',
			));
		}
		
		public function mapa(Application $app)
		{
			return 'mapa';
		}
	}
}