<?php

namespace App\Models;

use CodeIgniter\Model;

class HierarchyModel extends Model
{
    protected $table            = 'hierarchies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = \App\Entities\Hierarchy::class; //'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['actionBeforeInsert'];
    protected $afterInsert    = ['actionAfterInsert'];
    protected $beforeUpdate   = ['actionBeforeUpdate'];
    protected $afterUpdate    = ['actionAfterUpdate'];
    protected $beforeFind     = ['actionBeforeFind'];
    protected $afterFind      = ['actionAfterFind'];
    protected $beforeDelete   = ['actionBeforeDelete'];
    protected $afterDelete    = ['actionAfterDelete'];

    protected function initialize()
    {
        $this->allowedFields = ['name','denomination_id','level','options','key','created_at','created_by','updated_at','updated_by'];
    }

    function actionBeforeFind($data){
        
    }

    function actionAfterFind($data){
        // log_message('error', json_encode($data));
        return $data;
    }

    function actionBeforeUpdate($data){
        // log_message('error', json_encode($data));
        return $data;
    }

    function actionAfterUpdate($data){
        return $data;
    }

    function actionBeforeInsert($data){
        return $data;
    }

    function actionAfterInsert($data){
        return $data;
    }
}
