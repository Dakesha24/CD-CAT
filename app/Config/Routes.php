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
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
  $routes->get('dashboard', 'Admin::dashboard');
  // ... routes admin lainnya
});

// Guru routes
$routes->group('guru', ['namespace' => 'App\Controllers\Guru'], function ($routes) {
  $routes->get('dashboard', 'Guru::dashboard');
  $routes->get('jenis-ujian', 'Guru::jenisUjian');
  $routes->get('ujian', 'Guru::ujian');
  $routes->get('jadwal-ujian', 'Guru::jadwalUjian');
  $routes->get('hasil-ujian', 'Guru::hasilUjian');
  $routes->get('pengumuman', 'Guru::pengumuman');

  $routes->post('jenis-ujian/tambah', 'Guru::tambahJenisUjian');
  $routes->post('jenis-ujian/edit/(:num)', 'Guru::editJenisUjian/$1');
  $routes->get('jenis-ujian/hapus/(:num)', 'Guru::hapusJenisUjian/$1');

  $routes->post('ujian/tambah', 'Guru::tambahUjian');
  $routes->post('ujian/edit/(:num)', 'Guru::editUjian/$1');
  $routes->get('ujian/hapus/(:num)', 'Guru::hapusUjian/$1');

  $routes->get('soal/(:num)', 'Guru::kelolaSoal/$1');
  $routes->post('soal/tambah', 'Guru::tambahSoal');
  $routes->post('soal/edit/(:num)', 'Guru::editSoal/$1');
  $routes->get('soal/hapus/(:num)/(:num)', 'Guru::hapusSoal/$1/$2');

  $routes->post('jadwal-ujian/tambah', 'Guru::tambahJadwal');
  $routes->post('jadwal-ujian/edit/(:num)', 'Guru::editJadwal/$1');
  $routes->get('jadwal-ujian/hapus/(:num)', 'Guru::hapusJadwal/$1');

  $routes->post('pengumuman/tambah', 'Guru::tambahPengumuman');
  $routes->post('pengumuman/edit/(:num)', 'Guru::editPengumuman/$1');
  $routes->get('pengumuman/hapus/(:num)', 'Guru::hapusPengumuman/$1');

  $routes->get('hasil-ujian', 'Guru::hasilUjian');
  $routes->get('hasil-ujian/siswa/(:num)', 'Guru::daftarSiswa/$1');
  $routes->get('hasil-ujian/detail/(:num)', 'Guru::detailHasil/$1');

  $routes->get('profil', 'Guru::profil');
  $routes->post('profil/save', 'Guru::saveProfil');
});

$routes->get('guru/hasil-ujian/download-excel-html/(:num)', 'Guru\Guru::downloadExcelHTML/$1');
$routes->get('guru/hasil-ujian/download-pdf-html/(:num)', 'Guru\Guru::downloadPDFHTML/$1');

// Siswa routes
$routes->group('siswa', ['namespace' => 'App\Controllers\Siswa'], function ($routes) {
  $routes->get('dashboard', 'Siswa::dashboard');
  $routes->get('pengumuman', 'Siswa::pengumuman');
  $routes->get('ujian', 'Siswa::ujian');
  $routes->get('hasil', 'Siswa::hasil');
  $routes->get('hasil/detail/(:num)', 'Siswa::detailHasil/$1');
  $routes->get('profil', 'Siswa::profil');
  $routes->post('profil/save', 'Siswa::saveProfil');
  $routes->post('ujian/mulai', 'Siswa::mulaiUjian');
  $routes->get('ujian/soal/(:num)', 'Siswa::soal/$1');
  $routes->get('ujian/selesai/(:num)', 'Siswa::selesaiUjian/$1');
  $routes->get('hasil/review/(:num)', 'Siswa::review/$1');
  $routes->post('ujian/simpan-jawaban', 'Siswa::simpanJawaban');
  $routes->post('ujian/mulai', 'Siswa::mulaiUjian');
  $routes->get('ujian/soal/(:num)', 'Siswa::soal/$1');

  $routes->get('hasil/unduh/(:num)', 'Siswa::unduh/$1');
});
