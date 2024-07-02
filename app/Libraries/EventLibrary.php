<?php 

namespace App\Libraries;

class EventLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function requiredFields(){
        $fields = ['name','gatheringtype_id','start_date','end_date','location','denomination_id'];
        return $fields;
    }

    public function addFields(){
        $fields = ['name','gatheringtype_id','start_date','end_date','registration_fees','location','description','denomination_id','assemblies'];
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    public function editFields(){
        $fields = ['name','gatheringtype_id','start_date','end_date','registration_fees','location','description','denomination_id','assemblies'];
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    public function columns(){
        $fields = ['name','gatheringtype_id','start_date','end_date','registration_fees','location','description','denomination_id','created_at','created_by'];
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
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
    
    public function buildCrud($crud){
        $crud->setRelation('denomination_id', 'denominations', 'name');
        $crud->setRelation('gatheringtype_id', 'gatheringtypes', 'name');
        // $crud->setRelation('assembly_id', 'assemblies', 'name');
        $assemblyLibrary = new AssemblyLibrary();
        $assemblyOptions = $assemblyLibrary->getAllowableAssemblies();
        if(!empty($assemblyOptions)){
            $crud->fieldType('assemblies', 'multiselect', transposeRecordArray($assemblyOptions));
        }
        $crud->displayAs(['denomination_id' => get_phrase('denomination'),'assembly_id' => get_phrase('assembly'),'gatheringtype_id' => get_phrase('gathering_type')]);
       
    }
}