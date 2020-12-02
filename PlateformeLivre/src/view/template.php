<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo $title ?></title>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" href="skin/normalize.css"/>
    <link rel="stylesheet" href="skin/book.css"/>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="skin/book.js"></script>
</head>
<body>
<header>
    <a href=".">
        
        <img id="logo_banniere" src="skin/images/livres.jpg" alt="Bannière"/>
    </a>
    <h1>Plateforme de Partage de Livre</h1>
</header>
<nav class="menu">
    <ul>
		<?php
		foreach ($menu as $text => $link) {
			echo "\n\t\t\t\t<li>$link</li>";
		}
		echo "\n";
		?>
    </ul>
</nav>
<main>
    <!-- <h1><?php echo $title; ?></h1> -->
	<?php echo $content; ?>
</main>
<footer>
    <div class="footerContent">
        <ul>
            <li><a href="?action=apropos">A propos</a></li>
        </ul>
        <div class="auteur">
            <p>Ce site a été développé par 21506749 et 21615815. </p>
        </div>
    </div>
</footer>
</body>
</html>

