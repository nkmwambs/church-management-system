<?php 

if(!function_exists('get_phrase')){
    function get_phrase($phrase_key, $phrase_translation = ''){
        $translated = $phrase_translation;
        if($phrase_translation == ""){
            $translated = humanize($phrase_key,'_');
        }
        return $translated;
    }
}