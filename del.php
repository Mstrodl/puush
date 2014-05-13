<?php
require_once('config.php');
require_once('func.php');
require_once('bdd.php');

if(!empty($_POST['k']) && hash('sha256', $_POST['k']) == $api_key && !empty($_POST['i']))
{
    $donnees = null;

    $bdd = new BDD();
    $req = $bdd->prepare('SELECT * FROM puush WHERE id=?');
    $req->execute(array($_POST['i']));
    
    $donnees = $req->fetch();
    $req->closeCursor();

    if(!empty($donnees)) {
        $req = $bdd->prepare('DELETE FROM puush WHERE id=?');
        $req->execute(array($_POST['i']));

        unlink(UPLOAD_DIR . $donnees['name'] . '.' . $donnees['ext']);
        unlink(THUMBS_DIR . $donnees['name'] . '.' . $donnees['ext']);
    }

}