<?php

require "fonctions.php";

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<title>register</title>
	</head>
    <body>
    <h2>Bienvenue sur la page register ! </h2>
    <?php
if (!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=="logout") {
				session_destroy();
				$_SESSION=array();
			}			

if (!empty($_POST) && isset($_POST['login']) && isset($_POST['pass']) && empty(validate($_POST['login'],$_POST['pass'])))	{	
	
    
    insert_compte($login, $pass);
	

}

?>

<form onsubmit ="return verif_mdp()">  
    <p><label for="login">Login : </label><input type="text" id="login" name="login" /></p>
    <p><label for="pass">Mot de Passe : </label><input type = "password" id = "pass" value = ""><span id = "message_mdp" style="color:red"> </span></p>
    <p><input type="submit" id="submit" name="submit" value="Register" /> <button type = "reset" value = "Reset" >Reset</button></p>
    
    
</form>  

<form id="cnx" method="post" action="connexion.php">
		<p><input type="submit" id="submit" name="submit" value="Connexion" /></p>
</form>

    </body>