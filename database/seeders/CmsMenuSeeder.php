<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsMenu;

class CmsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $submaster = CmsMenu::where('name','Submaster')->value('id');

        $menus = [
            [
                'name'              => 'Breakeven Sales',
                'type'              => 'Route',
                'path'              => 'AdminBreakevenSalesControllerGetIndex',
                'color'             => 'normal',
                'icon'              => 'fa fa-file-text-o',
                'parent_id'         => $submaster ?? 0,
                'is_active'         => 1,
                'is_dashboard'      => 0,
                'id_cms_privileges' => 1,
                'sorting'           => 14
            ],
            [
                'name'              => 'Target Sales',
                'type'              => 'Route',
                'path'              => 'AdminTargetSalesControllerGetIndex',
                'color'             => 'normal',
                'icon'              => 'fa fa-file-text-o',
                'parent_id'         => $submaster ?? 0,
                'is_active'         => 1,
                'is_dashboard'      => 0,
                'id_cms_privileges' => 1,
                'sorting'           => 15
            ],
        ];
    
    foreach ($menus as $menu) {
        CmsMenu::updateOrCreate(['name' => $menu['name']], $menu);
    }

    $this->command->info('Seeder finished seeding menus.');
    }
}