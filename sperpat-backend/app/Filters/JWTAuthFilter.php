<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\JWTService;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtService = new JWTService();
        $token = $jwtService->getTokenFromHeader();

        if (!$token) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.',
                ]);
        }

        $userData = $jwtService->validateToken($token);

        if (!$userData) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Token tidak valid atau sudah kadaluarsa.',
                ]);
        }

        // Store user data in request for controllers
        $request->userData = $userData;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
