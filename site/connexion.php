<?php
session_start();

require "fonctions.php";

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<title>connexion</title>
	</head>
    <body>
<?php
if (!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=="logout") {
				echo "supression";
				session_destroy();
				$_SESSION=array();
			}			

if (!empty($_POST) && isset($_POST['login']) && isset($_POST['pass']))	{		
	if ( authentification($_POST["login"],$_POST["pass"]) )		{					
		$_SESSION["login"]=$_POST["login"];
			if ($_POST["login"]=="admin") {
				$_SESSION["statut"]="administrateur";
			}
			else {
			$_SESSION["statut"]="utilisateur";
			}
?>	
	    
<h1>Accueil depuis la page initiale</h1>
<a href="index.php">Lien vers la section membre</a>		
<p><a href="connexion.php?action=logout">Se déconnecter</a></p>
	    
<?php			
				
	}
}
			

if (empty($_SESSION)){
?>
	<form id="cnx" method="post" action="connexion.php">
		<p><label for="login">Login :</label><input type="text" id="login" name="login" /></p>
		<p><label for="pass">Mot de Passe :</label><input type="password" id="pass" name="pass" /></p>
		<p><input type="submit" id="submit" name="submit" value="Connexion" /></p>
	</form>
<?php
}
?>	    

	    <p>test</p>

    </body>
