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

	// Handle routing
	require_once 'external/altorouter/AltoRouter.php';
	$router = new AltoRouter();
	$router->setBasePath(BASE_PATH);
	require_once 'config/routes.php';
	$match = $router->match();

	// Extract controller name
	$_GET['controller'] = explode('#', $match['target'])[0];
	$controller_name = ucfirst($_GET['controller']).'Controller';
	
	// Extract action name
	$_GET['action'] = explode('#', $match['target'])[1];
	
	// If controller or action is undefined, response with a 404 Error
	if (empty($_GET['controller']) || empty($_GET['action'])) {
		header('HTTP/1.0 404 Not Found');
		require_once 'views/errors/404.php';
		exit;
	}

	// Require medoo
	require_once 'external/medoo/medoo.php';
	
	// Require all models
	require_once 'external/jhana/Model.php';
	Model::set_database(new medoo());
	foreach(glob('models/*.php') as $model)
	    require_once $model;

	// Require only the controller which is needed
	require_once 'external/jhana/Controller.php';
	require_once 'controllers/ApplicationController.php';
	require_once 'controllers/'.$controller_name.'.php';
		
	// Require all helpers
	require_once 'external/jhana/Jhana.php';
	foreach(glob('helpers/*.php') as $helper)
	    require_once $helper;
	
	// Set router for controller (inside actions we want to have access to all routes)
	$controller_name::set_router($router);
	
	// Execute filters
	$controller_name::filter();

	// Call action
	$controller_name::$_GET['action']($match['params']);
	
?>