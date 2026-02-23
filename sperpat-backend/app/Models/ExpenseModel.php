<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['description', 'category', 'amount', 'date'];
    protected $useTimestamps = true;

    public function getExpensesByPeriod($startDate, $endDate)
    {
        return $this->where('date >=', $startDate)
                    ->where('date <=', $endDate)
                    ->findAll();
    }
}
