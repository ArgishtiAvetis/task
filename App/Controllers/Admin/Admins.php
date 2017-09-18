<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\View;
use App\Models\Admin;
use App\Models\User;

class Admins extends Controller {

	// before any "non-existent" (with Action postfix) method gets called
	public function before() {

		// if not logged in, redirect to login page
		if (!array_key_exists('admin_email', $_SESSION)) {
			header("Location: /admins/login");
		} 
	}

	// after any "non-existent" (with Action postfix) method gets called
	public function after() {
		return true;
	}

	// the main protected route "Admins/dashboard"
	public function dashboardAction() {

		// if / else POST request
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$users = User::getAll();
			// if Admin logged in, pass his/her data to the view
			if (array_key_exists('admin_email', $_SESSION)) {
				$admin = $_SESSION['admin_email'];
			} else {
				$admin = '';
			}

			View::renderTemplate('Admins/dashboard.html', [
				'title' => 'User Management Plus',
				'users' => $users,
				'admin' => $admin
			]);
		} else {

			// POST request
			// sanitize data 
			$first_name = filter_var($_POST['first'], FILTER_SANITIZE_STRING);
			$last_name = filter_var($_POST['last'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email_address'], FILTER_SANITIZE_EMAIL);
			$password = trim($_POST['password']);

			// hash the password
			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			$errors = [];

			if (strlen($first_name) === 0) {
				$errors[] = "First Name is required";
			}

			if (strlen($last_name) === 0) {
				$errors[] = "Last Name is required";
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

			// if / else any errors
			if(count($errors) > 0) {

				// render with ERRORS
				$users = User::getAll();
				View::renderTemplate('Admins/dashboard.html', [
					'title' => 'User Management Plus',
					'admin' => $_SESSION["admin_email"],
					'users' => $users,
					'errors' => $errors
				]);				
			} else {

				// clean, continue
				$data = [
					"first_name" => $first_name,
					"last_name" => $last_name,
					"email" => $email,
					"password_hash" => $password_hash
				];
				
				// add the User
				User::add($data);
				
				header("Location: /admins/dashboard");
				
				// // get the new data
				// $users = User::getAll();
				
				// // render
				// View::renderTemplate('admins/dashboard.html', [
				// 	'title' => 'User Management Plus',
				// 	'admin' => $_SESSION["admin_email"],
				// 	'users' => $users
				// ]);
			}
		}
	}

	public function editAction() {
		
		if($_SERVER['REQUEST_METHOD'] !== "POST") {
			header("Location: /admins/dashboard");
		}

		// POST request
		// sanitize data 
		$id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
		$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
		$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
		$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
		$status = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);

		//$password = trim($_POST['password']);

		$errors = [];
		
		// if(!empty($password)) {
		// 	// hash the password
		// 	$password_hash = password_hash($password, PASSWORD_DEFAULT);

		// 	if ($password !== trim($_POST['password_r'])) {
		// 		$errors[] = "Passwords don't match";
		// 	}

		// }


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

			// render with ERRORS
			$users = User::getAll();
			View::renderTemplate('Admins/dashboard.html', [
				'title' => 'User Management Plus',
				'admin' => $_SESSION["admin_email"],
				'users' => $users,
				'errors' => $errors
			]);				
		} else {

			// clean, continue
			$data = [
				"id" => $id,
				"first_name" => $first_name,
				"last_name" => $last_name,
				"email" => $email,
				"status" => $status,
				"password" => ""
			];

			// if(!empty($password)) {
			// 	$data["password_hash"] = $password_hash;
			// }

			// edit the User
			User::edit($data);
			
			header("Location: /admins/dashboard");
		}

	}

	// delete User
	public function delete() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {

			// delete User by passing the ID and whether or not
			// it's a PERMANENT or TEMPORARY (soft) delete
			User::delete($_POST['id'], $_POST['soft']);

			$users = User::getAll();

			header("Location: /admins/dashboard");
		}

	}

	public function accountAction() {

		if($_SERVER['REQUEST_METHOD'] !== "POST") {
			$admin = Admin::getAdmin($_SESSION['admin_email']);

			View::renderTemplate('Admins/account.html', [
				'admin' => $admin
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

			if(count($errors)) {
				$admin = Admin::getAdmin($_SESSION['admin_email']);

				View::renderTemplate('Admins/account.html', [
					'admin' => $admin,
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

				Admin::edit($data);

				header("Location: /admins/account");
			}

		}
	}

	public function login() {

		// if GET => show view, else handle POST request
		if($_SERVER["REQUEST_METHOD"] !== "POST") {
			View::renderTemplate('Admins/login.html');
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
				View::renderTemplate('Admins/login.html', [
					'errors' => $errors
				]);	
			} else {
				// pass email and password to the Model
				if(Admin::login($email, $password)) {
					$_SESSION["admin_email"] = $email;
					header("Location: /admins/dashboard");
				} else {
					$errors[] = "Admin doesn't exist";
					
					View::renderTemplate('Admins/login.html', [
						'errors' => $errors
					]);					
				}
			}

		}
	}

	public function signup() {

		// if / else POST request 
		if($_SERVER["REQUEST_METHOD"] !== "POST") {
			View::renderTemplate('Admins/signup.html');
		} else {

			// sanitize inputs
			$first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
			$last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

			$errors = [];

			if (strlen($first_name) === 0) {
				$errors[] = "First Name is required.";
			}

			if (strlen($last_name) === 0) {
				$errors[] = "Last Name is required.";
			}

			// if errors, render view woth errors
			if(count($errors) > 0) {

				View::renderTemplate('Admins/signup.html', [
					'title' => 'Admin Management Plus',
					'admin' => $_SESSION["admin_email"],
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

				// create an admin account
				Admin::add($data);
				
				// put the email into the session
				$_SESSION["admin_email"] = $data["email"]; 

				// get the Admins
				$users = User::getAll();
				
				// render view
				View::renderTemplate('Admins/dashboard.html', [
					'title' => 'Users Management Plus',
					'admin' => $_SESSION["admin_email"],
					'users' => $users
				]);
			}

		} // if POST
	}

	public function logout() {
		
		// unset the session variable	
		unset($_SESSION["admin_email"]);

		// redirect Admin to login page
		header("Location: /admins/login");

	}

}