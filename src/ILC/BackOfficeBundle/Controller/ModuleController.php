<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Form\ModuleformationEditForm;
use ILC\BackOfficeBundle\Form\ModuleformationAddFrom;
use ILC\DataBundle\Entity\Moduleformation;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'Ajouter un Module');

		$mod = new Moduleformation();

		$modForm = $this->createForm(ModuleformationAddFrom::class, $mod);
		if ($request->getMethod() == 'POST') {
			$modForm->handleRequest($request);
			if ($modForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($mod);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_groupemodules_list'));
			}
		}
		$this->addTwigVar('mod_form', $modForm->createView());
		return $this->render('ILCBackOfficeBundle:Module:add.html.twig', $this->getTwigVars());
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
		$module = $em->getRepository('ILCDataBundle:Moduleformation')->findOneById($id);
		if (null == $module) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$modForm = $this->createForm(ModuleformationEditForm::class, $module);

		$this->addTwigVar('pagetitle', 'Détail Module ' . $module->getCode());
		$this->addTwigVar('module', $module);
		$this->addTwigVar('mod_form', $modForm->createView());
		return $this->render('ILCBackOfficeBundle:Module:show.html.twig', $this->getTwigVars());
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
		$module = $em->getRepository('ILCDataBundle:Moduleformation')->findOneById($id);
		if (null == $module) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$modForm = $this->createForm(ModuleformationEditForm::class, $module);
		$modForm->handleRequest($request);
		if ($modForm->isValid()) {
			$em->persist($module);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('bo_module_show', array(
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
		$module = $em->getRepository('ILCDataBundle:Moduleformation')->findOneById($id);
		if (null == $module) {
			return $this->redirect($this->generateUrl('bo_groupemodules_list'));
		}
		$em->remove($module);
		$em->flush();
		return $this->redirect($this->generateUrl('bo_groupemodules_list'));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function exportAction(Request $request)
	{
		$em = $this->getEntityManager();
		$modules = $em->getRepository('ILCDataBundle:Moduleformation')->findAll();

		// ask the service for a Excel5
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		// or $this->get('xls.service_pdf');
		// or create your own is easy just modify services.yml

		// create the object see http://phpexcel.codeplex.com documentation
		$phpExcelObject->getProperties()->setCreator("SALAH Abdelkader Seif Eddine")->setLastModifiedBy("SALAH Abdelkader Seif Eddine")->setTitle("Liste des Modules de formation ILC France")->setSubject("Liste des Modules de formation ILC France")->setDescription("Liste des Modules de formation ILC France.")->setKeywords("ILC France")->setCategory("ILC France");
		$phpExcelObject->setActiveSheetIndex(0);
		$workSheet = $phpExcelObject->getActiveSheet();
		$workSheet->setTitle('Liste des Modules');
		$workSheet->setCellValue('A1', 'Code');
		$workSheet->setCellValue('B1', 'Intitulé');
		$workSheet->setCellValue('C1', 'Description');

		$i = 1;
		foreach ($modules as $mod) {
			$i++;
			$workSheet->setCellValue('A' . $i, $mod->getCode(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('B' . $i, $mod->getIntitule(), \PHPExcel_Cell_DataType::TYPE_STRING2);
			$workSheet->setCellValue('C' . $i, $mod->getDescription(), \PHPExcel_Cell_DataType::TYPE_STRING2);
		}

		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$response = $this->get('phpexcel')->createStreamedResponse($writer);

		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

		$filename = $this->normalize('ILC_Modules_' . uniqid());
		$filename = str_ireplace('"', '|', $filename);
		$filename = str_ireplace(' ', '_', $filename);

		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;
	}
}
