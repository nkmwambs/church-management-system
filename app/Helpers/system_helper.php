<?php 

if(!function_exists('menuItems')){
    function menuItems(){
        $menuLibrary = new \App\Libraries\MenuLibrary();
        return $menuLibrary->getMenuItemsIdsGrouping();
    }
}

if (!function_exists('arrayIsList')) {
    function arrayIsList(array $arr)
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}