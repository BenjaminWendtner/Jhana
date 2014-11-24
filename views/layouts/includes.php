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
		
		<!-- Pjax -->
		<script src="<?php echo BASE_PATH; ?>external/pjax/pjax.min.js"></script>
		
		<!-- Load Javascript -->
		<script src="<?php echo BASE_PATH; ?>external/jhana/script.js"></script>
		<?php foreach(recursive_glob('assets/js/*.js') as $javascript) ?>
			<script src="<?php echo $javascript; ?>"></script>
		
		<!-- Load CSS (recommend using less) -->
		<?php foreach(recursive_glob('assets/css/*.css') as $css) ?>
			<link href="<?php echo $css; ?>" rel="stylesheet">
		
		<!-- Load Less (from cache if possible) -->
		<?php 
			require 'external/less/Cache.php';
			Less_Cache::$cache_dir = 'external/less/cache';
			
			$files = array();
			foreach(recursive_glob('assets/css/*.less') as $less)
				$files[$less] = $less;
		
			$css_file_name = Less_Cache::Get($files);
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH; ?>external/less/cache/<?php echo $css_file_name; ?>">
	</head>
  
	<body>
		<?php require_once 'layout.php'; ?>
	</body>
</html>