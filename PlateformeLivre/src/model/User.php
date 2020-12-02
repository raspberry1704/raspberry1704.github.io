<?php
ini_set('display_errors', 1);
/* Classe représentant un utilisateur. */
class User {

	protected $username;
	protected $password;
	protected $id;
	protected $level;
	protected $email;
	protected $adresse;
	protected $dateDeNaissance;

	public function __construct($username, $password, $id, $level, $email, $adresse, $dateDeNaissance) {
		$this->username = $username;
		$this->password = $password;
		$this->id = $id;
		$this->level = $level;
		$this->email = $email;
		$this->adresse = $adresse;
		$this->dateDeNaissance = $dateDeNaissance;
	}

	/* Retourne le nom de lutilisateur */
	public function getUsername() {
		return $this->username;
	}

	/* Retourne le mot de passe de lutilisateur */
	public function getPassword() {
		return $this->password;
	}

	/* Retourne lid de lutilisateur */
	public function getId() {
		return $this->id;
	}
	
	public function getLevel() {
		return $this->level;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getAdresse() {
		return $this->adresse;
	}

	public function getDateDeNaissance() {
		return $this->dateDeNaissance;
	}
	
}

?>