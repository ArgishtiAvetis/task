<?php

namespace Core;

abstract class Controller {

	protected $route_params = [];

	/* __call magic function gets fired every time 
	non accessible/existent method gets called */
	public function __call($name, $args) {
		
		$method = $name . "Action";

		if (method_exists($this, $method)) {
			if ($this->before() !== false) {
				call_user_func_array([$this, $method], $args);
				$this->after();
			}
		} else {
			echo "Method $method not found in class: " . get_class($this);
		}
	}

	public function before() {}
	public function after() {}

	public function __construct($route_params) {
		$this->route_params = $route_params;
	}
}