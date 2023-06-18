<?php

require "fonctions.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Connexion</title>
</head>
    <body>

<article>
<h2>Bienvenue sur la page connexion ! </h2>	
<?php
if (!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=="logout") {
				session_destroy();
				$_SESSION=array();
			}			

if (!empty($_POST) && isset($_POST['login']) && isset($_POST['pass']) && valide_cnx($_POST['login'],$_POST['pass']))	{	
	
	$tab = validate_tab($_POST['login'],$_POST['pass']);

	$_SESSION["pseudo"] = $tab[0];
	$_SESSION["statut"] = $tab[1];



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
</article>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
