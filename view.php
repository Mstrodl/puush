<?php

define('puush', '');
require_once 'config.php';
require_once 'func.php';
require_once 'bdd.php';
require_once 'encrypt.php';

$bdd = new BDD();

if (!isset($_GET['image']))
{
    exit ('ERR No image provided.');
}

// The image to find
$image = basename(urldecode($_GET['image']));

// Look for the image
$matched = glob (UPLOAD_DIR . $image . '.*');

// Did we find an image?
if (empty($matched))
{
    exit ('ERR No file found.');
}

// The matched image location (relative.)
$matched = $matched[0];

// Get the extension
$ext = strtolower(get_ext($matched));

// Look for an appropriate mime type
$mime = array_search($ext, $mime);

$key = null;

$req = $bdd->prepare('SELECT * FROM puush WHERE name=?');
$req->execute(array($_GET['image']));
$donnees = $req->fetch();
$key = $donnees['key'];
$req->closeCursor();

if(empty($_GET['k']) || $_GET['k'] != $key) {
    exit('Wrong key.');
}

// Did we find one?
if ($mime !== FALSE)
{
    $req = $bdd->prepare('UPDATE puush SET view=view+1 WHERE name=?');
    $req->execute(array($_GET['image']));
    // Set our headers
    header('Content-type: ' . $mime);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');

    // Prepare to send the image
    ob_clean();
    flush();

    // Send the image
    $content = file_get_contents($matched);
    $encrypt = new CRYPT($content, $_GET['k'], 'AES', 256);
    echo base64_decode($encrypt->decrypt());
}
else
{
    $req = $bdd->prepare('UPDATE puush SET view=view+1 WHERE name=?');
    $req->execute(array($_GET['image']));
    // Set our headers
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $image . '.' . $ext . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');

    // Prepare to send the image
    ob_clean();
    flush();

    // Send the image
    $content = file_get_contents($matched);
    $encrypt = new CRYPT($content, $_GET['k'], 'AES', 256);
    echo base64_decode($encrypt->decrypt());
}
