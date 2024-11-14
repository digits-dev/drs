<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsModule;


class CmsModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            [
                'name'         => 'Breakeven Sales',
                'icon'         => 'fa fa-file-text-o',
                'path'         => 'breakeven_sales',
                'table_name'   => 'breakeven_sales',
                'controller'   => 'AdminBreakevenSalesController',
                'is_protected' => 0,
                'is_active'    => 0,
            ],
            [
                'name'         => 'Target Sales',
                'icon'         => 'fa fa-file-text-o',
                'path'         => 'target_sales',
                'table_name'   => 'target_sales',
                'controller'   => 'AdminTargetSalesController',
                'is_protected' => 0,
                'is_active'    => 0,
            ],
        ];
        
        foreach ($modules as $module) {
            CmsModule::updateOrInsert(['name' => $module['name']], $module);
        }

        $this->command->info('Seeder finished seeding modules.');
    }
}