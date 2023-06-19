<?php

// visualisation des erreurs
// ini_set ('display_errors', 1);
// ini_set ('display_startup_errors', 1);
// error_reporting (E_ALL);

// autorisation des requetes pour le captcha
ini_set("allow_url_fopen", 1);

// création des sessions, utilisé uniquement dans cette page, comme elle ait incul dans toutes les autres
session_start();


function genNavBar($statut, $nom){
    /*
    Fonctionn qui permet de générer la NavBar; elle prend en parametre :
    $statut => si "administrateur", alors accès à insertion, modification et suppression
    sinon seulement accès au profil, la déconexiion et la pahe index

    $nom => le nom à afficher en dessous de la photo de profil
    */

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


function logout($ses,$stat){

    /*
    Deconnexion du compte 

    En paramètre :
    $ses : str : le login de l'utilisateur
    $stat :  str : le statut de l'utilisateur (admin/user)

    Retourne:
    Rien
    */

    analyseSQL("decnxComptes",[$ses,$stat]); /*Fonction log */

    /* Destruction de la session */
    session_destroy(); 
    $_SESSION = array();
}


function validate_tab($login, $pass){

    /*
    Validation du login et mdp en retournant un tableau

    En paramètre :
    $login : str : login de l'utilisateur
    $pass : str : le mot de passe de l'utilisateur

    Retourne:
    $valide_tab : array : tableau login, mdp et statut
    Cela retourne un tableau vide si le compte n'existe pas

    */

    

    $login = addslashes($login);
    $pass = addslashes($pass);

    $valide = false;
    $valide_tab = array();
    

    $dbc = new PDO('sqlite:bdd/comptes.sqlite');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $requete = "SELECT * FROM comptes;";


    $res = $dbc->query($requete);
    $comptes = $res->fetchAll(PDO::FETCH_ASSOC);

    if (!(empty($login) && empty($pass))){
        foreach($comptes as $compte){
            if ($compte["login"] === $login && $compte["motdepasse"] === $pass) {

                /* Selection du compte qui à le même login et mot de passe */

                $requete_status = "SELECT * FROM comptes WHERE login = '$login' AND motdepasse = '$pass'";
                $res = $dbc->query($requete_status);
                $tab_login = $res->fetchAll(PDO::FETCH_ASSOC);

                if ($tab_login[0]["statut"] === "administrateur"){

                    array_push($valide_tab, $login, $tab_login[0]["statut"]);
                    analyseSQL("cnxComptes",[$login,"administrateur"]); /* Fonction log*/
                    $valide = true;
                    
                }
                else{
                    array_push($valide_tab, $login, $tab_login[0]["statut"]);
                    analyseSQL("cnxComptes",[$login,"utilisateur"]);  /* Fonction log*/
                    $valide = true;

                }
            }

        }
        if($valide === false){
            analyseSQL("cnxEchoueCompte",[$login]); /* Fonction log en cas d'echec*/
        }
        
    }
    else{
            analyseSQL("cnxEchoueCompte",[$login]); /* Fonction log en cas d'echec*/
        }
        
    

    return $valide_tab;
}



function valide_cnx($login, $pass){

    /*
    Verification du login et mdp

    En paramètre :
    $login : str : login de l'utilisateur
    $pass : str : le mot de passe de l'utilisateur


    Retourne:
    $valide : booleen : retourne vrai si le compte existe, retourne faux dans le cas contraire






    */


    $login = addslashes($login);
    $pass = addslashes($pass);


    $valide = false;
    

    $dbc = new PDO('sqlite:bdd/comptes.sqlite');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    /* Selection de tous les comptes */

    $requete = "SELECT * FROM comptes;";


    $res = $dbc->query($requete);
    $comptes = $res->fetchAll(PDO::FETCH_ASSOC);

    if (!(empty($login) && empty($pass))){
        foreach($comptes as $compte){
            if ($compte["login"] === $login && $compte["motdepasse"] === $pass) {
            $valide = true;
            }
            
        }
    }

return $valide;
} 
    
function verif_mdp($pass){


        /*
    Verification du mot de passe

    En paramètre :
    $pass : str : le mot de passe de l'utilisateur

    Retourne:
    $verif : booleen : Retourne vrai si les contraintes sont respectées sinon faux

    */



    $verif = false;

    if (!(empty($pass))){

        if (strlen($pass) < 8 || strlen($pass) > 16) { /* La taille du mot de passe entre 8 et 16 caractères */
            $verif = false;
            echo "<p>Le mot de passe doit être entre 8 à 16 caractères<p>";

        }
        elseif (!preg_match("/[A-Z]/", $pass)) { /* Le mot de passe doit contenir au moins une majuscule  */
            $verif = false;
            echo "<p>Le mot de passe doit contenir au moins une majuscule<p>";

        }
        elseif (!preg_match("/[a-z]/", $pass)) { /* Le mot de passe doit contenir au moins une minuscule  */
            $verif = false;
            echo "<p>Le mot de passe doit contenir au moins une minuscule<p>";

        }
        elseif (!preg_match("/[0-9]]/", $pass)) { /* Le mot de passe doit contenir au moins un chiffre  */
            $verif = false;
            echo "<p>Le mot de passe doit contenir au moins un chiffre<p>";

        }   
        elseif (!preg_match("/\W/", $pass)) { /* Le mot de passe doit contenir au moins un caractère spécial */
            $verif = false;
            echo "<p>Le mot de passe doit contenir au moins un caractère spécial<p>";
 
        }
        elseif (preg_match("/\s/", $pass)) { /* Le mot de passe ne doit pas avoir des espaces vides */
            $verif = false;
            echo "<p>Le mot de passe doit contenir aucun espace<p>";

        }

        else{
            $verif = true;
        } 

    }

    return $verif;
    

}





function insert_compte($login, $pass){



            /*
    Insertion du compte

    En paramètre :
    $login : str : login de l'utilisateur créé
    $pass : str : le mot de passe de l'utilisateur créé

    Retourne:
    Rien

    */



    analyseSQL("ajouterComptes",[$login]); /* Fonction log */


    $dbc = new PDO('sqlite:bdd/comptes.sqlite');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    /* Inserstion du compte dans la base de donnée */

    $requete = "INSERT INTO COMPTES VALUES (:login, :pass,'utilisateur','images/photo1.png')";
    $statement = $dbc->prepare($requete);

    $statement->bindValue(':login', $login, PDO::PARAM_STR);
    $statement->bindValue(':pass', $pass, PDO::PARAM_STR);
        
    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        analyseSQL("ajouterComptesEchec",[$login]); /* Fonction log en cas d'échec */
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
        
    }
}




