<?php

namespace App\Libraries;

use App\Models\GroceryCrudModel;

class CoreLibrary
{

    protected $read_db;
    protected $write_db;
    protected $session;
    protected $feature;
    protected $segments;
    protected $uri;
    protected $action;
    protected $id;
    protected $request;
    public static $GC = true;
    protected $table;
    protected $crud;

    protected $customFieldsToInsert;
    public function __construct()
    {

        helper('setting');

        // Database con connections
        $this->read_db = \Config\Database::connect('read');
        $this->write_db = \Config\Database::connect('write');

        // Session variables
        $this->session = service('session');

        $this->uri = service('uri');
        $this->segments = $this->uri->getSegments();

        $this->feature = isset($this->segments[0]) ? $this->segments[0] : null;
        $this->action = isset($this->segments[1]) ? $this->segments[1] : 'list';
        $this->id = isset($this->segments[2]) ? $this->segments[2] : 0;

        $this->table = isset($this->feature) ? plural($this->feature) : null;

        $this->request = service('request');

        $this->crud = new CustomCrud();
    }

    private function getActionPermissionLabel($action)
    {
        $labels = [
            'add' => 'create',
            'edit' => 'update',
            'delete' => 'delete',
            'read' => 'read',
            'list' => 'read',
        ];
        return isset($labels[$action]) ? $labels[$action] : '';
    }

