<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function indexAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Acceuil');
		return $this->render('ILCBackOfficeBundle:Default:index.html.twig', $this->getTwigVars());
	}
}
