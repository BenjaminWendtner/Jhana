<?php

	/**
	 * Welcome to the Jhana framework. Don't change this file!
	 * If you don't know where to start, please check out the documentation.
	 * No really, hands off the index.php ;-)
	 */
	
	// Denfine Base Path
	define('BASE_PATH', substr($_SERVER['SCRIPT_NAME'], 0, -9));
	
	// Check PHP version
	if (phpversion() < '5.4') {
		$name = 'php_version_error';
		require_once 'external/jhana/Exception.php';
		exit;
	}
	
	// Require heart
	require_once 'external/jhana/Heart.php'
?>