    function crudViewRender()
    {
        $page_data['page_name'] = $this->feature;
        $page_data['custom'] = false;
        $crud = new CustomCrud();

        $crud->setTable(plural($this->feature));
        $crud->setSubject(pascalize($this->feature));
        $crud->setRead();
        // $crud->setTheme('datatables');
        $crud->unsetFields(['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'created_at', 'created_by']);
        $crud->unsetCloneFields(['deleted_at', 'deleted_by', 'updated_at', 'updated_by', 'created_at', 'created_by']);
        // $crud->setLangString('form_edit', 'Update'); 
        // $crud->setPrint();


        // Only list items not deleted
        if ($this->action == 'ajax_list') {
            $crud->where($this->table . '.deleted_at', NULL);
        }

        $this->userPermissionControls($crud);
        // log_message('error', $this->action);

        if (class_exists("App\\Libraries\\" . pascalize($this->feature) . 'Library')) {
            $featureLibrary = new ("App\\Libraries\\" . pascalize($this->feature) . 'Library')();
            $this->callBacks($crud, $featureLibrary);
            $this->fieldControlFunctions($crud, $featureLibrary);
        }

        if ($this->feature != 'customfield') {
            $this->customFields($crud);
            // $this->customFieldsReadValues($crud);
            $this->getACtionedUsersNames($crud);
        }
        

        $output = $crud->render();
        $output->page_data = $page_data;

        return $this->output($output);
    }

    private function getACtionedUsersNames($crud){
        foreach(['created_by', 'updated_by'] as $fieldName){
            $feature = $this->feature;
            $crud->callbackReadField($fieldName, function ($fieldValue, $primaryKeyValue) use ($feature, $fieldName) {
                    $fullName = $primaryKeyValue;
                    $builder = $this->read_db->table(plural($feature));
                    $builder->where('id', $primaryKeyValue);
                    $userObj = $builder->get();
                    $user = '';
                    if($userObj->getNumRows() > 0) {
                        $action_by = $userObj->getRow()->{$fieldName};
                        $user = $this->callClassMethod('user','getUserById', $action_by);
                        $fullName = $user['first_name']. ' ' . $user['last_name'];
                    }
    
                    return $fullName;
        
            });
        }
    }

    private function customFieldsReadValues($crud){
        $customFieldsName = array_column($this->getAllowableResults('customfield'), 'name');
        foreach ($customFieldsName as $fieldName) {
            $crud->callbackReadField($fieldName, function ($fieldValue, $primaryKeyValue) use ($fieldName) {
                $builder = $this->read_db->table('customvalues');
                $builder->join('customfields','customfields.id=customvalues.customfield_id');
                $builder->where(array('record_id' => $primaryKeyValue,'customfields.name' => $fieldName));
                $valueObj = $builder->get();
                $value = '';
                if($valueObj->getNumRows() > 0) {
                    $value = $valueObj->getRow()->value;
                }
                return $value;
            });
            
        }
    }

    protected function customFields($crud)
    {

        $addFields = $this->callClassMethod($this->feature, 'addFields');
        $editFields = $this->callClassMethod($this->feature, 'editFields');

        $customFields = $this->getAllowableResults('customfield');

        if (!empty($customFields)) {
            $crud->addFields([...$addFields, ...array_column($customFields, 'name')]);
            $crud->editFields([...$editFields, ...array_column($customFields, 'name')]);
            foreach ($customFields as $field_info) {
                if (!is_null($field_info['options'])) {
                    $crud->fieldType($field_info['name'], $field_info['type'], explode(',', $field_info['options']));
                } else {
                    $crud->fieldType($field_info['name'], $field_info['type']);
                }
            }
        } else {
            $crud->addFields([...$addFields]);
            $crud->editFields([...$editFields]);
        }
    }
    protected function callClassMethod($featureName, $methodName, ...$params)
    {
        $result = [];
        $featureLibrary = new ('\\App\Libraries\\' . pascalize($featureName) . 'Library')();
        if (method_exists($featureLibrary, $methodName)) {
            $result = $featureLibrary->{$methodName}(...$params);
        }
        return $result;
    }

    protected function getRecordById($feature, $recordId)
    {
        $table = plural($feature);
        $builder = $this->read_db->table($table);
        $builder->where($table . '.id', $recordId);
        $builder->where($table . '.deleted_at', NULL);
        $result = $builder->get()->getRowArray();
        return $result;
    }

    protected function setSelectField($crud, $featureName, $fieldName, $multiselect = true)
    {
        $options = $this->getAllowableResults($featureName);
        if (!empty($options)) {
            $crud->fieldType($fieldName, $multiselect ? 'multiselect' : 'dropdown', transposeRecordArray($options));
        } else {
            $crud->fieldType($fieldName, $multiselect ? 'multiselect' : 'dropdown', ['' => '']);
        }
    }

    protected function getAllowableResults($featureName)
    {
        $hyphenedFeatures = [
            'customfield' => 'CustomField',
            'collectiontype' => 'CollectionType',
           'subscriptiontype' => 'SubscriptionType',
           'reporttype' => 'ReportType',
            'gatheringtype' => 'GatheringType',
            'customvalue' => 'CustomValue',
        ];

        $pascalizeFeatureName = pascalize($featureName);

        if(array_key_exists($featureName, $hyphenedFeatures)){
            $pascalizeFeatureName  = $hyphenedFeatures[$featureName];
        }

        $featureLibary = new ('\\App\\Libraries\\' . $pascalizeFeatureName . 'Library')();
        return $featureLibary->{'getAllowable' . plural($pascalizeFeatureName)}();
    }
    private function userPermissionControls(&$crud)
    {
        // $roleLibrary = new \App\Libraries\RoleLibrary();
        $stateInfo = $crud->getState();

        $roleLibrary = new \App\Libraries\RoleLibrary();

        $isItemDeleted = $this->isItemDeleted($this->id);
        // Prevent displaying a page if user has no permissions
        if ($isItemDeleted || !$roleLibrary->checkRoleHasPermission($this->session->get('role_ids'), $this->feature, $this->getActionPermissionLabel($this->action))) {
            // Redirect to error page
            // log_message('error', 'Hello! You have no permissions');
            $page_data['page_name'] = $this->feature;
            $page_data['action'] = 'error';
            $page_data['message_type'] = 'warning';
            $page_data['id'] = $this->id;
            $page_data['result'] = ['message' => "Permission denied to " . $this->action . " " . plural($this->feature) . ". Contact your administrator for further information."];
            $page_data['custom'] = true;
            // log_message('error', json_encode($page_data));
            return view('index', ['page_data' => $page_data]);
        }

        if ($stateInfo == 'list') {
            $checkRoleHasCreatePermission = $roleLibrary->checkRoleHasPermission($this->session->get('role_ids'), $this->feature, 'create');
            $checkRoleHasDeletePermission = $roleLibrary->checkRoleHasPermission($this->session->get('role_ids'), $this->feature, 'delete');
            $checkRoleHasEditPermission = $roleLibrary->checkRoleHasPermission($this->session->get('role_ids'), $this->feature, 'update');

            if (!$checkRoleHasCreatePermission) {
                $crud->unsetAdd();
                $crud->unsetClone();
            }
            if (!$checkRoleHasDeletePermission) {
                $crud->unsetDelete();
            }
            if (!$checkRoleHasEditPermission) {
                $crud->unsetEdit();
            }
        }
    }

    private function isItemDeleted($id)
    {
        $isItemDeleted = false;
        if (in_array($this->action, ['read', 'edit'])) {
            $builder = $this->read_db->table($this->table);
            $builder->where($this->table . '.id', $id);
            $builder->where($this->table . '.deleted_at IS NOT NULL');
            $countDeleted = $builder->get()->getNumRows();
            // log_message('error', $countDeleted);
            if ($countDeleted) {
                $isItemDeleted = true;
            }
        }
        return $isItemDeleted;
    }
    private function fieldControlFunctions(&$crud, $featureLibrary)
    {
        $fieldControlFunctions = [
            'columns',
            'addFields',
            'editFields',
            'unsetAddFields',
            'unsetEditFields',
            'cloneFields',
            'displayAs',
            'unsetColumns',
            'unsetReadFields',
            'where',
            'requiredFields',
            'unsetReadFields',
            'uniqueFields',
            'unsetCloneFields',
        ];

        foreach ($fieldControlFunctions as $fieldControlFunction) {
            if (method_exists($featureLibrary, $fieldControlFunction)) {
                // if(in_array($fieldControlFunction, ['addFields','editFields'])){
                // log_message('error', 'Hello');
                // $this->customFields($crud, $featureLibrary->{$fieldControlFunction}());
                // }
                if (!empty($featureLibrary->{$fieldControlFunction}())) {
                    $crud->{$fieldControlFunction}($featureLibrary->{$fieldControlFunction}());
                }
            }
        }

        if (method_exists($featureLibrary, 'buildCrud')) {
            $featureLibrary->buildCrud($crud);
        }
    }

    private function callBacks(&$crud, $featureLibrary)
    {
        $crud->callbackColumn('created_by', function ($id, $row) {
            $userLibrary = new UserLibrary();
            $user = $userLibrary->getUserById($row->created_by);
            $userFullName = get_phrase('not_set');
            if (!empty($user)) {
                $userFullName = $user['first_name'] . ' ' . $user['last_name'];
            }
            return $userFullName;
        });

        $crud->callbackBeforeInsert(function ($stateParameters) use ($featureLibrary) {
            // Seperate the custom fields before posting
            $customFieldsName = array_column($this->getAllowableResults('customfield'), 'name');
            $this->customFieldsToInsert = [];
            foreach ($stateParameters->data as $fieldName => $postData) {
                if (in_array($fieldName, $customFieldsName)) {
                    $this->customFieldsToInsert[$fieldName] = $postData;
                    unset($stateParameters->data[$fieldName]);
                }
            }

            if (method_exists($featureLibrary, 'callbackBeforeInsert')) {

                $stateParameters = $featureLibrary->callbackBeforeInsert($stateParameters);
                $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['created_by'] = $this->session->user_id;
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['updated_by'] = $this->session->user_id;
            } else {
                $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['created_by'] = $this->session->user_id;
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['updated_by'] = $this->session->user_id;
            }
            return $stateParameters;
        });

        $crud->callbackAfterInsert(function ($stateParameters) use ($featureLibrary) {
            $insertData = [];
            if (!empty($this->customFieldsToInsert)) {
                $i = 0;
                foreach ($this->customFieldsToInsert as $field => $value) {
                    $insertData[$i]['record_id'] = $stateParameters->insertId;
                    $insertData[$i]['customfield_id'] = $this->callClassMethod('customfield', 'getCustomFieldIdByFieldName', $field); //$this->getCustomFieldIdByFieldName($field);
                    $insertData[$i]['value'] = $value;
                    $insertData[$i]['created_at'] = date('Y-m-d H:i:s');
                    $insertData[$i]['created_by'] = $this->session->user_id;
                    $insertData[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $insertData[$i]['updated_by'] = $this->session->user_id;
                    $i++;
                }
                $builder = $this->write_db->table('customvalues');
                $builder->insertBatch($insertData);
            }

            if (method_exists($featureLibrary, 'callbackAfterInsert')) {
                $stateParameters = $featureLibrary->callbackAfterInsert($stateParameters);
                return $stateParameters;
            }
        });

        $crud->callbackBeforeUpdate(function ($stateParameters) use ($featureLibrary) {
            if (method_exists($featureLibrary, 'callbackBeforeUpdate')) {
                $stateParameters = $featureLibrary->callbackBeforeUpdate($stateParameters);
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['updated_by'] = $this->session->user_id;
            } else {
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['updated_by'] = $this->session->user_id;
            }
            // log_message('error', json_encode($stateParameters));
            return $stateParameters;
        });

        $crud->callbackAfterUpdate(function ($postObj) use ($featureLibrary) {
            if (method_exists($featureLibrary, 'callbackAfterUpdate')) {
                $postObj = $featureLibrary->callbackAfterUpdate($postObj);
                return $postObj;
            }
        });
    }

    protected function setRelationIdsCondition($table, $ids)
    {
        return $table . '.id IN (' . implode(',', $ids) . ')';
    }

    protected function output($output = null)
    {
        return view('index', (array) $output);
    }

    /** Custom pages */
    public function getCustomResults($table, $action, $id = 0)
    {
        if ($action == 'delete') {

        } elseif ($action == 'add') {
            if ($this->request->getPost()) {

            } else {

            }
        } elseif ($action == 'edit') {
            if ($this->request->getPost()) {

            } else {

            }
        } elseif ($action == 'read') {

        } else { // list action
            return $this->getData($table);
        }
    }

    function getData($table)
    {
        $crudModel = new GroceryCrudModel();
        $selectedColumns = array_column($crudModel->get_field_types($table), 'name');
        $builder = $this->read_db->table($table);

        if (class_exists("App\\Models\\" . pascalize(singular($table)) . 'Model')) {
            $model = model("App\\Models\\" . pascalize(singular($table)) . 'Model');
            $builder = $model->builder();
        }

        if (class_exists("App\\Libraries\\" . pascalize(singular($table)) . 'Library')) {
            $featureLibrary = new ("App\\Libraries\\" . pascalize(singular($table)) . 'Library')();
            if (method_exists($featureLibrary, 'queryBuilder')) {
                $featureLibrary->queryBuilder($builder);
            }
            if (method_exists($featureLibrary, 'whereFilter')) {
                $builder->where($featureLibrary->whereFilter());
            }
            if (method_exists($featureLibrary, 'selectColumns')) {
                if (!arrayIsList($featureLibrary->selectColumns())) {
                    $selectedColumns = array_values($featureLibrary->selectColumns());
                    $builder->select(array_keys($featureLibrary->selectColumns()));
                } else {
                    $selectedColumns = $featureLibrary->selectColumns();
                    $builder->select($featureLibrary->selectColumns());
                }

            }
        }

        $result = $builder->get();

        return ['columns' => $selectedColumns, 'data' => array_values($result->getResultArray())];
    }
}