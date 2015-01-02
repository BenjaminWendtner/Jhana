<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Jhana Exception</title>

		<!-- Bootstrap -->
		<link href="<?php echo BASE_PATH; ?>external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		
		<style>
			.panel-danger .panel-heading {
				background-color: #d1001d ;
				color: white;
			}
			
			p.lead {
				margin: 30px 0 5px 0;
			}
			
			p.error-description {
				margin: 15px 0 5px 0;
				color: #d1001d;
			}
			
			ul {
				list-style: square;
			}
			
			h1 {font-size: 1.8em;}
			
			@media (min-width: 768px) {
				.row {
					margin-top: 30px;
				}
				
				h1 {font-size: 2.4em;}
			}		
		</style>
	</head>
  
	<body>

		<div class="row">
			<div class="col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
				<div class="panel panel-danger">
	
					<?php
					
						// PHP version error
						if ($name == 'php_version_error') { ?>
							
							<div class="panel-heading">
								<h1>You need a newer version of PHP</h1>
							</div>
							
							<div class="panel-body">
								<p class="lead error-description">Jhana needs at least PHP 5.4</p>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Probably you can set the PHP version with one additional line in the .htaccess file.</li>
								</ul>
						</div>
					<?php }
						
						// Database connection exception
						elseif ($name == 'database_connection_error') { ?>
							
							<div class="panel-heading">
								<h1>Could not connect to database</h1>
							</div>
							
							<div class="panel-body">
								<p class="lead error-description">Jhana was not able to connect to your database.</p>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check your database configuration config/config.php</li>
								</ul>
							</div>
					<?php }
					
						// Cache not writable
						elseif ($name == 'cache_not_writable') { ?>
							
							<div class="panel-heading">
								<h1>Cache not writable</h1>
							</div>
							
							<div class="panel-body">
								<p class="lead error-description">Jhana could not write to the <b>/external/less/cache</b> directory.</p>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Login to your FTP and make sure, the <b>/external/less/cache</b> directory has writing access.</li>
								</ul>
							</div>
							
					<?php }
					
						// Route not found exception
						elseif ($name == 'route_not_found') { ?>
							
							<div class="panel-heading">
								<h1>Route not found</h1>
							</div>
							
							<div class="panel-body">
								<p class="lead error-description">The route <b><?php echo $params['route']; ?></b> was not found among your routes.</p>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check if the route exists in config/routes.php.</li>
									<li>Check your route name for typos.</li>
								</ul>
							</div>
							
					<?php }
					
						// Controller not found exception
						elseif ($name == 'controller_not_found') { ?>
							
							<div class="panel-heading">
								<h1>Controller not found</h1>
							</div>
							
							<div class="panel-body">
							
								<p class="lead error-description"><b><?php echo $params['controller']; ?>.php</b> was not found in the controllers folder.</p>
							
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check if the controller file exists in the controllers folder.</li>
									<li>Check your controller name for typos.</li>
									<li>Check your route for typos.</li>
								</ul>
							</div>
					<?php } 
						
						// Action not found exception
						elseif ($name == 'action_not_found') { ?>
							
							<div class="panel-heading">
								<h1>Action not found</h1>
							</div>
							
							<div class="panel-body">
							
								<p class="lead error-description">The action <b><?php echo $params['action']; ?></b> was not found in <?php echo $params['controller']; ?>.</p>
							
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check if the controller contains the action.</li>
									<li>Check your action name for typos.</li>
									<li>Check your route for typos.</li>
								</ul>
							
							</div>
					<?php } 
						
						// Layout not found exception
						elseif ($name == 'layout_not_found') { ?>
							
							<div class="panel-heading">
								<h1>Layout not found</h1>
							</div>
							
							<div class="panel-body">
							
								<p class="lead error-description">The layout <b><?php echo $params['layout']; ?>.php</b> was not found.</p>
								
								<p class="lead">
									<?php 
										foreach (debug_backtrace() as $trace)
											if ($trace['function'] == 'do')
												break;
										
										Jhana::print_exception_location($trace);
									?>
								</p>
								<pre><?php Jhana::print_exception_line($trace); ?></pre>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check if the layout exists in the views folder.</li>
									<li>Check your layout path in your controller.</li>
								</ul>
							
							</div>
					<?php } 
						
						// View not found exception
						elseif ($name == 'view_not_found') { ?>
							
							<div class="panel-heading">
								<h1>View not found</h1>
							</div>
							
							<div class="panel-body">
							
								<p class="lead error-description">The view <b><?php echo $params['view']; ?>.php</b> was not found.</p>
								
								<p class="lead">
									<?php 
										foreach (debug_backtrace() as $trace)
											if ($trace['function'] == 'do')
												break;
										
										Jhana::print_exception_location($trace);
									?>
								</p>
								<pre><?php Jhana::print_exception_line($trace); ?></pre>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check if the view exists in the views folder.</li>
									<li>Check your view path in your controller.</li>
								</ul>
							
							</div>
					<?php } 
					
						// SQL exception
						elseif ($name == 'sql_error') { ?>
								
							<div class="panel-heading">
								<h1>You have an error in your query</h1>
							</div>
							
							<div class="panel-body">
										
								<p class="lead error-description">You have a syntax error in one of your queries.</p>								
										
								<p class="lead">
									<?php 
										foreach (debug_backtrace() as $trace)
											if (!empty($trace['class']) && $trace['class'] == 'Model' && $trace['function'] != 'execute_query')
												break;
												
										Jhana::print_exception_location($trace);
									?>
								</p>
								<pre><?php Jhana::print_exception_line($trace); ?></pre>
								
								<p class="lead">Here the SQL which resulted from your query:</p>
								
								<?php 
									foreach ($params['params'] as $param)
										if (is_string($param))
											$params['query'] = preg_replace('/[?]/', '\''.$param.'\'', $params['query'], 1);
										else
											$params['query'] = preg_replace('/[?]/', $param, $params['query'], 1);
								?>
								
								<pre><?php echo $params['query']; ?></pre>
								
								<p class="lead">How to solve this problem:</p>
								<ul>
									<li>Check your query for errors.</li>
									<li>Make shure the table exists in the database.</li>
									<li>Make shure the column exists in the database.</li>
								</ul>			
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
		
	</body>
</html>
