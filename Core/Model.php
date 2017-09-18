<?php

namespace Core;

use PDO;
use App\Config;

// NOTE! Config data is coming from App\Config class

abstract class Model {
	protected static function getDB() {
		
		static $db = null;

		if ($db === null) {

			try {
				$db = new PDO("mysql:host=".Config::DB_HOST.";dbname=".Config::DB_NAME.";charset=utf8", Config::DB_USERNAME, Config::DB_PASSWORD);
				// $db = new PDO("mysql:host=localhost;dbname=argiwebc_um;charset=utf8", "argiwebc_um001", "secretum001");
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

		return $db;
	}
}