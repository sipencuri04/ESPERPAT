<?php

namespace App\Controllers\Web;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class HomeController extends BaseWebController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'ESPERPAT - Toko Sparepart Online',
            'products'   => $this->productModel->getWithCategory(),
            'categories' => $this->categoryModel->getWithProductCount(),
            'featured'   => $this->productModel->select('products.*, categories.name as category_name')
                                ->join('categories', 'categories.id = products.category_id', 'left')
                                ->where('products.is_active', 1)->orderBy('products.created_at', 'DESC')->limit(8)->findAll(),
            'user'       => $this->getUser(),
            'cartCount'  => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('home/index', $data)
             . view('layouts/footer', $data);
    }

    public function products()
    {
        $page     = (int) ($this->request->getGet('page') ?? 1);
        $limit    = 12;
        $category = $this->request->getGet('category');
        $sort     = $this->request->getGet('sort');

        $builder = $this->productModel->select('products.*, categories.name as category_name')
                       ->join('categories', 'categories.id = products.category_id', 'left')
                       ->where('products.is_active', 1);

        if ($category) {
            $builder->where('products.category_id', $category);
        }

        $total = $builder->countAllResults(false);

        if ($sort === 'price_low') {
            $builder->orderBy('products.harga_jual', 'ASC');
        } elseif ($sort === 'price_high') {
            $builder->orderBy('products.harga_jual', 'DESC');
        } else {
            $builder->orderBy('products.created_at', 'DESC');
        }

        $products = $builder->limit($limit, ($page - 1) * $limit)->findAll();

        $data = [
            'title'      => 'Semua Produk - ESPERPAT',
            'products'   => $products,
            'categories' => $this->categoryModel->getWithProductCount(),
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'total'      => $total,
                'totalPages' => ceil($total / $limit),
            ],
            'sort'     => $sort,
            'user'      => $this->getUser(),
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('home/products', $data)
             . view('layouts/footer', $data);
    }

    public function productDetail($slug)
    {
        $product = $this->productModel->select('products.*, categories.name as category_name')
                       ->join('categories', 'categories.id = products.category_id', 'left')
                       ->where('products.slug', $slug)
                       ->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $related = $this->productModel->where('category_id', $product['category_id'])
                       ->where('id !=', $product['id'])
                       ->limit(4)
                       ->findAll();

        $data = [
            'title'   => $product['name'] . ' - ESPERPAT',
            'product' => $product,
            'related' => $related,
            'user'      => $this->getUser(),
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('home/product_detail', $data)
             . view('layouts/footer', $data);
    }

    public function category($slug)
    {
        $category = $this->categoryModel->where('slug', $slug)->first();
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $products = $this->productModel->getByCategory($category['id']);

        $data = [
            'title'      => $category['name'] . ' - ESPERPAT',
            'category'   => $category,
            'products'   => $products,
            'categories' => $this->categoryModel->getWithProductCount(),
            'user'       => $this->getUser(),
            'cartCount'  => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('home/category', $data)
             . view('layouts/footer', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $products = [];

        if ($keyword) {
            $products = $this->productModel->search($keyword);
        }

        $data = [
            'title'    => 'Hasil Pencarian: ' . $keyword . ' - ESPERPAT',
            'keyword'  => $keyword,
            'products' => $products,
            'user'      => $this->getUser(),
            'cartCount' => $this->getCartCount(),
        ];

        return view('layouts/header', $data)
             . view('home/search', $data)
             . view('layouts/footer', $data);
    }
}
