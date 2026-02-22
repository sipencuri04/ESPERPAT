<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'name', 'slug', 'category_id', 'harga_beli', 'harga_jual',
        'stok', 'deskripsi', 'image', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'        => 'required|min_length[3]|max_length[200]',
        'category_id' => 'required|integer',
        'harga_beli'  => 'required|decimal',
        'harga_jual'  => 'required|decimal',
        'stok'        => 'required|integer',
    ];

    public function getWithCategory()
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->findAll();
    }

    public function getByCategory(int $categoryId)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->where('products.category_id', $categoryId)
                    ->findAll();
    }

    public function search(string $keyword)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->groupStart()
                        ->like('products.name', $keyword)
                        ->orLike('products.deskripsi', $keyword)
                    ->groupEnd()
                    ->findAll();
    }

    public function getLowStock(int $threshold = 10)
    {
        return $this->where('stok <=', $threshold)->findAll();
    }
}
