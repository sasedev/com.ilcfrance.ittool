<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Form\GroupmoduleEditForm;
use ILC\BackOfficeBundle\Form\GroupmoduleAddForm;
use ILC\DataBundle\Entity\Groupmodule;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupmoduleController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function indexAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Modules');

		$em = $this->getEntityManager();

		$gms = $em->getRepository('ILCDataBundle:Groupmodule')->getAll();
		$this->addTwigVar('gms', $gms);

		return $this->render('ILCBackOfficeBundle:GM:index.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Ajouter un groupe de Modules');

		$gm = new Groupmodule();

		$gmForm = $this->createForm(GroupmoduleAddForm::class, $gm);
		if ($request->getMethod() == 'POST') {
			$gmForm->handleRequest($request);
			if ($gmForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($gm);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_groupemodules_list'));
			}
		}
		$this->addTwigVar('gm_form', $gmForm->createView());
		return $this->render('ILCBackOfficeBundle:GM:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function editAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$gm = $em->getRepository('ILCDataBundle:Groupmodule')->findOneById($id);
		if (null == $gm) {
			return $this->redirect($this->generateUrl("bo_groupemodules_list"));
		}
		$gmForm = $this->createForm(GroupmoduleEditForm::class, $gm);
		if ($request->getMethod() == 'POST') {
			$gmForm->handleRequest($request);
			if ($gmForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($gm);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_groupemodules_edit', array(
					'id' => $id
				)));
			}
		}
		$this->addTwigVar('pagetitle', 'Modifier le groupe de Modules ' . $gm->getName());
		$this->addTwigVar('gm', $gm);
		$this->addTwigVar('gm_form', $gmForm->createView());

		return $this->render('ILCBackOfficeBundle:GM:edit.html.twig', $this->getTwigVars());
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
		$gm = $em->getRepository('ILCDataBundle:Groupmodule')->findOneById($id);
		if (null != $gm) {
			$em->remove($gm);
			$em->flush();
		}
		return $this->redirect($this->generateUrl("bo_groupemodules_list"));
	}
}
