<?php

ini_set ('display_errors', 1);
ini_set ('display_startup_errors', 1);
error_reporting (E_ALL);

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
    else if ($qui === "cli"){
        $requete = "SELECT NOMC, VILLE FROM CLIENTS";
    }
    else{
        $requete = "SELECT NOMR,REPRESENTANTS.VILLE, NOMC, CLIENTS.VILLE AS VILLEC, NOMP, COUL, PRIX, QT FROM VENTES INNER JOIN REPRESENTANTS ON REPRESENTANTS.NR = VENTES.NR INNER JOIN CLIENTS ON CLIENTS.NC = VENTES.NC INNER JOIN PRODUITS ON PRODUITS.NP = VENTES.NP";
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
                <option value="PRODUITS">produits</option>
                <option value="VENTES">ventes</option>
                <option value="CLIENTS">clients</option>
            </select>

            <article class="REPRESENTANTS">
                <label for="id_nomr">Nom représentant : </label><input name="nomr" id="id_nomr" required size="20" />
                <label for="id_viller">Ville représentant : </label><input name="viller" id="id_viller" required size="20" />
            </article>

            <article class="PRODUITS">
                <label for="id_nomprod">Nom produit : </label><input name="nom" id="id_nomprod" required size="20" />
                <label for="id_couleurprod">Couleur : </label><input name="couleur" id="id_couleurprod" required size="20" />
                <label for="id_prixprod">Prix : </label><input name="prix" id="id_prixprod" required size="20" type="number" min="0"/>
            </article>

            <article class="VENTES">
                <label for="id_venterepr">Représentant :</label>
                <select id="id_venterepr" name="repr" size="1">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_repr = "SELECT NR as id, NOMR || ' de ' || VILLE AS field FROM REPRESENTANTS";

                        $res = $db->query($requete_repr);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".$val["id"].">".$val["field"]."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_venteclient">Client :</label>
                <select id="id_venteclient" name="client" size="1">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_c = "SELECT NC as id, NOMC || ' de ' || VILLE AS field FROM CLIENTS";

                        $res = $db->query($requete_c);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".$val["id"].">".$val["field"]."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_venteproduit">Produit :</label>
                <select id="id_venteproduit" name="produit" size="1">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_prod = "SELECT NP as id, NOMP || ' ' || COUL || ' (' || PRIX || '€)' AS field FROM PRODUITS";

                        $res = $db->query($requete_prod);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".$val["id"].">".$val["field"]."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_qtvente">Quantité : </label><input name="qt" id="id_qtvente" required size="20" type="number" min="0"/>

            </article>

            <article class="CLIENTS">
                <label for="id_nomclient">Nom Client : </label><input name="nomc" id="id_nomclient" required size="20" />
                <label for="id_villeclient">Ville du client : </label><input name="villec" id="id_villeclient" required size="20" />
            </article>

            <input type="submit" value="Insérer"/>

        </fieldset>
        <script>
            function changeForm(name){
                var names = ["REPRESENTANTS", "VENTES", "PRODUITS", "CLIENTS"]
                names.splice(names.indexOf(name.value), 1)
                // invisible les autres
                names.forEach(nom => Array.from(document.getElementsByClassName(nom)).forEach(elem => elem.style.display = 'none'))
                Array.from(document.getElementsByClassName(name.value)).forEach(elem => elem.style.display = 'block')
            }
            changeForm(document.getElementById('id_table'))
        </script>

    <?php
}




function ajoutClient($nom, $ville){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nom = $db->quote($nom);
    $ville = $db->quote($ville);

    $requete = "INSERT INTO CLIENTS(NOMC, VILLE) VALUES ($nom, $ville)";

    $res = $db->exec($requete_prod);
    if (!$res){
        echo "erreur";
    }
}
?>
