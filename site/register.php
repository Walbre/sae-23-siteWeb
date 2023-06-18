<?php

/* Page register
Author : Morgan MOOTOOSAMY
*/

require "fonctions.php";

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<title>register</title>
	</head>
    <body>
		<article>
    <h2>Bienvenue sur la page register ! </h2>
    <?php
	/* Verification si une session éxiste */
if (!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=="logout") {
				session_destroy();
				$_SESSION=array();
			}		
            
            

/* Verification du login et mdp */
if (!empty($_POST) && isset($_POST['login']) && isset($_POST['pass']) && !(valide_cnx($_POST['login'],$_POST['pass'])) && verif_mdp($_POST['pass']))	{	
	
    
    
    insert_compte($_POST['login'], $_POST['pass']); /* Insesrtion du compte*/
	

}

?>

<form id="rgs" method="post" action="register.php">
		<p><label for="login">Login : </label><input type="text" id="login" name="login" /></p>
		<p><label for="pass">Mot de Passe : </label><input type="password" id="pass" name="pass" /></p>
		<p><input type="submit" id="submit_reg" name="submit_reg" value="Register" /></p>
		
	</form>
<form id="cnx" method="post" action="connexion.php">
		<p><input type="submit" id="submit" name="submit" value="Connexion" /></p>
</form>
</article>
</br>

<?php genFooter(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>
