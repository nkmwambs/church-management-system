<div class = 'row'>
    <div class="col-sm-12">
        <div class="well">
            <h1><?= date('F, d Y') ?></h1>
            <h3><?= get_phrase('dashboard_welcome', 'Welcome to the site'); ?> <strong><?= session()->full_name; ?></strong></h3>
        </div>
    </div>
</div>