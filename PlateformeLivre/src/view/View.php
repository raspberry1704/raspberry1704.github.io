<?php



class View
{
	const PATH_UPLOAD = "upload/";
	protected $router;
	protected $title;
	protected $content;
	
	/**
	 * View constructor.
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
		$this->title = null;
		$this->content = null;
	}
	
	/* Affiche la page créée. */
	public function render()
	{
		if ($this->content === null) {
			$this->makeUnexpectedErrorPage();
		}
		$title = $this->title;
		$content = $this->content;
		
		$menu = array();
		
		array_push($menu, "<a href='" . $this->router->getHomeURL() . "'>Accueil</a>");
		array_push($menu, "<a class='has-sub' href='" . $this->router->getBooksURL() . "'>Bibliotheque</a>");
		if (key_exists('username', $_SESSION)) {
			if ($_SESSION['level'] == 2) {
				array_push($menu, "<a href='" . $this->router->getAdministrativeAccountURL() . "'>Gestion des comptes</a>");
			}
			array_push($menu, "<a href='" . $this->router->getProfilURL() . "'>Mon profil</a>");
			array_push($menu, "<a href='" . $this->router->getMyBooksURL() . "'>Mes livres</a>");
			array_push($menu, "<form action='" . $this->router->getLogOutURL() . "' method='POST'><input type='submit' value='Se Déconnecter' /></form>");
		} else {
			array_push($menu, "<a href='" . $this->router->getLogInURL() . "'>Se connecter</a>");
		}
		
		include("template.php");
	}
	
	
	/******************************************************************************/
	/* Méthodes de génération des pages                                           */
	/******************************************************************************/
	
	public function makeAccesInterditPage()
	{
		$this->title = "Accès interdit";
		$this->content = "Vous n'êtes pas autorisé à accéder au contenu.";
	}
	
	public function makeHomePage($last_books)
	{
		$this->title = "Plateforme partage livres";
		
		//Message accueil
		$this->content .= "<article class='accueil'>";
		$this->content .= "<h3>Bienvenue " . (isset($_SESSION['username']) ? $_SESSION["username"] : "") . " ! </h3>";
		$this->content .= "<p>Ce site est une plateforme de partage de livres, ici vous pouvez voir les livres ajoutés par d'autres utilisateurs dans la section <a class='has-sub' href='" . $this->router->getBooksURL() . "'><strong>Bibliotheque</strong></a>, ou en ajouter " . (!isset($_SESSION['username']) ? " en <a href='" . $this->router->getLogInURL() . "'>vous connectant</a> puis en allant dans <strong>Mes livres</strong>" : "en allant dans <a href='" . $this->router->getMyBooksURL() . "'>Mes livres</a>") ."</p>";
		$this->content .= "<noscript><p>Javascript est désactivé, veuillez l'activer dans votre navigateur pour avoir accès à l'intégralité du contenu sur cette page.</p></noscript>";
		if (!key_exists('username', $_SESSION)) {
			$this->makeLogInPage();
		}
		
		$this->content .= "</article>";
		//Derniers livres ajoutés
		
		$this->content .= "<div class='tableLivres accueil'>";
		$this->content .= "<h3>Derniers livres ajoutés</h3>";
		
		for ($i = 0; $i < sizeof($last_books); $i++) {
			$this->makeBookPage($last_books[$i]);
		}
		$this->content .= "</div>";
	}
	
	public function makeBookPage($book)
	{
		$this->title = "« " . $book->getTitle() . " », par " . $book->getAuthor();

		$this->content .= "<section>";
		$this->content .= "<div class='bandeau ficheLivre'><a href='" . $this->router->getBookURL($book->getId()) . "'><h2 class='title'>" . $book->getTitle() . "</h2></a>";
		if (isset($_SESSION['username'])) {
			if ($book->getUserOwner() == $_SESSION['ID']) {
				$this->content .= "<div class='action'><a id='actionEdit' href='" . $this->router->getBookModificationURL($book->getId()) . "'>Modifier</a>";
				$this->content .= "<form action='" . $this->router->getBookSuppressionURL($book->getId()) . "' method='POST'><input id='actionDelete' class='actionSuppression' type='submit' value='Supprimer' /></form></div>";
			}
		}
		$this->content .= "</div>";
		$this->content .= "<a href='" . $this->router->getBookURL($book->getId()) . "'><img class='imageLivre' alt='" . $book->getTitle() . "' src='" . SELF::PATH_UPLOAD . $book->getImage() . "'/></a>";
		$this->content .= "<h3>" . $book->getAuthor() . "</h3>";
		$this->content .= "<p>" . $book->getDescription() . "</p>";
		
		$this->content .= "</section>";
	}
	