function genCaptchat(){
    /*
    Fonction qui permet de générer le captcha à partir de l'api python que j'ai créé pour l'occasion
    */
    
    // récuperation des images
    $url = "http://20.216.129.46:8080/getcaptchat";
    $contents = file_get_contents($url);
    $vals = json_decode($contents, true);
    $_SESSION["captchat_id"] = $vals["id"];
    // affichage des images
    echo '<article id="captchat">';
    $i = 0;
    foreach($vals["images"] as $img){
        echo  '<img src="'.$img.'" alt="captchat'.$i.'" onclick="isClicked(this)">'."\n";
        $i = $i + 1;
    }
    // génération du formulaire
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
    // fonction qui permet de verifier le captchat avec l'api
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
    /* Fonction qui permet de rediriger au bon endroit un utilisateur
    Si l'utilisateur n'est pas connecté, redirection sur la page $pas_co
    SI l'utilisateur n'est pas adminstrateur redirection sur la page $pas_admin
    Si $pas_admin est null => pas de redirection en cas de compte utilisateur
    */
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
    /* Fonction qui permet de récuperer la table en fonction de $qui, pour l'afficher comme tableau dans insertion et suppression
    si $qui = repr => affichage de la table representants
    si $qui = prod => affichage de la table produits
    si $qui = cli => affichage de la table clients
    sinon affichage de toutes les tables avec des jointures
    */

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
    /* Fonction qui permet d'afficher un tableau avec le style bootstrap
    $tableau est un tableau qui a été généré avec SQL
    $head est une liste avec le nom de chqaue colonne
    */
    echo '<table class="table"'.">\n";
    // génération des noms de colonnes
    echo "<thead>\n<tr>\n";
    foreach ($head as $cle){
        echo '<th>'.htmlspecialchars($cle)."</th>";
    }
    echo "</tr>\n</thead>\n";
    // génération du corp du tableau
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
    /* Génération du formulaire pour l'insertion */
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
                        // récuperation dynamique des noms des vendeurs
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
                        // récuperation dynamique des noms des clients
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
                        // récuperation dynamique des noms des produits
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
    /* Ajout d'un client dans la table à partir de son nom et de sa ville */
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log de la requete sql
    analyseSQL("ajoutClient", [$nom, $ville]);

    // utilisation de statements car c'est la méthode recommandé pour bloquer les injections SQL
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
    /* Ajout d'un représentant dans la table à partir de son nom et de sa ville */
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log de la requete sql
    analyseSQL("ajoutRepr", [$nom, $ville]);

    // utilisation de statements car c'est la méthode recommandé pour bloquer les injections SQL
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
    /* Ajout d'un produit dans la table à partir de son nom, sa couleur et son prix */
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log de la requete sql
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
    /* Ajout d'une vente dans la table à partir de l'id du client, de l'id du représentant, de l'id du produit et du prix */
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log de la requete sql
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
    /* Cette fonction est similaire à get_table, à l'exception que celle-ci renvoi aussi les identifiants */

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

function formeModif(){

    /*
    Fonction former menu déroulante dynamic pour la modification

    En paramètre :
    Rien

    Retourne :
    Rien

    
    */

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
                <label for="id_venter">Représentant :</label>
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
                </br>
                    <h4>Les modifications à faire ci-dessous : </h4>
                </br>

            <article class="REPRESENTANTS">
                <label for="id_nomr">Nom représentant : </label><input name="nomr2" id="id_nomr" size="20" class="form-control"/>
                <label for="id_viller">Ville représentant : </label><input name="viller2" id="id_viller" size="20" class="form-control"/>
            </article>


            <article class="PRODUITS">
                <label for="id_nomprod">Nom produit : </label><input name="nom2" id="id_nomprod" size="20" class="form-control"/>
                <label for="id_couleurprod">Couleur : </label><input name="couleur2" id="id_couleurprod" size="20" class="form-control"/>
                <label for="id_prixprod">Prix : </label><input name="prix2" id="id_prixprod" size="20" type="number" min="0" class="form-control"/>
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
                <label for="id_qtvente2">Quantité : </label><input name="qt" id="id_qtvente" size="20" type="number" min="0" class="form-control"/>

            </article>


            <article class="CLIENTS">
                <label for="id_nomclient">Nom Client : </label><input name="nomc2" id="id_nomclient" size="20" class="form-control"/>
                <label for="id_villeclient">Ville du client : </label><input name="villec2" id="id_villeclient" size="20" class="form-control"/>

            </article>          
            <br>
            <div class="text-center">
                <input type="submit" class="btn btn-primary btn-customized justify-content-center" value="Modifier"/>
            </div>

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




function modifClient($nc,$nomc,$ville){


             /*
    Modification du client

    En paramètre :
    $nc : int: l'ID/ / clé primaire du client à modifier
    $nomc : str : le nom du client modifié
    $ville : str : la ville modifiée

    Retourne si echec :
    Un message d'erreur en cas d'échec

    Retourne:
    Rien

    */


    $db = new PDO('sqlite:bdd/repr.sqlite'); /*appel du fichier SQL*/
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("modifierClient", [$nc,$nomc,$ville]); /* Fonction log */


    /*Mise à jour du client dans la base de donnée (Table CLIENTS )*/


    $requete = "UPDATE CLIENTS SET NOMC= :nomc, VILLE= :ville WHERE NC= :nc;";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
    $statement->bindValue(':nomc', $nomc, PDO::PARAM_STR);
    $statement->bindValue(':ville', $ville, PDO::PARAM_STR);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        analyseSQL("modifierClientEchec", [$nc,$nomc,$ville]); /* Fonction log en cas d'échec*/
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
        
    }
}


function modifRepr($nr,$nomr,$ville){

                 /*
    Modification du représentant

    En paramètre :
    $nc : int: l'ID/ / clé primaire du représentant à modifier
    $nomc : str : le nom du représentant modifié
    $ville : str : la ville modifiée

    Retourne si echec :
    Un message d'erreur en cas d'échec

    Retourne:
    Rien

    */


    $db = new PDO('sqlite:bdd/repr.sqlite');/*appel du fichier SQL*/
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("modifierRepresentant", [$nr,$nomr,$ville]);/* Fonction log */

    /*Mise à jour du représentant dans la base de donnée (Table REPRESENTANTS)*/

    $requete = "UPDATE REPRESENTANTS SET NOMR= :nomr, VILLE= :ville WHERE NR= :nr";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nr', $nr, PDO::PARAM_INT);
    $statement->bindValue(':nomr', $nomr, PDO::PARAM_STR);
    $statement->bindValue(':ville', $ville, PDO::PARAM_STR);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        analyseSQL("modifierRepresentantEchec", [$nr,$nomr,$ville]);/* Fonction log en cas d'échec */
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
        
    }
}


