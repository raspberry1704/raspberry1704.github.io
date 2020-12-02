<?php

require_once("model/User.php");

/**
 * Représente une instance utilisateur en cours de manipulation (création
 * ou modification). Cette instance gèrera le traitement des données. 
 */
class UserBuilder {
	const USERNAME_LENGTH = 30;
	const PASSWORD_LENGTH = 30;
	const EMAIL_LENGTH = 100;
	const ADRESSE_LENGTH = 150;
	const DATE_DE_NAISSANCE_LENGTH = 10;
	const MOIS_VALID = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');


	/** Les données permettant de construire l'instance de User. */
	protected $dataUser;

	/** Les erreurs sur les données. */
	private $errorUser;

	/**
	 * Construit une nouvelle instance à partir d'un tableau
	 * de données.
	 */
	public function __construct(array $dataUser) {
		/* Vérification de l'existence des champs */

		if( !isset($dataUser['username']) )
			$dataUser['username'] = '';
		
		if( !isset($dataUser['password1']) )
			$dataUser['password1'] = '';

		if( !isset($dataUser['password2']) )
			$dataUser['password2'] = '';
	
		if( !isset($dataUser['email']) )
			$dataUser['email'] = '';
	
		if( !isset($dataUser['adresse']) )
			$dataUser['adresse'] = '';

		if( !isset($dataUser['day']) )
			$dataUser['day'] = '';

		if( !isset($dataUser['month']) )
			$dataUser['month'] = '';

		if( !isset($dataUser['year']) )
			$dataUser['year'] = '';

		$this->dataUser = $dataUser;
		$this->errorUser = null;
	}

	/**
	 * Retourne le tableau de données User.
	 */
	public function getData() {
		return $this->dataUser;
	}

	/**
	 * Renvoie les erreurs sur les données courantes,
	 * ou null si les données n'ont pas été validées.
	 */
	public function getError() {
		return $this->errorUser;
	}

	public function prepareFeedback() {
		$_SESSION['feedback'] = array();
		$_SESSION['tmp_username'] = $this->dataUser['username'];

		if( substr_count($this->errorUser, "\n") == 1) {
			array_push($_SESSION['feedback'], $this->errorUser);
		} else {
			$_SESSION['feedback'] = explode("\n", $this->errorUser);
			unset($_SESSION['feedback'][sizeof($_SESSION['feedback'])-1]);
		}
	}

	/**
	 * Construit une instance de User avec les données
	 * de cette instance.
	 */
	public function createUser() {
		$username = htmlentities(pg_escape_string($this->dataUser['username']));
		$password = htmlentities(pg_escape_string($this->dataUser['password1']));

		if( isset($this->dataUser['email']) ) {
			$email = htmlentities(pg_escape_string($this->dataUser['email']));
			$adresse = htmlentities(pg_escape_string($this->dataUser['adresse']));

			$day = htmlentities(pg_escape_string($this->dataUser['day']));
			$month = htmlentities(pg_escape_string($this->dataUser['month']));
			$year = htmlentities(pg_escape_string($this->dataUser['year']));
			$full_date = $day . "/" . $month . "/" . $year;
		} else 
			$email = $adresse = $full_date = "";

		return new User($username, $password, -1, -1, $email, $adresse, $full_date);
	}


	public function isValidModifyProfil() {
		$this->errorUser = '';

		if($this->dataUser['username'] === '')
			$this->errorUser .= "Veuillez renseigner un pseudo.\n";

		if( strlen($this->dataUser['username']) > self::USERNAME_LENGTH )
			$this->errorUser .= "Nombre de caractères incorrecte pour le nom d'utilisateur.\n";


		if( $this->dataUser['password1'] !== '' && $this->dataUser['password2'] !== '' )
			if ($this->dataUser['password1'] !== $this->dataUser['password2'] )
				$this->errorUser .= "Veuillez renseigner deux mots de passes similaires.\n";

		if( strlen($this->dataUser['password1']) > self::PASSWORD_LENGTH )
				$this->errorUser .= "Nombre de caractères incorrecte pour le mot de passe.\n";


		if($this->dataUser['email'] === '')
			$this->errorUser .= "Veuillez renseigner une adresse email.\n";

		if( strlen($this->dataUser['email']) > self::EMAIL_LENGTH )
				$this->errorUser .= "Nombre de caractères incorrecte pour l'adresse email.\n";

	
		if($this->dataUser['adresse'] === '')
			$this->errorUser .= "Veuillez renseigner une adresse.\n";

		if( strlen($this->dataUser['adresse']) > self::ADRESSE_LENGTH )
				$this->errorUser .= "Nombre de caractères incorrecte pour l'adresse.\n";


		//13 ans âge légal informations personnelles 
		
		//A implémenter : condition test valeur select est bonne(n'a pas été modifié dans le code) : !is_int($this->dataUser['day']) || !is_int($this->dataUser['year']) || $this->dataUser['day'] < 1 || $this->dataUser['day'] > 31 || || !key_exists( $this->dataUser['month'], self::MOIS_VALID )
		if( $this->dataUser['year'] > (date('Y')-13) )
			$this->errorUser .= "Date de naissance invalide, l'âge minimum requis est 13 ans.\n";

		return $this->errorUser === '';
	}


	/**
	 * Vérifie que les données sont valides pour la création d'un compte.
	 */
	public function isValid() {
		$this->errorUser = '';
		if ($this->dataUser['username'] === '')
			$this->errorUser .= "Veuillez renseigner un pseudo.\n";
				
		if ($this->dataUser['password1'] !== $this->dataUser['password2'] )
			$this->errorUser .= "Veuillez renseigner deux mots de passes similaires.\n";
		
		if ($this->dataUser['password1'] === '' || $this->dataUser['password2'] === '' )
			$this->errorUser .= "Veuillez renseigner deux mots de passes similaires non vides.\n";

		if ($this->dataUser['email'] === '')
			$this->errorUser .= "Veuillez renseigner une adresse email.\n";
	
		if ($this->dataUser['adresse'] === '')
			$this->errorUser .= "Veuillez renseigner une adresse.\n";

		//13 ans âge légal informations personnelles 
		if( $this->dataUser['year'] > (date('Y')-13) )
			$this->errorUser .= "Date de naissance invalide, l'âge minimum requis est 13 ans.\n";
		
		return $this->errorUser === '';
	}
	
	public function isValidConnect() {
		$this->errorUser = '';

		if ($this->dataUser['username'] === '')
			$this->errorUser .= "Veuillez renseigner un pseudo.\n";
		
		if ($this->dataUser['password1'] === '') {
			$this->errorUser .= "Veuillez renseigner un mot de passe.\n";
		}
		return $this->errorUser === '';
	}

	/**
	 * Renvoie la clef utilisée pour le nom de l'animal
	 * dans le tableau de données.
	 */
	public function getNameRef() {
		return "pseudo";
	}

	
	/**
	 * Renvoie la clef utilisée pour l'âge de l'animal
	 * dans le tableau de données.
	 */
	public function getPassword1Ref() {
		return "password1";
	}
	
	/**
	 * Renvoie la clef utilisée pour l'âge de l'animal
	 * dans le tableau de données.
	 */
	public function getPassword2Ref() {
		return "password2";
	}
}

?>