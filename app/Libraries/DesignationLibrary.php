<?php 

namespace App\Libraries;

class DesignationLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function requiredFields(){
        return ['name', 'denomination_id','hierarchy_id'];
    }

    public function columns(){
        $fields = ['name', 'denomination_id','department_id','hierarchy_id','created_at','created_by'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }
    public function addFields(){
        $fields = ['name', 'denomination_id','hierarchy_id','department_id'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
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
        $crud->setRelation('denomination_id', 'denominations', 'name', !$this->session->system_admin ? 'id = '.$this->session->denomination_id : null);
        $crud->setRelation('department_id', 'departments', 'name');
        $hierarchy_options = transposeRecordArray($this->getAllowableResults('hierarchy'));
        $hierarchy_options[0] = 'Church'; 
        ksort($hierarchy_options);
        
        $crud->fieldType('hierarchy_id', 'dropdown', $hierarchy_options);

        $crud->displayAs('denomination_id',get_phrase('denomination'));
        $crud->displayAs('hierarchy_id',get_phrase('hierarchy'));
        $crud->displayAs('department_id',get_phrase('department'));

        if(!$this->session->system_admin){
            $crud->where('designations.denomination_id = '.$this->session->denomination_id);
        }
    }


    
}