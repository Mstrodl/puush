<?php
require_once 'config.php';
require_once 'func.php';
require_once 'bdd.php';
require_once 'encrypt.php';

$bdd = new BDD();

$req = $bdd->query("SELECT * FROM puush");

while($donnees = $req->fetch()) {
	$filename = UPLOAD_DIR . $donnees['name'] . '.' . $donnees['ext'];
	if(file_exists($filename)) {
		if(empty($donnees['key'])) {
			$key = generateRandomString();
			$content = base64_encode(file_get_contents($filename));
			$encrypt = new CRYPT($content, $key, 'AES', 256);

			unlink($filename);

			file_put_contents($filename, $encrypt->encrypt());

			$filename = THUMBS_DIR . $donnees['name'] . '.' . $donnees['ext'];

			if(file_exists($filename)) {
				$content = base64_encode(file_get_contents($filename));
				$encrypt = new CRYPT($content, $key, 'AES', 256);
				unlink($filename);
				file_put_contents($filename, $encrypt->encrypt());
			}

			$req2 = $bdd->prepare('UPDATE puush SET `key`=? WHERE id=?');
			$req2->execute(array($key, $donnees['id']));
		}
	} else {
		$req2 = $bdd->prepare('DELETE FROM puush WHERE id=?');
		$req2->execute(array($donnees['id']));
		$filename = THUMBS_DIR . $donnees['name'] . '.' . $donnees['ext'];
		if(file_exists($filename)) {
			unlink($filename);
		}
	}
}

function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}