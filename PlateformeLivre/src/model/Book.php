<?php

/* Représente un livre. */
class Book {
	
	protected $id;
	protected $title;
	protected $image;
	protected $author;
	protected $description;
	protected $user_owner;

	public function __construct($id, $title, $image, $author, $description, $user_owner) {
		$this->id = $id;
		$this->title = $title;
		$this->image = $image;
		$this->author = $author;
		$this->description = $description;
		$this->user_owner = $user_owner;
	}

	public function getId() {
		return $this->id;
	}
	
	/* Renvoie le titre du poème */
	public function getTitle() {
		return $this->title;
	}

	/* Renvoie le nom du fichier contenant le portrait de l'auteur */
	public function getImage() {
		return $this->image;
	}

	/* Renvoie le nom de l'auteur */
	public function getAuthor() {
		return $this->author;
	}

	/* Renvoie le texte du poème, formaté en HTML */
	public function getDescription() {
		return $this->description;
	}
	
	/* Renvoie l'id de l'utilisateur qui a crée ce contenu */
	public function getUserOwner() {
		return $this->user_owner;
	}
}
?>