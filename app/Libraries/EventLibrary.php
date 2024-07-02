<?php 

namespace App\Libraries;

class EventLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function buildCrud($crud){
        $crud->setRelation('denomination_id', 'denominations', 'name');
        $crud->displayAs('denomination_id',get_phrase('denomination'));
    }
}