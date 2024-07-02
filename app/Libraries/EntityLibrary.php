<?php 

namespace App\Libraries;

class EntityLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function addFields(){
        $fields = ['name', 'hierarchy_id', 'parent_id', 'entity_leader'];
        return $fields;
    }

    public function editFields(){
        $fields = ['name', 'hierarchy_id', 'parent_id', 'entity_leader'];
        return $fields;
    }

    public function columns(){
        $fields = ['name', 'hierarchy_id','parent_id','entity_leader','created_at','created_by'];
        return $fields;
    }
    public function requiredFields(){
        return ['name', 'hierarchy_id'];
    }

    function callbackBeforeInsert($stateParameters) {
        // Append denomination name to resource name

        if(!$this->session->system_admin){
            if(!strpos($stateParameters->data['name'], $this->session->denomination_code)){
                $stateParameters->data['name'] = $this->session->denomination_code.' - '.$stateParameters->data['name'];
            }
        }

        return $stateParameters;
    }

    function callbackBeforeUpdate($stateParameters) {
        // Append denomination name to resource name

        if(!$this->session->system_admin){
            if(!strpos($stateParameters->data['name'], $this->session->denomination_code)){
                $stateParameters->data['name'] = $this->session->denomination_code.' - '.$stateParameters->data['name'];
            }
        }

        return $stateParameters;
    }

    public function buildCrud($crud){
        $crud->displayAs(['hierarchy_id' => get_phrase('hierarchy'), 'parent_id' => get_phrase('reporting_to_entity')]);
        $crud->setRelation('hierarchy_id', 'hierarchies', 'name', !$this->session->system_admin ? 'denomination_id = '.$this->session->denomination_id : null);
        // $crud->setRelation('hierarchy_id', 'hierarchies', 'name');
        
        $crud->setRelation('parent_id', 'entities', 'name');
        $crud->setRelation('entity_leader', 'members', '{first_name} {last_name} - {phone}');

        $crud->join([
            ['entities','hierarchy_id', 'hierarchies', 'id', !$this->session->system_admin ? ['hierarchies.denomination_id' => $this->session->denomination_id] : []]
        ]);
        
    }

    public function getReportingEntities($postData){
        $hierarchy_id = $postData['hierarchy_id'];
        
        $builder = $this->read_db->table('hierarchies');
        $builder->where('id', $hierarchy_id);
        $selectedLevel = $builder->get()->getFirstRow()->level;

        $builder = $this->read_db->table('entities');
        $builder->select('entities.id, entities.name');
        $builder->join('hierarchies','hierarchies.id = entities.hierarchy_id');
        $builder->where('level', $selectedLevel + 1);
        if(!$this->session->system_admin){
            $builder->where('hierarchies.denomination_id', $this->session->denomination_id);
        }
        $possibleReportingEntities = $builder->get()->getResultArray();

        return $possibleReportingEntities;
    }
    
    public function getAllowableEntities(){
        $user = $this->callClassMethod('user','getUserById', $this->session->user_id);
        $builder = $this->read_db->table('entities');
        $builder->select('entities.id as id, entities.name as name, denominations.name as denomination_name');
        $builder->join('hierarchies','hierarchies.id = entities.hierarchy_id');
        $builder->join('denominations','denominations.id = hierarchies.denomination_id');
        $builder->where('hierarchies.level', 1);
        if(!$this->session->system_admin){
            $builder->where('hierarchies.denomination_id', $this->session->denomination_id);
            if(!empty($user['permitted_entities'])){
                $builder->whereIn('entities.id', explode(',', $user['permitted_entities']));
            }else{
                $builder->where('entities.id', 0);
            }
        }
        $allowableEntities = $builder->get()->getResultArray();

        return $allowableEntities;
    }

}