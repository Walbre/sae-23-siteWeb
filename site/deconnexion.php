<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("suppression.php");
    die();
}

require "fonctions.php";

logout();
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
    <script>window.location = "connexion.php"</script>
</body>
</html>