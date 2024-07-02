<?php
foreach ($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

<div style='height:20px;'></div>
<div style="padding: 10px">
	<?php echo $output; ?>
</div>
<?php foreach ($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<?php 
	if(file_exists(VIEW_PATH.$feature.DIRECTORY_SEPARATOR.'js_script.php')){
		include VIEW_PATH.$feature.DIRECTORY_SEPARATOR.'js_script.php';
	}
?>