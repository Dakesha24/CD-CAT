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
  // ... routes admin lainnya
});

// Guru routes
$routes->group('guru', ['namespace' => 'App\Controllers\Guru'], function($routes) {
  $routes->get('dashboard', 'Guru::dashboard');
  // ... routes guru lainnya
});

// Siswa routes
$routes->group('siswa', ['namespace' => 'App\Controllers\Siswa'], function($routes) {
  $routes->get('dashboard', 'Siswa::dashboard');
  $routes->get('pengumuman', 'Siswa::pengumuman');
  $routes->get('ujian', 'Siswa::ujian');
  $routes->get('hasil', 'Siswa::hasil');
  $routes->get('profil', 'Siswa::profil');
  $routes->post('profil/save', 'Siswa::saveProfil');
  $routes->post('ujian/mulai', 'Siswa::mulaiUjian');
  $routes->get('ujian/soal/(:num)', 'Siswa::soal/$1');
  $routes->get('ujian/selesai/(:num)', 'Siswa::selesaiUjian/$1');
  $routes->get('hasil/review/(:num)', 'Siswa::review/$1');
  $routes->post('ujian/simpan-jawaban', 'Siswa::simpanJawaban');
});