<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Login::index', ['as' => 'login']);
$routes->get('/login/index', 'Login::index');
$routes->get('/login/logout', 'Login::logout');
$routes->post('/login', 'Login::ajax_login');

$featureLibrary = new \App\Libraries\FeatureLibrary();
$features = $featureLibrary->getFeatures();

foreach ($features as $feature) {
    $routeBase = strtolower(pascalize($feature->name));
    $controllerName = pascalize($feature->name);

    $routes->group($routeBase, static function ($routes) use ($controllerName) {
        $routes->get('systemReset', $controllerName.'::systemReset');
        $routes->add('/', $controllerName.'::index');
        $routes->add('(:segment)(/(:segment))?', $controllerName.'::index/$1/$2');
    });
}

$routes->post('ajax/(:segment)/(:segment)(/(:segment))?', 'Ajax::renderReponse/$1/$2/$3');
$routes->get('participant/mark_attendance/(:segment)/(:segment)', 'Participant::mark_attendance/$1/$2');

