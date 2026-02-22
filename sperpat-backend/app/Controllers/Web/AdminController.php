<?php

namespace App\Controllers\Web;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel;

class AdminController extends BaseWebController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->productModel   = new ProductModel();
        $this->categoryModel  = new CategoryModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->userModel      = new UserModel();
    }

    public function dashboard()
    {
        $data = [
            'title'          => 'Dashboard Admin - ESPERPAT',
            'user'           => $this->getUser(),
            'totalProducts'  => $this->productModel->countAllResults(),
            'totalOrders'    => $this->orderModel->countAllResults(),
            'totalCustomers' => $this->userModel->where('role', 'customer')->countAllResults(),
            'totalCategories' => $this->categoryModel->countAllResults(),
            'recentOrders'   => $this->orderModel->getWithUser(),
            'lowStock'       => $this->productModel->getLowStock(10),
            'pendingOrders'  => $this->orderModel->where('status', 'pending')->countAllResults(),
        ];

        // Calculate monthly revenue
        $month = date('m');
        $year  = date('Y');
        $monthlyOrders = $this->orderModel->getMonthlyReport((int)$month, (int)$year);
        $data['monthlyRevenue'] = array_sum(array_column($monthlyOrders, 'total'));

        return view('layouts/header', $data)
             . view('admin/dashboard', $data)
             . view('layouts/footer', $data);
    }

    // ---- PRODUCTS ----
    public function products()
    {
        $data = [
            'title'    => 'Kelola Produk - Admin',
            'user'     => $this->getUser(),
            'products' => $this->productModel->getWithCategory(),
        ];

        return view('layouts/header', $data)
             . view('admin/products', $data)
             . view('layouts/footer', $data);
    }

    public function createProduct()
    {
        $data = [
            'title'      => 'Tambah Produk - Admin',
            'user'       => $this->getUser(),
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('layouts/header', $data)
             . view('admin/product_form', $data)
             . view('layouts/footer', $data);
    }

    public function storeProduct()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'category_id' => 'required|integer',
            'harga_beli'  => 'required|decimal',
            'harga_jual'  => 'required|decimal',
            'stok'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => url_title($this->request->getPost('name'), '-', true) . '-' . time(),
            'category_id' => $this->request->getPost('category_id'),
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
            'stok'        => $this->request->getPost('stok'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ];

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $uploadDir = $this->ensureUploadDir();
            $image->move($uploadDir, $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->insert($data);
        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Produk - Admin',
            'user'       => $this->getUser(),
            'product'    => $product,
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('layouts/header', $data)
             . view('admin/product_form', $data)
             . view('layouts/footer', $data);
    }

    public function updateProduct($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'slug'        => url_title($this->request->getPost('name'), '-', true) . '-' . time(),
            'category_id' => $this->request->getPost('category_id'),
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
            'stok'        => $this->request->getPost('stok'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ];

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            // Delete old image
            if (!empty($product['image']) && file_exists(FCPATH . $product['image'])) {
                unlink(FCPATH . $product['image']);
            }
            $newName = $image->getRandomName();
            $uploadDir = $this->ensureUploadDir();
            $image->move($uploadDir, $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->update($id, $data);
        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diupdate.');
    }

    public function deleteProduct($id)
    {
        $product = $this->productModel->find($id);
        if ($product && !empty($product['image']) && file_exists(FCPATH . $product['image'])) {
            unlink(FCPATH . $product['image']);
        }
        $this->productModel->delete($id);
        return redirect()->to('/admin/products')->with('success', 'Produk berhasil dihapus.');
    }

    // ---- CATEGORIES ----
    public function categories()
    {
        $data = [
            'title'      => 'Kelola Kategori - Admin',
            'user'       => $this->getUser(),
            'categories' => $this->categoryModel->getWithProductCount(),
        ];

        return view('layouts/header', $data)
             . view('admin/categories', $data)
             . view('layouts/footer', $data);
    }

    public function storeCategory()
    {
        $name = $this->request->getPost('name');
        $this->categoryModel->insert([
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'description' => $this->request->getPost('description'),
        ]);
        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory($id)
    {
        $name = $this->request->getPost('name');
        $this->categoryModel->update($id, [
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'description' => $this->request->getPost('description'),
        ]);
        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil diupdate.');
    }

    public function deleteCategory($id)
    {
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil dihapus.');
    }

    // ---- ORDERS ----
    public function orders()
    {
        $status = $this->request->getGet('status');
        $orders = $status ? $this->orderModel->getByStatus($status) : $this->orderModel->getWithUser();

        $data = [
            'title'  => 'Kelola Pesanan - Admin',
            'user'   => $this->getUser(),
            'orders' => $orders,
            'status' => $status,
        ];

        return view('layouts/header', $data)
             . view('admin/orders', $data)
             . view('layouts/footer', $data);
    }

    public function orderDetail($id)
    {
        $order = $this->orderModel->select('orders.*, users.name as customer_name, users.email as customer_email, users.phone as customer_phone, users.address as customer_address')
                     ->join('users', 'users.id = orders.user_id', 'left')
                     ->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $order['items'] = $this->orderItemModel->getByOrder($id);

        $data = [
            'title' => 'Detail Pesanan #' . $order['invoice_number'] . ' - Admin',
            'user'  => $this->getUser(),
            'order' => $order,
        ];

        return view('layouts/header', $data)
             . view('admin/order_detail', $data)
             . view('layouts/footer', $data);
    }

    public function updateOrderStatus($id)
    {
        $status = $this->request->getPost('status');
        $this->orderModel->update($id, ['status' => $status]);

        if ($status === 'cancelled') {
            $items = $this->orderItemModel->getByOrder($id);
            foreach ($items as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $this->productModel->update($item['product_id'], ['stok' => $product['stok'] + $item['qty']]);
                }
            }
        }

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Status pesanan berhasil diupdate.');
    }

    public function updateResi($id)
    {
        $resi = $this->request->getPost('nomor_resi');
        $this->orderModel->update($id, ['nomor_resi' => $resi, 'status' => 'shipped']);
        return redirect()->to('/admin/orders/' . $id)->with('success', 'Nomor resi berhasil diupdate.');
    }

    // ---- REPORTS ----
    public function reports()
    {
        return redirect()->to('/admin/reports/sales');
    }

    public function salesReport()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year  = (int) ($this->request->getGet('year') ?? date('Y'));

        $orders     = $this->orderModel->getMonthlyReport($month, $year);
        $totalSales = array_sum(array_column($orders, 'total'));

        $data = [
            'title'      => 'Laporan Penjualan - Admin',
            'user'       => $this->getUser(),
            'orders'     => $orders,
            'totalSales' => $totalSales,
            'month'      => $month,
            'year'       => $year,
        ];

        return view('layouts/header', $data)
             . view('admin/report_sales', $data)
             . view('layouts/footer', $data);
    }

    public function profitReport()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year  = (int) ($this->request->getGet('year') ?? date('Y'));

        $orders      = $this->orderModel->getMonthlyReport($month, $year);
        $totalSales  = 0;
        $totalProfit = 0;

        foreach ($orders as &$order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $order['profit'] = $profitData['total_profit'];
            $totalSales += $order['total'];
            $totalProfit += $profitData['total_profit'];
        }

        $data = [
            'title'       => 'Laporan Laba - Admin',
            'user'        => $this->getUser(),
            'orders'      => $orders,
            'totalSales'  => $totalSales,
            'totalProfit' => $totalProfit,
            'month'       => $month,
            'year'        => $year,
        ];

        return view('layouts/header', $data)
             . view('admin/report_profit', $data)
             . view('layouts/footer', $data);
    }

    public function stockReport()
    {
        $products = $this->productModel->getWithCategory();
        $lowStock = $this->productModel->getLowStock(10);

        $data = [
            'title'    => 'Laporan Stok - Admin',
            'user'     => $this->getUser(),
            'products' => $products,
            'lowStock' => $lowStock,
        ];

        return view('layouts/header', $data)
             . view('admin/report_stock', $data)
             . view('layouts/footer', $data);
    }

    private function ensureUploadDir(): string
    {
        $dir = FCPATH . 'uploads/products';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }
}
