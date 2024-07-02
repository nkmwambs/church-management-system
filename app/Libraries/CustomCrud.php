<?php 
namespace App\Libraries;

class CustomCrud extends GroceryCrud {
    protected $join = array();
    function __construct(){
        parent::__construct();
    }

    protected function set_default_Model()
    {
        $this->basic_model = new \App\Models\CustomCrudModel();
    }

    function join(array $join){
        $this->join = $join;
        return $this;
    }

    protected function get_list()
    {
        $this->basic_model->setBuilder($this->basic_db_table);

        if(!empty($this->join)){
            // Get the initial table join
            for($i = 0; $i < sizeof($this->join); $i++){
                list($table, $field_name, $related_table, $related_field_name,$joinCondition) = $this->join[$i];
                if($table == $this->basic_db_table){
                    if(!empty($joinCondition)){
                        $this->basic_model->where($joinCondition);
                    }
                    $this->basic_model->join($related_table,$related_table.".".$related_field_name."=".$this->basic_db_table.".".$field_name);
                    unset($this->join[$i]);
                    break;
                }
            }

            // Apply remaining join conditions to the joined tables
            foreach($this->join as $join){
                list($table, $field_name, $related_table, $related_field_name,$joinCondition) = $join;
                if(!empty($joinCondition)){
                    $this->basic_model->where($joinCondition);
                }
                $this->basic_model->join($related_table,$related_table.".".$related_field_name."=".$table.".".$field_name);
            }
            
        }

        if(!empty($this->order_by)) {
            $this->basic_model->order_by($this->order_by[0], $this->order_by[1]);
        }

        if(!empty($this->where)) {
            foreach($this->where as $where) {
                $this->basic_model->where($where[0],$where[1],$where[2]);
            }

        }

        if(!empty($this->or_where)) {
            foreach($this->or_where as $or_where) {
                $this->basic_model->or_where($or_where[0],$or_where[1],$or_where[2]);
            }
        }

        if(!empty($this->like))
            foreach($this->like as $like)
                $this->basic_model->like($like[0],$like[1],$like[2]);

        if(!empty($this->or_like))
            foreach($this->or_like as $or_like)
                $this->basic_model->or_like($or_like[0],$or_like[1],$or_like[2]);

        if(!empty($this->having))
            foreach($this->having as $having)
                $this->basic_model->having($having[0],$having[1],$having[2]);

        if(!empty($this->or_having))
            foreach($this->or_having as $or_having)
                $this->basic_model->or_having($or_having[0],$or_having[1],$or_having[2]);
        
        if(!empty($this->relation))
            foreach($this->relation as $relation)
                $this->basic_model->join_relation($relation[0],$relation[1],$relation[2]);

        if(!empty($this->relation_n_n))
        {
            $columns = $this->get_columns();
            foreach($columns as $column)
            {
                //Use the relation_n_n ONLY if the column is called . The set_relation_n_n are slow and it will make the table slower without any reason as we don't need those queries.
                if(isset($this->relation_n_n[$column->field_name]))
                {
                    $this->basic_model->set_relation_n_n_field($this->relation_n_n[$column->field_name]);
                }
            }

        }

        if($this->theme_config['crud_paging'] === true)
        {
            if($this->limit === null)
            {
                $default_per_page = $this->config->default_per_page;
                if(is_numeric($default_per_page) && $default_per_page >1)
                {
                    $this->basic_model->limit($default_per_page);
                }
                else
                {
                    $this->basic_model->limit(10);
                }
            }
            else
            {
                $this->basic_model->limit($this->limit[0],$this->limit[1]);
            }
        }

        $results = $this->basic_model->get_list();

        return $results;
    }

    // function get_list()
    // {
    //     list($field_name, $related_table, $related_field_name) = $this->join;
    //     $select = "{$this->table_name}.*,$related_table.*";
        
    //      if(!empty($this->relation))
    //      {
    //           foreach($this->relation as $relation)
    //           {
    //                list($field_name , $related_table , $related_field_title) = $relation;
    //                $unique_join_name = $this->_unique_join_name($field_name);
    //                $unique_field_name = $this->_unique_field_name($field_name);
                  
    //                 if(strstr($related_field_title,'{'))
    //                 {
    //                     $select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE({$unique_join_name}.",", ''),'"),str_replace("'","\'",$related_field_title))."') as $unique_field_name";
    //                 }
    //                 else
    //                 {  
    //                     $select .= ", $unique_join_name.$related_field_title as $unique_field_name";
    //                 }
                      
    //                if($this->field_exists($related_field_title))
    //                {
    //                     $select .= ", {$this->table_name}.$related_field_title as '{$this->table_name}.$related_field_title'";
    //                }
    //           }
    //       }
         
    //      $this->db->select($select, false);
    //      $this->db->join("$related_table","$related_table.$related_field_name = {$this->table_name}.$field_name");
    //      $results = $this->db->get($this->table_name)->result();
    //      return $results;
    // }

