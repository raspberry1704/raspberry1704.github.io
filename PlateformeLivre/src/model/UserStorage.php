<?php

/* Inclusion des dependances de cette classe */
require_once("model/User.php");
ini_set('display_errors', 1);
interface UserStorage {

	/**
	 * Renvoie un tableau contenant tous les utilisateurs de la base,
	 * indexés par leur nom.
	 */
	public function readAll();

	/**
	 * Ajoute un nouvel utilisateur à la base.
	 */
	public function create(User $user);

	/**
	 * Supprime un utilisateur de la base.
	 * Renvoie true si lutilisateur a ete supprime,
	 * et false s'il n'existait pas.
	 */
	 
	 public function modificationProfil($data);
}

?>