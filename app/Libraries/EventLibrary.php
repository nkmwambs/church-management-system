<?php 

namespace App\Libraries;

class EventLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function AddFields(){
        $fields = ['name','start_date','end_date','registration_fees','location','description',''];
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }
    
    public function buildCrud($crud){
        $crud->setRelation('denomination_id', 'denominations', 'name');
        $crud->displayAs('denomination_id',get_phrase('denomination'));
    }
}