<?php 

namespace App\Libraries;

use App\Libraries\GroceryCrud;

class GatheringTypeLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function columns(){
        $fields = ['name','denomination_id','created_at', 'created_by'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }

    public function addFields(){
        $fields = ['name','denomination_id','description'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }

    public function editFields(){
        $fields = ['name','denomination_id','description'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id', $fields)]);
        }
        return $fields;
    }
    
    public function buildCrud($crud){
        $crud->setSubject(get_phrase('gathering_type'));
        $crud->setRelation('denomination_id','denominations', 'name');
        $crud->displayAs('denomination_id',get_phrase('denomination'));
    }
}   