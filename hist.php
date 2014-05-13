<?php
require_once('config.php');
require_once('bdd.php');

if(!empty($_POST['k']) && hash('sha256', $_POST['k']) == $api_key)
{
    $bdd = new BDD();

    $req = $bdd->query('SELECT *, DATE_FORMAT(date, "%d-%m-%Y %h:%i:%s") AS datef FROM puush ORDER BY date DESC LIMIT 10');
    ob_clean();
    flush();
    echo "0\r\n";
    while($donnees = $req->fetch())
    {
        echo $donnees['id'] . ',' . $donnees['datef'] . ',' . BASE_URL . '/'.$donnees['name'].'!'.$donnees['key'].','.$donnees['date'].','.$donnees['view'].",0\r\n";
    }
}
?>
