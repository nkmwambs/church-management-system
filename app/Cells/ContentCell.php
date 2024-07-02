<?php

namespace App\Cells;

class ContentCell
{
    public function show($params): string
    {
        extract($params['page_data']);
        $feature = isset($page_name) ? $page_name : '';
        $action = isset($action) ? $action : '';
        $component_path = "components".DIRECTORY_SEPARATOR;
        $feature_view_path = $feature.DIRECTORY_SEPARATOR;

        if(file_exists(VIEW_PATH.$feature)){
            $featurePath = VIEW_PATH.$feature.DIRECTORY_SEPARATOR;
            if (file_exists($featurePath.$action.'.tpl.php')) {
                return view($component_path.$action, ['result' => $result, 'feature' => $feature, 'action' => $action]);
            }elseif(file_exists($featurePath.$action.'.php')){
                return view($feature_view_path.$action, ['result' => $result, 'feature' => $feature, 'action' => $action]);
            }
        }
       
        if($action == 'error'){
            return view("components/error", ['result' => $result, 'message_type' => $message_type]); 
        }
        return view("components/content", ['feature' => $feature, 'action' => $action]);
    }
}