	public function makeMyBooksPage($books_list)
	{
		$this->title = "Mes livres";
		$this->content .= "<div class='fullButton'><a href='" . $this->router->getBookAjoutURL() . "'>Ajouter un Livre</a></div>";
		$this->content .= "<div class='tableLivres'>";
		for ($i = 0; $i < sizeof($books_list); $i++) {
			$this->makeBookPage($books_list[$i]);
		}
		$this->content .= "</div>";
	}
	
	public function makeBooksPage($books_list)
	{
		$this->title = "Nos livres";
		$this->content .= "<div class='tableLivres'>";
		
		$this->generateErrors();
		
		for ($i = 0; $i < sizeof($books_list); $i++) {
			$this->makeBookPage($books_list[$i]);
		}
		
		$this->content .= "</div>";
	}
	
	public function makeBookAddPage()
	{
		$this->title = "Ajouter un livre";
		$this->content = '<section><div class="formulaire large"> <h3>Ajouter un Livre</h3>';
		$this->generateErrors();
		$this->content .= '<form action=' . $this->router->getBookAjoutConfirmURL() . ' method="POST" enctype="multipart/form-data">' . "\n";
		$this->content .= $this->generate_formulaire_connection("Titre :", "text", "title");
		$this->content .= $this->generate_formulaire_connection("Auteur de l'oeuvre :", "text", "auteur");
		$this->content .= '<p><input type="hidden" name="MAX_FILE_SIZE" value="1048576" /></p>';
		$this->content .= $this->generate_formulaire_connection("Image :", "file", "image");
		$this->content .= '<p><label for="descriptionInput">Description du livre :</label> <textarea type="password" name="description" id="descriptionInput"></textarea></p>' . "\n";
		$this->content .= '<input type="submit" value="Ajouter le livre" />' . "\n";
		$this->content .= "</form>";
		
		$this->content .= "</div></section>";
	}
	
	public function makeBookModificationPage($book)
	{
		$this->title = "Modification livre";
		$this->content = '<section><div class="formulaire large"><h3>Edition</h3>' . "\n";
		$this->generateErrors();
		$this->content .=  '<form action=' . $this->router->getBookModificationConfirmURL($book->getId()) . ' method="POST" enctype="multipart/form-data">';
		$this->content .= $this->generate_formulaire_connection("Titre :", "text", "title", $book->getTitle());
		$this->content .= $this->generate_formulaire_connection("Auteur de l'oeuvre :", "text", "auteur", $book->getAuthor());
		$this->content .= '<p><input type="hidden" name="MAX_FILE_SIZE" value="1048576" /></p>';
		$this->content .= $this->generate_formulaire_connection("Image :", "file", "image", $book->getImage());
		$this->content .= '<p><label for="descriptionInput">Description du livre :</label> <textarea type="password" name="description" id="descriptionInput">' . $book->getDescription() . '</textarea></p>' . "\n";
		
		$this->content .= '<p><input type="submit" value="Appliquer les modifications" /></p>' . "\n";
		$this->content .= "</form>";
		
		
		$this->content .= "</div></section>";
	}
	
	private function generate_formulaire_connection($label, $type, $name, $value = null)
	{
		if ($value != null) {
			$value = 'value="' . $value . '";';
		}
		return "<p><label for='$name.Input'>$label</label><input $value type='$type' name='$name' id='$name.Input'></p>";
	}
	
	private function generate_select_birthday()
	{
		$formulaire = "<p><label>Date de naissance</label>";
		$months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		
		$formulaire .= "<select name='day' id='dayInput'>";
		for ($i = 1; $i < 32; $i++) {
			if ($i == 1)
				$formulaire .= "<option select='selected' value='$i'>$i</option>";
			else
				$formulaire .= "<option value='$i'>$i</option>";
		}
		
		$formulaire .= "</select>";
		
		$formulaire .= "<select name='month' id='monthInput'>";
		for ($i = 0; $i < sizeof($months); $i++) {
			if ($i == 0)
				$formulaire .= "<option selected='selected' value='" . ($i + 1) . "'>" . $months[$i] . "</option>";
			else
				$formulaire .= "<option value='" . ($i + 1) . "'>" . $months[$i] . "</option>";
		}
		$formulaire .= "</select>";
		
		
		$currentDate = date("Y");
		$formulaire .= "<select name='year' id='yearInput'>";
		for ($i = $currentDate - 130; $i < $currentDate + 1; $i++) {
			if ($i == $currentDate)
				$formulaire .= "<option selected='selected' value='$i'>$i</option>";
			else
				$formulaire .= "<option value='$i'>$i</option>";
		}
		$formulaire .= "</select></p>";
		return $formulaire;
	}
	
