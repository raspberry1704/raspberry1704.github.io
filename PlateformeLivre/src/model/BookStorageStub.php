<?php
require_once("model/Book.php");
require_once("model/BookStorage.php");

require_once("Utilitaires/DB.inc.php");

class BookStorageStub implements BookStorage {
	const PATH_UPLOAD = 'upload/';

	protected $books;
	protected $error;

	public function __construct() {
		$this->books = array();
		$this->error = null;
		
		$database = DB::getInstance();
		$books = $database->select("SELECT * FROM book ORDER BY date_ajout DESC");

		foreach ($books as $book)
			array_push($this->books, new Book($book->id, $book->title, $book->image, $book->author, $book->description, $book->owner));
	}

	public function read($id) {
		for( $i = 0; $i < sizeof($this->books); $i++ )
			if( $this->books[$i]->getId() == $id )
				return $this->books[$i];
		return null;
	}

	public function readAll() { return $this->books; }
	
	public function readLastElements( $nombreElement ) {
		$lastElements = array();
		for( $i = 0; $i < sizeof($this->books); $i++ ) {
			array_push($lastElements, $this->books[$i]);
			if( $i == $nombreElement-1 )
				break;
		}

		return $lastElements;
	}

	public function readUserBooks($id_user) {
		$userBooks = array();
		for( $i = 0; $i < sizeof($this->books); $i++ )
			if( $id_user == $this->books[$i]->getUserOwner() )	
				array_push($userBooks, $this->books[$i]);

		return $userBooks;		
	}

	private function refresh() {
		$this->books = array();
		$database = DB::getInstance();
		$books = $database->select("SELECT * FROM book ORDER BY date_ajout DESC");

		foreach ($books as $book)
			array_push($this->books, new Book($book->id, $book->title, $book->image, $book->author, $book->description, $book->owner));
	}
	
	public function ajouter($book) {
		if( !move_uploaded_file($_FILES['image']['tmp_name'], self::PATH_UPLOAD . $book->getImage() ) ) {
			$this->error .= "Erreur, l'image n'a pas pu être transféré.";
			return false;
		}

		$path_image = rand(1, 1000000000000000) . strrchr($book->getImage(), '.');
		while( file_exists(self::PATH_UPLOAD . $path_image) )
			$path_image = rand(1, 1000000000000000) . strrchr($book->getImage(), '.');		
		rename(self::PATH_UPLOAD . $book->getImage(), self::PATH_UPLOAD . $path_image);
		chmod(self::PATH_UPLOAD . $path_image, 0755);

		$database = DB::getInstance();
		$database->maj("INSERT INTO book (title, image, author, description, owner) VALUES ('".$book->getTitle()."', '".$path_image."', '".$book->getAuthor()."', '".$book->getDescription()."', ".$_SESSION['ID'].")");		
		$this->refresh();
		return true;
	}
	
	public function modifier($id, $book) {
		$currentbook = $this->read($id);
		if( !isset($currentbook) )
			return false;		

		if( $currentbook->getUserOwner() != $_SESSION['ID']  )
			return false;					

		$path_image = $currentbook->getImage();

		if( !empty( $_FILES['image']['name'])) {
			if( !move_uploaded_file($_FILES['image']['tmp_name'], self::PATH_UPLOAD . $book->getImage() ) ) {
				$this->error .= "Erreur, l'image n'a pas pu être transféré.";
				return false;
			}

			$path_image = rand(1, 1000000000000000) . strrchr($book->getImage(), '.');
			while( file_exists(self::PATH_UPLOAD . $path_image) )
				$path_image = rand(1, 1000000000000000) . strrchr($book->getImage(), '.');		
			rename(self::PATH_UPLOAD . $book->getImage(), self::PATH_UPLOAD . $path_image);
			chmod(self::PATH_UPLOAD . $path_image, 0755);
		}

		$database = DB::getInstance();				
		$database->maj("UPDATE BOOK SET title='".$book->getTitle()."', image='".$path_image."', author='".$book->getAuthor()."', description='".$book->getDescription()."' WHERE id=$id");
		$this->refresh();
		return true;
	}
	
	public function supprimer($id) {
		$book = $this->read($id);
		if( !isset($book) )
			return false;

		if( $book->getUserOwner() != $_SESSION['ID']  )
			return false;			

		unlink(self::PATH_UPLOAD . $book->getImage());
		$database = DB::getInstance();
		$database->maj("DELETE FROM book WHERE id=$id");
		$this->refresh();
		return true;
	}
		
	public function supprimerLivreCompte($id_account) {
		$database = DB::getInstance();
		$database->maj("DELETE FROM book WHERE owner=$id_account");
		$this->refresh();
		return true;
	}
}
?>