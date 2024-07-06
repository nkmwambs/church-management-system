<?php 

namespace App\Libraries;

class HierarchyLibrary extends CoreLibrary {
    public static $GC = false;
    protected $model;
    public function __construct() {
        parent::__construct();

        $this->model = model('HierarchyModel', true, $this->read_db);
    }
    
    public function columns(){
        $fields = ['denomination_id', 'name', 'level', 'created_at', 'created_by'];
        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    public function editFields(){
        $fields = ['denomination_id', 'name'];

        if(!$this->session->get('system_admin')){
            unset($fields[array_search('denomination_id',$fields)]);
        }
        return $fields;
    }

    public function requiredFields(){
        return ['denomination_id','name', 'level'];
    }

    public function addFields(){
        $fields = ['denomination_id','level', 'name'];

        if(!$this->session->get('system_admin')){
            // unset($fields[array_search('denomination_id',$fields)]);
        }

        return $fields;
    }

    public function where(){
        $condtionArray = '';
        if(!$this->session->get('system_admin')){
            // log_message('error', json_encode($this->session->get('denomination_id')));
            $condtionArray = "denomination_id = ".$this->session->get('denomination_id');
        }   
        return $condtionArray;
    }

    public function buildCrud($crud) {
        $crud->displayAs(['denomination_id' => get_phrase('denomination')]); 
        $crud->setRelation('denomination_id', 'denominations', 'name');
        // $crud->uniqueFields(['level']); 

        if($this->action == 'add'){
            $nextAvailableLevel = $this->lastUsedDenominationHierarchyLevel($this->session->get('denomination_id')) + 1;
            $crud->fieldType('level', 'dropdown', [
                $nextAvailableLevel => get_phrase('level'). ' ' . $nextAvailableLevel,
            ]);
        }

        $crud->callbackBeforeDelete([$this, 'validateDeleteDenominationIdHierarachy']);
    }

    function validateDeleteDenominationIdHierarachy($stateParameters){
        // Prevent deleting hierarchies below while we have other on top
        $id = $stateParameters->primaryKeyValue;
        
        $builder = $this->read_db->table('hierarchies');
        $builder->where('id', $id);
        $hierarchy = $builder->get()->getRow();

        $currentLevel = $hierarchy->level;
        
        $builder = $this->read_db->table('hierarchies');
        $builder->where('level > ', $currentLevel);
        $hierarchyCount = $builder->countAllResults();

        if($hierarchyCount == 0){
            return $stateParameters;
        }

        return false;
    }

    function callbackBeforeInsert($stateParameters) {
        // Set the denomination_id if not provided in the request data.
        if(!isset($stateParameters->data['denomination_id'])){
            $stateParameters->data['denomination_id'] = $this->session->get('denomination_id');
        }

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

    private function lastUsedDenominationHierarchyLevel($denomination_id){
        $builder = $this->read_db->table('hierarchies');
        $builder->selectMax('level');
        $builder->where('denomination_id', $denomination_id);
        $hierarchyObj = $builder->get();

        $lastUsedLevel = 0;
        
        if($hierarchyObj->getNumRows() > 0){
            $lastUsedLevel = array_column($hierarchyObj->getResultArray(),'level')[0];
        } 

        return $lastUsedLevel;
    }

    public function getNextHierarchyLevel($denomination_id){
        $nextAvailableLevel = $this->lastUsedDenominationHierarchyLevel($denomination_id) + 1;
        return [['levelNumber' => $nextAvailableLevel, 'levelName' => get_phrase('level'). ' ' . $nextAvailableLevel]];
    }

    public function getAllowableHierarchies(){
        $builder = $this->read_db->table('hierarchies');
        $builder->select('id,name,level');
        if(!$this->session->system_admin){
            $builder->where('denomination_id', $this->session->get('denomination_id'));
        }
        $denominationHierarchiesObj = $builder->get();

        $denominationHierarchies = [];

        if($denominationHierarchiesObj->getNumRows() > 0){
            $denominationHierarchies = $denominationHierarchiesObj->getResultArray();
        }

        return $denominationHierarchies;
    }

    // function selectColumns(){
    //     return ['id','name','level'];
    // }
    // function getCustomResults($table, $action, $id = 0){
    //     // $id = 52;
    //     // $hierarchy = $this->model->find($id);
    //     // log_message('error', json_encode($hierarchy->toArray()));
    //     // log_message('error', $hierarchy->key);
    //     // log_message('error', $hierarchy->getName());
    //     // unset($hierarchy->name);
    //     // if(!isset($hierarchy->name)){
    //     //     $hierarchy->name = "Ministry";
    //     // }
    //     // $this->model->save($hierarchy);

    //     // $hierarchy = new \App\Entities\Hierarchy();
    //     // $hierarchy->setTitle("jesus christ"); // $hierarchy->name = "jesus christ";
    //     // $hierarchy->denomination_id = 1;
    //     // $hierarchy->options = ['foo','bar','doo'];//[['foo' => 'bar'],['doo' => 'baz']];
    //     // $hierarchy->level = 4;
    //     // $hierarchy->key='I Love Jesus Christ';
    //     // $this->model->save($hierarchy);

    //     $columns = ['id', 'name','email',];
    //     $data = [
    //         ['id' => 1, 'name' => 'Joe Doe', 'email' => 'joe@example.com'],
    //         ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
    //         ['id' => 3, 'name' => 'John Doe', 'email' => 'john@example.com'],
    //     ];

    //     return ['columns' => $columns, 'data' => $data];
    // }
}   