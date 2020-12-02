<?php

/* Inclusion des classes nécessaires */
require_once("view/View.php");
require_once("model/BookStorage.php");
require_once("model/BookBuilder.php");
require_once("model/UserBuilder.php");
require_once("model/UserStorage.php");

class Controller {

	protected $view;
	protected $bookdb;
	protected $userdb;

	public function __construct(View $view, BookStorage $bookdb, UserStorage $userdb) {
		$this->view = $view;
		$this->bookdb = $bookdb;
		$this->userdb = $userdb;
	}

	public function confirm_connection(array $couple_log) {
		$userbuilder = new UserBuilder($couple_log);

		//Formulaire invalide
		if( !$userbuilder->isValidConnect() ) {
			$userbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		//Couple login invalide
		if( !$this->userdb->verifConnect($userbuilder->createUser()) ) {
			$this->userdb->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		header("Location: .", true, 303);
		return;
	}

	public function logOut() {
		$_SESSION = array();
	}

	public function modificationProfil(array $data) {
		$userbuilder = new UserBuilder($data);

		if( !$userbuilder->isValidModifyProfil() ) {
			$userbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);			
			return;			
		}

		$user = $userbuilder->createUser();
		if( !$this->userdb->modificationProfil( $user ) ) {
			$this->userdb->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);			
			return;						
		}

		$this->view->makeManageProfilPage($this->userdb->read($_SESSION['ID']));
	}

	public function confirm_sign_in(array $couple_log) {
		$userbuilder = new UserBuilder($couple_log);

		//Formulaire valide
		if( !$userbuilder->isValid() ) {
			$userbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		//Données d'inscription valides
		$user = $userbuilder->createUser();
		if( !$this->userdb->create( $user ) ) {
			$userbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		//Création du compte validé, on connecte l'utilisateur
		$this->confirm_connection($couple_log);
	}

	public function showbook($id) {
		$book = $this->bookdb->read($id);
		if ($book !== null) {
			/* Le livre existe, on prépare la page */
			$this->view->makebookPage($book);
		} else {
			$this->view->makeUnknownbookPage();
		}
	}	

	public function supprimerMonCompte() {
		$this->bookdb->supprimerLivreCompte($_SESSION['ID']);
		$this->userdb->supprimer($_SESSION['ID']);
		$this->logOut();
		header("Location: .", true, 303);
	}


	public function changerNiveau($id, $level) {
		if( session_status() == PHP_SESSION_NONE  )
			session_start();
		$resultat = $this->userdb->changerNiveau($id, $level);
		$this->view->makeAccountManagementPage($this->userdb->readAll());
	}
	
	public function supprimerCompte($id) {
		$resultat = $this->userdb->prepareSuppression($id);
		if( $resultat == "" ) {
			$this->bookdb->supprimerLivreCompte($id);
			$this->userdb->supprimer($id);
		}
		
		if( $_SESSION['ID'] == $id )
			$this->view->makeLogOutPage();
		else
			$this->view->makeAccountManagementPage($this->userdb->readAll());
	}
	
	public function ajouterLivre(array $databook) {
		$bookbuilder = new BookBuilder($databook);

		if( !$bookbuilder->isValid() ) {
			$bookbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		if( !$this->bookdb->ajouter($bookbuilder->createBook()) ) {
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		header("Location: ./?action=my_books", true, 303);
		return;
	}

	public function modificationBook($id, array $databook) {
		$bookbuilder = new BookBuilder($databook);
		if( !$bookbuilder->isValidModify() ) {
			$bookbuilder->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}

		if( !$this->bookdb->modifier($id, $bookbuilder->createBook()) ) {
			$this->bookdb->prepareFeedback();
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		} 

		header("Location: ./?action=my_books", true, 303);
		return;
	}
	
	public function supprimerLivre($id) {
		if( $this->bookdb->supprimer($id) ) {
			$this->bookdb->readUserBooks($_SESSION['ID']);
		} else {
			header("Location: " . $_SERVER['HTTP_REFERER'], true, 303);
			return;
		}
	}
}
?>