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
		
		/*
		 * The filter method
		 */	
		public static function filter() {
			
			if (isset(static::$filters) == FALSE) return;
			
			foreach (static::$filters as $filter_name => $actions) {
				foreach ($actions as $action) {
					if ($_GET['action'] == $action)
						static::$filter_name();
				}
			}
		}
		
		/*
		 * The render method
		 */	
		protected static function render($params=[], $title='') {
			
			// Router-Object should be accessible to views
			global $router;
			
			// Prepares variables for the view
			foreach ($params as $key => $value)
				$$key = $value;
			
			// Pagetitle
			if ($title == '')
				$title = APP_TITLE;
			
			// Requires layout and view (with Pjax support)
			$view = 'views/'.$_GET['controller'].'/'.$_GET['action'].'.php';
			if (isset($_SERVER['HTTP_X_PJAX'])) {
				echo '<div id="pjax-response" data-title="'.$title.'">';
				require_once 'views/layouts/layout.php';
				echo '</div>';
			} else
				require_once 'views/layouts/includes.php';
	
		}
		
		/*
		 * The redirect method
		 */	
		protected static function redirect($url) {
			if (isset($_SERVER['HTTP_X_PJAX']))
				echo $url;
			else	
				header('Location: '.$url);
		}		

		
	}
	
?>