<?php 

namespace App\Libraries;

class DepartmentLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function columns(){
        $fields = ['name', 'denomination_id', 'created_at', 'created_by'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    public function displayAs(){
        return ['denomination_id' => get_phrase('denomination')];
    }

    public function buildCrud($crud){
       $crud->setRelation('denomination_id','denominations', 'name');
    }

}