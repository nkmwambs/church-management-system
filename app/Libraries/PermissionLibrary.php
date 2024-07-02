<?php

namespace App\Libraries;

class PermissionLibrary extends CoreLibrary
{
    public function __construct()
    {
        parent::__construct();
    }

    private function getPermissionsByPermissionIds($permissionIds)
    {
        $builder = $this->read_db->table('permissions');
        $builder->select('label as permission_label,features.name as feature_name');
        $builder->join('features', 'permissions.feature_id = features.id');
        $builder->whereIn('permissions.id', $permissionIds);
        $permissions = $builder->get()->getResultArray();

        return $permissions;
    }

    private function groupPermisionsByFeatureKeyLabelValues($permissions)
    {
        $featurePermissions = [];
        foreach ($permissions as $permission) {
            $featureKey = $permission['feature_name'];
            $featureLabel = $permission['permission_label'];
            $featurePermissions[$featureKey][] = $featureLabel;
        }
        return $featurePermissions;
    }

    private function fillUpPermissionByStrength($groupedPermisionByFeatureKeyLabelValues){
        $filledUpPermissions = [];
        $labelOrder = ['delete','update','create','read'];
        foreach($groupedPermisionByFeatureKeyLabelValues as $featureName => $featurePermissionsLabels){
            $copyLabelOrder = $labelOrder;
            for($i = 0; $i < sizeof($labelOrder); $i++){
                if(!in_array($labelOrder[$i], $featurePermissionsLabels)){
                    unset($copyLabelOrder[$i]);
                }else{
                    break;
                }
            }
            $filledUpPermissions[$featureName] = array_values($copyLabelOrder);
        }

        return $filledUpPermissions;
    }

    public function getPermissionRangeByPermisionIds($permisionIds){
        
        $getPermissionsByPermissionIds = $this->getPermissionsByPermissionIds($permisionIds);
        $groupPermisionsByFeatureKeyLabelValues = $this->groupPermisionsByFeatureKeyLabelValues($getPermissionsByPermissionIds);
        $fillUpPermissionByStrength = $this->fillUpPermissionByStrength($groupPermisionsByFeatureKeyLabelValues);

        return $fillUpPermissionByStrength;
    }

    public function getAllPermissions(){

        $builder = $this->read_db->table('permissions');
        $builder->select('id, name');
        if(!$this->session->get('system_admin')){
            $builder->where('global_permission', 'no');
        }
        $permissions = $builder->get()->getResultArray();

        $ids = array_column($permissions, 'id');
        $names = array_column($permissions, 'name');
        $keyArray  = array_combine($ids, $names);

        return $keyArray;
    }
}