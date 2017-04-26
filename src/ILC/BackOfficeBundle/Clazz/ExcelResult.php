<?php
namespace ILC\BackOfficeBundle\Clazz;
class ExcelResult {
	private $filedate;
	private $filename;
	private $ligne;
	private $id;
	private $matricule;
	private $nom;
	private $prenom;
	private $email;
	private $nomcontact;
	private $emailcontact;
	private $module;
	private $code;

	public function getFiledate() {
		return $this->filedate;
	}

	public function setFiledate($filedate) {
		$this->filedate = $filedate;
	}

	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	public function getLigne() {
		return $this->ligne;
	}

	public function setLigne($ligne) {
		$this->ligne = $ligne;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getMatricule() {
		return $this->matricule;
	}

	public function setMatricule($matricule) {
		$this->matricule = $matricule;
	}

	public function getNom() {
		return $this->nom;
	}

	public function setNom($nom) {
		$this->nom = $nom;
	}

	public function getPrenom() {
		return $this->prenom;
	}

	public function setPrenom($prenom) {
		$this->prenom = $prenom;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getNomcontact() {
		return $this->nomcontact;
	}

	public function setNomcontact($nomcontact) {
		$this->nomcontact = $nomcontact;
	}

	public function getEmailcontact() {
		return $this->emailcontact;
	}

	public function setEmailcontact($emailcontact) {
		$this->emailcontact = $emailcontact;
	}

	public function getModule() {
		return $this->module;
	}

	public function setModule($module) {
		$this->module = $module;
	}

	public function getCode() {
		return $this->code;
	}

	public function setCode($code) {
		$this->code = $code;
	}

}
