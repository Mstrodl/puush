<?php

// Creating a class extending PDO class

class BDD extends PDO {

	// Defining some constants for the connection

	const HOST = "";
	const DATABASE = "";
	const USER = "";
	const PASS = "";

	// The constructor, called when the class is created

	function __construct() {
		// First, we're constructing the classic PDO class using our constrants
		parent::__construct('mysql:host=' . self::HOST . ';dbname=' . self::DATABASE, self::USER, self::PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

		// Then we try to set some attributes to get errors more easily
		try {
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		} catch (PDOException $e) {
			// If it fails, we get the error message
            die($e->getMessage());
        }
	}

}

// And there we're done with our custom PDO class

$bdd = new BDD(); // We can call it as simply as that