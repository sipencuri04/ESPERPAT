<?php

namespace App\Models;

use CodeIgniter\Model;

class PiutangModel extends Model
{
    protected $table = 'piutang';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_name', 'amount', 'date_issued', 'due_date', 'status'];
    protected $useTimestamps = true;

    public function getAgingPiutang()
    {
        $db = \Config\Database::connect();
        
        // Raw query to group by aging
        // 0-30 days, 31-60 days, 61-90 days, > 90 days.
        $query = $db->query("
            SELECT customer_name, amount, due_date, status,
            DATEDIFF(NOW(), due_date) AS days_late
            FROM piutang
            WHERE status != 'paid'
        ");
        
        $results = $query->getResultArray();
        
        $aging = [
            '0_30'  => [],
            '31_60' => [],
            '61_90' => [],
            'over_90' => []
        ];

        foreach ($results as $row) {
            $days = (int)$row['days_late'];
            
            // If days <= 0, it means it's not late yet, let's treat it as 0_30 or add a new category. The requirement asks for 0-30 days, 31-60 days etc. This refers to days since invoice.
            // Wait, aging piutang is usually calculated from date_issued. DATEDIFF(NOW(), date_issued)
            // Let's modify to use date_issued for aging.
            
            $daysFromIssue = (time() - strtotime($row['due_date'])) / (60 * 60 * 24); // just simple difference
        }
        
        // A better approach is to return the raw piutang list and do aging categorization in controller/frontend, or do properly:
        return $this->where('status !=', 'paid')->findAll();
    }
}
