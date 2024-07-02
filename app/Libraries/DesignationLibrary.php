<?php 

namespace App\Libraries;

class DesignationLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function requiredFields(){
        return ['name', 'denomination_id'];
    }

    public function columns(){
        $fields = ['name', 'denomination_id','created_at','created_by'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }
    public function addFields(){
        $fields = ['name', 'denomination_id'];
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
        $crud->setRelation('denomination_id', 'denominations', 'name', !$this->session->system_admin ? 'id = '.$this->session->denomination_id : '');
        $crud->displayAs('denomination_id',get_phrase('denomination'));

        if(!$this->session->system_admin){
            $crud->where('denomination_id = '.$this->session->denomination_id);
        }
    }


    
}