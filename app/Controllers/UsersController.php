<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UsersController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->findAll();
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Users', 'pagetitle' => 'Monitoring Administrasi']),
            'users' => $users,
        ];

        return view('user/index', $data);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|is_unique[users.username]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'nickname' => 'required',
                'level' => 'required',
                'busa' => 'required|integer',
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $data = [
                    'username' => $this->request->getPost('username'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'nickname' => $this->request->getPost('nickname'),
                    'level' => $this->request->getPost('level'),
                    'busa' => $this->request->getPost('busa'),
                ];

                if ($this->userModel->save($data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User created successfully']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to create user']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }
        }
    }

    public function edit($id)
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|is_unique[users.username,id,' . $id . ']',
                'nickname' => 'required',
                'level' => 'required',
                'password' => 'permit_empty|min_length[6]',
                'busa' => 'required|integer',
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $data = [
                    'username' => $this->request->getPost('username'),
                    'nickname' => $this->request->getPost('nickname'),
                    'level' => $this->request->getPost('level'),
                    'busa' => $this->request->getPost('busa'),
                ];

                $password = $this->request->getPost('password');
                if (!empty($password)) {
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                if ($this->userModel->update($id, $data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil di perbarui']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui user']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            if ($this->userModel->delete($id)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
            }
        }
    }

    // UsersController.php
    public function view($id)
    {
        if ($this->request->isAJAX()) {
            $user = $this->userModel->find($id);
            if ($user) {
                return $this->response->setJSON(['success' => true, 'data' => $user]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }
        }
    }

    public function editProfile()
    {
        $user = $this->userModel->find(session()->get('id'));
        $data = [
            'page_title' => view('layouts/partials/page_title', ['title' => 'Edit Profile', 'pagetitle' => 'Monitoring ']),
            'user' => $user,
        ];

        return view('user/edit_profile', $data);
    }

    public function updateProfile()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|is_unique[users.username,id,' . session()->get('id') . ']',
                'password' => 'permit_empty|min_length[5]',
                'confirm_password' => 'matches[password]',
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $data = [
                    'username' => $this->request->getPost('username'),
                ];

                if (!empty($this->request->getPost('password'))) {
                    $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                }

                if ($this->userModel->update(session()->get('id'), $data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Profil berhasil diperbaharui']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah data profil']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }
        }
    }
}
