<?php
if(!isset($_SESSION))
{
	session_start();
}
/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
ini_set('display_errors', 1);
set_include_path("./src");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once("model/BookStorageStub.php");
require_once("model/UserStorageStub.php");
/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */
$router = new Router(new BookStorageStub(), new UserStorageStub());
$router->main();
?>