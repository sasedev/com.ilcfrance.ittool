<?php
namespace ILC\BackOfficeBundle\Controller;

use ILC\BackOfficeBundle\Clazz\ExcelImportbug;
use ILC\BackOfficeBundle\Clazz\ExcelResult;
use ILC\BackOfficeBundle\Clazz\ExcelInfo;
use ILC\BackOfficeBundle\Form\StagiaireUpdateModuleForm;
use ILC\DataBundle\Entity\Moduleformation;
use ILC\DataBundle\Entity\Stagiaire;
use ILC\BackOfficeBundle\Form\StagiaireImportForm;
use ILC\BackOfficeBundle\Form\StagiaireAddForm;
use ILC\BackOfficeBundle\Form\StagiaireEditForm;
use ILC\DataBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

class StagiaireController extends BaseController
{

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function listAction($page, Request $request)
	{
		$this->addTwigVar('pagetitle', 'Liste des Stagiaires');
		$em = $this->getEntityManager();
		$query = $em->getRepository('ILCDataBundle:Stagiaire')->getAllFullQuery();

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $page, 25);
		$pagination->setPageRange(5);

		$this->addTwigVar('stagiaires', $pagination);

		return $this->render('ILCBackOfficeBundle:Stagiaire:index.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function searchAction($page, Request $request)
	{
		$this->addTwigVar('pagetitle', 'Recherche de Stagiaires');

		$q = $request->get('q');

		if (null == $q || trim($q) == "") {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}

		$em = $this->getEntityManager();
		$query = $em->getRepository('ILCDataBundle:Stagiaire')->searchQuery($q);

		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $page, 25);
		$pagination->setPageRange(5);

		$this->addTwigVar('stagiaires', $pagination);
		$this->addTwigVar('q', $q);

		return $this->render('ILCBackOfficeBundle:Stagiaire:search.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'ajouter manuellement un Stagiaire');
		$stagiaire = new Stagiaire();
		$sgForm = $this->createForm(StagiaireAddForm::class, $stagiaire);
		if ($request->getMethod() == 'POST') {
			$sgForm->handleRequest($request);
			if ($sgForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($stagiaire);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_stagiaire_list'));
			}
		}
		$this->addTwigVar('sg_form', $sgForm->createView());
		return $this->render('ILCBackOfficeBundle:Stagiaire:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function searchExcelAction($page, Request $request)
	{
		$this->addTwigVar('pagetitle', 'Recherche de Stagiaires dans les fichier Excel en ligne');

		$q = $request->get('q');

		if (null == $q || trim($q) == "") {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}

		$q = trim($q);

		$uploadDir = $this->getParameter('adapter_upload');
		$listFiles = @dir($uploadDir);

		$excelResults = array();
		$found = 0;

		while (false !== ($entry = $listFiles->read())) {
			$filename = "$uploadDir/$entry";
			$isexcel = false;
			if (is_file($filename)) {

				if ($this->endswith($filename, ".xls") === true || $this->endswith($filename, ".xlsx") === true) {
					$isexcel = true;
				}

				if ($isexcel == true) {
					$highestRow = 0;
					$highestColumn = 0;
					$ligne = 1;
					// $implign = 0;

					$currentFile = $entry;
					$currentDate = filemtime($filename);

					$excelObj = $this->get('phpexcel')->createPHPExcelObject($filename);
					$excelObj->setActiveSheetIndex(0);
					$worksheet = $excelObj->getActiveSheet();
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
					// if ($highestColumn != 'AN') {
					if ($highestColumnIndex < 10) {
						// $errs .=
						// $errno++ . " " . $uploadDate
						// . " : Nombre de colonnes incorrectes\n\n";
					} else {
						for ($row = 2; $row <= $highestRow; $row++) {
							$ligne++;

							$id1 = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
							// $id2 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$nom = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$prenom = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$email = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
							// $nomcontact =
							// $worksheet->getCellByColumnAndRow(17, $row)->getValue();
							// $emailcontact =
							// $worksheet->getCellByColumnAndRow(18, $row)->getValue();
							// $module =
							// $worksheet->getCellByColumnAndRow(29, $row)->getValue();
							$modulecode = $worksheet->getCellByColumnAndRow(18, $row)->getValue();

							if (strpos($q, " ") === false) {
								if (stripos($id1, $q) !== false || stripos($nom, $q) !== false || stripos($prenom, $q) !== false || stripos($email, $q) !== false || stripos($modulecode, $q) !== false) {
									$found++;

									$result = new ExcelResult();
									$result->setFilename($currentFile);
									$result->setFiledate($currentDate);
									$result->setLigne($ligne);
									$result->setId($id1);
									// $result->setMatricule($id2);
									$result->setNom($nom);
									$result->setPrenom($prenom);
									// $result->setNomcontact($nomcontact);
									// $result->setEmailcontact($emailcontact);
									// $result->setModule($module);
									$result->setCode($modulecode);

									$excelResults[$currentDate][$found] = $result;
								}
							} else {
								$keys = preg_split("/[\s]+/", $q);
								$isfound = false;
								$findinall = true;
								foreach ($keys as $key) {
									if (stripos($id1, $key) !== false || stripos($nom, $key) !== false || stripos($prenom, $key) !== false || stripos($email, $key) !== false || stripos($modulecode, $key) !== false) {
										$isfound = true;
									} else {
										$findinall = false;
									}
								}
								if ($isfound == true && $findinall == true) {
									$found++;

									$result = new ExcelResult();
									$result->setFilename($currentFile);
									$result->setFiledate($currentDate);
									$result->setLigne($ligne);
									$result->setId($id1);
									// $result->setMatricule($id2);
									$result->setNom($nom);
									$result->setPrenom($prenom);
									// $result->setNomcontact($nomcontact);
									// $result->setEmailcontact($emailcontact);
									// $result->setModule($module);
									$result->setCode($modulecode);

									$excelResults[$currentDate][$found] = $result;
								}
							}
						}
					}
				}
			}
		}

		ksort($excelResults);

		$this->addTwigVar('results', $excelResults);
		$this->addTwigVar('q', $q);

		return $this->render('ILCBackOfficeBundle:Stagiaire:excelsearch.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function updateNewIdsAction(Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaires = $em->getRepository('ILCDataBundle:Stagiaire')->getAll();

		$uploadDir = $this->getParameter('adapter_upload');
		$listFiles = @dir($uploadDir);

		$filesCount = 0;
		$errs = "";
		$infos = "";
		// $ExcelLog = array();

		$updated = 0;
		$leaved = 0;

		$errno = 1;
		$lineread = 0;

		// *

		while (false !== ($entry = $listFiles->read())) {
			$filename = "$uploadDir/$entry";
			$isexcel = false;
			if (is_file($filename)) {

				if ($this->endswith($filename, ".xls") === true || $this->endswith($filename, ".xlsx") === true) {
					$isexcel = true;
				}

				if (!$isexcel) {
					$errs .= $errno++ . " " . "<a href=\"/upload/$entry\">" . $entry . "</a> is Not Excel File\n\n";
				}

				if ($isexcel == true) {
					$highestRow = 0;
					$highestColumn = 0;
					$filesCount++;
					$ligne = 2;
					// $implign = 0;

					// $currentFile = $entry;
					// $currentDate = filemtime($filename);

					$uploadDate = "File $filesCount : " . date("F d Y H:i:s.", filemtime($filename)) . " <a href=\"/upload/$entry\">" . $entry . "</a> ";
					$excelObj = $this->get('phpexcel')->createPHPExcelObject($filename);
					$excelObj->setActiveSheetIndex(0);
					$worksheet = $excelObj->getActiveSheet();
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
					// if ($highestColumn != 'AN') {
					if ($highestColumnIndex < 10) {
						$errs .= $errno++ . " " . $uploadDate . " : Nombre de colonnes incorrectes\n\n";
						$excelObj->disconnectWorksheets();
						unset($excelObj);
					} else {
						for ($row = 2; $row <= $highestRow; $row++) {

							$id1 = strval($worksheet->getCellByColumnAndRow(1, $row)->getValue());
							$id2 = strval($worksheet->getCellByColumnAndRow(4, $row)->getValue());
							$exNom = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$exPrenom = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$exEmail = $worksheet->getCellByColumnAndRow(16, $row)->getValue();

							if (trim($id1) != "" && trim($id2) != "") {

								$stagiaireFound = false;

								foreach ($stagiaires as $stagiaire) {
									$username = $stagiaire->getUsername();
									$nom = $stagiaire->getNom();
									$prenom = $stagiaire->getPrenom();
									// $email = $stagiaire->getEmail();
									// $dtvalid = $stagiaire->getDtcrea();

									if ($id2 === $username) {
										$lineread++;
										$stagiaireFound = true;
										if (strtolower($nom) != strtolower($exNom) || strtolower($prenom) != strtolower($exPrenom)) {
											$errno++;

											$errs .= $errno . " " . $uploadDate . " - Ln " . $ligne . " - <u>$id2</u> - $id1 - OLDID : Diff DB / Excel -> $nom / $exNom -- $prenom / $exPrenom \n\n";
											$leaved++;
										}
										$stagiaire->setUsername($id1);
										$em->persist($stagiaire);
										$em->flush();
										$updated++;
									}
								}
								if ($stagiaireFound == false) {
									$lineread++;
									$errno++;
									$errs .= $errno . " " . $uploadDate . " - Ln " . $ligne . " - $id2 - $id1 -- $exNom -- $exPrenom : Stagiaire inexistant dans la base\n\n";
								}
							} else {
								if ($highestRow != $ligne && (trim($exNom) != "" || trim($exPrenom) != "" || trim($exEmail) != "" || trim($id1) != "" || trim($id2) != "")) {
									$errno++;
									$errs .= $errno . " " . $uploadDate . " - Ln " . $ligne . " ID Apprenant ou matricule Apprenant Vide !\n\n";
								}
							}
							$ligne++;
						}
						$excelObj->disconnectWorksheets();
						unset($excelObj);
					}
				}
			}
		}

		$infos = $filesCount . ' Fichiers Lus | ' . $updated . " / " . count($stagiaires) . " Stagiaires modifiés | " . $leaved . " lignes excel non traités car doublons erronés | " . $lineread . " Lignes Excel lues";

		$this->addFlash('err', $errs);
		$this->addFlash('info', $infos);

		return $this->redirect($this->generateUrl("bo_stagiaire_list"));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function checkBugsAction(Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaires = $em->getRepository('ILCDataBundle:Stagiaire')->getAll();

		$uploadDir = $this->getParameter('adapter_upload');
		$listFiles = @dir($uploadDir);

		$filesCount = 0;
		$errs = "";
		$infos = "";
		$ExcelLog = array();

		// $updated = 0;
		$leaved = 0;

		$errno = 1;
		$lineread = 0;

		// *

		while (false !== ($entry = $listFiles->read())) {
			$filename = "$uploadDir/$entry";
			$isexcel = false;
			if (is_file($filename)) {

				if ($this->endswith($filename, ".xls") === true || $this->endswith($filename, ".xlsx") === true) {
					$isexcel = true;
				}

				if (!$isexcel) {
					$errs .= $errno++ . " " . "<a href=\"/upload/$entry\">" . $entry . "</a> is Not Excel File\n\n";
				}

				if ($isexcel == true) {
					$highestRow = 0;
					$highestColumn = 0;
					$filesCount++;
					$ligne = 2;
					// $implign = 0;

					$currentFile = $entry;
					$currentDate = filemtime($filename);

					$uploadDate = "File $filesCount : " . date("F d Y H:i:s.", filemtime($filename)) . " <a href=\"/upload/$entry\">" . $entry . "</a> ";
					$excelObj = $this->get('phpexcel')->createPHPExcelObject($filename);
					$excelObj->setActiveSheetIndex(0);
					$worksheet = $excelObj->getActiveSheet();
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
					// if ($highestColumn != 'AN') {
					if ($highestColumnIndex < 10) {
						$errs .= $errno++ . " " . $uploadDate . " : Nombre de colonnes incorrectes\n\n";
						$excelObj->disconnectWorksheets();
						unset($excelObj);
					} else {
						for ($row = 2; $row <= $highestRow; $row++) {
							$lineread++;

							$id1 = strval($worksheet->getCellByColumnAndRow(1, $row)->getValue());
							$exNom = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$exPrenom = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$exEmail = $worksheet->getCellByColumnAndRow(16, $row)->getValue();

							if (trim($id1) != "") {

								$stagiaireFound = false;

								foreach ($stagiaires as $stagiaire) {
									$username = $stagiaire->getUsername();
									$nom = $stagiaire->getNom();
									$prenom = $stagiaire->getPrenom();
									$email = $stagiaire->getEmail();
									$dtvalid = $stagiaire->getDtcrea();
									if ($id1 === $username) {
										$stagiaireFound = true;
										if (strtolower($nom) != strtolower($exNom) || strtolower($prenom) != strtolower($exPrenom)) {
											$errno++;

											$excelbug = new ExcelInfo();

											$excelbug->setFilename($currentFile);
											$excelbug->setFiledate($currentDate);
											$excelbug->setErrmsg("Différence BD/Excel");
											$excelbug->setLigne($ligne);
											$excelbug->setDbUsername($username);
											$excelbug->setDbNom($nom);
											$excelbug->setDbPrenom($prenom);
											$excelbug->setDbMail($email);
											$excelbug->setDbdtvalid($dtvalid);
											$excelbug->setExId($id1);
											$excelbug->setExNom($exNom);
											$excelbug->setExPrenom($exPrenom);
											$excelbug->setExMail($exEmail);

											$ExcelLog[$currentDate][$errno] = $excelbug;
											$leaved++;
										}
									}
								}
								if ($stagiaireFound == false) {
									$errno++;

									$excelbug = new ExcelInfo();

									$excelbug->setFilename($currentFile);
									$excelbug->setFiledate($currentDate);
									$excelbug->setErrmsg("Stagiaire inexistant dans la base");
									$excelbug->setLigne($ligne);
									$excelbug->setExId($id1);
									$excelbug->setExNom($exNom);
									$excelbug->setExPrenom($exPrenom);
									$excelbug->setExMail($exEmail);

									$ExcelLog[$currentDate][$errno] = $excelbug;
								}
							} else {
								if ($highestRow != $ligne && (trim($exNom) != "" || trim($exPrenom) != "" || trim($exEmail) != "" || trim($id1) != "")) {
									$errno++;

									$excelbug = new ExcelInfo();

									$excelbug->setFilename($currentFile);
									$excelbug->setFiledate($currentDate);
									$excelbug->setErrmsg("ID Apprenant Vide");
									$excelbug->setLigne($ligne);
									$excelbug->setExId($id1);
									$excelbug->setExNom($exNom);
									$excelbug->setExPrenom($exPrenom);
									$excelbug->setExMail($exEmail);

									$ExcelLog[$currentDate][$errno] = $excelbug;
								}
							}
							$ligne++;
						}
						$excelObj->disconnectWorksheets();
						unset($excelObj);
					}
				}
			}
		}

		$infos = $filesCount . ' Fichier(s) Excel Lu(s) | ' . count($stagiaires) . " stagiaire(s) dans la base de donnée | " . $leaved . " différences avec la Base trouvés | " . $lineread . " lignes Excel lues";

		$this->addFlash('err', $errs);
		$this->addFlash('info', $infos);

		ksort($ExcelLog);

		$this->addTwigVar('pagetitle', 'Detection de bugs');
		$this->addTwigVar('bugs', $ExcelLog);

		return $this->render('ILCBackOfficeBundle:Stagiaire:checkbugs.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function importAction(Request $request)
	{
		$this->addTwigVar('pagetitle', 'importer une liste de Stagiaires');
		$sgForm = $this->createForm(StagiaireImportForm::class);
		$highestRow = 0;
		$highestColumn = 0;
		if ($request->getMethod() == 'POST') {
			$sgForm->handleRequest($request);
			if ($sgForm->isValid()) {

				// adapter_upload
				$extension = $sgForm['excel']->getData()->guessExtension();
				if ($extension == 'zip' || $extension == 'xlsx') {
					$extension = 'xlsx';
				} else {
					$extension = 'xls';
				}
				$filename = uniqid() . '.' . $extension;
				$sgForm['excel']->getData()->move($this->getParameter('adapter_upload'), $filename);
				$fullfilename = $this->getParameter('adapter_upload') . '/' . $filename;

				$excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);
				$excelObj->setActiveSheetIndex(0);
				$worksheet = $excelObj->getActiveSheet();
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
				// if ($highestColumn != 'AN') {
				if ($highestColumnIndex < 10) {
					$sgForm['excel']->addError(new FormError('Nombre de colonnes de fichier excel incorrecte'));
					$excelObj->disconnectWorksheets();
					unset($excelObj);
				} else {
					// $err = "";
					$em = $this->getEntityManager();
					$listmodule = $em->getRepository('ILCDataBundle:Moduleformation')->findAll();

					$errs = "";
					// $infos = "";
					$ligne = 1;
					$ExcelLog = array();

					$lignesLues = 0;
					$lignesNontraitees = 0;
					$nouveauxStagiaires = 0;
					$nouveauxStagiairesNonAffecte = 0;
					$nouveauxStagiairesAffecte = 0;
					$ancienStagiairesMisAJours = 0;
					$ancienStagiairesMisAJoursNonAffecte = 0;
					$ancienStagiairesMisAJoursAffecte = 0;
					$ancienStagiairesDifferents = 0;

					for ($row = 2; $row <= $highestRow; $row++) {
						$ligne++;
						$lignesLues++;

						// $exUsername = strval($worksheet->getCellByColumnAndRow(1, $row)->getValue()); // B
						$exUsername = strval($worksheet->getCellByColumnAndRow(7, $row)->getValue()); // H
						                                                                              // $exNom = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); // F
						$exNom = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); // F
						                                                                 // $exPrenom = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); // G
						$exPrenom = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); // G
						                                                                    // $exEmail = $worksheet->getCellByColumnAndRow(16, $row)->getValue(); // Q
						$exEmail = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); // I
						                                                                   // $exTel = $worksheet->getCellByColumnAndRow(17, $row)->getValue(); // R
						$exTel = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); // J
						                                                                 // $exNomcontact = $worksheet->getCellByColumnAndRow(18, $row)->getValue(); // S
						$exNomcontact = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); // K
						                                                                         // $exEmailcontact = $worksheet->getCellByColumnAndRow(19, $row)->getValue(); // T
						$exEmailcontact = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); // K
						                                                                           // $exMod = $worksheet->getCellByColumnAndRow(29, $row)->getValue(); // AD
						$exMod = $worksheet->getCellByColumnAndRow(19, $row)->getValue(); // T

						$excelbug = new ExcelImportbug();
						$excelbug->setLigne($ligne);
						$excelbug->setId($exUsername);
						$excelbug->setNom($exNom);
						$excelbug->setPrenom($exPrenom);
						$excelbug->setEmail($exEmail);
						$excelbug->setTel($exTel);
						$excelbug->setNomContact($exNomcontact);
						$excelbug->setEmailContact($exEmailcontact);
						$excelbug->setCode($exMod);

						if (trim($exUsername) == "") {

							$excelbug->setErrmsg("ID Vide dans Excel !!");
							$ExcelLog[] = $excelbug;
							$lignesNontraitees++;
						} elseif (trim($exEmail) == "") {
							$excelbug->setErrmsg("Email Vide dans Excel !!!");
							$ExcelLog[] = $excelbug;
							$lignesNontraitees++;
						} elseif ($this->check_email_address($exEmail) == false) {
							$excelbug->setErrmsg("Email Invalide dans Excel !!!");
							$ExcelLog[] = $excelbug;
							$lignesNontraitees++;
						} else {
							$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneByUsername($exUsername);
							if (null == $stagiaire) {
								$stagiaire = new Stagiaire();
								$stagiaire->setUsername($exUsername); // B
								$stagiaire->setNom($exNom); // F
								$stagiaire->setPrenom($exPrenom); // G
								$stagiaire->setEmail($exEmail); // Q
								$stagiaire->setTel($exTel); // R
								$stagiaire->setNomcontact($exNomcontact); // S
								$stagiaire->setEmailcontact($exEmailcontact); // T
								$excelbug->setStagiaire($stagiaire);
								try {
									$em->persist($stagiaire);
									$em->flush();
									$nouveauxStagiaires++;
									$modfound = false;
									for ($j = 0; $j < count($listmodule); $j++) {
										if ($listmodule[$j]->getCode() == $exMod) {
											$listmodule[$j]->addStagiaire($stagiaire);
											$em->persist($listmodule[$j]);
											$em->flush();
											$modfound = true;
										}
									}
									if ($modfound == false) {
										$excelbug->setErrmsg("nouveau stagiaire ajouté mais code module inconnu : " . $exMod);
										$ExcelLog[] = $excelbug;
										$nouveauxStagiairesNonAffecte++;
									} else {
										$nouveauxStagiairesAffecte++;
									}
								} catch (\Exception $e) {
									$excelbug->setErrmsg("Tentative d'ajout à la base échouée : " . $e->getMessage());
									$ExcelLog[] = $excelbug;
									$lignesNontraitees++;
								}
							} else {
								$newdate = new \DateTime();
								$newdate->modify("+12 day");
								$stagiaire->setDtajout($newdate);
								$excelbug->setStagiaire($stagiaire);
								if (strtolower(trim($stagiaire->getNom())) === strtolower(trim($exNom)) && strtolower(trim($stagiaire->getPrenom())) === strtolower(trim($exPrenom)) && strtolower(trim($stagiaire->getEmail())) === strtolower(trim($exEmail))) {
									if (trim($exTel) != "") {
										$stagiaire->setTel(trim($exTel));
									}
									if (trim($exNomcontact) != "") {
										$stagiaire->setNomcontact(trim($exNomcontact));
									}
									if (trim($exEmailcontact) != "" && $this->check_email_address($exEmailcontact) == true) {
										$stagiaire->setEmailcontact(trim($exEmailcontact));
									}
									try {

										$em->persist($stagiaire);
										$em->flush();
										$ancienStagiairesMisAJours++;
										$modfound = false;
										for ($j = 0; $j < count($listmodule); $j++) {
											if ($listmodule[$j]->getCode() == $exMod) {
												$listmodule[$j]->addStagiaire($stagiaire);
												$em->persist($listmodule[$j]);
												$em->flush();
												$modfound = true;
											}
										}
										if ($modfound == false) {
											$excelbug->setErrmsg("Stagiaire déjà présent en base mais code module inconnu : " . $exMod);
											$ExcelLog[] = $excelbug;
											$ancienStagiairesMisAJoursNonAffecte++;
										} else {
											$ancienStagiairesMisAJoursAffecte++;
										}
									} catch (\Exception $e) {
										$excelbug->setErrmsg("Tentative de mise à jour du stagiaire dans la base échouée : " . $e->getMessage());
										$ExcelLog[] = $excelbug;
										$lignesNontraitees++;
									}
								} else {
									$excelbug->setErrmsg("ID Stagiaire trouvé en base mais autres informations non identiques dans Excel");
									$ExcelLog[] = $excelbug;
									$ancienStagiairesDifferents++;
								}
							}
						}
					}
					$excelObj->disconnectWorksheets();
					unset($excelObj);

					$errs .= "<b>" . $lignesLues . "</b> Ligne(s) lue(s)\n";
					$errs .= "<b>" . $lignesNontraitees . "</b> Ligne(s) non traitée(s) à cause d'erreur(s)\n";
					$errs .= "<b>" . $nouveauxStagiaires . "</b> Nouveau(x) stagiaire(s) ajoutés dont :\n";
					$errs .= "<b>" . $nouveauxStagiairesAffecte . "</b> Affectés à un module:\n";
					$errs .= "<b>" . $nouveauxStagiairesNonAffecte . "</b> NON-Affectés à un module:\n";
					$errs .= "<b>" . $ancienStagiairesMisAJours . "</b> Anciens(x) stagiaire(s) mis-à-jour(s) dont :\n";
					$errs .= "<b>" . $ancienStagiairesMisAJoursAffecte . "</b> Affectés à un module:\n";
					$errs .= "<b>" . $ancienStagiairesMisAJoursNonAffecte . "</b> NON-Affectés à un module:\n";
					$errs .= "<b>" . $ancienStagiairesDifferents . "</b> Différent(s) entre base de donnée et Excel:\n";
					$this->addFlash('err', $errs);
					$this->addTwigVar('excellog', $ExcelLog);
					// return $this->redirect($this->generateUrl('bo_stagiaire_list'));
				}
			}
		}
		$this->addTwigVar('sg_form', $sgForm->createView());
		return $this->render('ILCBackOfficeBundle:Stagiaire:import.html.twig', $this->getTwigVars());
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
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null != $stagiaire) {
			$em->remove($stagiaire);
			$em->flush();
		}
		return $this->redirect($this->generateUrl("bo_stagiaire_list"));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function sendmailParamsAction(Request $request)
	{
		$em = $this->getEntityManager();
		$newstagiaires = $em->getRepository('ILCDataBundle:Stagiaire')->getAllNew();
		$i = 0;
		$err = "";
		foreach ($newstagiaires as $stagiaire) {
			try {
				$mvars = array();
				$mvars['username'] = $stagiaire->getUsername();
				$mvars['password'] = $stagiaire->getClearpassword();
				$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();
				$message = \Swift_Message::newInstance()->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($stagiaire->getEmail(), $stagiaire->getNom() . ' ' . $stagiaire->getPrenom())->setSubject('Ateliers d\'anglais-Programmation URGENT')->setBody($this->renderView('ILCBackOfficeBundle:Mail:params_connexion.html.twig', $mvars), 'text/html');
				$this->sendmail($message);
				$stagiaire->setInfosent(true);
				$em->persist($stagiaire);
				$em->flush();
				$i++;
			} catch (\Exception $e) {
				$err .= "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n";
			}
		}
		if ($err != "") {
			$this->addFlash('err', $err);
		}
		$this->addFlash('info', $i . ' mails ont étés envoyés aux nouveaux stagiaires');

		return $this->redirect($this->generateUrl("bo_stagiaire_list"));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function sendmailNewIdsAction(Request $request)
	{
		$em = $this->getEntityManager();
		$newstagiaires = $em->getRepository('ILCDataBundle:Stagiaire')->getAllValid();
		$i = 0;
		$err = "";
		foreach ($newstagiaires as $stagiaire) {
			try {
				$mvars = array();
				$mvars['username'] = $stagiaire->getUsername();
				$mvars['password'] = $stagiaire->getClearpassword();
				$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();
				$message = \Swift_Message::newInstance()->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($stagiaire->getEmail(), $stagiaire->getNom() . ' ' . $stagiaire->getPrenom())->setSubject('Ateliers d\'anglais-Programmation URGENT')->setBody($this->renderView('ILCBackOfficeBundle:Mail:updateids.html.twig', $mvars), 'text/html');
				$this->sendmail($message);
				$stagiaire->setInfosent(true);
				$em->persist($stagiaire);
				$em->flush();
				$i++;
			} catch (\Exception $e) {
				$err .= "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n";
			}
		}
		if ($err != "") {
			$this->addFlash('err', $err);
		}
		$this->addFlash('info', $i . ' mails ont étés envoyés aux stagiaires');

		return $this->redirect($this->generateUrl("bo_stagiaire_list"));
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
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$sgForm = $this->createForm(StagiaireEditForm::class, $stagiaire);

		$sgModForm = $this->createForm(StagiaireUpdateModuleForm::class, $stagiaire);

		if ($request->getMethod() == 'POST') {
			$sgForm->handleRequest($request);
			if ($sgForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($stagiaire);
				$em->flush();
				return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
					'id' => $id
				)));
			}
		}
		$this->addTwigVar('sg_form', $sgForm->createView());
		$this->addTwigVar('sg_modform', $sgModForm->createView());

		$this->addTwigVar('pagetitle', 'Informations Stagiaire ' . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom());
		$this->addTwigVar('st', $stagiaire);
		return $this->render('ILCBackOfficeBundle:Stagiaire:show.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function sendmailRappelAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$mvars = array();

		$mvars['username'] = $stagiaire->getUsername();
		$mvars['password'] = $stagiaire->getClearpassword();
		$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();

		$mvars['username'] = $stagiaire->getUsername();
		$mvars['password'] = $stagiaire->getClearpassword();
		$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();
		try {
			$message = \Swift_Message::newInstance()->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($stagiaire->getEmail(), $stagiaire->getNom() . ' ' . $stagiaire->getPrenom())->setSubject('Ateliers d\'anglais-Programmation URGENT')->setBody($this->renderView('ILCBackOfficeBundle:Mail:params_connexion.html.twig', $mvars), 'text/html');
			$this->sendmail($message);
			$this->addFlash('info', 'Un mail de rappel a été envoyé contenant les paramètres de ce stagiaire');
		} catch (\Exception $e) {
			$this->addFlash('err', "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n");
		}
		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function remSessAction($id, $ses, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($ses);
		if (null != $session) {
			$session->removeStagiaire($stagiaire);
			$em->persist($session);
			$em->flush();
			$this->addFlash('info', 'Suppression du stagiaire de la session ' . $session->getIntitule() . ' ' . $session->getCode());
		} else {
			$this->addFlash('err', 'Session inconnue');
		}
		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function remModAction($id, $mod, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$module = $em->getRepository('ILCDataBundle:Moduleformation')->findOneById($mod);
		if (null != $module) {
			$module->removeStagiaire($stagiaire);
			$em->persist($module);
			$em->flush();
			$sessions = $stagiaire->getSessions();
			foreach ($sessions as $session) {
				if ($session->getModuleformation()->getId() == $module->getId()) {
					$session->removeStagiaire($stagiaire);
					$em->persist($session);
					$em->flush();
				}
			}

			$this->addFlash('info', 'Suppression du stagiaire du session ' . $module->getIntitule() . ' ' . $module->getCode() . ' et de ces sessions');
		} else {
			$this->addFlash('err', 'Module inconnu');
		}
		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function updateModAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}

		$fullmodules = $em->getRepository('ILCDataBundle:Moduleformation')->getAll();
		$fullsessions = $em->getRepository('ILCDataBundle:Sessionformation')->getAll();

		$sgModForm = $this->createForm(StagiaireUpdateModuleForm::class, $stagiaire);

		if ($request->getMethod() == 'POST') {
			$sgModForm->handleRequest($request);
			if ($sgModForm->isValid()) {

				foreach ($fullmodules as $module) {
					$module->removeStagiaire($stagiaire);
					$em->persist($module);
				}
				$em->flush();

				$modules = $sgModForm->get('modules')->getData();
				if (count($modules) != 0) {
					foreach ($modules as $modsel) {
						$modsel->addStagiaire($stagiaire);
						$em->persist($modsel);
					}
				}
				$em->flush();

				if (count($modules) == 0) {
					foreach ($fullsessions as $session) {
						$session->removeStagiaire($stagiaire);
						$em->persist($session);
					}
					$em->flush();
				} else {
					foreach ($fullsessions as $session) {
						$sesexist = false;
						foreach ($modules as $modsel) {
							if ($modsel->getId() == $session->getModuleformation()->getId()) {
								$sesexist = true;
							}
						}
						if (!$sesexist) {
							$session->removeStagiaire($stagiaire);
							$em->persist($session);
							$em->flush();
						}
					}
				}

				$this->addFlash('info', 'Mise à jour de la liste des modules auxquels est inscrit ce stagiaire efféctée avec succès');
				return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
					'id' => $id
				)));
			} else {
				$this->addFlash('err', 'Erreur lors de la mise à jour des association stagiaire / Modules');
			}
		}
		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function updateSessAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}

		$datas = $request->request->all();
		// $hassession = false;
		foreach ($datas as $data) {
			if (strpos($data, 'mod') == 0) {
				$val = $data;
				$session = $em->getRepository('ILCDataBundle:Sessionformation')->findOneById($val);
				if (null != $session) {
					// $hassession = true;
					$currmodule = $session->getModuleformation();
					$sessionislocked = false;
					// $samesession = false;
					if (!$sessionislocked) {

						$oldsess = null;
						foreach ($stagiaire->getSessions() as $ses) {
							if ($ses->getModuleformation()->getId() == $currmodule->getId()) {
								$oldsess = $ses;
							}
						}
						if (null != $oldsess) {
							if ($oldsess->getId() != $session->getId()) {
								$oldsess->removeStagiaire($stagiaire);
								$session->addStagiaire($stagiaire);
								$em->persist($oldsess);
								$em->persist($session);
								$em->flush();
							}
						} else {
							$session->addStagiaire($stagiaire);
							$em->persist($session);
							$em->flush();
						}
					}
				}
			}
		}

		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addtendayswithmaildateAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$date = $stagiaire->getDtcrea();
		$date->modify("+12 day");

		$stagiaire->setDtcrea(new \DateTime($date->format("Y-m-d H:i:s")));
		$em->persist($stagiaire);
		$em->flush();

		$mvars = array();
		$mvars['username'] = $stagiaire->getUsername();
		$mvars['password'] = $stagiaire->getClearpassword();
		$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();
		try {
			$message = \Swift_Message::newInstance()->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($stagiaire->getEmail(), $stagiaire->getNom() . ' ' . $stagiaire->getPrenom())->setSubject('Ateliers d\'anglais-Rappel Programmation URGENT')->setBody($this->renderView('ILCBackOfficeBundle:Mail:addtendayswithmaildate.html.twig', $mvars), 'text/html');
			$this->sendmail($message);
			$this->addFlash('info', 'Un mail de rappel a été envoyé pour indiquer l\'extension de 10 jours de validité  des paramètres de ce stagiaire et la notification de nouvelles sessions');
		} catch (\Exception $e) {
			$this->addFlash('info', 'Un mail de rappel a été envoyé pour indiquer l\'extension de 10 jours de validité  des paramètres de ce stagiaire et la notification de nouvelles sessions');
			$this->addFlash('err', "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n");
		}

		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addtendayswithmailAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$date = $stagiaire->getDtcrea();
		$date->modify("+12 day");

		$stagiaire->setDtcrea(new \DateTime($date->format("Y-m-d H:i:s")));
		$em->persist($stagiaire);
		$em->flush();

		$mvars = array();
		$mvars['username'] = $stagiaire->getUsername();
		$mvars['password'] = $stagiaire->getClearpassword();
		$mvars['name'] = $stagiaire->getNom() . ' ' . $stagiaire->getPrenom();
		try {
			$message = \Swift_Message::newInstance()->setFrom('formation@ilcfrance.com', 'ILCFrance')->setTo($stagiaire->getEmail(), $stagiaire->getNom() . ' ' . $stagiaire->getPrenom())->setSubject('Ateliers d\'anglais-Rappel Programmation URGENT')->setBody($this->renderView('ILCBackOfficeBundle:Mail:addtendayswithmail.html.twig', $mvars), 'text/html');
			$this->sendmail($message);
			$this->addFlash('info', 'Un mail de rappel a été envoyé pour indiquer l\'extension de 10 jours de validité  des paramètres de ce stagiaire');
		} catch (\Exception $e) {
			$this->addFlash('info', 'Un mail de rappel a été envoyé pour indiquer l\'extension de 10 jours de validité  des paramètres de ce stagiaire');
			$this->addFlash('err', "Une erreur s'est produite lors de l'envoie de l'email (" . $stagiaire->getNom() . ' ' . $stagiaire->getPrenom() . " " . $stagiaire->getEmail() . ")\n");
		}

		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function addtendaysAction($id, Request $request)
	{
		$em = $this->getEntityManager();
		$stagiaire = $em->getRepository('ILCDataBundle:Stagiaire')->findOneById($id);
		if (null == $stagiaire) {
			return $this->redirect($this->generateUrl("bo_stagiaire_list"));
		}
		$date = $stagiaire->getDtcrea();
		$date->modify("+12 day");

		$stagiaire->setDtcrea(new \DateTime($date->format("Y-m-d H:i:s")));
		$em->persist($stagiaire);
		$em->flush();

		$this->addFlash('info', 'La validité du compte de ce stagiaire a été repoussée de dix jours');

		return $this->redirect($this->generateUrl('bo_stagiaire_show', array(
			'id' => $id
		)));
	}
}
