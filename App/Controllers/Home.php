<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class Home extends Controller {

	// Homepage
	public function indexAction() {

		$options["title"] = "User Management Plus";

		// is a USER or an ADMIN logged in? (changes navbar)
		if(array_key_exists('user_email', $_SESSION)) {
			$options["user"] = $_SESSION["user_email"];
		}

		if(array_key_exists('admin_email', $_SESSION)) {
			$options["admin"] = $_SESSION["admin_email"];
		}		

		View::renderTemplate('Home/index.html', $options);
	}

	// how it works / about page (should be add)
	public function aboutAction() {
		echo "About";
	}
	
}