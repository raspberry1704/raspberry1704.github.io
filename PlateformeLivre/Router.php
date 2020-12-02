<?php
require_once("model/BookStorage.php");
require_once("model/UserStorage.php");
require_once("view/View.php");
require_once("control/Controller.php");

/*
 * Le routeur s'occupe d'analyser les requêtes HTTP
 * pour décider quoi faire et quoi afficher.
 * Il se contente de passer la main au contrôleur et
 * à la vue une fois qu'il a déterminé l'action à effectuer.
 */
class Router {
	
	private $bookdb;
	private $userdb;
	private $view;
	
	public function __construct(BookStorage $bookdb, UserStorage $userdb) {
		$this->bookdb = $bookdb;
		$this->userdb = $userdb;
	}
	
	public function main() {
		
		/* vue de base */
		$this->view = new View($this);
		$controleur = new Controller($this->view, $this->bookdb, $this->userdb);
		
		
		
		try {
			/* Analyse de l'URL */
			if( key_exists('action', $_GET) ) {
				switch( $_GET['action'] ) {
					case 'log_in':
						$this->view->makeLogInPage();
						break;
					
					case 'log_out':
						if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
							if(key_exists('username', $_SESSION)) {
								$this->view->makeLogOutPage();
								$controleur->logOut();
							}
						}
						else {
							header("Location: .", true, 303);
						}
						
						break;
					
					case 'sign_in':
						$this->view->makeSignInPage();
						break;
					
					case 'confirm_sign_in':
						if( $_SERVER["REQUEST_METHOD"] === "POST" )
							$controleur->confirm_sign_in($_POST);
						else
							$this->view->makeAccesInterditPage();
						break;
					
					case 'confirm_connection':
						if( $_SERVER["REQUEST_METHOD"] === "POST" )
							$controleur->confirm_connection($_POST);
						else
							$this->view->makeAccesInterditPage();
						break;
					
					case 'manage_account':
						if( $_SESSION['level'] == 2 )
							$this->view->makeAccountManagementPage($this->userdb->readAll());
						break;
					
					case 'my_books':
						if( isset( $_SESSION['username'] ) )
							$this->view->makeMyBooksPage( $this->bookdb->readUserBooks($_SESSION['ID']) );
						break;
					
					case 'book':
					if( isset($_GET['id']) ) {
						$book = array();
						array_push($book ,$this->bookdb->read($_GET['id']));
						if( isset($book[0]) )
							$this->view->makeBooksPage( $book );
						else
							$this->view->makeUnknownbookPage();							
					}

					break;
					
					case 'deleteAccount':
						if( $_SESSION['level'] == 2  )
							$controleur->supprimerCompte($_GET['id']);
						break;
					
					case 'changeLevel':
						if( $_SESSION['level'] != 2  )
							$controleur->changerNiveau($_GET['id'], $_GET['level']);
						break;
					
					case 'addBook':
						if( isset($_SESSION['username']) )
							$this->view->makeBookAddPage();
						break;
					
					case 'addBookConfirm':
						if( isset($_SESSION['username']) && isset($_POST) && $_SERVER['REQUEST_METHOD'] === "POST" )
							$controleur->ajouterLivre($_POST);
						break;
					
					case 'modificationBook':
						if( key_exists("id", $_GET) && isset($_SESSION['username']) )
							$this->view->makeBookModificationPage($this->bookdb->read($_GET['id']));
						break;
					
					case 'modificationBookConfirm':
						if( key_exists("id", $_GET) && isset($_SESSION['username']) && isset($_POST) && $_SERVER['REQUEST_METHOD'] === "POST" )
							$controleur->modificationBook($_GET['id'], $_POST);
						else
							$this->view->makeAccesInterditPage();
						break;
					
					case 'manage_profil':
						if( isset($_SESSION['username']) )
							$this->view->makeManageProfilPage($this->userdb->read($_SESSION['ID']));
						break;
					
					case 'confirm_modify_profil':
						if( isset($_SESSION['username']) && isset($_POST) )
							$controleur->modificationProfil( $_POST );
						break;
					
					case 'deleteMyAccount':
						if( isset( $_SESSION['username']) )
							$controleur->supprimerMonCompte();
						break;
					
					case 'deleteBook':
						if( $_SERVER['REQUEST_METHOD'] === 'POST' && key_exists("id", $_GET) )
								$controleur->supprimerLivre($_GET['id']);
						else
							$this->view->makeAccesInterditPage();
						break;
					
					case 'books':
						$this->view->makeBooksPage($this->bookdb->readAll());
						break;
					
					case 'apropos':
						$this->view->makeAProposPage();
						break;
					
					case 'home':
						$this->view->makeHomePage($this->bookdb->readLastElements(4));
						break;
					
					default:
						$this->view->makeHomePage($this->bookdb->readLastElements(4));
						break;
				}
			} else {
				$this->view->makeHomePage($this->bookdb->readLastElements(4));
			}
			
		} catch (Exception $e) {
			/* Si on arrive ici, il s'est passé quelque chose d'imprévu
			* (par exemple un problème de base de données) */
			$this->view->makeUnexpectedErrorPage($e);
		}
		
