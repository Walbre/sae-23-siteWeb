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
        <section>

            <h1>Bienvenue sur la page connexion</h1>

        <?php
		if (empty($_SESSION)){
        
        ?>
        
        <h2>Veuillez saisir vos identifiants : </h2>

        



        <?php

        }
		?>

        </section>
    

    </body>