<?php 

namespace App\Libraries;

class MenuLibrary extends CoreLibrary {

    public function __construct(){
        parent::__construct();
    }

    public function getMenuItems($menuId = 0){
        $builder = $this->read_db->table('menus');
        $builder->select('menus.id as id, menus.name as name, icon, visible, feature_id, features.name as feature_name, parent_id');
        $builder->where('visible', 'yes');
        $builder->where('order<>', NULL);
        if($menuId > 0){
            $builder->where('menus.id', $menuId);
        }
        $builder->orderBy('order', 'ASC');
        $builder->join('features', 'menus.feature_id = features.id');
        $menusObj = $builder->get();

        $menus = [];
        $roleLibrary = new RoleLibrary();
        if($menusObj->getNumRows() > 0){
            $menusRaw = $menusObj->getResultArray();
            foreach($menusRaw as $menuRaw){
                $hasReadPermission = $roleLibrary->checkRoleHasPermission($this->session->get('role_ids'), $menuRaw['feature_name'], 'read');
                if(!$hasReadPermission){
                    continue;
                }
                $menus[$menuRaw['id']] = $menuRaw;
            }
        }

        return $menus;
    }

    public function getMenuItemsIdsGrouping(){
        $menuItems = $this->getMenuItems();

        $menuItemsIdsGrouping = [];

        foreach($menuItems as $menuItem){
            if($menuItem['parent_id'] == 0){
                $menuItemsIdsGrouping[$menuItem['id']]['def'] = $menuItem;
            }else{
                $menuItemsIdsGrouping[$menuItem['parent_id']]['children'][$menuItem['id']] = $menuItem;
            }
            
        }

        return $menuItemsIdsGrouping;
    }

    public function getMenuChildren($featureName){
        $children = [];
        $builder = $this->read_db->table('menus');
        $builder->select('id, parent_id');
        $builder->where('name', strtolower($featureName));
        $menuIdObj = $builder->get();

        $menuId = 0;

        if($menuIdObj->getNumRows() > 0){
            $menuId = $menuIdObj->getRowArray()['parent_id'] > 0 ? $menuIdObj->getRowArray()['parent_id'] : $menuIdObj->getRowArray()['id'];

            $groupedMenu = $this->getMenuItemsIdsGrouping();

            if(array_key_exists($menuId, $groupedMenu) && array_key_exists('children', $groupedMenu[$menuId])){
                $children[] = $this->getMenuItems($menuId)[$menuId]; // Add the parent in the list of children
                $children = array_merge($children, $this->getMenuItemsIdsGrouping()[$menuId]['children']);
            }
        }
        return $children;
    }
}