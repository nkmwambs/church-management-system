<?php 

namespace App\Libraries;

class FeatureLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    public function getFeatures() {
        $builder = $this->read_db->table('features');
        $features = $builder->get()->getResultObject();

        return $features;
    }
}