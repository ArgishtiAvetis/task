<?php  

namespace App\Models;

use PDO;
use Core\Model;

class User extends Model {

	// get all users
	public static function getAll() {

		try {

			$db = static::getDB();
			$stmt = $db->query("SELECT * FROM users");
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return $result;
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	public static function getUser($email) {
		try {

			$db = static::getDB();
			$stmt = $db->query("SELECT * FROM users WHERE email = '$email'");
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

			$created = date("m/d/Y @ g:i A");

			$stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password_hash, created) VALUES (:first_name, :last_name, :email, :password_hash, :created)");

			$stmt->bindParam(':first_name', $data["first_name"]);
			$stmt->bindParam(':last_name', $data["last_name"]);
			$stmt->bindParam(':email', $data["email"]);
			$stmt->bindParam(':password_hash', $data["password_hash"]);
			$stmt->bindParam(':created', $created);

			$stmt->execute();
			
		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	// delete a user
	public static function delete($id, $soft) {
		try {
			
			$id = htmlspecialchars(trim($_POST['id']));
			
			$db = static::getDB(); 

			if (!$soft) {
				$stmt = $db->prepare("DELETE FROM users WHERE id = '$id'");
				$stmt->execute();
			} else {
				$stmt = $db->prepare("UPDATE users SET status = 'deactivated' WHERE id='$id'");
				$stmt->execute();
			}

		} catch(PDOException $e) {
			new \Error($e->getMessage());
		}
	}

	public static function edit($data) {
		$db = static::getDB();

		$id = $data["id"];
		$first_name = $data["first_name"];
		$last_name = $data["last_name"];
		$email = $data["email"];
		//$status = $data["status"];
		$updated = date("m/d/Y @ g:i A");

		$password = $data["password"];
		$status = $data["status"];

		if(strlen($password) !== 0) {
			$password_str = " password_hash = '" . password_hash($password, PASSWORD_DEFAULT) . "', ";  
		} else {
			$password_str = " ";
		}

		if(strlen($status) !== 0) {
			$status_str = ", status = '".$status."' ";
		} else {
			$status_str = "";
		}

		try {
			$stmt = $db->prepare("UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email',".$password_str." updated = '$updated' ".$status_str." WHERE id='$id'");

			$stmt->execute();
		} catch(PDOException $e) {
			throw new \Error("error");
		}
	}

	// log user in
	public static function login($email, $password) {
		$db = static::getDB();

		$password_ok = false;

		$stmt = $db->prepare("SELECT * FROM users WHERE email = '$email'");
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

	// given an email, checks if an account exists 
	public static function checkAccountExists($email) {
		$db = static::getDB();

		$stmt = $db->prepare("SELECT * FROM users WHERE email = '$email'");
		$stmt->execute();
		
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// user exists? 
		if($row) {
			return true;
		} else {
			return false;
		}
	}

}