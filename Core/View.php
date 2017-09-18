<?php

namespace Core;

class View {
	public static function renderTemplate($template, $args = []) {

		static $twig = null;

		if ($twig === null) {
			$loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
			$twig = new \Twig_Environment($loader); 
		}

		echo $twig->render($template, $args);

	}
}