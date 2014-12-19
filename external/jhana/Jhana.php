<?php

	/** 
	 * This is the Jhana helper.
	 * It provides basic functionality for translation, validation and so on.
	 */
	class Jhana {
		
		private static $router;
		
		/**
		 * Sets the router for this helper.
		 * @param $router: The router generated in Heart.php.
		 */
		public static function set_router($router) {
			self::$router = $router;
		}
		
		/**
		 * Requires all assets.
		 */
		public static function load_assets() {
			require_once 'Assets.php';
		}
		
		/**
		 * Iterates through a given folder recursively. 
		 * This is used for example to find all assets in all subfolders.
		 * @param $pattern: The folder path.
		 * @return Array of files.
		 */
		public static function recursive_glob($pattern, $flags = 0) {
	    	$files = glob($pattern, $flags);
	     	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
				$files = array_merge($files, self::recursive_glob($dir.'/'.basename($pattern), $flags));
	
	  		return $files;
	  	}
		
		/**
		 * Displays the Jhana Error page with more details.
		 */
		public static function exception($name, $params=[]) {
			ob_end_clean();
			require_once 'external/jhana/Exception.php';
			exit;
		}
		
		/**
		 * Print location of error.
		 * This is used by the Jhana error page.
		 */
		public static function print_exception_location($trace) {
			$path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
			$file = str_replace($path, '', $trace['file']);
			
			echo 'In <b>'.$file.'</b> on line <b>'.$trace['line'].'</b>:<br />';
			echo str_replace(['<?php','?>'], '', file($trace['file'])[$trace['line'] - 1]);
		}
		
		
		/**
		 * Translates a given string using the language files 
		 * located in config/languages/. This function does not return anything
		 * but directly prints the result.
		 * @param $string: The key which is then used for finding the translation.
		 */
		public static function t($string) {
			if (empty($_SESSION['language']))
				$_SESSION['language'] = DEFAULT_LANGUAGE;
			
			$language = require_once 'config/languages/'.$_SESSION['language'].'.php';
			echo $language[$string];
		}
		
		/**
		 * Sets the language of the application.
		 * @param $language: The language in form of a string like "en" or "de".
		 */
		public static function set_language($language) {
			$_SESSION['language'] = $language;
		}
		
		/**
		 * Gets the language of the application.
		 * @return String: The language in form of a string like "en" or "de".
		 */
		public static function get_language() {
			return $_SESSION['language'];
		}
		
		/**
		 * Gets the notices from the Session-Array.
		 * @return $notices: The notices.
		 */
		 public static function notices() {
		 	return $_SESSION['notices'];
		 }
		 
		/**
		 * Tries to generate URL.
		 * @param $route_name: An abitrary named route from config/routes.php.
		 * @param $params: If route needs parameters.
		 * @return string: The URL.
		 */
		public static function route($route_name, $params=[]) {
			try {
				return self::$router->generate($route_name, $params);
			} catch (Exception $e){
				Jhana::exception('route_not_found', ['route' => $route_name]);
			}
		}
		
		/**
		 * Checks if a field is an URL.
		 */
		public static function validate_url($field) {
			 return preg_match('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS', $field);
		}
		
		/**
		 * Checks if a field is an email adress.
		 * @param $field: a string.
		 * @return Boolean.
		 */
		public static function validate_email($field) {
			return preg_match('/^[_a-z0-9-äÄöÖüÜ]+(\.[_a-z0-9-äÄöÖüÜ]+)*@[a-z0-9-äÄöÖüÜ]+(\.[a-z0-9-äÄöÖüÜ]+)*(\.[a-zäÄöÖüÜ]{2,3})$/i', $field);
		}
		
		/**
		 * Checks if a field has a certain length.
		 * @param $field: a string.
		 * @param $pattern: an integer or a string like "[1..10]", "[..10]" or "[1..]".
		 * @return Boolean.
		 */
		public static function validate_length($field, $pattern) {
			
			$length = strlen($field);
			
			if (is_int($pattern))
				return $length == $pattern;
			elseif ($pattern[0] == '.')
				return $length <= substr($pattern, 2);
			elseif (substr($pattern, -1) == '.')
				return $length >= substr($pattern, 0, -2);
			else {
				$min_max = explode("..", $pattern);
				return $length >= $min_max[0] && $length <= $min_max[1];
			}
		}
		
		/**
		 * Checks if a field is present.
		 * @param $field: a field of any variable type.
		 * @return Boolean.
		 */
		public static function validate_presence($field) {
			return !empty($field) || $field === 0;
		}
		
		/**
		 * Checks if a field is an integer.
		 * @param $field: a field of any variable type.
		 * @return Boolean.
		 */
		public static function validate_is_integer($field) {
			return is_int($field);
		}
		
		/**
		 * Checks if a field is a boolean.
		 * @param $field: a field of any variable type.
		 * @return Boolean.
		 */
		public static function validate_is_boolean($field) {
			return is_bool($field);
		}
		
		/**
		 * Checks if a field is contained in an array.
		 * @param $field: a field of any variable type.
		 * @param $array: an array.
		 * @return Boolean.
		 */
		public static function validate_exists_in($field, $array) {
			return in_array($field, $array);
		}
	}

?>