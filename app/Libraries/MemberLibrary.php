<?php 

namespace App\Libraries;

class MemberLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function columns(){
        $fields = ['first_name', 'last_name', 'email', 'date_of_birth','designation_id','phone', 'assembly_id', 'created_at', 'created_by'];
        return $fields;
    }

    public function addFields(){
        $fields = ['first_name', 'last_name','phone', 'email', 'date_of_birth','designation_id', 'assembly_id'];
        $assemblyLibrary = new AssemblyLibrary();
        if(empty($assemblyLibrary->getAllowableAssemblies())){
           unset($fields[array_search('assembly_id', $fields)]); 
        }
        return $fields;
    }

    public function requiredFields(){
        return ['first_name', 'last_name', 'date_of_birth','designation_id','phone', 'assembly_id'];
    }

    // public function uniqueFields(){
    //     return ['email'];
    // }

    public function displayAs(){
        return ['designation_id' => get_phrase('designation'),'assembly_id' => get_phrase('assembly')];
    }

    // public function customFields($crud, $array = []){
    //     $builder = $this->read_db->table('fields');
    //     $builder->select('id,name,type,options,visible');
    //     $builder->where(['visible' => 'yes', 'feature_id' => 11]);
    //     $customFieldsObj = $builder->get();

    //     if($customFieldsObj->getNumRows() > 0){
    //         $customFields = $customFieldsObj->getResultArray();
    //         $crud->addFields([...$this->addFields(), ...array_column($customFields, 'name')]);
    //         $crud->editFields([...$this->addFields(), ...array_column($customFields, 'name')]);
    //         foreach($customFields as $field_info){
    //             if(!is_null($field_info['options'])){
    //                 $crud->fieldType($field_info['name'], $field_info['type'], explode(',',$field_info['options']));
    //             }else{
    //                 $crud->fieldType($field_info['name'], $field_info['type']);
    //             }
    //         }
    //     }else{
    //         $crud->addFields([...$this->addFields()]);
    //         $crud->editFields([...$this->addFields()]);
    //     }
    // }

    // public function callbackBeforeInsert($stateParameters){
    //     // Seperate the custom fields before posting
    //     $customFieldsName = array_column($this->getAllowableResults('customfield'),'name');
    //     $this->customFieldsToInsert = [];
    //     foreach($stateParameters->data as $fieldName => $postData){
    //         // log_message('error', json_encode($fieldName));
    //         if(in_array($fieldName, $customFieldsName)){
    //             $this->customFieldsToInsert[$fieldName] = $postData;
    //             unset($stateParameters->data[$fieldName]);
    //         }
    //     }
    //     return $stateParameters;
    // }

    // public function getCustomFieldIdByFieldName($fieldName){
    //     $builder = $this->read_db->table('customfields');
    //     $builder->select('id');
    //     $builder->where('name', $fieldName);
    //     $customFieldObj = $builder->get();
    //     if($customFieldObj->getNumRows() > 0){
    //         return $customFieldObj->getRow()->id;
    //     }
    //     return null;
    // }
    // function callBackAfterInsert($stateParameters){
    //     $insertData = [];
    //     if(!empty($this->customFieldsToInsert)){
    //         $i = 0;
    //         foreach($this->customFieldsToInsert as $field => $value){
    //             $insertData[$i]['record_id'] = $stateParameters->insertId;
    //             $insertData[$i]['field_id'] = $this->getCustomFieldIdByFieldName($field);
    //             $insertData[$i]['value'] = $value;
    //             $insertData[$i]['created_at'] = date('Y-m-d H:i:s');
    //             $insertData[$i]['created_by'] = $this->session->user_id;
    //             $insertData[$i]['updated_at'] = date('Y-m-d H:i:s');
    //             $insertData[$i]['updated_by'] = $this->session->user_id;
    //             $i++;
    //         }
    //         $builder = $this->write_db->table('customvalues');
    //         $builder->insertBatch($insertData);
    //     }

    //     return $stateParameters;
    // }

    public function buildCrud($crud){
        // $crud->setRelation('assembly_id','assemblies','name','denomination_id ='.$this->session->denomination_id);
        
        $crud->setRelation('designation_id','designations','name', !$this->session->system_admin ? 'denomination_id ='.$this->session->denomination_id: null);
        $assemblyLibrary = new AssemblyLibrary();
        // log_message('error', json_encode($assemblyLibrary->getAllowableAssemblies()));
        if(!empty($assemblyLibrary->getAllowableAssemblies())){
            $crud->fieldType('assembly_id', 'dropdown', transposeRecordArray($assemblyLibrary->getAllowableAssemblies()));
        }

        // $this->customFields($crud);

        // $crud->join(
        //     [
        //         ['members','assembly_id', 'assemblies', 'id', []],
        //         ['assemblies','entity_id', 'entities', 'id', []],
        //         ['entities','hierarchy_id', 'hierarchies', 'id', !$this->session->system_admin ? ['hierarchies.denomination_id' => $this->session->denomination_id] : []]
        //     ]
        // );
    }
}