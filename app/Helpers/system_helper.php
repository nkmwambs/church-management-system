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

if(!function_exists('transposeRecordArray')){
    function transposeRecordArray($array, $keyColumnName = 'id', $valueColumnName = 'name'){
        $transposedArray = [];
        if(!empty($array)){
            $keysArray = array_column($array, $keyColumnName);
            $valuesArray = array_column($array, $valueColumnName);

            if(sizeof($keysArray) == sizeof($valuesArray)){
                $transposedArray = array_combine($keysArray, $valuesArray);
            }
        }
        return $transposedArray;
    }
}