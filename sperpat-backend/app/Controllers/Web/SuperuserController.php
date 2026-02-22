<?php

namespace App\Controllers\Web;

use App\Models\UserModel;
use App\Models\OrderModel;

class SuperuserController extends BaseWebController
{
    protected UserModel $userModel;
    protected OrderModel $orderModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function dashboard()
    {
        $data = [
            'title'            => 'Dashboard Superuser - ESPERPAT',
            'user'             => $this->getUser(),
            'totalAdmins'      => $this->userModel->where('role', 'admin')->countAllResults(),
            'totalCustomers'   => $this->userModel->where('role', 'customer')->countAllResults(),
            'inactiveCustomers'=> $this->userModel->where('role', 'customer')->where('is_active', 0)->countAllResults(),
            'totalOrders'      => $this->orderModel->countAllResults(),
            'recentUsers'      => $this->userModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
        ];

        return view('layouts/header', $data)
             . view('superuser/dashboard', $data)
             . view('layouts/footer', $data);
    }

    public function admins()
    {
        $data = [
            'title'  => 'Kelola Admin - Superuser',
            'user'   => $this->getUser(),
            'admins' => $this->userModel->whereIn('role', ['admin', 'superuser'])
                                        ->orderBy('created_at', 'DESC')
                                        ->findAll(),
        ];

        return view('layouts/header', $data)
             . view('superuser/admins', $data)
             . view('layouts/footer', $data);
    }

    public function storeAdmin()
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

        $this->userModel->insert([
            'name'              => $this->request->getPost('name'),
            'email'             => $this->request->getPost('email'),
            'password'          => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'phone'             => $this->request->getPost('phone'),
            'role'              => 'admin',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'is_active'         => 1,
        ]);

        return redirect()->back()->with('success', 'Admin berhasil ditambahkan.');
    }

    public function updateAdmin($id)
    {
        $admin = $this->userModel->find($id);
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin tidak ditemukan.');
        }

        if ($admin['role'] !== 'admin') {
            return redirect()->back()->with('error', 'Akun ini tidak dapat diubah.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'phone'    => 'permit_empty|max_length[20]',
            'password' => 'permit_empty|min_length[6]',
            'is_active'=> 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $updateData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];

        $isActive = $this->request->getPost('is_active');
        if ($isActive !== null) {
            $updateData['is_active'] = (int) $isActive;
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->back()->with('success', 'Admin berhasil diupdate.');
    }

    public function deleteAdmin($id)
    {
        $admin = $this->userModel->find($id);
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin tidak ditemukan.');
        }

        if ($admin['role'] !== 'admin') {
            return redirect()->back()->with('error', 'Akun ini tidak dapat dihapus.');
        }

        if (($this->getUser()['id'] ?? null) === $admin['id']) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $this->userModel->delete($id);
        return redirect()->back()->with('success', 'Admin berhasil dihapus.');
    }

    public function customers()
    {
        $data = [
            'title'     => 'Kelola Customer - Superuser',
            'user'      => $this->getUser(),
            'customers' => $this->userModel->where('role', 'customer')
                                           ->orderBy('created_at', 'DESC')
                                           ->findAll(),
        ];

        return view('layouts/header', $data)
             . view('superuser/customers', $data)
             . view('layouts/footer', $data);
    }

    public function toggleCustomer($id)
    {
        $customer = $this->userModel->find($id);
        if (!$customer || $customer['role'] !== 'customer') {
            return redirect()->back()->with('error', 'Customer tidak ditemukan.');
        }

        $newStatus = $customer['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        return redirect()->back()->with('success', 'Status customer berhasil diupdate.');
    }

    public function settings()
    {
        $data = [
            'title' => 'Pengaturan - Superuser',
            'user'  => $this->getUser(),
        ];

        return view('layouts/header', $data)
             . view('superuser/settings', $data)
             . view('layouts/footer', $data);
    }
}
