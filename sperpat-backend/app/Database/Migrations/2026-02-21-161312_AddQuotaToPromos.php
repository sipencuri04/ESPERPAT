<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuotaToPromos extends Migration
{
    public function up()
    {
        $fields = [
            'quota' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null, // Null means unlimited
                'after'      => 'discount_value'
            ],
            'claimed_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'quota'
            ]
        ];

        $this->forge->addColumn('promos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('promos', 'quota');
        $this->forge->dropColumn('promos', 'claimed_count');
    }
}
