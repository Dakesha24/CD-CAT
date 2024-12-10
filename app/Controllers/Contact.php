<?php
// app/Controllers/Contact.php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FeedbackModel;

class Contact extends Controller
{
    protected $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
    }

    public function index()
    {
        return view('pages/contact');
    }

    public function submit()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'message' => 'required|min_length[10]'
        ];

        if ($this->validate($rules)) {
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'message' => $this->request->getPost('message')
            ];

            $this->feedbackModel->insert($data);
            return redirect()->to('/contact')->with('success', 'Terima kasih! Pesan Anda telah terkirim.');
        } else {
            return redirect()->to('/contact')
                           ->with('error', 'Mohon periksa kembali input Anda.')
                           ->withInput();
        }
    }
}
