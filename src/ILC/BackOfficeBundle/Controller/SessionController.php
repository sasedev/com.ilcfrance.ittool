<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Form\SessionformationEditForm;
use ILC\BackOfficeBundle\Form\SessionformationAddForm;
use ILC\DataBundle\Entity\Sessionformation;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function byDateAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Sessions Par Date');

		$em = $this->getEntityManager();

		$ses = $em->getRepository('ILCDataBundle:Sessionformation')->getAll();
		$this->addTwigVar('ses', $ses);

		return $this->render('ILCBackOfficeBundle:Session:index.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Ajouter une session');

		$session = new Sessionformation();
		$sesFom = $this->createForm(SessionformationAddForm::class, $session);

		if ($request->getMethod() == 'POST') {
			$sesFom->handleRequest($request);
			if ($sesFom->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($session);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_groupemodules_list'));
			}
		}

		$this->addTwigVar('ses_form', $sesFom->createView());
		return $this->render('ILCBackOfficeBundle:Session:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function showAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($id);
		if (null == $session) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$this->addTwigVar('pagetitle', 'Informations de session ' . $session->getCode());

		$sesFom = $this->createForm(SessionformationEditForm::class, $session);
		$this->addTwigVar('session', $session);
		$this->addTwigVar('ses_form', $sesFom->createView());
		return $this->render('ILCBackOfficeBundle:Session:show.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function convocationAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($id);
		if (null == $session) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}

		$convocations = $session->getSessionincriptions();
		$i = 0;
		$err = "";
		foreach ($convocations as $c) {
			if ($c->getConvocation() == false) {
				try {
					$mvars = array();
					$mvars['stagiaire'] = $c->getStagiaire();
					$mvars['session'] = $session;
					$message = \Swift_Message::newInstance();
					$mvars['plan'] = $message->embed(\Swift_Image::fromPath($_SERVER['DOCUMENT_ROOT'] . '/res/images/plan.jpg'));
					$message->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($c->getStagiaire()->getEmail(), $c->getStagiaire()->getNom() . ' ' . $c->getStagiaire()->getPrenom());
					if (null != $c->getStagiaire()->getEmailcontact() && "" != $c->getStagiaire()->getEmailcontact()) {
						$message->setCc($c->getStagiaire()->getEmailcontact(), $c->getStagiaire()->getNomcontact());
					}
					$message->setSubject('Ateliers d\'anglais - Convocation ' . $session->getIntitule())->setBody($this->renderView('ILCBackOfficeBundle:Mail:convocation.html.twig', $mvars), 'text/html');
					$this->sendmail($message);
					$c->setConvocation(true);
					$em->persist($c);
					$em->flush();
					$i++;
				} catch (\Exception $e) {
					try {
						$mvars = array();
						$stagiaire = $c->getStagiaire();
						$mvars['stagiaire'] = $stagiaire;
						$mvars['session'] = $session;
						$message = \Swift_Message::newInstance();
						$mvars['plan'] = $message->embed(\Swift_Image::fromPath($_SERVER['DOCUMENT_ROOT'] . '/res/images/plan.jpg'));
						$message->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($c->getStagiaire()->getEmail(), $c->getStagiaire()->getNom() . ' ' . $c->getStagiaire()->getPrenom())->setSubject('Ateliers d\'anglais - Convocation ' . $session->getIntitule())->setBody($this->renderView('ILCBackOfficeBundle:Mail:convocation.html.twig', $mvars), 'text/html');
						$this->sendmail($message);
						$c->setConvocation(true);
						$em->persist($c);
						$em->flush();
						$i++;
					} catch (\Exception $e) {
						$err .= "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n";
					}
				}
			}
		}
		if ($err != "") {
			$this->addFlash('err', $err);
		}
		$this->addFlash('info', $i . ' mails de convocation ont étés envoyés aux stagiaires de cette session');
		return $this->redirect($this->generateUrl('bo_session_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function convocationfullAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($id);
		if (null == $session) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}

		$convocations = $session->getSessionincriptions();
		$i = 0;
		$err = "";
		foreach ($convocations as $c) {
			try {
				$mvars = array();
				$mvars['stagiaire'] = $c->getStagiaire();
				$mvars['session'] = $session;
				$message = \Swift_Message::newInstance();
				$mvars['plan'] = $message->embed(\Swift_Image::fromPath($_SERVER['DOCUMENT_ROOT'] . '/res/images/plan.jpg'));
				$message->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($c->getStagiaire()->getEmail(), $c->getStagiaire()->getNom() . ' ' . $c->getStagiaire()->getPrenom());
				if (null != $c->getStagiaire()->getEmailcontact() && "" != $c->getStagiaire()->getEmailcontact()) {
					$message->setCc($c->getStagiaire()->getEmailcontact(), $c->getStagiaire()->getNomcontact());
				}
				$message->setSubject('Ateliers d\'anglais - Convocation ' . $session->getIntitule())->setBody($this->renderView('ILCBackOfficeBundle:Mail:convocation.html.twig', $mvars), 'text/html');
				$this->sendmail($message);
				$c->setConvocation(true);
				$em->persist($c);
				$em->flush();
				$i++;
			} catch (\Exception $e) {
				try {
					$mvars = array();
					$stagiaire = $c->getStagiaire();
					$mvars['stagiaire'] = $stagiaire;
					$mvars['session'] = $session;
					$message = \Swift_Message::newInstance();
					$mvars['plan'] = $message->embed(\Swift_Image::fromPath($_SERVER['DOCUMENT_ROOT'] . '/res/images/plan.jpg'));
					$message->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($c->getStagiaire()->getEmail(), $c->getStagiaire()->getNom() . ' ' . $c->getStagiaire()->getPrenom())->setSubject('Ateliers d\'anglais - Convocation ' . $session->getIntitule())->setBody($this->renderView('ILCBackOfficeBundle:Mail:convocation.html.twig', $mvars), 'text/html');
					$this->sendmail($message);
					$c->setConvocation(true);
					$em->persist($c);
					$em->flush();
					$i++;
				} catch (\Exception $e) {
					$err .= "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n";
				}
			}
		}
		if ($err != "") {
			$this->addFlash('err', $err);
		}
		$this->addFlash('info', $i . ' mails de convocation ont étés envoyés aux stagiaires de cette session');
		return $this->redirect($this->generateUrl('bo_session_show', array(
			'id' => $id
		)));
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
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($id);
		if (null == $session) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$sesFom = $this->createForm(SessionformationEditForm::class, $session);
		$sesFom->handleRequest($request);
		if ($sesFom->isValid()) {
			$em = $this->getEntityManager();
			$em->persist($session);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('bo_session_show', array(
			'id' => $id
		)));
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
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($id);
		if (null == $session) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$em->remove($session);
		$em->flush();
		return $this->redirect($this->generateUrl('bo_groupemodules_list'));
	}
}
