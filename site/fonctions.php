<?php

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
    echo '<li>deconnexion</li>';
    echo '</ul>';

}


?>