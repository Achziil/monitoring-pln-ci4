<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nickname' => 'Administrator',
                'username' => '7600',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'level' => 'admin',
                'busa' => '7600',
            ],
            [
                'nickname' => 'UIW PPB',
                'username' => '7601',
                'password' => password_hash('wilayah', PASSWORD_DEFAULT),
                'level' => 'wilayah',
                'busa' => '7601',
            ],
            [
                'nickname' => 'UP3 Merauke',
                'username' => '7611',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7611',
            ],
            [
                'nickname' => 'UP3 Sorong',
                'username' => '7612',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7612',
            ],
            [
                'nickname' => 'UP3 Biak',
                'username' => '7613',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7613',
            ],
            [
                'nickname' => 'UP3 Jayapura',
                'username' => '7614',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7614',
            ],
            [
                'nickname' => 'UP3 Manokwari',
                'username' => '7615',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7615',
            ],
            [
                'nickname' => 'UP3 Timika',
                'username' => '7616',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7616',
            ],
            [
                'nickname' => 'UP3 Wamena',
                'username' => '7617',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7617',
            ],
            [
                'nickname' => 'UP3 Nabire',
                'username' => '7618',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7618',
            ],
            [
                'nickname' => 'UPK PPB',
                'username' => '7631',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7631',
            ],
            [
                'nickname' => 'UP3B',
                'username' => '7641',
                'password' => password_hash('pelaksana', PASSWORD_DEFAULT),
                'level' => 'pelaksana',
                'busa' => '7641',
            ],
        ];

        // Menggunakan insertBatch untuk memasukkan banyak baris data
        $this->db->table('users')->insertBatch($data);
    }
}
