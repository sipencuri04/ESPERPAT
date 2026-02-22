<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'discount_type' => [
                'type'       => 'ENUM',
                'constraint' => ['percent', 'fixed'],
                'default'    => 'percent',
            ],
            'discount_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'valid_from' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'valid_to' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Let's add foreign keys if needed, though they mock it out sometimes
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('promos');
    }

    public function down()
    {
        $this->forge->dropTable('promos');
    }
}
