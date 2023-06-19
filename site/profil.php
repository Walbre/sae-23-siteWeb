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
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Profil</title>
</head>
<body>
    <?php
    if (isset($_SESSION) && isset($_SESSION["pseudo"])){

        // backend
        $erreurPhoto = "";
        if (!empty($_POST) && isset($_POST["photo"])){
            $erreurPhoto = changePDP($_POST["photo"], $_SESSION["pseudo"], $_SESSION["statut"]);
        }

        $erreurMdp = "";

        if (!empty($_POST) && isset($_POST["mdp"]) && $_POST["mdp"] !== ""){
            $erreurMdp = changePasswd($_POST["mdp"], $_SESSION["pseudo"]);
        }

        genNavBar($_SESSION["statut"], $_SESSION["pseudo"]);

        echo '<div class="container mb-3">';

        if ($erreurPhoto !== "" && $erreurPhoto !== null){
            echo '<div class="alert alert-danger" role="alert">'.htmlspecialchars($erreurPhoto).'</div>';
        }

        if ($erreurMdp !== "" && $erreurMdp !== null){
            echo '<div class="alert alert-danger" role="alert">'.htmlspecialchars($erreurMdp).'</div>';
        }

        echo '<h2 class="display-5 fw-bold text-center">Bienvenue '.htmlspecialchars($_SESSION["pseudo"]).'</h2>';

        formChagePDP($_SESSION["statut"]);
        formPasswd();

        echo '</div>';
    }
    else{
        redirect("connexion.php", null);
    }
    ?>

    <?php genFooter(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>