	private function generateErrors() {
		if (isset($_SESSION['feedback'])) {
			$this->content .= "<div class='listeErreurs'>";
			for ($i = 0; $i < sizeof($_SESSION['feedback']); $i++)
				$this->content .= "<p class='erreur'>" . $_SESSION['feedback'][$i] . "</p>";
			$this->content .= "</div>";
			$_SESSION['feedback'] = array();
		}
	}
	
	public function makeLogInPage()
	{
		if(empty($this->title)) {$this->title = "Connection";}
		
		$this->content .= "<div class='formulaire'><h3>Se connecter</h3>" . "\n";
		$this->generateErrors();
		$this->content .= "<form action='" . $this->router->getLogInConfirmationURL() . "' method='POST'>";
		if (isset($_SESSION['tmp_username']))
			$this->content .= $this->generate_formulaire_connection("Pseudo :", "text", "username", $_SESSION['tmp_username']);
		else
			$this->content .= $this->generate_formulaire_connection("Pseudo :", "text", "username");
		
		$this->content .= $this->generate_formulaire_connection("Mot de Passe :", "password", "password1");
		$this->content .= "<input type='submit' value='Se connecter' />" . "\n";
		$this->content .= "</form>";
		
		
		$this->content .= "<p>Vous n'avez pas de compte? <a href='" . $this->router->getSignInURL() . "'>Inscrivez vous.</a></p>";
		$this->content .= "</div>";
	}
	
	public function makeLogOutPage()
	{
		$this->title = "Déconnection";
		$this->content .= "<article class='accueil'>";
		$this->content .= "<h3>Déconnection</h3>";
		$this->content .= "<p>Merci de votre visite " . $_SESSION['username'] . ".</p>";
		$this->content .= "</article>";
	}
	
	public function makeSignInPage()
	{
		$this->title = "Inscription";
		$this->content = "<div class='formulaire'><h3>Inscription</h3>";
		$this->generateErrors();
		
		$this->content .= "<form action='" . $this->router->getSignInConfirmationURL() . "' method='POST'>" . "\n";
		$this->content .= $this->generate_formulaire_connection("Nom d'utilisateur", "text", "username");
		$this->content .= $this->generate_formulaire_connection("Mot de Passe", "password", "password1");
		$this->content .= $this->generate_formulaire_connection("Confirmer le mot de Passe", "password", "password2");
		$this->content .= $this->generate_formulaire_connection("Email", "text", "email");
		$this->content .= $this->generate_formulaire_connection("Adresse", "text", "adresse");
		$this->content .= $this->generate_select_birthday();
		$this->content .= "<span><input type='submit' value='Sinscrire' /></span>" . "\n";
		$this->content .= "</form>";
		
		
		$this->content .= "</div>";
	}
	
	/**
	 * @param $users_list
	 */
	public function makeAccountManagementPage($users_list)
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		
		$this->title = "Administration des comptes";
		$this->content .= "<div><table><tr><td>ID</td><td>Nom d'utilisateur</td><td>Mot de passe</td><td>Niveau d'administration</td><td>Email</td><td>Adresse</td><td>Date de naissance</td><td>Action</td></tr>";
		
