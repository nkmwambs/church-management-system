<?php
foreach ($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

<nav class="navbar sticky-top navbar-light bg-light">
	<?php
	$children = $page_data['children'];
	$feature = $page_data['page_name'];
	if (isset($children) && !empty($children)) {
		foreach ($children as $child) {
	?>
		<a class="navbar-brand" href="<?= site_url(strtolower(pascalize($child['name']))); ?>"><?= ucfirst(humanize(plural($child['name']))); ?></a>
	<?php
		}
	}
	?>
</nav>


<div style='height:20px;'></div>
<div style="padding: 10px">
	<?php echo $output; ?>
</div>
<?php foreach ($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<?php
if (file_exists(VIEW_PATH . $feature . DIRECTORY_SEPARATOR . 'js_script.php')) {
	include VIEW_PATH . $feature . DIRECTORY_SEPARATOR . 'js_script.php';
}
?>