function modifProduit($np,$nomp,$coul,$prix){

                     /*
    Modification du produit

    En paramètre :
    $np : int: l'ID/ / clé primaire du produit à modifier
    $nomp : str : le nom du produit modifié
    $coul : str : la couleur modifiée
    $prix : str : le prix modifié

    Retourne si echec :
    Un message d'erreur en cas d'échec

    Retourne:
    Rien

    */


    $db = new PDO('sqlite:bdd/repr.sqlite');/*appel du fichier SQL*/
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("modifierProduit", [$np,$nomp,$coul,$prix]);/* Fonction log */

    /*Mise à jour du produit dans la base de donnée (Table PRODUITS)*/

    $requete = "UPDATE PRODUITS SET NOMP= :nomp, COUL= :coul, PRIX = :prix WHERE NP = :np";
    $statement = $db->prepare($requete);

    $statement->bindValue(':np', $np, PDO::PARAM_INT);
    $statement->bindValue(':nomp', $nomp, PDO::PARAM_STR);
    $statement->bindValue(':coul', $coul, PDO::PARAM_STR);
    $statement->bindValue(':prix', $prix, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        analyseSQL("modifierProduitEchec", [$np,$nomp,$coul,$prix]);/* Fonction log en cas d'échec*/
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
        
    }
}

