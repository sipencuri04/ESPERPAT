<?php

namespace App\Controllers\Api;

use App\Models\UserModel;

class UserController extends BaseApiController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role = $this->request->getGet('role');

        if ($role) {
            $users = $this->userModel->where('role', $role)->findAll();
        } else {
            $users = $this->userModel->findAll();
        }

        // Remove sensitive fields
        foreach ($users as &$user) {
            unset($user['password'], $user['verification_token']);
        }

        return $this->successResponse($users);
    }

    public function create()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,customer]',
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);
        $data['password']          = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['email_verified_at'] = date('Y-m-d H:i:s');
        $data['is_active']         = 1;

        $this->userModel->insert($data);
        $user = $this->userModel->find($this->userModel->getInsertID());
        unset($user['password'], $user['verification_token']);

        return $this->successResponse($user, 'User berhasil ditambahkan.', 201);
    }

    public function update($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan.', 404);
        }

        // Cannot modify superuser
        if ($user['role'] === 'superuser') {
            return $this->errorResponse('Tidak dapat mengubah akun superuser.', 403);
        }

        $data = $this->request->getJSON(true);
        $updateData = [];

        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['role'])) $updateData['role'] = $data['role'];
        if (isset($data['is_active'])) $updateData['is_active'] = $data['is_active'];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);
        $user = $this->userModel->find($id);
        unset($user['password'], $user['verification_token']);

        return $this->successResponse($user, 'User berhasil diupdate.');
    }

    public function delete($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan.', 404);
        }

        if ($user['role'] === 'superuser') {
            return $this->errorResponse('Tidak dapat menghapus akun superuser.', 403);
        }

        $this->userModel->delete($id);
        return $this->successResponse(null, 'User berhasil dihapus.');
    }
}
