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

    public function buildCrud($crud){
        $crud->setRelation('designation_id','designations','name', 'denomination_id ='.$this->session->denomination_id);
        // $crud->setRelation('assembly_id','assemblies','name','denomination_id ='.$this->session->denomination_id);
        $assemblyLibrary = new AssemblyLibrary();
        $crud->fieldType('assembly_id', 'dropdown', transposeRecordArray($assemblyLibrary->getAllowableAssemblies()));

        $crud->join(
            [
                ['members','assembly_id', 'assemblies', 'id', []],
                ['assemblies','entity_id', 'entities', 'id', []],
                ['entities','hierarchy_id', 'hierarchies', 'id', !$this->session->system_admin ? ['hierarchies.denomination_id' => $this->session->denomination_id] : []]
            ]
        );
    }
}