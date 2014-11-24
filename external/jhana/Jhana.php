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
	}
?>