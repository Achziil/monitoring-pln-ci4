<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class AuthController extends Controller
{

    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    public function login()
    {
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Login', 'pagetitle' => 'Monitoring Anggaran Biaya Administrasi UP3']),
        ];

        return view('pages/auth-login', $data);
    }

    public function authenticate()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('username', $username)->first();

        log_message('debug', 'User data: ' . print_r($user, true));
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $ses_data = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'level' => $user['level'],
                    'nickname' => $user['nickname'],
                    'busa' => $user['busa'],
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);

                // Redirect based on user's role
                switch ($user['level']) {
                    case 'admin':
                        return redirect()->to('/admin/dashboard');
                    case 'wilayah':
                        return redirect()->to('/wilayah/dashboard');
                    case 'pelaksana':
                        return redirect()->to('/pelaksana/dashboard');
                    default:
                        return redirect()->to('/login');
                }
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Username Tidak Ditemukan');
            return redirect()->to('/login');
        }
    }


    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
