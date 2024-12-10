<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\FeedbackModel;

class Admin extends Controller
{
    public function __construct()
    {
        // Check if user is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }
    }

    public function dashboard()
    {
        $feedbackModel = new FeedbackModel();
        $data['unreadCount'] = $feedbackModel->getUnreadCount();
        
        return view('admin/dashboard', $data);
    }
}