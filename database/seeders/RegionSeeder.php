<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Parish;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            [
                'name' => 'Central Region', 'code' => 'CR', 'is_active' => true,
                'parishes' => [
                    ['name' => 'Kampala', 'code' => 'KMP', 'district' => 'Kampala'],
                    ['name' => 'Wakiso', 'code' => 'WAK', 'district' => 'Wakiso'],
                    ['name' => 'Mukono', 'code' => 'MUK', 'district' => 'Mukono'],
                    ['name' => 'Mpigi', 'code' => 'MPI', 'district' => 'Mpigi'],
                    ['name' => 'Buikwe', 'code' => 'BUI', 'district' => 'Buikwe'],
                ],
            ],
            [
                'name' => 'Eastern Region', 'code' => 'ER', 'is_active' => true,
                'parishes' => [
                    ['name' => 'Jinja', 'code' => 'JIN', 'district' => 'Jinja'],
                    ['name' => 'Mbale', 'code' => 'MBA', 'district' => 'Mbale'],
                    ['name' => 'Soroti', 'code' => 'SOR', 'district' => 'Soroti'],
                    ['name' => 'Iganga', 'code' => 'IGA', 'district' => 'Iganga'],
                    ['name' => 'Tororo', 'code' => 'TOR', 'district' => 'Tororo'],
                ],
            ],
            [
                'name' => 'Northern Region', 'code' => 'NR', 'is_active' => true,
                'parishes' => [
                    ['name' => 'Gulu', 'code' => 'GUL', 'district' => 'Gulu'],
                    ['name' => 'Lira', 'code' => 'LIR', 'district' => 'Lira'],
                    ['name' => 'Arua', 'code' => 'ARU', 'district' => 'Arua'],
                    ['name' => 'Kitgum', 'code' => 'KIT', 'district' => 'Kitgum'],
                    ['name' => 'Apac', 'code' => 'APA', 'district' => 'Apac'],
                ],
            ],
            [
                'name' => 'Western Region', 'code' => 'WR', 'is_active' => true,
                'parishes' => [
                    ['name' => 'Mbarara', 'code' => 'MBA2', 'district' => 'Mbarara'],
                    ['name' => 'Fort Portal', 'code' => 'FTP', 'district' => 'Kabarole'],
                    ['name' => 'Kabale', 'code' => 'KAB', 'district' => 'Kabale'],
                    ['name' => 'Kasese', 'code' => 'KAS', 'district' => 'Kasese'],
                    ['name' => 'Bushenyi', 'code' => 'BUS', 'district' => 'Bushenyi'],
                ],
            ],
        ];

        foreach ($regions as $regionData) {
            $parishes = $regionData['parishes'];
            unset($regionData['parishes']);

            $region = Region::updateOrCreate(['code' => $regionData['code']], $regionData);

            foreach ($parishes as $parishData) {
                Parish::updateOrCreate(
                    ['name' => $parishData['name'], 'region_id' => $region->id],
                    array_merge($parishData, ['region_id' => $region->id, 'is_active' => true])
                );
            }
        }

        $this->command->info('Regions and parishes seeded: 4 regions, 20 parishes');
    }
}
