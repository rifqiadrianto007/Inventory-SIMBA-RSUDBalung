<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Exception;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super admin',
            'admin gudang umum',
            'penanggung jawab',
            'ppk',
            'teknis',
            'instalasi',
        ];

        foreach ($roles as $role) {
            try {
                Role::create([
                    'name' => $role,
                    'guard_name' => 'web',
                ]);
                $this->command->info("Role '{$role}' berhasil dibuat.");
            } catch (Exception $e) {
                $this->command->warn("Role '{$role}' sudah ada, dilewati.");
            }
        }

        $this->command->info('✅ RoleSeeder selesai dijalankan.');
    }
}
