<?php 

namespace App\Libraries;

class UserLibrary extends CoreLibrary {

    public function __construct(){
        parent::__construct();
    }

    public function getUserByEmailAndPassword($userEmail, $userPassword){
        $builder = $this->read_db->table('users');
        $builder->where(['email' => $userEmail, 'password' => $userPassword]);
        $userObj = $builder->get();
        
        $user = [];

        if($userObj->getNumRows() > 0){
            $user = $userObj->getRowArray();
        }

        return $user;
    }

    public function getAccessCount($userId){
        $builder = $this->read_db->table('users');
        $builder->where('id', $userId);
        $builder->selectSum('access_count');
        $accessCount = $builder->get()->getRowArray()['access_count'];
        return $accessCount;
    }

    public function updateUserLoginData($userId){
        $data = ['accessed_at' => date('Y-m-d H:i:s'),'access_count' => $this->getAccessCount($userId)+1];
        $builder = $this->read_db->table('users');
        $builder->where('id', $userId);
        $builder->update($data);
    }

    public function columns(){
        $fields = ['first_name', 'last_name', 'denomination_id','roles','email','gender','is_active','accessed_at','access_count'];
         if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id',$fields)]);
         }   
        return $fields;
    }

    function callbackBeforeInsert($stateParameters) {
        // Set the denomination_id if not provided in the request data.
        if(!isset($stateParameters->data['denomination_id'])){
            $stateParameters->data['denomination_id'] = $this->session->get('denomination_id');
        }

        if(isset($stateParameters->data['pass_confirm'])){
            unset($stateParameters->data['pass_confirm']);
        }

        return $stateParameters;
    }

    public function addFields(){
        $fields = ['first_name', 'last_name','roles','permitted_entities','permitted_assemblies', 'date_of_birth', 'gender','phone','email','denomination_id','password', 'pass_confirm'];
        if($this->session->get('system_admin')){
            array_push($fields, 'is_system_admin');
        }
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id',$fields)]);
         } 
        return $fields;
    }

    public function editFields(){
        $fields = ['first_name', 'last_name','roles', 'permitted_entities','permitted_assemblies','date_of_birth', 'gender', 'phone','email'];
        if($this->session->get('system_admin')){
            array_push($fields, 'is_system_admin');
        }
        
        if($this->id == $this->session->get('user_id')){
            unset($fields[array_search('email',$fields)]);
            unset($fields[array_search('is_system_admin',$fields)]);
        }

        if($this->action == 'edit'){
            $user = $this->getUserById($this->id);
            if($user['is_system_admin'] == 'yes'){
                unset($fields[array_search('roles',$fields)]);
            }
        }

        return $fields;
    }

    public function getUserById($id){
        $builder = $this->read_db->table('users');
        $builder->where('id', $id);
        $userObj = $builder->get();
        
        $user = [];

        if($userObj->getNumRows() > 0){
            $user = $userObj->getRowArray();
        }

        return $user;
    }

    public function requiredFields(){
        return ['first_name', 'last_name','roles', 'date_of_birth', 'gender','phone','email','denomination_id','password'];
    }

    public function unsetReadFields(){
        return ['password','deleted_at','deleted_by'];
    }

    public function uniqueFields(){
        return ['email'];
    }


    public function buildCrud($crud){
        $crud->setRelation('denomination_id', 'denominations', 'name', !$this->session->system_admin ? 'id = '.$this->session->denomination_id : null);
        $crud->displayAs('denomination_id',get_phrase('denomination'));

        $this->setSelectField($crud, 'role', 'roles');
        $this->setSelectField($crud, 'entity', 'permitted_entities',false);
        $this->setSelectField($crud, 'assembly', 'permitted_assemblies');

        // Prevent listing self
        $crud->where('users.id<>', $this->session->get('user_id'));

        if(!$this->session->system_admin){
            $crud->where('users.denomination_id', $this->session->get('denomination_id'));
        }

        $crud->setRule('phone', 'Phone', 'required|min_length[10]');
        $crud->setRule('email', 'Email', 'required|max_length[254]|valid_email');
        $crud->setRule('password', 'Password', 'required|max_length[255]|min_length[8]');   
        $crud->setRule('pass_confirm', 'Password Confirmation', 'required|max_length[255]|matches[password]');  

        $crud->fieldType('password', 'password');
        $crud->fieldType('pass_confirm', 'password');
    }

}