function modifVente($nr,$np,$nc,$qt){

                         /*
    Modification du produit

    En paramètre :
    $nc : int: l'ID/ / clé primaire du représentant à modifier
    $np : int: l'ID/ / clé primaire du produit à modifier
    $np : int: l'ID/ / clé primaire du client à modifier
    $qt : int: Quantité modifiée de la vente

    Retourne si echec :
    Un message d'erreur en cas d'échec

    Retourne:
    Rien

    */


    $db = new PDO('sqlite:bdd/repr.sqlite');/*appel du fichier SQL*/
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    analyseSQL("modifierVente", [$nr,$np,$nc,$qt]);/* Fonction log */

    /*Mise à jour des ventes dans la base de donnée (Table VENTES)*/

    $requete = "UPDATE VENTES SET QT = :qt WHERE  NR= :nr AND NP= :np AND NC = :nr";
    $statement = $db->prepare($requete);

    $statement->bindValue(':nr', $nr, PDO::PARAM_INT);
    $statement->bindValue(':np', $np, PDO::PARAM_INT);
    $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
    $statement->bindValue(':qt', $qt, PDO::PARAM_INT);

    try{
        $statement->execute();
    }
    catch (Exception $e){
        $statement = null;
    }
    if (!$statement){
        analyseSQL("modifierVenteEchec", [$nr,$np,$nc,$qt]);/* Fonction log en cas d'echec*/
        return "Erreur, veuillez verifier votre entrée puis rééssayer";
        
    }
}


