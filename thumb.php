<?php
require_once('config.php');
require_once('func.php');
require_once('bdd.php');
require_once('encrypt.php');

if(!empty($_GET))
	$_POST = $_GET;

if(!empty($_POST['k']) && hash('sha256', $_POST['k']) == $api_key && !empty($_POST['i']))
{
    $bdd = new BDD();
    $req = $bdd->prepare('SELECT * FROM puush WHERE id=?');
    $req->execute(array($_POST['i']));
    
    $donnees = $req->fetch();

    // Look for the image
    $matched = glob (THUMBS_DIR.$donnees['name']. '.*');

    // Did we find an image?
    if (empty($matched))
    {
        exit ('ERR No image found.');
    }

    // The matched image location (relative.)
    $matched = $matched[0];
    
    $ext = strtolower(get_ext($matched));

    // Look for an appropriate mime type
    $mime = array_search($ext, $mime);
    
    // Did we find one?
    if ($mime !== FALSE)
    {
        // Set our headers
        header('Content-type: ' . $mime);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');

        // Prepare to send the image
        ob_clean();
        flush();

        // Send the image
        $content = file_get_contents($matched);
        $encrypt = new CRYPT($content, $donnees['key'], 'AES', 256);
        echo base64_decode($encrypt->decrypt());
    }
}
