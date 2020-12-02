<?php

/* Interface représentant un système de stockage des livres. */
interface BookStorage {
	/* Renvoie l'instance de livre correspondant à l'identifiant donné,
	 * ou null s'il n'y en a pas. */
	public function read($id);

	/* Renvoie un tableau associatif id=>livre avec tous les livres de la base. */
	public function readAll();

	public function ajouter($book);
	public function modifier($id, $book);
	public function supprimer($id);
	public function supprimerLivreCompte($id_account);
}

?>