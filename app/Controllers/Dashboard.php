<?php

namespace App\Controllers;

use App\Libraries\CoreLibrary;

class Dashboard extends BaseController
{

    function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
        parent::initController($request, $response, $logger);
    }
    
}
