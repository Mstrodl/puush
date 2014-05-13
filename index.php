<?php

require_once('config.php');
require_once('bdd.php');
require_once('encrypt.php');
require_once('func.php');

if(empty($_GET['k']) || hash('sha256', $_GET['k']) != $api_key)
{
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Puush</title>
    <style type="text/css">
    .blank{
      width: 700px;
      min-height: 20px;
      background-color: white;
      border-radius: 5px;
      margin: auto;
      margin-top: 10%;
      border: solid 1px #DADADA;
      font-family: "Calibri";
      text-align: center;

    }
    </style>
    <meta name="author" content="A. Janvier" />
    <!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body style="background-color:#2D2D2D">
  <div class="blank">
    <div style="padding: 20px;">
<?php if(!empty($_SERVER['HTTP_USER_AGENT']) && strlen(strstr($_SERVER['HTTP_USER_AGENT'],"Firefox")) > 0 ) {  ?>
        <object width="640" height="360"><param name="movie" value="//www.youtube-nocookie.com/v/Ygnez_odlNg?hl=fr_FR&amp;version=3&amp;rel=0&amp;autoplay=1&amp;start=3&amp;fs=0&amp;autohide=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="//www.youtube-nocookie.com/v/Ygnez_odlNg?hl=fr_FR&amp;version=3&amp;rel=0&amp;autoplay=1&amp;start=3&amp;fs=0&amp;autohide=1" type="application/x-shockwave-flash" width="640" height="360" allowscriptaccess="always" allowfullscreen="true"></embed></object>
<?php } else { ?>
        <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/Ygnez_odlNg?rel=0&amp;autoplay=1&amp;start=3&amp;fs=0&amp;autohide=1" frameborder="0" allowfullscreen></iframe>
<?php } ?>
        <br />- yourwebsite.com -
    </div>
  </div>
</body>
</html>
<?php
exit();
}

function createGallery( $pathToImages, $pathToThumbs, $imgwhitelist, $mime ) 
{
  //echo "Creating gallery.html <br />";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Puush</title>
</head>
<body style="background-color:#2D2D2D">
    <table>
        <tr>
<?php

  $bdd = new BDD();
  $req = $bdd->query('SELECT * FROM puush ORDER BY date DESC');

  $counter = 0;
  while($donnees = $req->fetch()) {
    if(in_array($donnees['ext'], $imgwhitelist)) {
      $thumb = file_get_contents(THUMBS_DIR . $donnees['name'] . '.' . $donnees['ext']);
      $encrypt = new CRYPT($thumb,$donnees['key'],'AES',256);
      $base64 = $encrypt->decrypt();
      echo "<td style=\"padding-left:15px;\"><a href=\"" . BASE_URL . "/{$donnees['name']}!{$donnees['key']}\">";
      echo "<img src=\"data:".array_search($donnees['ext'], $mime).";base64,".$base64."\" style=\"border: 1px black solid;\" />";
      echo "</a></td>";
      $counter += 1;
      if ( $counter % 10 == 0 ) { echo "</tr><tr>"; }
    }
  }

?>
        </tr>
    </table>
</body>
</html>
<?php
}

createGallery(UPLOAD_DIR,THUMBS_DIR,$image_whitelist,$mime);
?>
