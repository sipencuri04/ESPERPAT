<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function getUserData()
    {
        return $this->request->userData ?? null;
    }

    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        return $this->respond([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'status'  => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $this->respond($response, $code);
    }
}
