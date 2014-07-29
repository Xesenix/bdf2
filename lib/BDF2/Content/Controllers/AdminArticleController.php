<?php

namespace BDF2\Content\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use BDF2\Content\Entity\Article;
	use BDF2\Content\Form\Type\ArticleType;
	
	class AdminArticleController {
		
		public function __construct(Application $app)
		{
			$this->app = $app;
			$this->request = $app['request'];
		}
		
		public function listAction()
		{
			$entityManager = $this->app['orm.em'];
			
			return $this->app['twig']->render('article/list.html', array(
				'pageTitle' => 'Lista artykułów',
				'articles' => $entityManager->getRepository('BDF2\Content\Entity\Article')->findAll()
			));
		}
		
		public function editAction()
		{
			$entityManager = $this->app['orm.em'];
			
			$id = $this->request->get('id');
			
			$article = $entityManager->getRepository('BDF2\Content\Entity\Article')->findOneBy(array('id' => $id));
			
			if ($article == null)
			{
				$this->app->abort(404, "Artykuł id:$id nie istnieje.");
			}
			
			$form = $this->app['form.factory']->create(new ArticleType($this->app['form.data_transformer.date_time']), $article);
			
			if ($this->request->getMethod() == 'POST')
			{
				$form->bind($request);
				
				if ($form->isValid())
				{
					$entityManager->persist($article);
					$entityManager->flush();
				}
			}
			
			return $this->app['twig']->render('article/edit.html', array(
				'pageTitle' => 'Edycja artykułu',
				'form' => $form->createView(),
			));
		}
	}
}
