<?php
foreach ($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

<ul class="nav nav-pills">
	<?php
	$children = $page_data['children'];
	$feature = $page_data['page_name'];
	if (isset($children) && !empty($children)) {
		foreach ($children as $child) {
	?>
		<li class="nav-item">
			<a class="nav-link <?=$feature == strtolower(pascalize($child['name'])) ? 'active': '';?>" aria-current="role" href="<?= site_url(strtolower(pascalize($child['name']))); ?>"><?= ucfirst(humanize(plural($child['name']))); ?></a>
		</li>
		
	<?php
		}
	}
	?>
</ul>


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