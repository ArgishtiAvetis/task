<?php

namespace App\Controllers\User;

use Core\Controller;
use Core\View;
use App\Models\User;

class Users extends Controller {

	// before any "non-existent" (with Action postfix) method gets called
	public function before() {
		// if not logged in, redirect to login page
		if (!array_key_exists('user_email', $_SESSION)) {
			header("Location: /account/users/login");
		} 
	}

	// after any "non-existent" (with Action postfix) method gets called
	public function after() {
		return true;
	}

	// the main protected route "account/users/profile"
	public function profileAction() {

		if($_SERVER["REQUEST_METHOD"] !== "POST") {

			$user = User::getUser($_SESSION['user_email']); 

			View::renderTemplate('Users/profile.html', [
				'user' => $user,
			]);
		} else {

			// POST request
			// sanitize data 
			$id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
			$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
			$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
			$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

			$password = trim($_POST['password']);

			$errors = [];
			
			if(!empty($password)) {
				// hash the password
				// check if two passwords match
				if ($password !== trim($_POST['password_r'])) {
					$errors[] = "Passwords don't match";
				}

			}
 
			if (strlen($first_name) === 0) {
				$errors[] = "First Name is required";
			}

			if (strlen($last_name) === 0) {
				$errors[] = "Last Name is required";
			}

			if (strlen($email) === 0) {
				$errors[] = "Email is required";
			}

			// if / else any errors
			if(count($errors) > 0) {

				// render with ERRORS;
				View::renderTemplate('Users/profile.html', [
					'title' => 'User Management Plus',
					'admin' => $_SESSION["user_email"],
					'errors' => $errors
				]);				
			} else {

				// clean, continue
				$data = [
					"id" => $id,
					"first_name" => $first_name,
					"last_name" => $last_name,
					"email" => $email,
					"password" => $password
				];

				// if(!empty($password)) {
				// 	$data["password_hash"] = $password_hash;
				// }

				// edit the User
				User::edit($data);
				
				header("Location: /account/users/profile");
			}

			}
	}

	// delete Admin
	public function delete() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {

			// delete Admin by passing the ID and whether or not
			// it's a PERMANENT or TEMPORARY (soft) delete
			User::delete($_POST['id'], $_POST['soft']);

			$users = User::getAll();

			header("Location: /admins/dashboard");
		}

	}

	public function login() {

		// if GET => show view, else handle POST request
		if($_SERVER["REQUEST_METHOD"] !== "POST") {
			View::renderTemplate('Users/login.html');
		} else {

			$email = trim($_POST['email']);
			$password = trim($_POST['password']);

			$errors = [];

			if (strlen($email) === 0) {
				$errors[] = "Email is required";
			}

			if (strlen($password) === 0) {
				$errors[] = "Password is required";
			} 

			if(count($errors) > 0) {
				View::renderTemplate('Users/login.html', [
					'errors' => $errors
				]);	
			} else {
				// pass email and password to the Model
				if(User::login($email, $password)) {
					$_SESSION["user_email"] = $email;
					header("Location: /account/users/profile");
				} else {
					$errors[] = "Invalid email / password";
					View::renderTemplate('Users/login.html', [
						'errors' => $errors
					]);					
				}
			}	 
		}
	}

	public function signup() {

		// if / else POST request 
		if($_SERVER["REQUEST_METHOD"] !== "POST") {
			View::renderTemplate('Users/signup.html');
		} else {

			// sanitize inputs
			$first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
			$last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$password = trim($_POST['password']);
			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			$errors = [];

 			// check if account exists with the same email
			if (User::checkAccountExists($email)) {
				$errors[] = "User with an email " . $email . " exists."; 
			}

			if (strlen($first_name) === 0) {
				$errors[] = "First Name is required.";
			}

			if (strlen($last_name) === 0) {
				$errors[] = "Last Name is required.";
			}

			if (strlen($email) === 0) {
				$errors[] = "Email is required";
			}

			if (strlen($password) === 0) {
				$errors[] = "Password is required";
			}

			if ($password !== trim($_POST['password_r'])) {
				$errors[] = "Passwords don't match";
			}

			// if errors, render view woth errors
			if(count($errors) > 0) {

				View::renderTemplate('Users/signup.html', [
					'title' => 'User Management Plus',
					'errors' => $errors
				]);				
			} else {

				// if OK, continue
				$data = [
					"first_name" => $first_name,
					"last_name" => $last_name,
					"email" => $email,
					"password_hash" => $password_hash
				];

				// insert data
				User::add($data);
				
				// put the email into the session
				$_SESSION["user_email"] = $data["email"]; 
				
				header("Location: /account/users/profile");
			}

		} // if POST
	}

	public function logout() {
		
		// unset the session variable	
		unset($_SESSION["user_email"]);

		// redirect Admin to login page
		header("Location: /account/users/login");

	}

}