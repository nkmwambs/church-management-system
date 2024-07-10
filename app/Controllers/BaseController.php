<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Libraries\CoreLibrary;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $read_db;
    protected $write_db;
    protected $session;

    protected $feature;
    protected $segments;
    protected $uri;
    protected $action;
    protected $id;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        $this->session = \Config\Services::session();

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

        if(!service('settings')->get('App.siteName')){
            service('settings')->set('App.siteName', 'Church Management System');
        }

    }

    function index()
    {
        if(file_exists(APPPATH.'Views/'.$this->feature.'/'.$this->action.'.php')
         || file_exists(APPPATH.'Views/'.$this->feature.'/'.$this->action.'.tpl.php')){
            $featureLibary = new ("App\\Libraries\\" . pascalize($this->feature).'Library')();
            $page_data['page_name'] = $this->feature;
            $page_data['action'] = $this->action;
            $page_data['id'] = $this->id;
            $page_data['result'] = $featureLibary->getCustomResults(plural($this->feature), $this->action, $this->id);
            $page_data['custom'] = true;
            // log_message('error', json_encode($page_data));
            return view('index', ['page_data' => $page_data]);
        }else{
            $core = new CoreLibrary();
            return $core->crudViewRender();
        }
    }

    public function systemReset(){
        $coreLibrary = new CoreLibrary();
        $coreLibrary->truncateTables();
        return redirect()->to('/dashboard');
    }

}