		for ($i = 0; $i < sizeof($users_list); $i++) {
			if ($_SESSION['level'] >= $users_list[$i]->getLevel()) {
				
				$this->content .= "<tr>";
				$this->content .= "<td>" . $users_list[$i]->getId() . "</td>";
				$this->content .= "<td>" . $users_list[$i]->getUsername() . "</td>";
				//$this->content .= "<td>******</td>";
				$this->content .= "<td>" . $users_list[$i]->getLevel() . "</td>";
				$this->content .= "<td>" . $users_list[$i]->getEmail() . "</td>";
				$this->content .= "<td>" . $users_list[$i]->getAdresse() . "</td>";
				$this->content .= "<td>" . $users_list[$i]->getDateDeNaissance() . "</td>";
				
				if ($_SESSION['level'] > $users_list[$i]->getLevel()) {
					$this->content .= "<td><a href='" . $this->router->getChangeLevelAccountURL($users_list[$i]->getId(), $users_list[$i]->getLevel() + 1) . "'>Promouvoir</a> ";
					$this->content .= "<a class='actionSuppressionCompte' href='" . $this->router->getDeleteAccountURL($users_list[$i]->getId()) . "'>Supprimer le compte</a>";
					$this->content .= "<a href='" . $this->router->getChangeLevelAccountURL($users_list[$i]->getId(), -1) . "'>Bannir le compte</a></td>";
					
				}
				
				
				$this->content .= "</tr>";
			}
		}
		
		$this->content .= "</table>";
		
		if (isset($_SESSION['feedback'])) {
			$this->content .= "<div class='listeErreurs'>";
			for ($i = 0; $i < sizeof($_SESSION['feedback']); $i++)
				$this->content .= "<p class='erreur'>" . $_SESSION['feedback'][$i] . "</p>";
			$this->content .= "</div>";
			$_SESSION['feedback'] = array();
			
		}
		$this->content .= "</div>";
	}
	
	public function makeManageProfilPage($user)
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		
		$this->title = "Mon profil";
		$this->content .= "<section>";
		if (strcmp(@$_GET['request'], "modify") == 0) {
			$this->content .= '<div class="formulaire">';
			$this->content .= '<h3>Mes informations</h3>';
			$this->generateErrors();
			$this->content .= '<form action=' . $this->router->getModifyProfilConfirmationURL() . ' method="POST">' . "\n";
			
			$this->content .= $this->generate_formulaire_connection("Nom d'utilisateur", "text", "username", $user->getUsername());
			$this->content .= $this->generate_formulaire_connection("Mot de Passe", "password", "password1");
			$this->content .= $this->generate_formulaire_connection("Confirmer le mot de Passe", "password", "password2");
			$this->content .= $this->generate_formulaire_connection("Email", "text", "email", $user->getEmail());
			$this->content .= $this->generate_formulaire_connection("Adresse", "text", "adresse", $user->getAdresse());
			$this->content .= $this->generate_select_birthday();
			
			$this->content .= '<p><input type="submit" value="Valider les modifications" /></p>' . "\n";
			$this->content .= "</form></div>";
			
		} else {
			$this->content .= "<div class='profil'>";
			$this->content .= "<h3>Mes informations</h3><div class='info'>";
			$this->content .= "<p><label>Nom d'utilisateur :</label> <span>" . $user->getUsername() . "</span></p>";
			$this->content .= "<p><label>Mot de passe :</label> <span id='passwd'></span></p>";
			$this->content .= "<p><label>Email :</label> <span>" . $user->getEmail() . "</span></p>";
			$this->content .= "<p><label>Adresse :</label> <span>" . $user->getAdresse() . "</span></p>";
			$this->content .= "<p><label>Né le :</label><span>" . $user->getDateDeNaissance() . "</span></p></div>";
			$this->content .= "<div class='actionForm'>";
			$this->content .= "<a href='" . $this->router->getModifyProfilURL() . "'>Modifier mes informations</a>";
			$this->content .= "<form action='" . $this->router->getDeleteMyAccountURL() . "' method='POST'><input class='actionSuppressionCompte' type='submit' value='Supprimer mon compte' /></form>";
			$this->content .= "</div></div>";
		}
		
		
		$this->content .= "</section>";
		
	}

	public function makeUnknownbookPage()
	{
		$this->title = "Erreur 404 not Found";
		$this->content .= "<article class='accueil'>";
		$this->content .= "<h3>Erreur 404 not Found</h3>";
		$this->content .= "<p>Le livre demandé n'existe pas.</p>";
		$this->content .= "</article>";
	}
	
	public function makeUnexpectedErrorPage()
	{
		$this->title = "Erreur";
		$this->content .= "<article class='accueil'>";
		$this->content .= "<h3>Erreur</h3>";
		$this->content .= "<p>Une erreur inattendue s'est produite.</p>";
		$this->content .= "</article>";
	}
	
	public function makeAProposPage()
	{
		$this->title = "A propos";
		$this->content .= "<article class='accueil'>";
		$this->content .= "<h3>A Propos</h3>";
		$this->content .= file_get_contents("APropos.html", true);
		$this->content .= "</article>";
	}
}