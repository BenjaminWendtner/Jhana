<!-- jQuery -->
<script src="<?php echo BASE_PATH; ?>external/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<link href="<?php echo BASE_PATH; ?>external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo BASE_PATH; ?>external/bootstrap/js/bootstrap.min.js"></script>

<!-- Pjax -->
<script src="<?php echo BASE_PATH; ?>external/pjax/pjax.min.js"></script>

<!-- Javascript -->
<?php foreach(Jhana::recursive_glob('assets/js/*.js') as $javascript) { ?>
	<script src="<?php echo $javascript; ?>"></script>
<?php } ?>

<!-- CSS -->
<?php foreach(Jhana::recursive_glob('assets/css/*.css') as $css) { ?>
	<link href="<?php echo $css; ?>" rel="stylesheet">
<?php } ?>

<!-- Less -->
<?php 
	require 'external/less/Cache.php';
	
	Less_Cache::$cache_dir = 'external/less/cache';
		
	if (!is_writable(Less_Cache::$cache_dir))
		Jhana::exception('cache_not_writable');
	
	$files = array();
	foreach(Jhana::recursive_glob('assets/css/*.less') as $less)
		$files[$less] = $less;

	$css_file_name = Less_Cache::Get($files);
?>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH; ?>external/less/cache/<?php echo $css_file_name; ?>">
