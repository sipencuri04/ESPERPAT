<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountingTables extends Migration
{
    public function up()
    {
        // 1. Expenses / Biaya Operasional
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'category'    => ['type' => 'VARCHAR', 'constraint' => 50], // listik, gaji, sewa, marketing, etc
            'amount'      => ['type' => 'INT', 'constraint' => 11],
            'date'        => ['type' => 'DATE'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('expenses', true);

        // 2. Piutang (Receivables)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount'        => ['type' => 'INT', 'constraint' => 11],
            'date_issued'   => ['type' => 'DATE'],
            'due_date'      => ['type' => 'DATE'],
            'status'        => ['type' => 'ENUM', 'constraint' => ['unpaid', 'paid', 'overdue'], 'default' => 'unpaid'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('piutang', true);

        // 3. Hutang (Payables)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount'        => ['type' => 'INT', 'constraint' => 11],
            'date_issued'   => ['type' => 'DATE'],
            'due_date'      => ['type' => 'DATE'],
            'status'        => ['type' => 'ENUM', 'constraint' => ['unpaid', 'paid', 'overdue'], 'default' => 'unpaid'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hutang', true);

        // 4. Audit Logs
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 255], // e.g., 'Export Laba Rugi PDF'
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('expenses', true);
        $this->forge->dropTable('piutang', true);
        $this->forge->dropTable('hutang', true);
        $this->forge->dropTable('audit_logs', true);
    }
}
