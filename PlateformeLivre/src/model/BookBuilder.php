<?php

require_once("model/Book.php");

/**
 * Représente une instance utilisateur en cours de manipulation (création
 * ou modification). Cette instance gèrera le traitement des données. 
 */
class BookBuilder {
	const TITRE_LENGTH = 75;
	const DESCRIPTION_LENGTH = 1500;
	const AUTEUR_LENGTH = 75;

	const IMAGE_SIZE = 100000;
	const IMAGE_PATH_LENGTH = 75;
	const EXTENSIONS_IMAGE = array('.jpg', '.jpeg', '.png');


	/** Les données permettant de construire l'instance de User. */
	protected $dataBook;

	/** Les erreurs sur les données. */
	private $errorUser;

	/**
	 * Construit une nouvelle instance à partir d'un tableau
	 * de données.
	 */
	public function __construct(array $dataBook) {
		/* Vérification de l'existence des champs */
		if (!isset($dataBook['title']))
			$dataBook['title'] = '';
			
		if (!isset($dataBook['image']))
			$dataBook['image'] = '';

		if (!isset($dataBook['description']))
			$dataBook['description'] = '';

		if (!isset($dataBook['auteur']))
			$dataBook['auteur'] = '';

		$this->dataBook = $dataBook;
		$this->errorUser = null;
	}

	/**
	 * Retourne le tableau de données User.
	 */
	public function getData() {
		return $this->dataBook;
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
	public function createBook() {
		$title = htmlentities(pg_escape_string($this->dataBook['title'])); 
		$image = htmlentities(pg_escape_string($_FILES['image']['name'])); 
		$description = htmlentities(pg_escape_string($this->dataBook['description'])); 
		$auteur = htmlentities(pg_escape_string($this->dataBook['auteur']));
		return new Book( -1, $title, $image, $auteur, $description, -1 );
	}

	/**
	 * Vérifie que les données sont valides.
	 */
	public function isValid() {
		$this->errorUser = '';
		if( $this->dataBook['title'] === '' )
			$this->errorUser .= "Veuillez renseigner un titre.\n";
		if( strlen($this->dataBook['title']) > self::TITRE_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs titre.\n";

		if( !isset($_FILES['image']['name']) )
			$this->errorUser .= "Veuillez entrer une image.\n";

		if( strlen($_FILES['image']['name']) >  self::IMAGE_PATH_LENGTH )
			$this->errorUser .= "Le nom du fichier est trop long.\n";
	
		if( filesize($_FILES['image']['tmp_name']) > self::IMAGE_SIZE ) 
			$this->errorUser .= "Votre image est trop volumineuse.\n";

		if( !in_array( strrchr($_FILES['image']['name'], '.'), self::EXTENSIONS_IMAGE ) ) 
			$this->errorUser .= "L'extension de votre image n'est pas autorisé";

		if( $this->dataBook['description'] === '' )
			$this->errorUser .= "Veuillez renseigner une description.\n";
		if( strlen($this->dataBook['description']) > self::DESCRIPTION_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs description.\n";

		if( $this->dataBook['auteur'] === '' )
			$this->errorUser .= "Veuillez renseigner l'auteur du livre.\n";
		if( strlen($this->dataBook['auteur']) > self::AUTEUR_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs auteur.\n";

		return $this->errorUser === '';
	}

	public function isValidModify() {
		$this->errorUser = '';
		if( $this->dataBook['title'] === '' )
			$this->errorUser .= "Veuillez renseigner un titre.\n";
		if( strlen($this->dataBook['title']) > self::TITRE_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs titre.\n";

		if( !empty($_FILES['image']['name']) ) {
			if( strlen($_FILES['image']['name']) >  self::IMAGE_PATH_LENGTH )
				$this->errorUser .= "Le nom du fichier est trop long.\n";
		
			if( filesize($_FILES['image']['tmp_name']) > self::IMAGE_SIZE ) 
				$this->errorUser .= "Votre image est trop volumineuse.\n";

			if( !in_array( strrchr($_FILES['image']['name'], '.'), self::EXTENSIONS_IMAGE ) ) 
				$this->errorUser .= "L'extension de votre image n'est pas autorisé";
		}

		if( $this->dataBook['description'] === '' )
			$this->errorUser .= "Veuillez renseigner une description.\n";
		if( strlen($this->dataBook['description']) > self::DESCRIPTION_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs description.\n";

		if( $this->dataBook['auteur'] === '' )
			$this->errorUser .= "Veuillez renseigner l'auteur du livre.\n";
		if( strlen($this->dataBook['auteur']) > self::AUTEUR_LENGTH ) 
			$this->errorUser .= "Nombre de caractère incorrecte pour le champs auteur.\n";

		return $this->errorUser === '';
	}
}

?>