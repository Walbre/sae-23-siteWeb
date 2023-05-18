<?php

session_start();


function genNavBar($statut){

    ?>
    
    <ul>
        <li><a href="index.php">index</a></li>

    <?php

    if ($statut === "administrateur"){
        ?>
            <li><a href="insertion.php">insertion</a></li>
            <li><a href="modification.php">modification</a></li>
            <li><a href="suppression.php">suppression</a></li>

        <?php
    }

    echo '<li>langue</li>';
    echo '<li><a href="deconnexion.php">deconnexion</a></li>';
    echo '</ul>';

}


function logout(){
    session_destroy();
    $_SESSION = array();
}

function genCaptchat(){
    $url = "http://20.216.129.46/getcaptchat";
    $contents = file_get_contents($url);
    echo json_decode($contents);
}

function redirect($pas_co, $pas_admin){
    if (!empty($_SESSION) && isset($_SESSION["statut"])){
        if (!is_null($pas_admin) && $_SESSION["statut"] !== "administrateur"){
            echo "<script>window.location = 'index.php'</script>";
        }
    }
    else{
        echo "<script>window.location = 'connexion.php'</script>";
    }
}

function get_table($qui){

    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($qui === "repr"){
        $requete = "SELECT NOMR, VILLE FROM REPRESENTANTS";
    }
    else if ($qui === "prod"){
        $requete = "SELECT NOMP, COUL, PRIX FROM PRODUITS";
    }
    else{
        $requete = "SELECT NOMR,REPRESENTANTS.VILLE, NOMC, CLIENTS.VILLE, NOMP, COUL, PRIX, QT FROM VENTES INNER JOIN REPRESENTANTS ON REPRESENTANTS.NR = VENTES.NR INNER JOIN CLIENTS ON CLIENTS.NC = VENTES.NC INNER JOIN PRODUITS ON PRODUITS.NP = VENTES.NP";
    }


    $res = $db->query($requete);
    if ($res){
        $tab = $res->fetchAll(PDO::FETCH_ASSOC);
        return $tab;
    }
}

function affiche_tableau($tableau, $head){
    echo "<table>\n";
    echo "<thead>\n<tr>\n";
    foreach ($head as $cle){
        echo "<th>$cle</th>";
    }
    echo "</tr>\n</thead>\n";
    echo "<tbody>\n";
    foreach($tableau as $tab){
        echo "<tr>";
        foreach($tab as $sous_tab){
            echo "<td>$sous_tab</td>";
        }
        echo "</tr>\n";
    }
    echo "</tbody>\n";
    echo "</table>\n";
}

function formInsertion(){
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
        <label for="id_table">Table :</label> 
            <select id="id_table" name="table" size="1" onchange="changeForm(this)">
                <option value="REPRESENTANTS">représentants</option>
                <option value="VENTES">ventes</option>
                <option value="PRODUITS">produits</option>
            <select>
        </fieldset>

        <fieldset class="REPRESENTANTS">
            <label for="id_nomr">Nom représentant : </label><input name="nom" id="id_nomr" required size="20" />
            <label for="id_viller">Ville représentant : </label><input name="ville" id="id_viller" required size="20" />
        </fieldset>

        <fieldset class="VENTES">

        </fieldset>

        <fieldset class="PRODUITS">

        </fieldset>

        <input type="submit" value="Insérer"/>

        <script>
            function changeForm(name){
                var names = ["REPRESENTANTS", "VENTES", "PRODUITS"]
                names.splice(names.indexOf(name.value), 1)
                // invisible les autres
                names.forEach(nom => Array.from(document.getElementsByClassName(nom)).forEach(elem => elem.style.display = 'none'))
                Array.from(document.getElementsByClassName(name.value)).forEach(elem => elem.style.display = 'block')
            }
        </script>

    <?php
}

?>