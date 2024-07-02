<?php

namespace App\Controllers;

class Entity extends BaseController
{
   function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
    parent::initController($request, $response, $logger);
   }
}
