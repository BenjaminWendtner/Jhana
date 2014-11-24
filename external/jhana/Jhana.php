<?php
	
	class Jhana {
		
		public static function t($string) {
			if (empty($_SESSION['language']))
				$_SESSION['language'] = DEFAULT_LANGUAGE;
			
			$language = require_once 'config/languages/'.$_SESSION['language'].'.php';
			echo $language[$string];
		}
		
		public static function set_language($language) {
			$_SESSION['language'] = $language;
		}
		
		public static function recursive_glob($pattern, $flags = 0) {
	    	$files = glob($pattern, $flags);
	     	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
				$files = array_merge($files, self::recursive_glob($dir.'/'.basename($pattern), $flags));
	
	  		return $files;
	  	}
	}
?>