<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("suppression.php");
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
    <title>suppression</title>
</head>
<body>
    <?php
        if (!empty($_SESSION) && isset($_SESSION["pseudo"])){
            if (isset($_SESSION["statut"]) && $_SESSION["statut"] === "administrateur"){

                if (!empty($_POST) && isset($_POST["table"])){
                    if ($_POST["table"] === "CLIENTS"){
                        if (isset($_POST["client"]) && !($_POST["client"] === "")){
                            supprimerClient($_POST["client"]);
                        }
                    }

                    else if ($_POST["table"] === "REPRESENTANTS"){
                        if (isset($_POST["repr"]) && !($_POST["repr"] === "")){
                            supprimerRepr($_POST["repr"]);
                        }
                    }

                    else if ($_POST["table"] === "VENTES"){
                        if (isset($_POST["vente"]) && !($_POST["vente"] === "")){
                            $array_ventes = explode(",", $_POST["vente"]);
                            if (sizeof($array_ventes) === 3){
                                supprimerVente($array_ventes[0], $array_ventes[1], $array_ventes[2]);
                            }
                        }
                    }
                    else if ($_POST["table"] === "PRODUITS"){
                        if (isset($_POST["prod"]) && !($_POST["prod"] === "")){
                            supprimerProduit($_POST["prod"]);
                        }
                    }
                }

                genNavBar($_SESSION["statut"]);
                echo "<h1>Bienvenue admin</h1>";
                echo "<section>\n<h2>Suprimez un objet</h2>\n</section>";
                echo "<section>\n<h2>Tables</h2>\n</section>";
                formSupression();

                echo "<article>\n<h3>Les représentants</h3>\n";
                affiche_tableau(get_table("repr"), ["Nom représentant", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h3>Les produits</h3>\n";
                affiche_tableau(get_table("prod"), ["Nom produit", "Couleur", "Prix"]);
                echo "</article>\n";

                echo "<article>\n<h3>Les clients</h3>\n";
                affiche_tableau(get_table("cli"), ["Nom client", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h3>Tout</h3>\n";
                affiche_tableau(get_table(""), ["Nom représentant", "Ville représentant", "Nom client", "Ville client", "Nom produit", "Couleur", "Prix", "Quantité"]);
                echo "</article>\n";
            }
            else{
                echo '<script>window.location = "index.php"</script>';
            }
        }
        else{
            echo '<script>window.location = "connection.php"</script>';
        }
    ?>
</body>
</html>