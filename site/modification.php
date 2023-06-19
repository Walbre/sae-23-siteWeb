<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("modification.php");
    die();
}

require "fonctions.php";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<title>modification</title>
	</head>
    <body>

<?php
        if (!empty($_SESSION) && isset($_SESSION["pseudo"])){
            if (isset($_SESSION["statut"]) && $_SESSION["statut"] === "administrateur"){
                $erreur = "";



                if (!empty($_POST) && isset($_POST["table"])){
                    if ($_POST["table"] === "CLIENTS"){
                        if (isset($_POST["nomc2"]) && isset($_POST["villec2"]) && isset($_POST["client"]) && !($_POST["nomc2"] === "") && !($_POST["villec2"] === "") && !($_POST["client"] === "") && !($_POST["nomc2"] === 0) && !($_POST["villec2"] === 0)){

                            $erreur = modifClient($_POST["client"],$_POST["nomc2"], $_POST["villec2"]);
                        }
                    }

                    else if ($_POST["table"] === "REPRESENTANTS"){
                        if (isset($_POST["nomr2"]) && isset($_POST["viller2"]) && !($_POST["nomr2"] === "" && isset($_POST["repr"]) && !($_POST["repr"] === ""))){
                            $erreur = modifRepr($_POST["repr"],$_POST["nomr2"], $_POST["viller2"]);
                        }
                    }

                    else if ($_POST["table"] === "VENTES"){
                        if (isset($_POST["repr"]) && isset($_POST["client"]) && isset($_POST["produit"]) && isset($_POST["qt"])){
                            if (!($_POST["repr"] === "") && !($_POST["client"] === "") && !($_POST["produit"] === "") && !($_POST["qt"] === "")){
                                $erreur = modifVente($_POST["repr"], $_POST["produit"],$_POST["client"], $_POST["qt"]);
                            }
                            
                        }
                    }
                    else if ($_POST["table"] === "PRODUITS"){
                        if (isset($_POST["nom2"]) && isset($_POST["couleur2"]) && isset($_POST["prix2"]) && isset($_POST["prod"]) && !($_POST["prod"] === "")){
                            if (!($_POST["nom2"] === "") && !($_POST["couleur2"] === "") && !($_POST["prix2"] === "")){
                                $erreur = modifProduit($_POST["prod"],$_POST["nom2"], $_POST["couleur2"], $_POST["prix2"]);
                            }
                            
                        }
                    }
                }


                genNavBar($_SESSION["statut"], $_SESSION["pseudo"]);
                
                echo '<div class="container">';

                echo '<h1 class="display-5 fw-bold text-center">Bienvenue '.$_SESSION["pseudo"].'</h1>';

                if ($erreur !== "" && $erreur !== null){
                    echo '<div class="alert alert-danger" role="alert">'.htmlspecialchars($erreur).'</div>';
                }

                echo '<h2 class="display-5 text-center">Page de modification d\'objets</h2>';
                

                echo '<section id="section_formulaire" class="row justify-content-center mb-3">'."\n";
                echo '<div class="col-4">';

                echo '<h3 class="fw-bold text-center">Formulaire</h3>'."\n";
                formeModif();

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
        }
        else{
            redirect("index.php", "conexion.php");
        }
    ?>
        <?php genFooter(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
