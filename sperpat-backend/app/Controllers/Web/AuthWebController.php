<?php

namespace App\Controllers\Web;

use App\Services\AuthService;

class AuthWebController extends BaseWebController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $user = $this->getUser();
            if ($user['role'] === 'superuser') return redirect()->to('/superuser/dashboard');
            if (in_array($user['role'], ['admin'])) return redirect()->to('/admin/dashboard');
            return redirect()->to('/customer/dashboard');
        }

        $redirect = $this->request->getGet('redirect');

        $data = [
            'title'    => 'Login - ESPERPAT',
            'user'      => null,
            'redirect'  => $redirect,
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('auth/login', $data)
             . view('layouts/footer', $data);
    }

    public function doLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $redirect = $this->request->getPost('redirect');

        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            $this->session->set([
                'token' => $result['token'],
                'user'  => $result['user'],
            ]);

            // If there's a redirect URL (from buy button), go there first
            if (!empty($redirect)) {
                $scheme = parse_url($redirect, PHP_URL_SCHEME);
                $isLocalPath = $scheme === null && str_starts_with($redirect, '/');
                if ($isLocalPath) {
                    return redirect()->to($redirect)->with('success', 'Login berhasil! Silakan lanjutkan pembelian.');
                }
            }

            if ($result['user']['role'] === 'superuser') return redirect()->to('/superuser/dashboard');
            if ($result['user']['role'] === 'admin') return redirect()->to('/admin/dashboard');
            return redirect()->to('/customer/dashboard');
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function register()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        $data = [
            'title'     => 'Register - ESPERPAT',
            'user'      => null,
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('auth/register', $data)
             . view('layouts/footer', $data);
    }

    public function doRegister()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'phone'    => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'phone'    => $this->request->getPost('phone'),
        ];

        $result = $this->authService->register($data);

        if ($result['success']) {
            return redirect()->to('/login')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    public function verify($token)
    {
        $result = $this->authService->verifyEmail($token);

        if ($result['success']) {
            return redirect()->to('/login')->with('success', $result['message']);
        }

        return redirect()->to('/login')->with('error', $result['message']);
    }
}
