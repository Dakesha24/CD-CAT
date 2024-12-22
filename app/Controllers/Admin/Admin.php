<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\FeedbackModel;

class Admin extends Controller
{
    public function dashboard()
    {
        return view('admin/dashboard');
    }
}