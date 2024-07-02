<?php 
namespace App\Entities;

use CodeIgniter\Entity\Entity;
class Hierarchy extends Entity {

    protected $casts = [
        'options' => 'csv',
        'key' => 'base64',
    ];
    
     // Bind the type to the handler
     protected $castHandlers = [
        'base64' => Cast\CastBase64::class,
    ];
    // protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $attributes = [
        'name',
        'denomination_id',
        'level',
        'options',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    protected $datamap = [
        'title' => 'name'
    ];

    function setTitle($name){
        $this->attributes['name'] = ucwords($name);
        return $this;
    }
    
    function getTitle(){
        return strtolower($this->attributes['name']);
    }
}
