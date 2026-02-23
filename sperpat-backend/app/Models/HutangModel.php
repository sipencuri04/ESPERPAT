<?php

namespace App\Models;

use CodeIgniter\Model;

class HutangModel extends Model
{
    protected $table = 'hutang';
    protected $primaryKey = 'id';
    protected $allowedFields = ['supplier_name', 'amount', 'date_issued', 'due_date', 'status'];
    protected $useTimestamps = true;

    public function getAgingHutang()
    {
        return $this->where('status !=', 'paid')->findAll();
    }
}
