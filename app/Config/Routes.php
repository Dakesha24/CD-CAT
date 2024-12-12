<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->get('guide', 'Home::guide');
$routes->get('profile', 'Home::profile');
$routes->get('contact', 'Home::contact');
$routes->get('about', 'Home::about');
$routes->get('faq', 'Home::faq');
$routes->get('bantuan', 'Home::bantuan');

// Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
  $routes->get('dashboard', 'Admin::dashboard');
  $routes->get('feedback', 'Feedback::index');
  $routes->post('feedback/mark-read/(:num)', 'Feedback::markRead/$1');
  $routes->post('feedback/mark-all-read', 'Feedback::markAllRead');
  $routes->post('feedback/delete/(:num)', 'Feedback::delete/$1');
  $routes->post('feedback/delete-selected', 'Feedback::deleteSelected');
});



// User routes
$routes->get('user/dashboard', 'User::dashboard');

$routes->get('contact', 'Contact::index');
$routes->post('contact/submit', 'Contact::submit');