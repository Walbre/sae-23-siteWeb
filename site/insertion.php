<?php
if (!empty($_GET) && isset($_GET["source"])){
    show_source("insertion.php");
    die();
}

session_start();
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
                echo "<h1>Welcome admin</h1>";
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