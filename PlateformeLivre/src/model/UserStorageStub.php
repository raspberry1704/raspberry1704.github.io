<?php
/* Inclusion des dépendances de cette classe */
require_once("model/User.php");
require_once("model/UserStorage.php");
ini_set('display_errors', 1);
require_once("Utilitaires/DB.inc.php");

class UserStorageStub implements UserStorage {

	protected $users;
	protected $error;

	/**
	 * Construit une nouvelle instance, qui utilise le fichier donné en paramètre.
	 * Si le fichier n'existe pas, la base est initialisée
	 * avec trois animaux.
	 */
	public function __construct() {		
		$this->users = array();
		$this->error = null;

		$database = DB::getInstance();
		$users = $database->select("SELECT * FROM users");

		foreach ($users as $user) {
			array_push($this->users, new User($user->username, $user->password, $user->id, $user->ad_level, $user->email, $user->adresse, $user->datedenaissance));
		}
		
	}
	
	public function read($id) {
		for( $i = 0; $i < sizeof($this->users); $i++ )
			if( $this->users[$i]->getId() == $id )
				return $this->users[$i];
		$this->error .= null;
	}

	public function readAll() {
		return $this->users;
	}

	/* !!! ajouter l'ajout au tableau */
	public function create(User $user) {
		$this->error = "";

		//On verifie qu un utilisateur na pas le meme pseudo
		$database = DB::getInstance();
		$user_exist = $database->select("SELECT username FROM users WHERE username='".$user->getUsername()."'");

		if( !empty($user_exist) ) {
			$this->error = "Erreur, nom d'utilisateur déjà pris.";
			return false;
		}

		$hash = password_hash($user->getPassword(), PASSWORD_BCRYPT);
		$database = DB::getInstance();
		$database->maj("INSERT INTO users (username,password,ad_level, email, adresse, dateDeNaissance) VALUES('".$user->getUsername()."','".$hash."', 0,'". $user->getEmail() ."','". $user->getAdresse() ."','". $user->getDateDeNaissance() ."')");
		$this->refresh();
		return true;
	}
	
	public function supprimer($id) {
		$database = DB::getInstance();
		$database->maj("DELETE FROM users WHERE id=$id");
		$this->refresh();
	}
	
	public function prepareSuppression($id) {
		$resultat = "";
		if( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		
		if( isset($_SESSION['ID']) ) {
			$user = $this->read($id);
			
			if( $user != null ) {
				if(  $_SESSION['ID'] == $user->getId() || $_SESSION['level'] > $user->getLevel()  ) {
					$this->error .= $resultat;
				}
				else {
					$resultat = "Erreur, vous n'êtes pas autorisé à supprimer ce compte.";			
				}
			} else {
				$resultat = "Erreur, impossible de trouver le compte demandé.";			
			}
		} else {
			$resultat = "Erreur, requête de suppression impossible. Vous devez être connecté.";
		}
		
		
		$this->error .= $resultat;
	}
	
	private function refresh() {
		$this->users = array();
		$database = DB::getInstance();
		$users = $database->select("SELECT * FROM users ORDER BY id");

		foreach ($users as $user)
			array_push($this->users, new User($user->username, $user->password, $user->id, $user->ad_level, $user->email, $user->adresse, $user->datedenaissance));
	}

	public function modificationProfil( $user_modified ) {
		$current = $this->read($_SESSION['ID']);

		if( empty($user_modified->getPassword() ))
			$user_modified = new User($user_modified->getUsername(), $current->getPassword(), $current->getId(), $current->getLevel(), $user_modified->getEmail(), $user_modified->getAdresse(), $user_modified->getDateDeNaissance());
		else { 
			$password = password_hash($user_modified->getPassword(), PASSWORD_BCRYPT);
			$user_modified = new User($user_modified->getUsername(), $password, $current->getId(), $current->getLevel(), $user_modified->getEmail(), $user_modified->getAdresse(), $user_modified->getDateDeNaissance());
		}

		$database = DB::getInstance();
		if( strcmp($user_modified->getUsername(), $current->getUsername()) != 0 ) {
			$user_exist = $database->select("SELECT username FROM users WHERE username='".$user_modified->getUsername()."'");
		}

		if( !empty($user_exist) ) {
			$this->error .= "Ce nom d'utilisateur est déjà pris.";
			return false;
		}

		$database->maj("UPDATE users SET username='".$user_modified->getUsername()."', password='".$user_modified->getPassword()."', email='".$user_modified->getEmail()."', adresse='".$user_modified->getAdresse()."', datedenaissance='".$user_modified->getDateDeNaissance()."' WHERE id='".$user_modified->getId()."'");
		$this->refresh();
		$_SESSION['username'] = $user_modified->getUsername();
		return true;
	}

	public function changerNiveau($id, $level) {
		if( session_status() == PHP_SESSION_NONE  )
			session_start();
		echo "On est dans la fonction<br/>";
		$user = $this->read($id);
		print_r($user);

		if( empty($user) )
			$this->error .= "Erreur, utilisateur introuvable";


		echo "Etape 1<br/>";
		echo "Etape 2<br/>";

		if( $level == -1 )
			if( $_SESSION['level'] < $user->getLevel() )
				$this->error .= "Vous n'avez pas la permission pour cette action";
		else {
			if( $_SESSION['level'] <= $level ) {
				$this->error .= "Vous n'avez pas la permission pour cette action";
			}
		}
		echo "Etape 3<br/>";
		$database = DB::getInstance();
		$database->maj("UPDATE users SET ad_level=$level WHERE id='".$user->getId()."'");
	}
	
	public function prepareFeedback() {
		$_SESSION['feedback'] = array();
		if( substr_count($this->error, "\n") == 1) {
			array_push($_SESSION['feedback'], $this->error);
		} else {
			$_SESSION['feedback'] = explode("\n", $this->error);
			unset($_SESSION['feedback'][sizeof($_SESSION['feedback'])-1]);
		}
	}

	public function verifConnect(User $user) {
		$this->error = "";
		$connectionIsPossible = true;

		foreach($this->users as $u)
			if( strcmp($user->getUsername(), $u->getUsername()) == 0 )
				$user_find = $u;

		if( !isset($user_find) ) {
			$this->error .= "Nom d'utilisateur introuvable.\n";
			return false;
		}

		if( !password_verify($user->getPassword(), $user_find->getPassword()) ) {
			$this->error .= "Mot de passe incorrect.\n";
			$connectionIsPossible = false;
		}

		if( $user_find->getLevel() == -1 ) {
			$this->error .= "Votre compte a été banni. Veuillez contacter un administrateur.";
			$connectionIsPossible = false;
		}

		if( $connectionIsPossible == false ) {
			$_SESSION['tmp_username'] = $user->getUsername();
			return false;
		}

		$_SESSION['username'] = $user_find->getUsername();
		$_SESSION['level'] = $user_find->getLevel();
		$_SESSION['ID'] = $user_find->getId();
		
		return true;
	}
}
?>