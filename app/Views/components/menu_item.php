<?php foreach(menuItems() as $menuItem){?>
<li class="menu-item <?=isset($menuItem['children']) ? 'has-sub' : ''?>">
    <a href="<?=!isset($menuItem['children']) ? strtolower(pascalize(site_url($menuItem['def']['name']))): "#";?>" >
        <i class="<?=$menuItem['def']['icon'];?>"></i>
        <span class="title"><?=plural(get_phrase($menuItem['def']['name']));?></span>
    </a>
    <?php if(isset($menuItem['children']) && !empty($menuItem['children'])){?>
        <ul>
            <li>
                <a href="<?=site_url(strtolower(pascalize($menuItem['def']['name'])));?>" >
                    <i class="<?=$menuItem['def']['icon'];?>"></i>
                    <span class="title"><?=plural(get_phrase($menuItem['def']['name']));?></span>
                </a>
            </li>
            <?php foreach($menuItem['children'] as $childMenuItem){?>
                <li>
                    <a href="<?=site_url(strtolower(pascalize($childMenuItem['name'])));?>" >
                        <i class="<?=$childMenuItem['icon'];?>"></i>
                        <span class="title"><?=plural(get_phrase($childMenuItem['name']));?></span>
                    </a>
                </li>
            <?php }?>
        </ul>
    <?php }?>
</li>
<?php }?>

<script>
    $(".menu-item").on('click', function (){
        
        if($(this).hasClass('has-sub')){
            $(this).addClass('opened');
        }
        $(this).addClass('active');
    });
</script>
