<?php
	
	/** 
	 * This is the controller baseclass. 
	 * It provides filtering, rendering and redirecting
	 */
	class Controller {
		
		protected static $router;

	   	public static function set_router($router) {
	       self::$router = $router;
	   	}
		
		/**
		 * Calls all filters which are defined in the controllers.
		 */	
		public static function filter(&$params) {
			
			if (isset(static::$filters) == FALSE) return;
			
			foreach (static::$filters as $filter_name => $actions) {
				foreach ($actions as $action) {
					if ($_GET['action'] == $action)
						static::$filter_name($params);
				}
			}
		}
		
		/**
		 * Renders a specific view from the views folder by using the
		 * controller and action names. 
		 * @param $params: The params which should be passed to the view.
		 * @param $title: Optional the HTML-Title which should be updated in the view.
		 */	
		protected static function render($params=[], $title='') {
			
			// Router-Object should be accessible to views
			global $router;
			
			// Prepares variables for the view
			foreach ($params as $key => $value)
				$$key = $value;
			
			// Use default pagetitle if no title is provided
			if ($title == '')
				$title = APP_TITLE;
			
			// Requires layout and view (with Pjax support)
			$view = 'views/'.$_GET['controller'].'/'.$_GET['action'].'.php';
			if (isset($_SERVER['HTTP_X_PJAX'])) {
				echo '<div id="pjax-response" data-title="'.$title.'">';
				require_once 'views/layouts/layout.php';
				echo '</div>';
			} else
				require_once 'views/layouts/main.php';
			
			exit;
		}
		
		/**
		 * Redirects the user to another site.
		 * @param $url: The url where the user should be redirected.
		 */	
		protected static function redirect($url) {
			if (isset($_SERVER['HTTP_X_PJAX']))
				echo $url;
			else	
				header('Location: '.$url);
			
			exit;
		}		

		
	}
	
?>