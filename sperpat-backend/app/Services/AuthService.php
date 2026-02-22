<?php

namespace App\Services;

use App\Models\UserModel;

class AuthService
{
    protected UserModel $userModel;
    protected JWTService $jwtService;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->jwtService = new JWTService();
    }

    public function register(array $data): array
    {
        $verificationToken = bin2hex(random_bytes(32));

        $userData = [
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'              => $data['phone'] ?? null,
            'address'            => $data['address'] ?? null,
            'role'               => 'customer',
            'verification_token' => $verificationToken,
            'is_active'          => 0,
        ];

        $this->userModel->insert($userData);
        $userId = $this->userModel->getInsertID();

        // Send verification email
        $this->sendVerificationEmail($data['email'], $data['name'], $verificationToken);

        return [
            'success' => true,
            'message' => 'Registrasi berhasil. Silakan cek email untuk verifikasi.',
            'user_id' => $userId,
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }

        if ($user['role'] === 'customer' && empty($user['email_verified_at'])) {
            return ['success' => false, 'message' => 'Silakan verifikasi email terlebih dahulu.'];
        }

        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Akun Anda tidak aktif. Hubungi admin.'];
        }

        $token = $this->jwtService->generateToken($user);

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'      => $user['id'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'role'    => $user['role'],
                'phone'   => $user['phone'] ?? null,
                'address' => $user['address'] ?? null,
            ],
        ];
    }

    public function verifyEmail(string $token): array
    {
        $user = $this->userModel->findByVerificationToken($token);

        if (!$user) {
            return ['success' => false, 'message' => 'Token verifikasi tidak valid.'];
        }

        $this->userModel->update($user['id'], [
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'verification_token' => null,
            'is_active'          => 1,
        ]);

        return ['success' => true, 'message' => 'Email berhasil diverifikasi. Silakan login.'];
    }

    public function resendVerification(string $email): array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email tidak ditemukan.'];
        }

        if (!empty($user['email_verified_at'])) {
            return ['success' => false, 'message' => 'Email sudah diverifikasi.'];
        }

        $newToken = bin2hex(random_bytes(32));
        $this->userModel->update($user['id'], ['verification_token' => $newToken]);

        $this->sendVerificationEmail($user['email'], $user['name'], $newToken);

        return ['success' => true, 'message' => 'Email verifikasi telah dikirim ulang.'];
    }

    protected function sendVerificationEmail(string $email, string $name, string $token): bool
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom(env('email.SMTPUser', 'noreply@esperpat.com'), 'ESPERPAT Store');
            $emailService->setTo($email);
            $emailService->setSubject('Verifikasi Email - ESPERPAT Store');

            $verifyUrl = base_url("api/verify/{$token}");
            $message = "
                <h2>Halo {$name},</h2>
                <p>Terima kasih telah mendaftar di ESPERPAT Store.</p>
                <p>Klik link berikut untuk verifikasi email Anda:</p>
                <p><a href='{$verifyUrl}' style='background:#0d6efd;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;'>Verifikasi Email</a></p>
                <p>Atau copy link ini: {$verifyUrl}</p>
                <br>
                <p>Salam,<br>ESPERPAT Store</p>
            ";

            $emailService->setMessage($message);
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Email verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
