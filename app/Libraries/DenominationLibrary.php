<?php 

namespace App\Libraries;

use App\Libraries\GroceryCrud;

class DenominationLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function requiredFields(){
        return ['name', 'registration_date','head_office','email','phone'];
    }

    public function columns(){
        return ['name', 'registration_date','head_office','email','phone','created_at','created_by'];
    }

    public function buildCrud($crud) {
        $crud->uniqueFields(['phone','name','email']);
        
        if(!$this->session->get('system_admin')){
            $crud->where('id', $this->session->get('denomination_id'));
        }
        
    }
}   