<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;

class CategoryController extends BaseApiController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->getWithProductCount();
        return $this->successResponse($categories);
    }

    public function show($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }
        return $this->successResponse($category);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|max_length[100]'];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);
        $data['slug'] = url_title($data['name'], '-', true);

        $this->categoryModel->insert($data);
        $category = $this->categoryModel->find($this->categoryModel->getInsertID());

        return $this->successResponse($category, 'Kategori berhasil ditambahkan.', 201);
    }

    public function update($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        $data = $this->request->getJSON(true);
        if (isset($data['name'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        $this->categoryModel->update($id, $data);
        $category = $this->categoryModel->find($id);

        return $this->successResponse($category, 'Kategori berhasil diupdate.');
    }

    public function delete($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->errorResponse('Kategori tidak ditemukan.', 404);
        }

        $this->categoryModel->delete($id);
        return $this->successResponse(null, 'Kategori berhasil dihapus.');
    }
}
