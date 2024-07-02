<?php

namespace App\Controllers;

class Ajax extends BaseController
{
   function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
    parent::initController($request, $response, $logger);
   }

   function renderReponse($feature, $ajaxMethod, ...$params){
      $response = null;

      if(class_exists("App\\Libraries\\" . pascalize($feature).'Library')){
         $featureLibrary = new ("App\\Libraries\\" . pascalize($feature).'Library')();
         // $response = $featureLibrary->{$ajaxMethod}(...$params);
         $postData = $this->request->getPost();
         $response = $featureLibrary->{$ajaxMethod}($postData);
      }
    
    return $this->response->setJSON($response);
   }

}
