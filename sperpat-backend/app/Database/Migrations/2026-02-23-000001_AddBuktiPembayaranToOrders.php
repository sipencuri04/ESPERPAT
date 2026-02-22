<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuktiPembayaranToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'bukti_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'metode_pembayaran'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'bukti_pembayaran');
    }
}
