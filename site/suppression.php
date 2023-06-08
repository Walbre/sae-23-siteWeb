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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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


                genNavBar($_SESSION["statut"], $_SESSION["pseudo"]);


                echo '<div class="container">';

                echo '<h1 class="display-5 fw-bold text-center">Bienvenue '.$_SESSION["pseudo"].'</h1>';
                echo '<h2 class="display-5 text-center">Page de suppression d\'objets</h2>';
                

                echo '<section id="section_formulaire" class="row justify-content-center mb-3">'."\n";
                echo '<div class="col-4">';

                echo '<h3 class="fw-bold text-center">Formulaire</h3>'."\n";
                formSupression();

                echo '</div>';
                echo "</section>";
                

                echo '<section id="section_tables" class="text-center">'."\n".'<h3 class="fw-bold">Tables</h3>'."\n";

                echo "<article>\n<h4>Les représentants</h4>\n";
                affiche_tableau(get_table("repr"), ["Nom représentant", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h4>Les produits</h4>\n";
                affiche_tableau(get_table("prod"), ["Nom produit", "Couleur", "Prix"]);
                echo "</article>\n";

                echo "<article>\n<h4>Les clients</h4>\n";
                affiche_tableau(get_table("cli"), ["Nom client", "Ville"]);
                echo "</article>\n";

                echo "<article>\n<h4>Tout</h4>\n";
                affiche_tableau(get_table(""), ["Nom représentant", "Ville représentant", "Nom client", "Ville client", "Nom produit", "Couleur", "Prix", "Quantité"]);
                echo "</article>\n";

                echo "</section>";


                echo '</div>';
            }
            else{
                echo '<script>window.location = "index.php"</script>';
            }
        }
        else{
            echo '<script>window.location = "connection.php"</script>';
        }
    ?>

    
    <?php genFooter(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>