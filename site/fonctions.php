<?php

ini_set ('display_errors', 1);
ini_set ('display_startup_errors', 1);
ini_set("allow_url_fopen", 1);

error_reporting (E_ALL);

session_start();


function genNavBar($statut, $nom){

    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <!-- Bouton de la navabr en mode réduit -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    
                

    <?php

                if ($statut === "administrateur"){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="insertion.php">Insertion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="modification.php">Modification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="suppression.php">Suppression</a>
                    </li>

                    <?php
                }
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown09" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Français</a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown09">
                            <li><a class="dropdown-item" href="#">English</a></li>
                            <li><a class="dropdown-item" href="#">Francais</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="deconnexion.php">deconnexion</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav ml-auto w-100 justify-content-end">
                    <li class="nav-item">
                        <a href="profil.php">
                            <img src="<?php getProfilPic($nom) ?>" alt="photo de profil" style="height:50px;width:auto;">
                            <br><div class="text-white"><?php echo htmlspecialchars($nom); ?></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php

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

?>
<script>  
function verif_mdp() {  
  var pw = document.getElementById("pass").value;  
  var lettres = /[a-zA-Z]/;
  var nombres = /[0-9]/;
  var car =  /[!-*]/;

  var verif = true;
  if(pw == "" ) {  
     document.getElementById("message_mdp").innerHTML = "**Mot de passe à remplir";  
     var verif = false;  
  }  
  else if(pw.length < 8) {  
     document.getElementById("message_mdp").innerHTML = "**Il faut au moins 8 caractères";  
     var verif = false;
  }   
  else if(!lettres.test(pw)){
    document.getElementById("message_mdp").innerHTML = "**Il faut au moins une lettre majuscule and une lettre minuscule";  
    var verif = false;
  }
  else if(!nombres.test(pw)){
    document.getElementById("message_mdp").innerHTML = "**Il faut au moins un chiffre";  
    var verif = false;
  }
  else if(!car.test(pw)){
    document.getElementById("message_mdp").innerHTML = "**Il faut au moins un caractère spécial";  
    var verif = false;
  }
  return verif;
  } 
</script>  

<?php

function insert_compte($login, $pass){

    $verif = true;

    $dbc = new PDO('sqlite:bdd/comptes.sqlite');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $requete = "INSERT INTO comptes VALUES ('$login','$pass',utilisateur);";
    $res = $dbc->query($requete);

    if (!$res){
        echo "erreur";
        $verif = false;
    }

    return $verif;

}



function genCaptchat(){
    $url = "http://20.216.129.46:8080/getcaptchat";
    $contents = file_get_contents($url);
    $vals = json_decode($contents, true);
    $_SESSION["captchat_id"] = $vals["id"];
    echo '<article id="captchat">';
    $i = 0;
    foreach($vals["images"] as $img){
        echo  '<img src="'.$img.'" alt="captchat'.$i.'" onclick="isClicked(this)">'."\n";
        $i = $i + 1;
    }
    ?>
    <input type="hidden" id="rep" name="captchat" value="000000000">
    <script>
        var to_send = "000000000"
        function isClicked(endroit){
            index = parseInt(endroit.alt[8])
            if (endroit.classList.contains("clicked")){
                endroit.classList.remove("clicked")
                to_send = to_send.substring(0, index) + "0" + to_send.substring(index+1);
            }
            else{
                endroit.classList.add("clicked")
                to_send = to_send.substring(0, index) + "1" + to_send.substring(index+1)
            }
            elem = document.getElementById("rep").value = to_send
        }
    </script>
</article>

    <?php
}

function verifyCaptchat($res){
    $data = array("id" => $_SESSION["captchat_id"], "solve" => $res);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents("http://20.216.129.46:8080/verifycaptchat", false, $context);
    if ($result){
        $decoded_res = json_decode($result, true);
        if ($decoded_res["reponse"] === "true"){
            return true;
        }
        else{
            return "Erreur : ".$decoded_res["error"];
        }
    }
    else{
        return "Erreur du captchat, veuillez le recompléter.";
    }
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
    echo '<table class="table"'.">\n";
    echo "<thead>\n<tr>\n";
    foreach ($head as $cle){
        echo '<th>'.htmlspecialchars($cle)."</th>";
    }
    echo "</tr>\n</thead>\n";
    echo "<tbody>\n";
    foreach($tableau as $tab){
        echo "<tr>";
        foreach($tab as $sous_tab){
            echo '<td>'.htmlspecialchars($sous_tab)."</td>";
        }
        echo "</tr>\n";
    }
    echo "</tbody>\n";
    echo "</table>\n";
}

function formInsertion(){
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-group">
        <fieldset>
            <label for="id_table">Table :</label> 
            <select id="id_table" name="table" size="1" class="form-control" onchange="changeForm(this)">
                <option value="REPRESENTANTS">représentants</option>
                <option value="PRODUITS">produits</option>
                <option value="VENTES">ventes</option>
                <option value="CLIENTS">clients</option>
            </select>

            <br>

            <article class="REPRESENTANTS">
                <label for="id_nomr">Nom représentant : </label><input name="nomr" id="id_nomr" size="20" class="form-control">
                <label for="id_viller">Ville représentant : </label><input name="viller" id="id_viller" size="20" class="form-control">
            </article>


            <article class="PRODUITS">
                <label for="id_nomprod">Nom produit : </label><input name="nom" id="id_nomprod" size="20" class="form-control">
                <label for="id_couleurprod">Couleur : </label><input name="couleur" id="id_couleurprod" size="20" class="form-control">
                <label for="id_prixprod">Prix : </label><input name="prix" id="id_prixprod" type="number" min="0" class="form-control">
            </article>


            <article class="VENTES">
                <label for="id_venterepr">Représentant :</label>
                <select id="id_venterepr" name="repr" size="1" class="form-control">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_repr = "SELECT NR as id, NOMR || ' de ' || VILLE AS field FROM REPRESENTANTS";

                        $res = $db->query($requete_repr);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".htmlspecialchars($val["id"]).">".htmlspecialchars($val["field"])."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_venteclient">Client :</label>
                <select id="id_venteclient" name="client" size="1" class="form-control">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_c = "SELECT NC as id, NOMC || ' de ' || VILLE AS field FROM CLIENTS";

                        $res = $db->query($requete_c);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".htmlspecialchars($val["id"]).">".htmlspecialchars($val["field"])."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_venteproduit">Produit :</label>
                <select id="id_venteproduit" name="produit" size="1" class="form-control">
                    <?php
                        $db = new PDO('sqlite:bdd/repr.sqlite');
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $requete_prod = "SELECT NP as id, NOMP || ' ' || COUL || ' (' || PRIX || '€)' AS field FROM PRODUITS";

                        $res = $db->query($requete_prod);
                        if ($res){
                            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($tab as $val){
                                echo "<option value=".htmlspecialchars($val["id"]).">".htmlspecialchars($val["field"])."</option>\n";
                            }
                        }
                    ?>
                </select>
                <label for="id_qtvente">Quantité : </label><input name="qt" id="id_qtvente" type="number" min="0" class="form-control">

            </article>


            <article class="CLIENTS">
                <label for="id_nomclient">Nom Client : </label><input name="nomc" id="id_nomclient" size="20" class="form-control">
                <label for="id_villeclient">Ville du client : </label><input name="villec" id="id_villeclient" size="20" class="form-control">
            </article>

            <br>
            <div class="text-center">
                <input type="submit" class="btn btn-primary btn-customized justify-content-center" value="Insérer">
            </div>

        </fieldset>
        </form>
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

    analyseSQL("ajoutClient", [$nom, $ville]);

    $requete = "INSERT INTO CLIENTS(NOMC, VILLE) VALUES (:nom, :ville)";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nom', $nom, PDO::PARAM_STR);
    $statement->bindValue(':ville', $ville, PDO::PARAM_STR);
        
    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function ajoutRepr($nom, $ville){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("ajoutRepr", [$nom, $ville]);

    $requete = "INSERT INTO REPRESENTANTS(NOMR, VILLE) VALUES (:nom, :ville)";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nom', $nom, PDO::PARAM_STR);
    $statement->bindValue(':ville', $ville, PDO::PARAM_STR);
        

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function ajoutProduit($nom, $couleur, $prix){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("ajoutProduit", [$nom, $couleur, $prix]);

    $requete = "INSERT INTO PRODUITS(NOMP, COUL, PRIX) VALUES (:nom, :couleur, :prix)";

    $statement = $db->prepare($requete);

    $statement->bindValue(':nom', $nom, PDO::PARAM_STR);
    $statement->bindValue(':couleur', $couleur, PDO::PARAM_STR);
    $statement->bindValue(':prix', $prix, PDO::PARAM_INT);
        

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function ajoutVente($nr, $nc, $np, $quantite){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("ajoutVente", [$nr, $nc, $np, $quantite]);

    $requete = "INSERT INTO VENTES(NR, NC, NP, QT) VALUES (:nr, :nc, :np, :quantite)";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nr', $nr, PDO::PARAM_INT);
    $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
    $statement->bindValue(':np', $np, PDO::PARAM_INT);
    $statement->bindValue(':quantite', $quantite, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
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
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-group">
        <fieldset>
            <label for="id_table">Table :</label> 
            <select id="id_table" name="table" size="1" onchange="changeForm(this)" class="form-control">
                <option value="REPRESENTANTS">représentants</option>
                <option value="PRODUITS">produits</option>
                <option value="VENTES">ventes</option>
                <option value="CLIENTS">clients</option>
            </select>

            <br>

            <article class="REPRESENTANTS">
                <label for="id_repr">Représentant :</label>
                <select id="id_repr" name="repr" size="1" class="form-control">
                <?php
                    $data = get_table_with_id("repr");
                    foreach ($data as $val){
                        echo "<option value=".htmlspecialchars($val["NR"]).'>'.htmlspecialchars($val["NOMR"]).' de '.htmlspecialchars($val["VILLE"]).'</option>';
                    }

                ?>
                </select>
            </article>

            <article class="PRODUITS">
                <label for="id_prod">Produit : </label>
                <select id="id_prod" name="prod" size="1" class="form-control">
                <?php
                    $data = get_table_with_id("prod");
                    foreach ($data as $val){
                        echo "<option value=".htmlspecialchars($val["NP"]).'>'.htmlspecialchars($val["NOMP"]).' '.htmlspecialchars($val["COUL"]).' ('.htmlspecialchars($val["PRIX"]).'€)'.'</option>';
                    }

                ?>
                </select>
            </article>

            <article class="VENTES">
                <label for="id_vente">Représentant :</label>
                <select id="id_vente" name="vente" size="1" class="form-control">
                    <?php
                    $data = get_table_with_id("");
                    foreach ($data as $val){
                        echo "<option value=".htmlspecialchars($val["NR"]).','.htmlspecialchars($val["NC"]).','.htmlspecialchars($val["NP"]).'>'.htmlspecialchars($val["NOMR"]).' de '.htmlspecialchars($val["VILLE"]).' -> '.$val["NOMC"].' de '.htmlspecialchars($val["VILLEC"]).' : '.htmlspecialchars($val["NOMP"]).' '.htmlspecialchars($val["COUL"]).' ('.htmlspecialchars($val["PRIX"]).'€) x '.htmlspecialchars($val["QT"]).'</option>';
                    }
                    ?>
                </select>

            </article>

            <article class="CLIENTS">
            <label for="id_client">Client : </label>
                <select id="id_client" name="client" size="1" class="form-control">
                <?php
                    $data = get_table_with_id("cli");
                    foreach ($data as $val){
                        echo "<option value=".htmlspecialchars($val["NC"]).'>'.htmlspecialchars($val["NOMC"]).' '.htmlspecialchars($val["VILLE"]).'</option>';
                    }

                ?>
                </select>
            </article>

            <?php genCaptchat(); ?>

            <br>
            <div class="text-center">
                <input type="submit" class="btn btn-primary btn-customized justify-content-center" value="Supprimer">
            </div>

        </fieldset>
    </form>
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

    analyseSQL("supprimerClient", [$nc]);

    $requete = "DELETE FROM CLIENTS WHERE nc=:nc";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nc', $nc, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function supprimerRepr($nr){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("supprimerRepr", [$nr]);

    $requete = "DELETE FROM REPRESENTANTS WHERE nr=:nr";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nr', $nr, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function supprimerProduit($np){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("supprimerProduit", [$np]);

    $requete = "DELETE FROM PRODUITS WHERE np=:np";
    $statement = $db->prepare($requete);

    $statement->bindValue(':np', $np, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function supprimerVente($nr, $nc, $np){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("supprimerVente", [$nr, $nc, $np]);

    $requete = "DELETE FROM VENTES WHERE nc=:nc AND np=:np AND nr=:nr";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
    $statement->bindValue(':np', $np, PDO::PARAM_INT);
    $statement->bindValue(':nr', $nr, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
    }
}


function genSearchBar(){

    ?>
    <div class="row justify-content-center">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="col-8">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="rechercher" aria-label="Recipient's username">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
                </div>
            </div>
        </form>
    </div>
    
    
    <?php
}

function getSearch($val){
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("getSearch", [$val]);

    $val = "%$val%";
    $requete = "SELECT v.NR, v.NP, v.NC, NOMR,r.VILLE, NOMC, c.VILLE AS VILLEC, NOMP, COUL, PRIX, QT FROM VENTES v INNER JOIN REPRESENTANTS r ON r.NR = v.NR INNER JOIN CLIENTS c ON c.NC = v.NC INNER JOIN PRODUITS p ON p.NP = v.NP WHERE c.VILLE LIKE :val OR NOMC LIKE :val OR r.VILLE LIKE :val OR NOMR LIKE :val OR COUL LIKE :val OR NOMP LIKE :val";

    $statement = $db->prepare($requete);

    $statement->bindValue(':val', $val, PDO::PARAM_STR);

    $statement->execute();
    if ($statement){
        $tab = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $tab;
    }
}


function afficheLien($tab){
    ?>

    <table class="table" id="id_table">
    <thead>
        <tr>
        <th onclick="trier(this)">#</th>
        <th onclick="trier(this)">Client</th>
        <th onclick="trier(this)">Vendeur</th>
        <th onclick="trier(this)">Produit</th>
        <th onclick="trier(this)">Quantité</th>
        </tr>
    </thead>
    <tbody>

    <?php
    $i = 1;
    foreach($tab as $vals){
        echo '<tr onclick="'."window.location='"."index.php?page=".$vals["NR"].$vals["NC"].$vals["NP"]."'".'">'."\n".'<td>'."$i"."</td>\n";
        echo '<td>'.htmlspecialchars($vals["NOMR"]).' de '.htmlspecialchars($vals["VILLE"])."</td>\n<td>".htmlspecialchars($vals["NOMC"]).' de '.htmlspecialchars($vals["VILLEC"])."</td>\n<td>".htmlspecialchars($vals["NOMP"]).' '.htmlspecialchars($vals["COUL"]).' ('.htmlspecialchars($vals["PRIX"])."€)</td>\n<td>".htmlspecialchars($vals["QT"])."</td>\n";
        echo "</tr>\n";
        $i += 1;
    }

    ?>
    </tbody>
    </table>

    <script>
        function trier(colonne_ref){
            // lignes de la table
            var tr = document.getElementById("id_table").getElementsByTagName("tbody")[0].getElementsByTagName("tr")
            var table = []
            Array.from(tr).forEach(elem => table.push(elem.getElementsByTagName("td")))
            // recuperer l'ordre des head
            var order = []
            Array.from(document.getElementById("id_table").getElementsByTagName("thead")[0].getElementsByTagName("th")).forEach(elem => order.push(elem.innerHTML))
            // indice de tri
            var index = order.indexOf(colonne_ref.innerHTML)
            //tri de la liste
            var table_sort = []
            Array.from(tr).forEach(elem => table_sort.push(Array.from(elem.getElementsByTagName("td")).map(function(elem){return elem.innerHTML})))
            table_sort.sort((a, b) => (a[index].localeCompare(b[index])))

            for (i = 0; i < table.length; i++){
                for (j = 0; j < table[0].length; j++){
                    
                    table[i][j].innerHTML = table_sort[i][j]
                }
            }
        }
    </script>

    <?php
}

function getPage($id){

    if (strlen($id) === 3){
        $db = new PDO('sqlite:bdd/repr.sqlite');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $nr = $id[0];
        $nc = $id[1];
        $np = $id[2];
        
        analyseSQL("getPage", [$nr, $nc, $np]);

        $requete = "SELECT NOMR,r.VILLE, NOMC, c.VILLE AS VILLEC, NOMP, COUL, PRIX, QT FROM VENTES v INNER JOIN REPRESENTANTS r ON r.NR = v.NR INNER JOIN CLIENTS c ON c.NC = v.NC INNER JOIN PRODUITS p ON p.NP = v.NP WHERE v.NR=:nr AND v.NC=:nc AND v.NP=:np";
        $statement = $db->prepare($requete);

        $statement->bindValue(':nr', $nr, PDO::PARAM_INT);
        $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
        $statement->bindValue(':np', $np, PDO::PARAM_INT);
        

        $statement->execute();
        if ($statement){
            $tab = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (sizeof($tab) === 1){
                echo 'Representant : <b class="fw-bold">'.htmlspecialchars($tab[0]["NOMR"]).' de '.htmlspecialchars($tab[0]["VILLE"]).'</b><br>';
                echo 'Client : <b class="fw-bold">'.htmlspecialchars($tab[0]["NOMC"]).' de '.htmlspecialchars($tab[0]["VILLEC"]).'</b><br>';
                echo 'Achat : <b class="fw-bold">'.htmlspecialchars($tab[0]["NOMP"]).' '.htmlspecialchars($tab[0]["COUL"]).' ('.htmlspecialchars($tab[0]["PRIX"]).'€)</b><br>';
                echo 'Nombre : <b class="fw-bold">'.htmlspecialchars($tab[0]["QT"]).'</b><br>';
            }
        }
    }
}


function genFooter(){
    ?>

    <footer class="bg-dark container-fluid">
        <div class="justify-content-between row">
            <div class="col-4">
                <p class="text-light">© Brewal Guyon et Morgan Mootoosamy</p>
            </div>
            <div class="col-4">
                <p class="text-light text-end"><a href="#" onclick="history.back()">Revenir en arrière</a></p>
            </div>
        </div>
    </footer>

    <?php
}


function getProfilPic($username){
    $db = new PDO('sqlite:bdd/comptes.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $requete = "SELECT photo FROM comptes WHERE login=:nom";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nom', $username, PDO::PARAM_STR);

    $statement->execute();
    if ($statement){
        $tab = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo $tab[0]["photo"];
        return;
    }
}

function getDispoPictures($statut){
    $vals = array_diff(scandir("images/"), [".", ".."]);
    if ($statut !== "administrateur"){
        $retour = [];
        foreach($vals as $val){
            if (strpos($val, "admin") === false){
                array_push($retour, $val);
            }
        }
    }
    else{
        $retour = $vals;
    }
    return $retour;
}

function formChagePDP($statut){
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
        <label for="id_photo">Changer de photo de profil : </label>
        <select id="id_photo" name="photo" size="1" class="form-control">
        <?php
            $data = getDispoPictures($statut);
            foreach ($data as $val){
                echo "<option value=".htmlspecialchars($val).'>'.htmlspecialchars($val).'</option>';
            }

        ?>
        </select>
        <div class="text-center">
            <input type="submit" class="btn btn-primary btn-customized justify-content-center" value="Changer la photo de profil">
        </div>
        </fieldset>
    </form>

    <?php
}

function changePDP($photo, $username, $statut){
    if (preg_match('/[a-zA-Z0-9]\.png/', $photo) && in_array($photo, getDispoPictures($statut), true)){

        $photo = "images/".$photo;

        $db = new PDO('sqlite:bdd/comptes.sqlite');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $requete = "UPDATE comptes SET photo=:photo WHERE login = :nom";
        $statement = $db->prepare($requete);

        $statement->bindValue(':nom', $username, PDO::PARAM_STR);
        $statement->bindValue(':photo', $photo, PDO::PARAM_STR);
        
        try{
            $statement->execute();
        }
        catch (Exception $e){
            return "Une erreur est survenu, veuillez rééssayer !";
        }
        
    }
    else{
        return "Une erreur est survenu, aucune photo de profil reconnu !";
    }
}

function formPasswd(){
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
            <label for="id_password">Changer de mot de passe : </label>
            <input name="mdp" id="id_password" size="20" class="form-control" type="password" placeholder="aucune modification">
        </fieldset>
        <div class="text-center">
            <input type="submit" class="btn btn-primary btn-customized justify-content-center" value="Changer le mot de passe">
        </div>
    </form>
    <?php
}

function changePasswd($new_mdp, $username){
    $db = new PDO('sqlite:bdd/comptes.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $requete = "UPDATE comptes SET motdepasse=:mdp WHERE login = :nom";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nom', $username, PDO::PARAM_STR);
    $statement->bindValue(':mdp', $new_mdp, PDO::PARAM_STR);
    
    try{
        $statement->execute();
    }
    catch (Exception $e){
        return "Une erreur est survenu, veuillez rééssayer !";
    }
}


function analyseSQL($nom_methode, $parametres){
    $res = $_SERVER['REMOTE_ADDR']." -> ".$nom_methode."(";
    foreach($parametres as $param){
        $res = $res."[$param]";
    }
    $res = $res.")\n";
    // recherche de possibles tentatives de XSS et SQLI
    
    $suspect = "";
    foreach($parametres as $param){
        if (strpos($param, "'") !== false){
            $suspect = $suspect."Parametre suspect : $param, ' trouvé\n";
        }
        if (strpos($param, '"') !== false){
            $suspect = $suspect."Parametre suspect : $param, ".'" trouvé'."\n";
        }
        if (strpos($param, "<") !== false){
            $suspect = $suspect."Parametre suspect : $param, < trouvé\n";
        }
        if (strpos($param, ">") !== false){
            $suspect = $suspect."Parametre suspect : $param, > trouvé\n";
        }
    }
    if ($suspect !== ""){
        $filename = "suspect.log";
    }
    else{
        $filename = "normal.log";
    }

    $file = fopen("logs/$filename", "a");
    fwrite($file, $res.$suspect);
    fclose($file);
    
}


?>