    protected function db_insert($state_info)
    {
        $validation_result = $this->db_insert_validation();

        if($validation_result->success)
        {
            $post_data = $state_info->unwrapped_data;

            if ($this->config->xss_clean) {
                $post_data = $this->filter_data_from_xss($post_data);
            }

            $add_fields = $this->get_add_fields();

            if($this->callback_insert === null)
            {
                if($this->callback_before_insert !== null)
                {
                    $stateParameters = (object)[
                        'data' => $post_data
                    ];
                    $callback_return = call_user_func($this->callback_before_insert, $stateParameters);

                    if(!empty($callback_return) && is_object($callback_return)) {
                        $post_data = $stateParameters->data;
                        $post_fields = array_keys($post_data);
                        $add_field_names = array_column((array)$add_fields,'field_name');
                        foreach($post_fields as $post_field){
                            if(!in_array($post_field, $add_field_names)){
                                $add_fields[] = (object)['field_name' => $post_field,'display_name' => $post_field];
                            }
                        }
                    } elseif($callback_return === false) {
                        return false;
                    }
                }

                $insert_data = array();
                $types = $this->get_field_types();
                foreach($add_fields as $num_row => $field)
                {
                    /* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
                    if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
                    {
                        $post_data[$field->field_name] = array();
                    }

                    if(array_key_exists($field->field_name, $post_data) && !isset($this->relation_n_n[$field->field_name]))
                    {
                        if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
                        {
                            $insert_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
                        {
                            $insert_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
                        {
                            $insert_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
                        {
                            //This empty if statement is to make sure that a readonly field will never inserted/updated
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
                        {
                            $insert_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
                            $insert_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
                                substr($post_data[$field->field_name],10);
                        }
                        else
                        {
                            $insert_data[$field->field_name] = $post_data[$field->field_name];
                        }
                    }
                }

                $insert_result =  $this->basic_model->db_insert($insert_data);

                if($insert_result !== false)
                {
                    $insert_primary_key = $insert_result;
                }
                else
                {
                    return false;
                }

                if(!empty($this->relation_n_n))
                {
                    foreach($this->relation_n_n as $field_name => $field_info)
                    {
                        $relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
                        $this->db_relation_n_n_update($field_info, $relation_data  ,$insert_primary_key);
                    }
                }

                if($this->callback_after_insert !== null)
                {
                    $stateParameters = (object)[
                        'data' => $post_data,
                        'insertId' => $insert_primary_key
                    ];

                    $callback_return = call_user_func($this->callback_after_insert, $stateParameters);

                    if($callback_return === false)
                    {
                        return false;
                    }

                }
            }else
            {
                $stateParameters = (object)[
                    'data' => $post_data
                ];
                $callback_return = call_user_func($this->callback_insert, $stateParameters);

                $insert_primary_key = property_exists($stateParameters, 'insertId')
                    ? $stateParameters->insertId : null;

                if($callback_return === false) {
                    return false;
                }

                return $insert_primary_key;
            }

            if(isset($insert_primary_key)) {
                return $insert_primary_key;
            }

            return true;
        }

        return false;

    }

    protected function db_update($state_info)
    {
        $validation_result = $this->db_update_validation();

        $edit_fields = $this->get_edit_fields();

        if($validation_result->success)
        {
            $post_data 		= $state_info->unwrapped_data;
            $primary_key 	= $state_info->primary_key;

            if ($this->config->xss_clean) {
                $post_data = $this->filter_data_from_xss($post_data);
            }

            if($this->callback_update === null)
            {
                if($this->callback_before_update !== null)
                {
                    $stateParameters = (object)[
                        'primaryKeyValue' => $primary_key,
                        'data' => $post_data
                    ];
                    $callbackReturn = call_user_func($this->callback_before_update, $stateParameters);

                    if(!empty($callbackReturn) && is_object($callbackReturn)) {
                        // $post_data = $callbackReturn->data;
                        $post_data = $callbackReturn->data;
                        $post_fields = array_keys($post_data);
                        $edit_field_names = array_column((array)$edit_fields,'field_name');
                        foreach($post_fields as $post_field){
                            if(!in_array($post_field, $edit_field_names)){
                                $edit_fields[] = (object)['field_name' => $post_field,'display_name' => $post_field];
                            }
                        }
                    } elseif($callbackReturn === false) {
                        return false;
                    }

                }

                $update_data = array();
                $types = $this->get_field_types();
                foreach($edit_fields as $num_row => $field)
                {
                    /* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
                    if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
                    {
                        $post_data[$field->field_name] = array();
                    }

                    if(array_key_exists($field->field_name, $post_data) && !isset($this->relation_n_n[$field->field_name]))
                    {
                        if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
                        {
                            $update_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
                        {
                            $update_data[$field->field_name] = null;
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
                        {
                            $update_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
                        {
                            //This empty if statement is to make sure that a readonly field will never inserted/updated
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
                        {
                            $update_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
                        }
                        elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
                            $update_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
                                substr($post_data[$field->field_name],10);
                        }
                        else
                        {
                            $update_data[$field->field_name] = $post_data[$field->field_name];
                        }
                    }
                }

                if($this->basic_model->db_update($update_data, $primary_key) === false)
                {
                    return false;
                }

                if(!empty($this->relation_n_n))
                {
                    foreach($this->relation_n_n as $field_name => $field_info)
                    {
                        if (   $this->unset_edit_fields !== null
                            && is_array($this->unset_edit_fields)
                            && in_array($field_name,$this->unset_edit_fields)
                        ) {
                            continue;
                        }

                        $relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
                        $this->db_relation_n_n_update($field_info, $relation_data ,$primary_key);
                    }
                }

                if($this->callback_after_update !== null)
                {
                    $stateParameters = (object)[
                        'primaryKeyValue' => $primary_key,
                        'data' => $post_data
                    ];

                    $callbackReturn = call_user_func($this->callback_after_update, $stateParameters);

                    if($callbackReturn === false)
                    {
                        return false;
                    }

                }
            }
            else
            {
                $stateParameters = (object)[
                    'primaryKeyValue' => $primary_key,
                    'data' => $post_data
                ];

                $callbackReturn = call_user_func($this->callback_update, $stateParameters);

                if($callbackReturn === false)
                {
                    return false;
                }
            }

            return true;
        }
        else
        {
            return false;
        }
    }
}