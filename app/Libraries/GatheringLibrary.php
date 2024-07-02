<?php 

namespace App\Libraries;

class GatheringLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function displayAs(){
        return ['assembly_id' => get_phrase('assembly')];
    }

    public function buildCrud($crud){
       $crud->setRelation('assembly_id','assemblies', 'name');
       $crud->setRelation('gathering_type_id','gathering_types', 'name');

    }
}