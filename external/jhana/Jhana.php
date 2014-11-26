<?php

	/** 
	 * This is the Jhana helper.
	 * It provides some basic functionalities like translation for exmple.
	 */
	class Jhana {
		
		/**
		 * Translates a given string using the language files 
		 * located in config/languages/. This function does not return anything
		 * but directly prints the result.
		 * @param: $string: The key which is then used for finding the translation.
		 */
		public static function t($string) {
			if (empty($_SESSION['language']))
				$_SESSION['language'] = DEFAULT_LANGUAGE;
			
			$language = require_once 'config/languages/'.$_SESSION['language'].'.php';
			echo $language[$string];
		}
		
		/**
		 * Sets the language of the application.
		 * @param: $language: The language in form of a string like "en" or "de".
		 */
		public static function set_language($language) {
			$_SESSION['language'] = $language;
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
		 * Checks if a field is an email adress.
		 * @param $field: a string.
		 * @return boolean.
		 */
		public static function validate_email($field) {
			return preg_match('/^[_a-z0-9-äÄöÖüÜ]+(\.[_a-z0-9-äÄöÖüÜ]+)*@[a-z0-9-äÄöÖüÜ]+(\.[a-z0-9-äÄöÖüÜ]+)*(\.[a-zäÄöÖüÜ]{2,3})$/i', $field);
		}
		
		/**
		 * Checks if a field has a certain length.
		 * @param $field: a string.
		 * @param $pattern: a string like "[1..10]", "[..10]" or "[1..]".
		 * @return boolean.
		 */
		public static function validate_length($field, $pattern) {
			
			$length = strlen($field);
			
			if ($pattern[0] == '.')
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
		 * @return boolean.
		 */
		public static function validate_presence($field) {
			return !empty($field) || $field === 0;
		}
		
		/**
		 * Checks if a field is an integer.
		 * @param $field: a field of any variable type.
		 * @return boolean.
		 */
		public static function validate_is_integer($field) {
			return is_int($field);
		}
		
		/**
		 * Checks if a field is a boolean.
		 * @param $field: a field of any variable type.
		 * @return boolean.
		 */
		public static function validate_is_boolean($field) {
			return is_bool($field);
		}
		
		/**
		 * Checks if a field is contained in an array.
		 * @param $field: a field of any variable type.
		 * @param $array: an array.
		 * @return boolean.
		 */
		public static function validate_exists_in($field, $array) {
			return in_array($field, $array);
		}
	}
?>