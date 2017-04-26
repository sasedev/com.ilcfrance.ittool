<?php
namespace ILC\BackOfficeBundle\Clazz;
class ExcelImportbug {
	private $ligne;
	private $id;
	private $nom;
	private $prenom;
	private $email;
	private $tel;
	private $nomContact;
	private $emailContact;
	private $code;
	private $stagiaire;
	private $errmsg;

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

	public function getTel() {
		return $this->tel;
	}

	public function setTel($tel) {
		$this->tel = $tel;
	}

	public function getNomContact() {
		return $this->nomContact;
	}

	public function setNomContact($nomContact) {
		$this->nomContact = $nomContact;
	}

	public function getEmailContact() {
		return $this->emailContact;
	}

	public function setEmailContact($emailContact) {
		$this->emailContact = $emailContact;
	}

	public function getCode() {
		return $this->code;
	}

	public function setCode($code) {
		$this->code = $code;
	}

	public function getStagiaire() {
		return $this->stagiaire;
	}

	public function setStagiaire($stagiaire) {
		$this->stagiaire = $stagiaire;
	}

	public function getErrmsg() {
		return $this->errmsg;
	}

	public function setErrmsg($errmsg) {
		$this->errmsg = $errmsg;
	}

}
