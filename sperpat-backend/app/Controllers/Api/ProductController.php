<?php

namespace App\Controllers\Api;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ExpenseModel;

class ProductController extends BaseApiController
{
    protected ProductModel $productModel;
    protected ExpenseModel $expenseModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->expenseModel = new ExpenseModel();
    }

    public function restock($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        $qty = (int) $this->request->getPost('qty');
        $hargaBeli = (float) $this->request->getPost('harga_beli');
        $hargaJual = (float) $this->request->getPost('harga_jual');

        if ($qty <= 0) {
            return $this->errorResponse('Quantity harus lebih dari 0.');
        }

        // 1. Update Product
        $newStock = $product['stok'] + $qty;
        $this->productModel->update($id, [
            'stok' => $newStock,
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaJual
        ]);

        // 2. Record Expense (Cash Out)
        $totalAmount = $qty * $hargaBeli;
        $this->expenseModel->insert([
            'description' => "Restok Barang: " . $product['name'] . " ($qty pcs)",
            'category'    => 'Restok',
            'amount'      => $totalAmount,
            'date'        => date('Y-m-d')
        ]);

        return $this->successResponse([
            'product' => $this->productModel->find($id),
            'expense_recorded' => $totalAmount
        ], 'Restok berhasil dan kas keluar telah dicatat.');
    }

    public function index()
    {
        $categoryId = $this->request->getGet('category_id');
        $search     = $this->request->getGet('search');
        $page       = (int) ($this->request->getGet('page') ?? 1);
        $limit      = (int) ($this->request->getGet('limit') ?? 20);

        $builder = $this->productModel->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.is_active', 1);

        if ($categoryId) {
            $builder->where('products.category_id', $categoryId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('products.name', $search)
                    ->orLike('products.deskripsi', $search)
                    ->groupEnd();
        }

        $total    = $builder->countAllResults(false);
        $products = $builder->orderBy('products.created_at', 'DESC')
                            ->limit($limit, ($page - 1) * $limit)
                            ->findAll();

        return $this->successResponse([
            'products'   => $products,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'total'      => $total,
                'totalPages' => ceil($total / $limit),
            ],
        ]);
    }

    public function show($id = null)
    {
        $product = $this->productModel->select('products.*, categories.name as category_name')
                       ->join('categories', 'categories.id = products.category_id', 'left')
                       ->find($id);

        if (!$product) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        return $this->successResponse($product);
    }

    public function create()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'category_id' => 'required|integer',
            'harga_beli'  => 'required|decimal',
            'harga_jual'  => 'required|decimal',
            'stok'        => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data = [];
        if (strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $data = $this->request->getJSON(true);
        } else {
            $data = $this->request->getPost();
        }
        $data['slug'] = url_title($data['name'], '-', true) . '-' . time();

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $uploadDir = $this->ensureUploadDir();
            $image->move($uploadDir, $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->insert($data);
        $product = $this->productModel->find($this->productModel->getInsertID());

        return $this->successResponse($product, 'Produk berhasil ditambahkan.', 201);
    }

    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->errorResponse('Produk ID ' . $id . ' tidak ditemukan di database.', 404);
        }

        // Get data from JSON or Post
        $data = [];
        if (strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $data = $this->request->getJSON(true);
        } else {
            $data = $this->request->getPost();
        }

        // Cast numeric fields to satisfy validation if they came as strings from FormData
        if (isset($data['harga_beli']))  $data['harga_beli']  = (float) $data['harga_beli'];
        if (isset($data['harga_jual']))  $data['harga_jual']  = (float) $data['harga_jual'];
        if (isset($data['stok']))        $data['stok']        = (int) $data['stok'];
        if (isset($data['category_id'])) $data['category_id'] = (int) $data['category_id'];

        if (isset($data['name'])) {
            $data['slug'] = url_title($data['name'], '-', true) . '-' . time();
        }

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            if (!empty($product['image']) && file_exists(FCPATH . $product['image'])) {
                try {
                    unlink(FCPATH . $product['image']);
                } catch (\Exception $e) {
                    // Ignore unlink error
                }
            }
            $newName = $image->getRandomName();
            $uploadDir = $this->ensureUploadDir();
            $image->move($uploadDir, $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        // Validate before update
        if (!$this->productModel->update($id, $data)) {
            return $this->errorResponse('Gagal update produk.', 422, $this->productModel->errors());
        }

        $product = $this->productModel->find($id);
        return $this->successResponse($product, 'Produk berhasil diupdate.');
    }

    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->errorResponse('Produk tidak ditemukan.', 404);
        }

        // Delete image if exists
        if (!empty($product['image']) && file_exists(FCPATH . $product['image'])) {
            unlink(FCPATH . $product['image']);
        }

        $this->productModel->delete($id);

        return $this->successResponse(null, 'Produk berhasil dihapus.');
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        if (empty($keyword)) {
            return $this->errorResponse('Kata kunci pencarian diperlukan.');
        }

        $products = $this->productModel->search($keyword);
        return $this->successResponse($products);
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
