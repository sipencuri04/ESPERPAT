<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name', 'email', 'password', 'phone', 'address', 'role',
        'verification_token', 'email_verified_at', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar.',
        ],
    ];

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findByVerificationToken(string $token)
    {
        return $this->where('verification_token', $token)->first();
    }

    public function getCustomers()
    {
        return $this->where('role', 'customer')->findAll();
    }

    public function getAdmins()
    {
        return $this->whereIn('role', ['admin', 'superuser'])->findAll();
    }
}
