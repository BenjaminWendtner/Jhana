<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>

		<!-- jQuery -->
		<script src="<?php echo BASE_PATH; ?>external/jquery/jquery_2.1.1.min.js"></script>

		<!-- Bootstrap -->
		<link href="<?php echo BASE_PATH; ?>external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<script src="<?php echo BASE_PATH; ?>external/bootstrap/js/bootstrap.min.js"></script>
	</head>
  
	<body>
		<center>
			<br /><br /><br /><br /><br /><br />
			<h1>Welcome to Jhana! </h1><br />
			
			This is the temporary Welcome-Page ;-)<br />
			We're still in development!<br />
			Later you will find instructions on where to start...
			
			<br /><br />
			
			In the meanwhile, go and checkout Jhana on GitHub:<br />
			<a href="https://github.com/SebastianPoell/Jhana">Jhana on GitHub</a>
			
			<br /><br />
					
			This page will disappear as soon as you define<br />
			the root url in config/routes.php. Example: <br />
			<b>$router->map('GET', '', 'user#index');</b>
		</center>
	</body>
</html>