<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
  protected $userModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
    helper(['form', 'url']);
  } 

  public function login()
  {
    if ($this->request->is('post')) {
      $rules = [
        'username' => 'required',
        'password' => 'required|min_length[6]'
      ];

      if ($this->validate($rules)) {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
          $session = session();
          $sessionData = [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => TRUE
          ];
          $session->set($sessionData);

          // Redirect based on role
          switch ($user['role']) {
            case 'admin':
              return redirect()->to('/admin/dashboard');
            case 'guru':
              return redirect()->to('/guru/dashboard');
            case 'siswa':
              return redirect()->to('/siswa/dashboard');
            default:
              return redirect()->to('/');
          }
        }

        return redirect()->back()->with('error', 'Invalid username or password');
      }
    }

    return view('auth/login');
  }

  public function register()
  {
    if ($this->request->is('post')) {
      $rules = [
        'username' => 'required|min_length[4]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'confirm_password' => 'required|matches[password]'
      ];

      if ($this->validate($rules)) {
        $data = [
          'username' => $this->request->getPost('username'),
          'email' => $this->request->getPost('email'),
          'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
          'role' => 'siswa'
        ];

        $this->userModel->save($data);
        return redirect()->to('/login')->with('success', 'Registration successful! Please login.');
      } else {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
      }
    }

    return view('auth/register');
  }

  public function logout()
  {
    $session = session();
    $session->destroy();
    return redirect()->to('/');
  }
}
