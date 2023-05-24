<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("insertion.php");
    die();
}

require "fonctions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertion</title>
</head>
<body>
    <?php
        if (!empty($_SESSION) && isset($_SESSION["pseudo"])){
            if (isset($_SESSION["statut"]) && $_SESSION["statut"] === "administrateur"){
                genNavBar($_SESSION["statut"]);
                echo "<h1>Bienvenue admin</h1>";
                echo "<section>\n<h2>Inserer un objet</h2>\n</section>";
                echo "<section>\n<h2>Tables</h2>\n</section>";
                formInsertion();

                echo "<article>\n<h3>Les représentants</h3>\n";
                affiche_tableau(get_table("repr"), ["Nom représentant", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h3>Les produits</h3>\n";
                affiche_tableau(get_table("prod"), ["Nom produit", "Couleur", "Prix"]);
                echo "</article>\n";

                echo "<article>\n<h3>Les clients</h3>\n";
                affiche_tableau(get_table("clients"), ["Nom client", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h3>Tout</h3>\n";
                affiche_tableau(get_table(""), ["Nom représentant", "Ville représentant", "Nom client", "Ville client", "Nom produit", "Couleur", "Prix", "Quantité"]);
                echo "</article>\n";
            }
        }
        else{
            redirect("index.php", "conexion.php");
        }
        
    ?>
</body>
</html>