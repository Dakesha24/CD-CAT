<?php
namespace App\Controllers\Guru;

use CodeIgniter\Controller;

class Guru extends Controller
{
    public function dashboard()
    {
        return view('guru/dashboard');
    }
}