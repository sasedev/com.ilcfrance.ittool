<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Form\AdministratorPasswordForm;
use ILC\BackOfficeBundle\Form\AdministratorProfileForm;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function profileAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Mon Profile');
		$admin = $this->getSecurityTokenStorage()->getToken()->getUser();
		$profileform = $this->createForm(AdministratorProfileForm::class, $admin);
		$passwdform = $this->createForm(AdministratorPasswordForm::class, $admin);
		if ($request->getMethod() == 'POST') {
			$profileform->handleRequest($request);
			if ($profileform->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($admin);
				$em->flush();
				return $this->redirect($this->generateUrl('_security_profile'));
			}
		}
		$this->addTwigVar('admin', $admin);
		$this->addTwigVar('profile_form', $profileform->createView());
		$this->addTwigVar('passwd_form', $passwdform->createView());
		return $this->render('ILCBackOfficeBundle:Security:profile.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function updatepassAction(Request $request)
	{
		$admin = $this->getSecurityTokenStorage()->getToken()->getUser();
		$passwdform = $this->createForm(AdministratorPasswordForm::class, $admin);
		$passwdform->handleRequest($request);
		if ($passwdform->isValid()) {
			$em = $this->getEntityManager();
			$em->persist($admin);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('_security_profile'));
	}
}
