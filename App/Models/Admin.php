<?php  

namespace App\Models;

use PDO;
use Core\Model;

class Admin extends Model {

	public static function getAdmin($email) {
		try {

			$db = static::getDB();
			$stmt = $db->query("SELECT * FROM admins WHERE email = '$email'");
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			return $result;
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	// add new user
	public static function add($data) {
		try {

			$db = static::getDB();

			$stmt = $db->prepare("INSERT INTO admins (first_name, last_name, email, password_hash) VALUES (:first_name, :last_name, :email,  :password_hash)");

			$stmt->bindParam(':first_name', $data["first_name"]);
			$stmt->bindParam(':last_name', $data["last_name"]);
			$stmt->bindParam(':email', $data["email"]);
			$stmt->bindParam(':password_hash', $data["password_hash"]);

			$stmt->execute();
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	public static function edit($data) {
		$db = static::getDB();

		$id = $data["id"];
		$first_name = $data["first_name"];
		$last_name = $data["last_name"];
		$email = $data["email"];

		try {
			$stmt = $db->prepare("UPDATE admins SET first_name = '$first_name', last_name = '$last_name', email = '$email' WHERE id='$id'");

			$stmt->execute();
		} catch(PDOException $e) {
			throw new \Error("error");
		}
	}

	// log user in
	public static function login($email, $password) {
		$db = static::getDB();

		$password_ok = false;

		$stmt = $db->prepare("SELECT * FROM admins WHERE email = '$email'");
		$stmt->execute();
		
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// if a user exists, check for password
		if($row) {
			// true / false
			$password_ok = password_verify($password, $row[0]["password_hash"]);
		}

		if($password_ok) {
			return true;
		} else {
			return false;
		}

	}
}