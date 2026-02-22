<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SessionAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $token = $session->get('token');
        $user  = $session->get('user');

        if (empty($token) || empty($user)) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check role if arguments are provided
        if ($arguments && !in_array($user['role'], $arguments)) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
