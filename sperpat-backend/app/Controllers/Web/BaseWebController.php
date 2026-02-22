<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;

class BaseWebController extends Controller
{
    protected $session;
    protected $userData;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = session();
        $this->userData = $this->session->get('user');
    }

    protected function isLoggedIn(): bool
    {
        return !empty($this->session->get('token'));
    }

    protected function getUser(): ?array
    {
        return $this->session->get('user');
    }

    protected function getToken(): ?string
    {
        return $this->session->get('token');
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return null;
    }

    protected function requireRole(array $roles)
    {
        $user = $this->getUser();
        if (!$user || !in_array($user['role'], $roles)) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses.');
        }
        return null;
    }

    protected function getCartCount(): int
    {
        $cart = $this->session->get('cart') ?? [];
        return array_sum(array_column($cart, 'qty'));
    }
}
