<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDB::index');
$routes->get('excel', 'Excel::index');
$routes->post('excel/import', 'Excel::import');
$routes->get('/dashboard', 'Excel::dashboard');
$routes->get('/ver/clientes', 'Excel::verClientes');
$routes->get('/ver/productos', 'Excel::verProductos');
$routes->get('/ver/ventas', 'Excel::verVentas');
$routes->post('eliminar/(:num)', 'Excel::eliminar/$1');
$routes->get('obtener/(:segment)/(:num)', 'Excel::obtener/$1/$2');
$routes->post('actualizar/(:segment)/(:num)', 'Excel::actualizar/$1/$2');

