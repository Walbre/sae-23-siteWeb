<?php

/* Page deconnexion
Author : Morgan MOOTOOSAMY
*/

if (!empty($_GET) && isset($_GET["source"])){
    show_source("deconnexion.php");
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
    <title>deconnexion</title>
</head>
<body>

<!-- Redirection vers la page connexion -->
<script>
    
    window.location = "connexion.php"; 
    
    </script>



    <article>
<?php

$ses = $_SESSION["pseudo"];
$stat = $_SESSION["statut"];

logout($ses,$stat); /*deconnexion du compte */
?> 


    </article>
</body>
</html>
