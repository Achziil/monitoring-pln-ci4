<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MonitoringOptimasi extends Migration
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
            'busa' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'bulan' => [
                'type' => 'DATE',
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'target_amount' => [
                'type' => 'DOUBLE',
            ],
            'realisasi_amount' => [
                'type' => 'DOUBLE',
            ],
            'selisih_amount' => [
                'type' => 'DOUBLE',
            ],
            'last_refresh_date' => [
                'type' => 'DATETIME',
            ],
            'annual_total_amount' => [
                'type' => 'DOUBLE',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('monitoring_optimasi');
    }

    public function down()
    {
        $this->forge->dropTable('monitoring_optimasi');
    }
}