<?php
error_reporting(0);

define('puush', '');
require_once 'config.php';
require_once 'func.php';
require_once 'bdd.php';
require_once 'encrypt.php';

$bdd = new BDD();

// ?
$k = get_post_var('k');

// ?
$c = get_post_var('c');

if (hash('sha256', $k) != $api_key)
{
    exit ('Account unauthorized.');
}

// Check for the file
if (!isset($_FILES['f']))
{
    exit ('ERR No file provided.');
}

// The file they are uploading
$file = $_FILES['f'];

// Check the size, max 250 MB
if ($file['size'] > MAX_FILE_SIZE)
{
    exit ('ERR File is too big.');
}

$thumb = true;
// Ensure the image is actually a file and not a friendly virus
if (validate_image($file) === FALSE)
{
    $thumb = false;
}

// Generate a new file name
$ext = get_ext($file['name']);
$generated_name = '';
if($thumb)
	$generated_name = generate_upload_name($ext);
else
	$generated_name = $ext . '_' .  generate_upload_name($ext);

$key = generateRandomString();

// Move the file
//move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $generated_name . '.' . $ext);
$image_binary = fread(fopen($file["tmp_name"], "r"), 
                                filesize($file["tmp_name"]));
$filecontent =  base64_encode($image_binary);
$crypted = new CRYPT($filecontent, $key, 'AES', 256);
file_put_contents(UPLOAD_DIR . $generated_name . '.' . $ext, $crypted->encrypt());
if($thumb) {
	createThumb($file["tmp_name"], THUMBS_DIR . $generated_name . '.' . $ext);
  $image_binary = fread(fopen(THUMBS_DIR . $generated_name . '.' . $ext, "r"), 
                                filesize(THUMBS_DIR . $generated_name . '.' . $ext));
  $filecontent =  base64_encode($image_binary);
  $crypted = new CRYPT($filecontent, $key, 'AES', 256);
  unlink(THUMBS_DIR . $generated_name . '.' . $ext);
  file_put_contents(THUMBS_DIR . $generated_name . '.' . $ext, $crypted->encrypt());
}

$req = $bdd->prepare("INSERT INTO puush VALUES('', NOW(), ?, ?, 0, ?)");
$req->execute(array($generated_name, $ext, $key));
echo '0,' . sprintf(FORMATTED_URL, $generated_name). '!'.$key.',-1,-1';
// ahem

function createThumb($src, $dest, $width = 100, $height = 100, $deform = false, $no_img = './assets/img/trans.gif' ) {
  
  ## Make directory
  @mkdir(dirname($dest)) ;
  
  ## Get infos on file with exif
  list($width_original, $height_original, $image_mime) = @getimagesize($src);  
  
  // Init w &amp; h
  $_w = ( $width ? $width : $width_original ) ;
  $_h = ( $height ? $height : $height_original ) ;
  
  // If error 
  if ( ! $image_mime ) {
    $src = $no_img ;
    list($width_original, $height_original, $image_mime) = getimagesize($src);  
  }
  
  ## Detect Portait( => 0) / paysage ( => 1)
  if ( $width_original <= $height_original ) {
    $image_type = 0 ;
  } else {
    $image_type = 1 ;
  }
  
  ## If width or height is empty => auto
  if ( $_w == 0 && $_h > 0 ) {
    $_w = $width_original * ($_h / $height_original ) ;
  } else if ( $width > 0 && $height == 0 ) { 
    $_h = $height_original * ($_w / $width_original ) ;
  } else {
    $is_error = true ;
  }
      
  ## Define best size to thumb depending params
  if ( $image_type == 0 ) {
    $best_height  = $_h ;
    if ( $height_original ) $best_width   = (int) ( $width_original * ($_h / $height_original ) ) ;
  } else {
    $best_width   = $_w ;
    if ( $width_original ) $best_height   = (int) ( $height_original * ( $_w / $width_original ) ) ;    
  }
  
  ## If picture is little than wanted => don't stretch it, juste create a standard thumbnail
  if ( $width_original < $_w && $height_original < $_h ) {
    $best_width = $width_original ;
    $best_height = $height_original ;   
  } 
  
  ## If 'iar' is setted => do desired stretching and no more
  if ( $deform == "1" ) {
    $best_width = (int) $width ;
    $best_height = (int) $height ;
  }
    
  // set image type, blending and set functions for gif, jpeg and png  
  switch($image_mime){  
    case IMAGETYPE_PNG:  $img = 'png';  $blending = false; break;  
    case IMAGETYPE_GIF:  $img = 'gif';  $blending = true;  break;  
    case IMAGETYPE_JPEG: $img = 'jpeg'; break;  
  }  
  $imagecreate = "imagecreatefrom$img";  
  $imagesave   = "image$img";  
  
  // initialize image from the file  
  $image1 = $imagecreate($src);  
  
  // create a new true color image with dimensions $width2 and $height2  
  $image2 = imagecreatetruecolor($best_width, $best_height);  
  
  // preserve transparency for PNG and GIF images  
  if ($img == 'png' || $img == 'gif'){  
    // allocate a color for thumbnail  
    $background = imagecolorallocate($image2, 0, 0, 0);  
    // define a color as transparent  
    imagecolortransparent($image2, $background);  
    // set the blending mode for thumbnail  
    imagealphablending($image2, $blending);  
    // set the flag to save alpha channel  
    imagesavealpha($image2, true);  
  }  
  
  // save thumbnail image to the file  
  imagecopyresampled($image2, $image1, 0, 0, 0, 0, $best_width, $best_height, $width_original, $height_original);  
  $imagesave($image2, $dest, ($img == 'jpeg' ? 100 : true));    
  ImageDestroy($image2); 
}

function stripAccents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    
    return $str;
}

function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}