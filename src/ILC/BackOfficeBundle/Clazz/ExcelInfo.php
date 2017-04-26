<?php
namespace ILC\BackOfficeBundle\Clazz;
class ExcelInfo {
	private $filename;
	private $filedate;

	private $errmsg;

	private $ligne;

	private $dbUsername;
	private $dbNom;
	private $dbPrenom;
	private $dbMail;
	private $dbDtvalid;

	private $exId;
	private $exMatricule;
	private $exNom;
	private $exPrenom;
	private $exMail;

	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	public function getFiledate() {
		return $this->filedate;
	}

	public function setFiledate($filedate) {
		$this->filedate = $filedate;
	}

	public function getErrmsg() {
		return $this->errmsg;
	}

	public function setErrmsg($errmsg) {
		$this->errmsg = $errmsg;
	}

	public function getLigne() {
		return $this->ligne;
	}

	public function setLigne($ligne) {
		$this->ligne = $ligne;
	}

	public function getDbUsername() {
		return $this->dbUsername;
	}

	public function setDbUsername($dbUsername) {
		$this->dbUsername = $dbUsername;
	}

	public function getDbNom() {
		return $this->dbNom;
	}

	public function setDbNom($dbNom) {
		$this->dbNom = $dbNom;
	}

	public function getDbPrenom() {
		return $this->dbPrenom;
	}

	public function setDbPrenom($dbPrenom) {
		$this->dbPrenom = $dbPrenom;
	}

	public function getDbMail() {
		return $this->dbMail;
	}

	public function setDbMail($dbMail) {
		$this->dbMail = $dbMail;
	}

	public function getDbdtvalid() {
		return $this->dbDtvalid;
	}

	public function setDbdtvalid($dbDtvalid) {
		$this->dbDtvalid = $dbDtvalid;
	}

	public function getExId() {
		return $this->exId;
	}

	public function setExId($exId) {
		$this->exId = $exId;
	}

	public function getExMatricule() {
		return $this->exMatricule;
	}

	public function setExMatricule($exMatricule) {
		$this->exMatricule = $exMatricule;
	}

	public function getExNom() {
		return $this->exNom;
	}

	public function setExNom($exNom) {
		$this->exNom = $exNom;
	}

	public function getExPrenom() {
		return $this->exPrenom;
	}

	public function setExPrenom($exPrenom) {
		$this->exPrenom = $exPrenom;
	}

	public function getExMail() {
		return $this->exMail;
	}

	public function setExMail($exMail) {
		$this->exMail = $exMail;
	}

}
