<?php 

namespace App\Libraries;

class CustomfieldLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function requiredFields(){
        $fields = ['feature_id','name','type'];
        return $fields;
    }
    function addFields(){
        $fields = ['denomination_id','feature_id','name','type','options'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    function columns(){
        $fields = ['denomination_id','feature_id','name','type','options'];
        if(!$this->session->system_admin){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    // public function actionBeforeInsert($stateParameters){
    //     $stateParameters->data['name'] = underscore(strtolower($stateParameters->data['name']));
    //     return $stateParameters;
    // }

    function buildCrud($crud){
        $crud->setRelation('denomination_id','denominations','name');   
        $crud->setRelation('feature_id','features','name');
        $crud->displayAs(['denomination_id' => get_phrase('denomination'),'feature_id' => get_phrase('feature')]) ;
    }

    public function getAllowableCustomFields(){
        $builder = $this->read_db->table('customfields');
        $builder->select('customfields.id,customfields.name as name,type,options,visible');
        $builder->join('features','features.id = customfields.feature_id');
        $builder->where(['visible' => 'yes', 'features.name' => $this->feature]);
        if(!$this->session->system_admin){
                $builder->groupStart();
                $builder->where('denomination_id', $this->session->denomnation_id);
                $builder->orWhere('denomination_id', NULL);
                $builder->groupEnd();
            }
        $customFieldsObj = $builder->get();
        $customFields = [];
        if($customFieldsObj->getNumRows() > 0){
            $customFields = $customFieldsObj->getResultArray();
        }
        return $customFields;
    }

    public function getCustomFieldIdByFieldName($fieldName){
        $builder = $this->read_db->table('customfields');
        $builder->select('id');
        $builder->where('name', $fieldName);
        $customFieldObj = $builder->get();
        if($customFieldObj->getNumRows() > 0){
            return $customFieldObj->getRow()->id;
        }
        return null;
    }

}