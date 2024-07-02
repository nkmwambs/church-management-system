<?php 

namespace App\Libraries;

use App\Libraries\GroceryCrud;

class AssemblyLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }
    
    public function requiredFields(){
        return ['name', 'entity_id', 'location', 'planted_at','assembly_leader'];
    }

    public function editFields(){
        return ['name', 'entity_id', 'location', 'planted_at','assembly_leader'];
    }

    public function columns(){
        return ['name', 'entity_id', 'location', 'planted_at','assembly_leader', 'created_at', 'created_by'];
    }

    public function buildCrud($crud) {
        $crud->displayAs(['entity_id' => get_phrase('entity')]); 

        // Only drop first level enties when creating an assembly
        $firstLevelEntityIds = $this->firstLevelEntityIds();
        $firstLevelEntityConditionString = $this->setRelationIdsCondition('entities', $firstLevelEntityIds);
        $crud->setRelation('entity_id', 'entities', 'name',$firstLevelEntityConditionString);
        $crud->setRelation('assembly_leader', 'members', '{first_name} {last_name} - {phone}');

        // $feature = $this->feature;
        // $crud->setActionButton('More', 'entypo-dot-3', function ($id) use($feature) {
        //     return $feature.'/read/' . $id;
        // }, true);

        $crud->join(
            [
                ['assemblies','entity_id', 'entities', 'id', []],
                ['entities','hierarchy_id', 'hierarchies', 'id', !$this->session->system_admin ? ['hierarchies.denomination_id' => $this->session->denomination_id] : []]
            ]
        );
    }

    public function getAllowableAssemblies(){
        $builder = $this->read_db->table('assemblies');
        $builder->select('assemblies.id, assemblies.name');
        $builder->join('entities', 'entities.id=assemblies.entity_id');
        $builder->join('hierarchies', 'hierarchies.id=entities.hierarchy_id');
        if(!$this->session->system_admin){
            $builder->where(['hierarchies.denomination_id' => $this->session->denomination_id]);
        }
        $allowableAssemblies = $builder->get()->getResultArray();
        // $ids = array_column($allowableAssemblies, 'id');
        // $names = array_column($allowableAssemblies, 'name');
        // $keyedArray = array_combine($ids, $names);
        return $allowableAssemblies;
    }

    private function firstLevelEntityIds(){
        $builder = $this->read_db->table('entities');
        $builder->select('entities.id');
        $builder->where('level', 1);
        $builder->join('hierarchies', 'hierarchies.id=entities.hierarchy_id');
        if(!$this->session->system_admin){
            $builder->where(['hierarchies.denomination_id' => $this->session->denomination_id]);
        }
        $firstLevelEntityIds = $builder->get()->getResultArray();
        $firstLevelEntityIdsArray = array_column($firstLevelEntityIds, 'id');
        return $firstLevelEntityIdsArray;
    }
}   