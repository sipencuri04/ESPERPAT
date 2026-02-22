<?php

namespace App\Controllers\Api;

use App\Services\AuthService;
use App\Models\UserModel;

class AuthController extends BaseApiController
{
    protected AuthService $authService;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userModel   = new UserModel();
    }

    public function register()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'phone'    => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $result = $this->authService->register($this->request->getJSON(true));

        if ($result['success']) {
            return $this->successResponse($result, $result['message'], 201);
        }

        return $this->errorResponse($result['message']);
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data   = $this->request->getJSON(true);
        $result = $this->authService->login($data['email'], $data['password']);

        if ($result['success']) {
            return $this->successResponse($result, $result['message']);
        }

        return $this->errorResponse($result['message'], 401);
    }

    public function profile()
    {
        $userData = $this->getUserData();
        $user = $this->userModel->find($userData->id);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan.', 404);
        }

        unset($user['password'], $user['verification_token']);

        return $this->successResponse($user);
    }

    public function updateProfile()
    {
        $userData = $this->getUserData();
        $data = $this->request->getJSON(true);

        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['phone'])) $updateData['phone'] = $data['phone'];
        if (isset($data['address'])) $updateData['address'] = $data['address'];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (!empty($updateData)) {
            $this->userModel->update($userData->id, $updateData);
        }

        $user = $this->userModel->find($userData->id);
        unset($user['password'], $user['verification_token']);

        return $this->successResponse($user, 'Profile berhasil diupdate.');
    }

    public function verify($token)
    {
        $result = $this->authService->verifyEmail($token);

        if ($result['success']) {
            return $this->successResponse(null, $result['message']);
        }

        return $this->errorResponse($result['message']);
    }

    public function resendVerification()
    {
        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Email tidak valid.', 422);
        }

        $data   = $this->request->getJSON(true);
        $result = $this->authService->resendVerification($data['email']);

        if ($result['success']) {
            return $this->successResponse(null, $result['message']);
        }

        return $this->errorResponse($result['message']);
    }

    public function logout()
    {
        // JWT is stateless, just return success
        return $this->successResponse(null, 'Logout berhasil.');
    }
}
