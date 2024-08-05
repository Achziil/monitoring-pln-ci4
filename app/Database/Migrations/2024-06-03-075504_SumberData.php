<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SumberData extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'document_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'doc_date' => [
                'type' => 'DATE',
            ],
            'posting_date' => [
                'type' => 'DATE',
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'busa' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'order' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'wbs_element' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'amount_doc_curr' => [
                'type' => 'DOUBLE',
            ],
            'amount_local_curr' => [
                'type' => 'DOUBLE',
            ],
            'offst_acct' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'text' => [
                'type' => 'TEXT',
            ],
            'cost_ctr' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'cocd' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'reason_for_trip' => [
                'type' => 'TEXT',
            ],
            'document_header_text' => [
                'type' => 'TEXT',
            ],
            'vendor' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'account' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'clrng_doc' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'assignment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sumber_data');
    }

    public function down()
    {
        $this->forge->dropTable('sumber_data');
    }
}