<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	foreach ($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
	<?php endforeach; ?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
	
	<div>
		<a href='<?php echo site_url('examples/customers_management') ?>'>Customers</a> |
		<a href='<?php echo site_url('examples/orders_management') ?>'>Orders</a> |
		<a href='<?php echo site_url('examples/products_management') ?>'>Products</a> |
		<a href='<?php echo site_url('examples/offices_management') ?>'>Offices</a> |
		<a href='<?php echo site_url('examples/employees_management') ?>'>Employees</a> |
		<a href='<?php echo site_url('examples/film_management') ?>'>Films</a>
	</div>
	<div style='height:20px;'></div>
	<div style="padding: 10px">
		<?php echo $output; ?>
	</div>
	<?php foreach ($js_files as $file): ?>
		<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
</body>

</html>