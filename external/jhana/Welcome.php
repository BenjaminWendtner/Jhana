<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Welcome to Jhana</title>

		<!-- Bootstrap -->
		<link href="<?php echo BASE_PATH; ?>external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
		<style>
			
			body {
			  padding-bottom: 20px;
			}

			.where-to-go,
			.footer {
			  padding-right: 15px;
			  padding-left: 15px;
			}

			.footer {
			  padding-top: 19px;
			  color: #777;
			  border-top: 1px solid #e5e5e5;
			}

			@media (min-width: 768px) {
			  .container {
				max-width: 730px;
			  }
			}

			.jumbotron {
			  margin-top: 30px;
			  text-align: center;
			  border-bottom: 1px solid #e5e5e5;
			  
			  background-color: #BF006E;
			  color: white;
			}
			
			h2 {
				font-size: 1.8em;
				color: #BF006E;
			}
			.where-to-go {
			  margin: 40px 0;
			}

			.where-to-go p {
				font-size: 1.2em;
			}
			.where-to-go p + h4 {
			  margin-top: 28px;
			}
			
			ol {
				font-size: 1.4em;
			}
			
			ol li {
				margin-top: 15px;
			}
			
			.col {
				margin-top: 20px;
			}
			
			a, a:visited, a:hover, a:active {
				color: #BF006E;
			}

			@media screen and (min-width: 768px) {
			  .header,
			  .where-to-go,
			  .footer {
				padding-right: 0;
				padding-left: 0;
			  }
			  .header {
				margin-bottom: 30px;
			  }
			  .jumbotron {
				border-bottom: 0;
			  }
			}
			
			@media screen and (max-width: 768px) {
				.jumbotron {
					width: 100%;
					margin: 0;
					-webkit-border-radius: 0px !important;
					-moz-border-radius: 0px !important;
					border-radius: 0px !important;
				}
				.container {
					padding: 0;
				}
			}
		
		</style>
	</head>
  
	<body>

		<div class="container">

			<div class="jumbotron">
				<h1>Welcome to Jhana</h1>
					<p class="lead">You have successfully installed Jhana.</p>
			</div>
			
			<div class="row where-to-go">
				
				<div class="col-lg-12">
					<h2><i class="glyphicon glyphicon-th-list"></i> Next Steps</h2>
					<ol>
						<li>Configure your database in <em>config/config.php</em>.</li>
						<li>Create Models, Views and Controllers in their respective folders.</li>
						<li>Define a root URL in <em>config/routes.php</em> to replace this page.</li>
					</ol>
				</div>
				
				<div class="col col-lg-6">
					<h2><i class="glyphicon glyphicon-book"></i> Documentation</h2>
					<p>Read the full documentation on the official <a target="_blank" href="https://github.com/SebastianPoell/Jhana"><i class="glyphicon glyphicon-share"></i> <b>Jhana homepage</b></a>.</p>
				</div>

				<div class="col col-lg-6">
					<h2><i class="glyphicon glyphicon-time"></i> Quick Tutorial</h2>
					<p>You can learn how to use Jhana in less than one hour with <a target="_blank" href="https://github.com/SebastianPoell/Jhana"><i class="glyphicon glyphicon-share"></i> <b>this tutorial</b></a>.</p>
				</div>
			</div>

			<footer class="footer">
				<p>&copy; The <b>Jhana Framework</b> was created by Sebastian P&ouml;ll &amp; Benjamin Wendtner</p>
			</footer>

		</div> <!-- /container -->
	
	</body>
</html>
