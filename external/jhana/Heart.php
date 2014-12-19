<?php

	/**
	 * This script is the heart of the framework.
	 * It evaluates the routes and maps them to the right controller and action.
	 * It also calls the filter-function before the action is executed.
	 */

	// Start session
	session_start();
	
	// Require config
	require_once 'config/config.php';
	
	// Denfine Base Path
	define('BASE_PATH', substr($_SERVER['SCRIPT_NAME'], 0, -9));

	// Handle routing
	require_once 'external/altorouter/AltoRouter.php';
	$router = new AltoRouter();
	$router->setBasePath(BASE_PATH);
	require_once 'config/routes.php';
	$match = $router->match();
	
	// Require all helpers
	require_once 'external/jhana/Jhana.php';
	Jhana::set_router($router);
	foreach(glob('helpers/*.php') as $helper)
	    require_once $helper;
	
	if (!empty($match['target'])) {
		// Extract controller name
		$_GET['controller'] = explode('#', $match['target'])[0];
		$controller_name = ucfirst($_GET['controller']).'Controller';
		
		// Extract action name
		$_GET['action'] = explode('#', $match['target'])[1];
	}
	
	// If controller or action is undefined, response with Welcome page or 404 Error
	if (empty($_GET['controller']) || empty($_GET['action'])) {
		if (BASE_PATH == $_SERVER['REQUEST_URI'])
			require_once 'external/jhana/Welcome.php';
		else {
			header('HTTP/1.0 404 Not Found');
			$view = 'views/errors/404.php';
			require_once 'views/layouts/main.php';
		}
		
		exit;
	}
	
	// Require all models
	require_once 'external/jhana/Model.php';
	Model::set_database();
	foreach(glob('models/*.php') as $model)
	    require_once $model;

	// Require only the controller which is needed
	require_once 'external/jhana/Controller.php';
	require_once 'controllers/ApplicationController.php';
	require_once 'controllers/'.$controller_name.'.php';
	
	// Execute filters
	$controller_name::filter($match['params']);

	// Call action
	$controller_name::$_GET['action']($match['params']);
	
?>
