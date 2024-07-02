<?php 

namespace App\Models;

class CustomCrudModel extends GroceryCrudModel {

    function get_list()
    {
    	if($this->table_name === null)
    		return false;

    	$select = "`{$this->table_name}`.*";

    	//set_relation special queries
    	if(!empty($this->relation))
    	{
    		foreach($this->relation as $relation)
    		{
    			list($field_name , $related_table , $related_field_title) = $relation;
    			$unique_join_name = $this->_unique_join_name($field_name);
    			$unique_field_name = $this->_unique_field_name($field_name);

				if(strstr($related_field_title,'{'))
				{
					$related_field_title = str_replace(" ","&nbsp;",$related_field_title);
    				$select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE({$unique_join_name}.",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $unique_field_name";
				}
    			else
    			{
    				$select .= ", $unique_join_name.$related_field_title AS $unique_field_name";
    			}

    			if($this->field_exists($related_field_title))
    				$select .= ", `{$this->table_name}`.$related_field_title AS '{$this->table_name}.$related_field_title'";
    		}
    	}

    	//set_relation_n_n special queries. We prefer sub queries from a simple join for the relation_n_n as it is faster and more stable on big tables.
    	if(!empty($this->relation_n_n))
    	{
			$select = $this->relationNtoNQueries($select);
    	}

        $this->builder = $this->builder->select($select, false);
		
		// Always order by lasted ids first
		$this->builder->orderBy($this->get_primary_key(), 'desc');

    	$results = $this->builder->get()->getResult();

    	$this->builder = null;

    	return $results;
    }

    function db_delete($primary_key_value)
    {
    	$primary_key_field = $this->get_primary_key();

    	if($primary_key_field === false || empty($primary_key_value)) {
            return false;
        }

        $this->db->table($this->table_name)->where(array( $primary_key_field => $primary_key_value))
        ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return true;
    }
}