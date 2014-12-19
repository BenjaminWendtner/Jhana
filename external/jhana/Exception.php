<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Jhana Exception</title>

		<!-- Bootstrap -->
		<link href="<?php echo BASE_PATH; ?>external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<script src="<?php echo BASE_PATH; ?>external/bootstrap/js/bootstrap.min.js"></script>
	</head>
  
	<body>
		
		<?php 
			
			// Database connection exception
			if ($name == 'database_connection_error') { ?>
				<b>Could not connect to database!</b>
				<br/><br/>
				
				<ul>
					<li>Check your database configuration config/routes.php</li>
				</ul>
		<?php }
		
		 	// Route not found exception
			elseif ($name == 'route_not_found') { ?>
				<b>Route not found!</b>
				<br/><br/>
				The route "<?php echo $params['route']; ?>" was not found among your routes.<br/><br/>
				
				<ul>
					<li>Check if the route exists in config/rotues.php.</li>
					<li>Check your route name for typos.</li>
				</ul>
		<?php }
		
		 	// Controller not found exception
			elseif ($name == 'controller_not_found') { ?>
				<b>Controller not found!</b>
				<br/><br/>
				"<?php echo $params['controller']; ?>" was not found in the controllers folder.<br/><br/>
				
				<ul>
					<li>Check if the controller file exists in the controllers folder.</li>
					<li>Check your controller name for typos.</li>
					<li>Check your route for typos.</li>
				</ul>
		<?php } 
			
			// Action not found exception
			elseif ($name == 'action_not_found') { ?>
				<b>Action not found!</b>
				<br/><br/>
				The action "<?php echo $params['action']; ?>" was not found in <?php echo $params['controller']; ?>.<br/><br/>
				
				<ul>
					<li>Check if the controller contains the action.</li>
					<li>Check your action name for typos.</li>
					<li>Check your route for typos.</li>
				</ul>
		<?php } 
			
			// Layout not found exception
			elseif ($name == 'layout_not_found') { ?>
				<b>Layout not found!</b>
				<br/><br/>
				
				The layout "<?php echo $params['layout']; ?>" was not found.<br/><br/>
				
				<?php 
					foreach (debug_backtrace() as $trace)
						if ($trace['function'] == 'do')
							break;
					
					Jhana::print_exception_location($trace);
				?>
				
				<br/><br/>
				<ul>
					<li>Check if the layout exists in the views folder.</li>
					<li>Check your layout path in your controller.</li>
				</ul>
		<?php } 
			
			// View not found exception
			elseif ($name == 'view_not_found') { ?>
				<b>View not found!</b>
				<br/><br/>
				
				The view "<?php echo $params['view']; ?>" was not found.<br/><br/>
				
				<?php 
					foreach (debug_backtrace() as $trace)
						if ($trace['function'] == 'do')
							break;
					
					Jhana::print_exception_location($trace);
				?>
				
				<br/><br/>
				<ul>
					<li>Check if the view exists in the views folder.</li>
					<li>Check your view path in your controller.</li>
				</ul>
		<?php } 
		
			// SQL exception
			elseif ($name == 'sql_error') { ?>
				<b>You have an error in your MySQL!</b>
				<br/><br/>
	
				<?php 
					foreach (debug_backtrace() as $trace)
						if ($trace['class'] == 'Model' && $trace['function'] != 'execute_query')
							break;
					
					Jhana::print_exception_location($trace);
				?>
	
				<br/><br/>
				Here the SQL which resulted from your query:<br />
				<?php 
					foreach ($params['params'] as $param)
						if (is_string($param))
							$params['query'] = preg_replace('/[?]/', '\''.$param.'\'', $params['query'], 1);
						else
							$params['query'] = preg_replace('/[?]/', $param, $params['query'], 1);
					
					echo $params['query'];
				?>
				
				<br /><br />
				<ul>
					<li>Check your query for errors.</li>
					<li>Make shure the table exists in the database.</li>
					<li>Make shure the column exists in the database.</li>
				</ul>
		<?php } ?>

	</body>
</html>