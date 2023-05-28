<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("index.php");
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
    <title>Document</title>
</head>
<body>
    <?php
    if (isset($_SESSION) && isset($_SESSION["pseudo"])){
        genNavBar($_SESSION["statut"]);
        genSearchBar();
        if (!empty($_GET)){
            if (isset($_GET["search"])){
                echo "Recherche : ".htmlspecialchars($_GET["search"]);
                $rep = getSearch($_GET["search"]);
                echo '</br>';
                afficheLien($rep);
            }
            elseif(isset($_GET["page"])){
                getPage($_GET["page"]);
            }
        }

    }
    else{
        redirect("connexion.php", null);
    }
    ?>


</body>
</html>