<?php

//if (!defined('puush')) exit('Bonjour');

// The folder where uploads are stored in
define ('BASE_DIR', '/var/www/i/');
define ('UPLOAD_DIR', BASE_DIR . 'uploads/');
define ('THUMBS_DIR', BASE_DIR . 'thumbs/');

define ('BASE_URL', 'http://i.mywebsite.com');

// The formatted url to send to the client, where %s is the generated file name
define ('FORMATTED_URL', BASE_URL . '/%s');

// The max file size, default 250 MB ( 250 * 1024 * 1024 )
define ('MAX_FILE_SIZE', 250 * 1024 * 1024);

$api_key = ''; // sha256 of your api key

// Mime types (authorized)
$mime = array('image/gif' => 'gif',
    'image/jpeg' => 'jpeg',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/psd' => 'psd',
    'image/bmp' => 'bmp',
    'image/tiff' => 'tiff',
    'image/tiff' => 'tiff',
    'image/jp2' => 'jp2',
    'image/iff' => 'iff',
    'image/vnd.wap.wbmp' => 'bmp',
    'image/xbm' => 'xbm',
    'image/vnd.microsoft.icon' => 'ico',
    'text/plain' => 'txt',
    'audio/mpeg' => 'mp3');

// Extension whitelist
$image_whitelist = array('jpg', 'jpeg', 'png', 'gif','bmp');
