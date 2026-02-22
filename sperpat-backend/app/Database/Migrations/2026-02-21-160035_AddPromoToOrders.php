<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromoToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'promo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'discount_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ]
        ];

        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'promo_id');
        $this->forge->dropColumn('orders', 'discount_amount');
    }
}
