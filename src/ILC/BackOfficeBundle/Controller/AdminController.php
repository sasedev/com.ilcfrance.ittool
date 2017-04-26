<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Form\AdministratorAddForm;
use ILC\DataBundle\Entity\Administrator;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function indexAction(Request $request)
	{
		$this->addTwigVar('pagetitle', "Liste des Administrateurs");
		$em = $this->getEntityManager();
		$admins = $em->getRepository('ILCDataBundle:Administrator')->getAll();
		$this->addTwigVar('admins', $admins);
		$admin = $this->getSecurityTokenStorage()->getToken()->getUser();
		$this->addTwigVar('admin', $admin);
		return $this->render('ILCBackOfficeBundle:Admin:index.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'ajouter un nouvel Administrateur');
		$admin = new Administrator();
		$admform = $this->createForm(AdministratorAddForm::class, $admin);
		if ($request->getMethod() == 'POST') {
			$admform->handleRequest($request);
			if ($admform->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($admin);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_admin_list'));
			}
		}

		$this->addTwigVar('adm_form', $admform->createView());
		return $this->render('ILCBackOfficeBundle:Admin:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function delAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$admin = $em->getRepository('ILCDataBundle:Administrator')->findOneById($id);
		if (null == $admin) {
			return $this->redirect($this->generateUrl('bo_admin_list'));
		}
		$em->remove($admin);
		$em->flush();
		return $this->redirect($this->generateUrl('bo_admin_list'));
	}
}
