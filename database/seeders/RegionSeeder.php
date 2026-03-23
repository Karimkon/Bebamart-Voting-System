<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\County;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        County::query()->delete();
        Region::query()->delete();

        $region = Region::create([
            'name'      => 'Buganda Kingdom',
            'code'      => 'BK',
            'is_active' => true,
        ]);

        $amasaza = [
            ['name' => 'Gomba',      'code' => 'BK01'],
            ['name' => 'Kyaddondo', 'code' => 'BK02'],
            ['name' => 'Busiro',     'code' => 'BK03'],
            ['name' => 'Kyaggwe',   'code' => 'BK04'],
            ['name' => 'Kabula',     'code' => 'BK05'],
            ['name' => 'Bugerere',  'code' => 'BK06'],
            ['name' => 'Buvuma',    'code' => 'BK07'],
            ['name' => 'Ssesse',     'code' => 'BK08'],
            ['name' => 'Buddu',      'code' => 'BK09'],
            ['name' => 'Butambala', 'code' => 'BK10'],
            ['name' => 'Mawogola',  'code' => 'BK11'],
            ['name' => 'Buluuli',   'code' => 'BK12'],
            ['name' => 'Busujju',   'code' => 'BK13'],
            ['name' => 'Buwekula',  'code' => 'BK14'],
            ['name' => 'Ssingo',     'code' => 'BK15'],
            ['name' => 'Mawokota',  'code' => 'BK16'],
            ['name' => 'Kkooki',    'code' => 'BK17'],
            ['name' => 'Bulemeezi', 'code' => 'BK18'],
        ];

        foreach ($amasaza as $county) {
            County::create([
                'name'      => $county['name'],
                'code'      => $county['code'],
                'district'  => 'Buganda',
                'region_id' => $region->id,
                'is_active' => true,
            ]);
        }

        $this->command->info('Seeded 1 region (Buganda Kingdom) with 18 Amasaza.');
    }
}
