<?php
	
	/** 
	 * This is the controller baseclass. 
	 * It provides filtering, rendering and redirecting by using a query builder.
	 * Example: self::render()->title("asdfsdf")->view("user/show")->do();
	 */
	class Controller {

		// Holds either "render" or "redirect"
		private $do;
		
		// Variables for rendering
		private $params;
		private $title;
		private $layout;
		private $view;
		
		// Variables for redirecting
		private $url;
		
		
		/**
		 * Calls all filters which are defined in the child-controller.
		 * Filters are always executed BEFORE an controller action is called.
		 * @param $params: The parameters are called by reference, so that a filter
		 * 				   can edit params before they reach the action.
		 */	
		public static function filter(&$params) {
			
			if (isset(static::$filters) == FALSE) return;
			
			foreach (static::$filters as $key => $value) {
				
				// If filter affects all actions
				if (empty($key))
					static::$value($params);
				
				// If filter affects only certain actions
				elseif (in_array($_GET['action'], $value))
					static::$key($params);
			}
		}
		
		/**
		 * Renders a view into a layout. 
		 * Uses method chaining for setting all the optinal parameters.
		 * It also provides fallbacks for everything and support Pjax.
		 */	
		private function do_render() {

			// Construct variables for the view
			if ($this->params != '')
				foreach ($this->params as $key => $value)
					$$key = $value;
			
			// Use default pagetitle if no title is provided
			$title = ($this->title == '') ? APP_TITLE : $this->title;

			// Use default layout if no layout is provided
			$layout = ($this->layout == '') ? 'views/layouts/layout.php' : 'views/'.$this->layout.'.php';
			
			// Use default view if no view is provided
			$view = ($this->view == '') ? 'views/'.$_GET['controller'].'/'.$_GET['action'].'.php' : 'views/'.$this->view.'.php';
			
			if (isset($_SERVER['HTTP_X_PJAX'])) {
				echo '<div id="pjax-response" data-title="'.$title.'">';
				require_once $layout.'.php';
				echo '</div>';
			} else
				require_once 'views/layouts/main.php';
			
			// Unset $notices
			unset($_SESSION['notices']);
			
			// Exit after rendering
			exit;
		}
		
		/**
		 * Redirects to a given URL. 
		 * Uses method chaining for setting all the optinal parameters.
		 * Supports Pjax.
		 */	
		private function do_redirect() {

			if (isset($_SERVER['HTTP_X_PJAX']))
				echo $this->url;
			else	
				header('Location: '.$this->url);

			// Exit after redirecting
			exit;
		}
		
		/**
		 * Render and Redirect are always concluded with the do() method.
		 * Since "do" is a reserved PHP keyword, the magic PHP function "__call" is used.
		 */	
		public function __call($method, $args) {
			if ($method == 'do')
		    	return $this->jhana_do();
		}
		
		/**
		 * Gets called via the magic PHP function "__call".
		 * Executes the action which is set in the $do variable.
		 */	
		private function jhana_do() {
			switch ($this->do) {
				case 'render'  : $this->do_render();   break;
				case 'redirect': $this->do_redirect(); break;
			}
		}
		
		/**
		 * Method chaining: Sets the parameters for the rendered page.
		 * The parameters can be accessed in views.
		 */	
		public function params($params) {
			$this->params = $params;
			return $this;
		}
		
		/**
		 * Method chaining: Sets the title of the rendered page.
		 */	
		public function title($title) {
			$this->title = $title;
			return $this;
		}
		
		/**
		 * Method chaining: Sets notices for the rendered or redirected page.
		 * Notices are volatile: A page reload, resets all notices.
		 */
		public function notice($notices) {
			if ($_SESSION['notices'] == '')
				$_SESSION['notices'] = $notices;
			
			return $this;
		}
		
		/**
		 * Method chaining: Sets the layout path.
		 * The default layout is views/layouts/layout.php.
		 */
		public function layout($layout) {
			$this->layout = (strpos($layout,'/') === FALSE) ? 'layouts/'.$layout : $layout;
			return $this;
		}
		
		/**
		 * Method chaining: Sets the view path.
		 * The default layout is views/[Controller]/[Action].php.
		 */
		public function view($view) {
			$this->view = (strpos($view,'/') === FALSE) ? $_GET['controller'].'/'.$view : $view;
			return $this;
		}
		
		/**
		 * Method chaining: Sets the URL for redirection.
		 * Can handle either an URL or a Jhana route name with parameters.
		 */
		public function to($url_or_route_name, $params=[]) {
			if (Jhana::validate_url($url_or_route_name))
				$this->url = $url_or_route_name;
			else 
				$this->url = Jhana::route($url_or_route_name, $params);
				
			return $this;
		}
		
		/**
		 * Spawns an object and sets the action to "render".
		 */	
		protected static function render() {
			$temp = new static();
			$temp->do = 'render';
			return $temp;
		}
		
		/**
		 * Spawns an object and sets the action to "redirect".
		 */	
		protected static function redirect() {
			$temp = new static();
			$temp->do = 'redirect';
			return $temp;
		}		

	}
	
?>