function formSupression(){
    /* Génération du formulaire de suppression */

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
                    // formulaire dynamique pour les représentants
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
                    // formulaire dynamique pour les produits
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
                    // formulaire dynamique pour les ventes
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
                    // formulaire dynamique pour les clients
                    $data = get_table_with_id("cli");
                    foreach ($data as $val){
                        echo "<option value=".htmlspecialchars($val["NC"]).'>'.htmlspecialchars($val["NOMC"]).' '.htmlspecialchars($val["VILLE"]).'</option>';
                    }

                ?>
                </select>
            </article>
            <?php genCaptchat(); /* Génération du captcha */?>

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
    // Fonction qui permet de supprimer un client à partir de son identifiant
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log du SQL
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
    // Fonction qui permet de supprimer un représentant à partir de son identifiant
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log du SQL
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
    // Fonction qui permet de supprimer un produit à partir de son identifiant
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log du SQL
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
    // Fonction qui permet de supprimer une vente à partir de son identifiant
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log du SQL
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
    // Fonction qui permet de générer la barre de recherche

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
    // Fonction qui permet de rechercher une valeur, $val étant la bvaleur que le visiteur du site a mis dans la barre de recherche
    $db = new PDO('sqlite:bdd/repr.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // log du sql
    analyseSQL("getSearch", [$val]);

    // préparation de la valeur à rechercher
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
    /* Fonction qui permet d'afficher le tableau après une recherche, chque ligne du tableau étant un lien
    $tab => liste généré par sql */
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
    // génération des colonnes avec lien clickable
    $i = 1;
    foreach($tab as $vals){
        echo '<tr onclick="'."window.location='"."index.php?page=".$vals["NR"].','.$vals["NC"].','.$vals["NP"]."'".'">'."\n".'<td>'."$i"."</td>\n";
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
    /* Fonction qui permet d'afficher les détails d'une vente en fonction de l'id
    $id est contruit sous la forme a,b,c avec
    a => id représentant
    b => id client
    c => id produit*/

    if (substr_count($id, ',') === 2){
        $db = new PDO('sqlite:bdd/repr.sqlite');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $array_id = explode(',', $id);

        $nr = $array_id[0];
        $nc = $array_id[1];
        $np = $array_id[2];
        
        // log du sql
        analyseSQL("getPage", [$nr, $nc, $np]);

        $requete = "SELECT NOMR,r.VILLE, NOMC, c.VILLE AS VILLEC, NOMP, COUL, PRIX, QT FROM VENTES v INNER JOIN REPRESENTANTS r ON r.NR = v.NR INNER JOIN CLIENTS c ON c.NC = v.NC INNER JOIN PRODUITS p ON p.NP = v.NP WHERE v.NR=:nr AND v.NC=:nc AND v.NP=:np";
        $statement = $db->prepare($requete);

        $statement->bindValue(':nr', $nr, PDO::PARAM_INT);
        $statement->bindValue(':nc', $nc, PDO::PARAM_INT);
        $statement->bindValue(':np', $np, PDO::PARAM_INT);
        
        // affichage du tableau
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
    // génération du footer
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
    // récupere la photo de profil en fonction de l'utilisateur $username
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
    // recupere toutes les photos de profil disponibles en fonction du status
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
    // Formulaire de changement de la photo de profil, $statut étant le statut de l'utilisateur actuel
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
    // Focntion qui permet de changer la photo de profil
    // verification que la photo est valide
    if (preg_match('/[a-zA-Z0-9]\.png/', $photo) && in_array($photo, getDispoPictures($statut), true)){
        // ajout de la photo de profil

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
    // FOrmulaire de changement de mot de passe
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
    // fonction qui permet de changer le mot de passe en fonction de l'utilisateur : 
    // $new_mdp => nouveau mot de passe
    // $username => nom d'utilisateur de l'utilisateur actuel
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
    // Analyse le SQL pour le loguer, $nom_methode => fonction qui a été appelé, $parametres => parametres à analyser
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
    // enregistre dans suspect.log si suspect, sinon dans normal.log
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



