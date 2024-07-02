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

    public function dbAddFields(){
        $fields = ['first_name', 'last_name','phone', 'email', 'date_of_birth','designation_id', 'assembly_id'];
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

    public function customFields($crud){
        $customFields = [
            'marital_status' => [
                'type' => 'dropdown',
                'options' => ['male' => 'male', 'female' => 'female']
            ],
            'spouse_name' => [
                'type' => 'string'
            ],
            'marriage_date' => [
                'type' => 'date'
            ]
        ];
        $crud->addFields([...$this->dbAddFields(), ...array_keys($customFields)]);

        foreach($customFields as $field_name => $field_info){
            if($field_info['type'] == 'dropdown'){
                $crud->fieldType($field_name, 'dropdown', $field_info['options']);
            }else{
                $crud->fieldType($field_name, $field_info['type']);
            }
        }
    }

    public function buildCrud($crud){
        $crud->setRelation('designation_id','designations','name', 'denomination_id ='.$this->session->denomination_id);
        // $crud->setRelation('assembly_id','assemblies','name','denomination_id ='.$this->session->denomination_id);
        $assemblyLibrary = new AssemblyLibrary();
        $crud->fieldType('assembly_id', 'dropdown', transposeRecordArray($assemblyLibrary->getAllowableAssemblies()));

        $this->customFields($crud);

        $crud->join(
            [
                ['members','assembly_id', 'assemblies', 'id', []],
                ['assemblies','entity_id', 'entities', 'id', []],
                ['entities','hierarchy_id', 'hierarchies', 'id', !$this->session->system_admin ? ['hierarchies.denomination_id' => $this->session->denomination_id] : []]
            ]
        );
    }
}