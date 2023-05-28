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


function validate($login, $pass){

    $login = addslashes($login);
    $pass = addslashes($pass);

    $valide = array();
    

    $dbc = new PDO('sqlite:bdd/comptes.sqlite');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $requete = "SELECT * FROM comptes;";


    $res = $dbc->query($requete);
    $comptes = $res->fetchAll(PDO::FETCH_ASSOC);

    if (!(empty($login) && empty($pass))){
        foreach($comptes as $compte){
            if ($compte["login"] === $login && $compte["motdepasse"] === $pass) {

                $requete_status = "SELECT * FROM comptes WHERE login = '$login' AND motdepasse = '$pass'";
                $res = $dbc->query($requete_status);
                $tab_login = $res->fetchAll(PDO::FETCH_ASSOC);

                if ($tab_login[0]["statut"] === "administrateur"){

                    array_push($valide, $login, $tab_login[0]["statut"]);
                }
                else{
                    array_push($valide, $login, $tab_login[0]["statut"]);  
                }
            }
        }
        
    }

    return $valide;
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
                <label for="id_nomr">Nom représentant : </label><input name="nomr" id="id_nomr" size="20" />
                <label for="id_viller">Ville représentant : </label><input name="viller" id="id_viller" size="20" />
            </article>

            <article class="PRODUITS">
                <label for="id_nomprod">Nom produit : </label><input name="nom" id="id_nomprod" size="20" />
                <label for="id_couleurprod">Couleur : </label><input name="couleur" id="id_couleurprod" size="20" />
                <label for="id_prixprod">Prix : </label><input name="prix" id="id_prixprod" size="20" type="number" min="0"/>
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
                <label for="id_qtvente">Quantité : </label><input name="qt" id="id_qtvente" size="20" type="number" min="0"/>

            </article>

            <article class="CLIENTS">
                <label for="id_nomclient">Nom Client : </label><input name="nomc" id="id_nomclient" size="20" />
                <label for="id_villeclient">Ville du client : </label><input name="villec" id="id_villeclient" size="20" />
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

    $nom = addslashes($nom);
    $ville = addslashes($ville);

    $requete = "INSERT INTO CLIENTS(NOMC, VILLE) VALUES ('$nom', '$ville')";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function ajoutRepr($nom, $ville){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nom = addslashes($nom);
    $ville = addslashes($ville);

    $requete = "INSERT INTO REPRESENTANTS(NOMR, VILLE) VALUES ('$nom', '$ville')";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function ajoutProduit($nom, $couleur, $prix){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nom = addslashes($nom);
    $couleur = addslashes($couleur);
    $prix = addslashes($prix);

    $requete = "INSERT INTO PRODUITS(NOMP, COUL, PRIX) VALUES ('$nom', '$couleur', '$prix')";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function ajoutVente($nr, $nc, $np, $quantite){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nr = addslashes($nr);
    $nc = addslashes($nc);
    $np= addslashes($np);
    $quantite = addslashes($quantite);

    $requete = "INSERT INTO VENTES(NR, NC, NP, QT) VALUES ('$nr', '$nc', '$np', '$quantite')";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}



function get_table_with_id($qui){

    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($qui === "repr"){
        $requete = "SELECT NR, NOMR, VILLE FROM REPRESENTANTS";
    }
    else if ($qui === "prod"){
        $requete = "SELECT NP, NOMP, COUL, PRIX FROM PRODUITS";
    }
    else if ($qui === "cli"){
        $requete = "SELECT NC, NOMC, VILLE FROM CLIENTS";
    }
    else{
        $requete = "SELECT VENTES.NR, VENTES.NP, VENTES.NC, NOMR,REPRESENTANTS.VILLE, NOMC, CLIENTS.VILLE AS VILLEC, NOMP, COUL, PRIX, QT FROM VENTES INNER JOIN REPRESENTANTS ON REPRESENTANTS.NR = VENTES.NR INNER JOIN CLIENTS ON CLIENTS.NC = VENTES.NC INNER JOIN PRODUITS ON PRODUITS.NP = VENTES.NP";
    }


    $res = $db->query($requete);
    if ($res){
        $tab = $res->fetchAll(PDO::FETCH_ASSOC);
        return $tab;
    }
}



function formSupression(){

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
                <label for="id_repr">Représentant :</label>
                <select id="id_repr" name="repr" size="1">
                <?php
                    $data = get_table_with_id("repr");
                    foreach ($data as $val){
                        echo "<option value=".$val["NR"].'>'.$val["NOMR"].' de '.$val["VILLE"].'</option>';
                    }

                ?>
                </select>
            </article>

            <article class="PRODUITS">
                <label for="id_prod">Produit : </label>
                <select id="id_prod" name="prod" size="1">
                <?php
                    $data = get_table_with_id("prod");
                    foreach ($data as $val){
                        echo "<option value=".$val["NP"].'>'.$val["NOMP"].' '.$val["COUL"].' ('.$val["PRIX"].'€)'.'</option>';
                    }

                ?>
                </select>
            </article>

            <article class="VENTES">
                <label for="id_venter">Représentant :</label>
                <select id="id_vente" name="vente" size="1">
                    <?php
                    $data = get_table_with_id("");
                    foreach ($data as $val){
                        echo "<option value=".$val["NR"].','.$val["NP"].','.$val["NR"].'>'.$val["NOMR"].' de '.$val["VILLE"].' -> '.$val["NOMC"].' de '.$val["VILLEC"].' : '.$val["NOMP"].' '.$val["COUL"].' ('.$val["PRIX"].'€) x '.$val["QT"].'</option>';
                    }
                    ?>
                </select>

            </article>

            <article class="CLIENTS">
            <label for="id_client">Client : </label>
                <select id="id_client" name="client" size="1">
                <?php
                    $data = get_table_with_id("cli");
                    foreach ($data as $val){
                        echo "<option value=".$val["NC"].'>'.$val["NOMC"].' '.$val["VILLE"].'</option>';
                    }

                ?>
                </select>
            </article>

            <input type="submit" value="Supprimer"/>

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

function supprimerClient($nc){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nc = addslashes($nc);

    $requete = "DELETE FROM CLIENTS WHERE nc='$nc'";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function supprimerRepr($nr){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nr = addslashes($nr);

    $requete = "DELETE FROM REPRESENTANTS WHERE nr='$nr'";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function supprimerProduit($np){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $np = addslashes($np);

    $requete = "DELETE FROM PRODUITS WHERE np='$np'";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}


function supprimerVente($nr, $nc, $np){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nr = addslashes($nr);
    $nc = addslashes($nc);
    $np= addslashes($np);

    $requete = "DELETE FROM VENTES WHERE nc='$nc' AND np='$np' AND nr='$nr'";

    $res = $db->exec($requete);
    if (!$res){
        echo "erreur";
    }
}






?>



