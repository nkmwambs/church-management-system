<?php 

namespace App\Libraries;

class RoleLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function columns(){
        return ['name', 'permissions'];
    }

    public function addFields(){
        $fields = ['name', 'permissions','denomination_id'];
        if($this->session->get('system_admin')){
            array_push($fields, 'default_role');
        }
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }

    public function editFields(){
        $fields = ['name', 'permissions'];
        if($this->session->get('system_admin')){
            array_push($fields, 'default_role');
        }
        return $fields;
    }

    function callbackBeforeInsert($stateParameters) {
        // Set the denomination_id if not provided in the request data.
        if(!isset($stateParameters->data['denomination_id'])){
            $stateParameters->data['denomination_id'] = $this->session->get('denomination_id');
        }
        return $stateParameters;
    }
    
    private function getPermissionsIdsByRoleIds(array $roleIds = []) {
        $permissionsIds = array();
        $builder = null;
        if(!empty($roleIds)){
            $builder = $this->read_db->table('roles');
            $builder->select('permissions');
            $builder->whereIn('id', $roleIds);
            $permissionsObj = $builder->get();
            if($permissionsObj->getNumRows() > 0) {
                $permissionsIdsList = $permissionsObj->getResultArray();
                foreach($permissionsIdsList as $rolePermissionIds){
                    $permissionsIds = array_merge($permissionsIds, explode(',',$rolePermissionIds['permissions']));
                }
            }
        }else{
            $builder = $this->read_db->table('permissions');
            $builder->select('id');
            $permissionsObj = $builder->get();
            if($permissionsObj->getNumRows() > 0) {
                $permissionsIds = array_column($permissionsObj->getResultArray(),'id');
            }
        }

        $permissionsIds = array_values(array_unique($permissionsIds));
        
        $permissionLibrary = new PermissionLibrary();
        $permissions = $permissionLibrary->getPermissionRangeByPermisionIds($permissionsIds);

        return $permissions;
    }

    public function checkRoleHasPermission($roleIds, $featureName, $permissionsLabel){
        $checkRoleHasPermission = false;
        if(in_array('*', $roleIds)){
            $roleIds = [];
        }
        // log_message('error', json_encode($roleIds));
        $getPermissionsIdsByRoleIds = $this->getPermissionsIdsByRoleIds($roleIds);

        if(array_key_exists($featureName, $getPermissionsIdsByRoleIds)){
            if(in_array($permissionsLabel,$getPermissionsIdsByRoleIds[$featureName])){
                $checkRoleHasPermission = true;
            }
        }
        return $checkRoleHasPermission;
    }

    public function buildCrud($crud){
        $permissionLibrary = new PermissionLibrary();
        $permissionOptions = $permissionLibrary->getAllPermissions();
        $crud->fieldType('permissions', 'multiselect', $permissionOptions);

        if(!$this->session->system_admin){
            $crud->where('default_role', 'no');
            $crud->where('roles.denomination_id', $this->session->denomination_id);
        }   
    }

    public function getRoles(){
        $builder = $this->read_db->table('roles');
        $builder->select('id, name');
        if(!$this->session->system_admin){
            $builder->where('roles.denomination_id', $this->session->denomination_id);
            $builder->orWhere('roles.denomination_id', NULL);
        }
        $roles = $builder->get()->getResultArray();

        $ids = array_column($roles, 'id');
        $names = array_column($roles, 'name');

        $keyedArray = array_combine($ids, $names);

        return $keyedArray;
    }
    
}