<?php

require "fonctions.php";

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<title>connexion</title>
	</head>
    <body>
	<h2>Bienvenue sur la page connexion ! </h2>
<?php
if (!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=="logout") {
				session_destroy();
				$_SESSION=array();
			}			

if (!empty($_POST) && isset($_POST['login']) && isset($_POST['pass']) && !(empty(validate($_POST['login'],$_POST['pass']))))	{	
	
	$tab = validate($_POST['login'],$_POST['pass']);

	$_SESSION["pseudo"] = $tab[0];
	$_SESSION["statut"] = $tab[1];

	/*if ( authentification($_POST["login"],$_POST["pass"]) )		{					
		$_SESSION["login"]=$_POST["login"];
			if ($_POST["login"]=="admin" && $_POST["pass"]=="Lannion!")  {
				$_SESSION["statut"]="administrateur";
				echo "<p>C'est bon</p>";
			}
			else {
			$_SESSION["statut"]="utilisateur";
			}
	}
*/



	echo '<script>document.location.replace("index.php")</script>';
	    			

	
}

			

if (empty($_SESSION)){



?>
	<form id="cnx" method="post" action="connexion.php">
		<p><label for="login">Login : </label><input type="text" id="login" name="login" /></p>
		<p><label for="pass">Mot de Passe : </label><input type="password" id="pass" name="pass" /></p>
		<p><input type="submit" id="submit" name="submit" value="Connexion" /></p>
		
	</form>

	<form id="rgs" method="post" action="register.php">
	<input type="submit" id="register" name="register" value="Register" />
	</form>

<?php
}
?>	    

    </body>