		/* Enfin, on affiche la page préparée */
		$this->view->render();
		
	}
	
	/* URL de la page d'accueil */
	public function getHomeURL() {
		return ".";
	}
	
	/* URL de la page de connection */
	public function getLogInURL() {
		return "?action=log_in";
	}
	
	/* URL de la page de déconnection */
	public function getLogOutURL() {
		return "?action=log_out";
	}
	
	/* URL de la page de gestion des comptes administrateurs */
	public function getAdministrativeAccountURL() {
		return "?action=manage_account";
	}
	
	public function getProfilURL() {
		return "?action=manage_profil";
	}
	
	public function getModifyProfilURL() {
		return "?action=manage_profil&request=modify";
	}
	
	public function getModifyProfilConfirmationURL() {
		return "?action=confirm_modify_profil";
	}
	
	/* URL de la page d'inscription */
	public function getSignInURL() {
		return "?action=sign_in";
	}
	
	/* URL de la page de confirmation d'inscription */
	public function getSignInConfirmationURL() {
		return "?action=confirm_sign_in";
	}
	
	/* URL de la page de confirmation de connection */
	public function getLogInConfirmationURL() {
		return "?action=confirm_connection";
	}
	
	/* URL de la page du poème d'identifiant $id */
	public function getBooksURL() {
		return "?action=books";
	}
	
	public function getBookModificationConfirmURL($id) {
		return "?action=modificationBookConfirm&&id=$id";
	}
	
	public function getBookModificationURL($id) {
		return "?action=modificationBook&id=$id";
	}
	
	public function getBookSuppressionURL($id) {
		return "?action=deleteBook&id=$id";
	}
	
	public function getBookAjoutURL() {
		return "?action=addBook";
	}
	
	public function getMyBooksURL() {
		return "?action=my_books";
	}
	
	public function getBookURL($id) {
		return "?action=book&id=$id";
	}
	
	public function getBookAjoutConfirmURL() {
		return "?action=addBookConfirm";
	}
	
	public function getModifyAccountURL($id) {
		return "?action=manage_account&request=modify&id=$id";
	}
	
	public function getModifyAccountConfirmationURL($id) {
		return "?action=modifyAccount&id=$id";
	}
	
	public function getDeleteMyAccountURL() {
		return "?action=deleteMyAccount";
	}
	
	public function getDeleteAccountURL($id) {
		return "?action=deleteAccount&id=$id";
	}
	
	public function getChangeLevelAccountURL($id, $level) {
		return "?action=changeLevel&id=$id&level=$level";
	}
	
	
	/* URL de la page à propos */
	public function getAProposURL() {
		return "?action=apropos";
	}
	
}

?>