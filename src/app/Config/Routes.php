<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('comments', 'Home::comments');
$routes->post('comments', 'Home::create');
$routes->post('comments/(:num)/delete', 'Home::delete/$1');
