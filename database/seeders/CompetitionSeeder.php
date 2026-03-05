<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Contestant;
use App\Models\Parish;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        // Active competition
        $competition = Competition::updateOrCreate(
            ['slug' => 'miss-uganda-2025'],
            [
                'name' => 'Miss Uganda 2025',
                'type' => 'beauty_pageant',
                'description' => 'The official Miss Uganda beauty pageant, celebrating beauty, intelligence, and talent across Uganda.',
                'rules' => 'Must be a Ugandan citizen aged 18-27. Social media voting open to the public.',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'status' => 'active',
                'voting_enabled' => true,
                'total_votes' => 0,
            ]
        );

        $competition->settings()->updateOrCreate(
            ['competition_id' => $competition->id],
            [
                'number_of_parishes' => 4,
                'contestants_per_parish' => 3,
                'number_of_rounds' => 3,
                'votes_per_user_per_day' => 1,
                'votes_per_contestant_per_day' => 1,
                'require_social_login' => true,
            ]
        );

        $parishes = Parish::take(12)->get();

        $contestants = [
            ['full_name' => 'Amara Nalubega', 'age' => 22, 'profile_photo' => null, 'biography' => 'Environmental activist and law student from Kampala. Champions youth empowerment and clean energy.'],
            ['full_name' => 'Zara Nakato', 'age' => 24, 'profile_photo' => null, 'biography' => 'Medical student running free health clinics in rural Uganda for the past two years.'],
            ['full_name' => 'Diana Akello', 'age' => 21, 'profile_photo' => null, 'biography' => 'Computer science graduate who founded a startup teaching coding to girls in underserved communities.'],
            ['full_name' => 'Grace Apio', 'age' => 23, 'profile_photo' => null, 'biography' => 'Professional dancer and cultural ambassador who has performed at international festivals worldwide.'],
            ['full_name' => 'Faith Namukasa', 'age' => 25, 'profile_photo' => null, 'biography' => 'Agricultural science graduate transforming smallholder farming with modern technology.'],
            ['full_name' => 'Hope Atim', 'age' => 20, 'profile_photo' => null, 'biography' => 'Gifted vocalist who won the East Africa Youth Talent Award. Performing since age 12.'],
            ['full_name' => 'Joy Nalwoga', 'age' => 22, 'profile_photo' => null, 'biography' => 'Fashion designer creating eco-friendly African fashion featured in Vogue Africa.'],
            ['full_name' => 'Peace Achan', 'age' => 26, 'profile_photo' => null, 'biography' => 'Civil engineer leading a project connecting remote communities in Northern Uganda.'],
            ['full_name' => 'Love Nabirye', 'age' => 23, 'profile_photo' => null, 'biography' => 'Journalist fighting misinformation. Anchors a popular youth affairs programme on national TV.'],
            ['full_name' => 'Blessing Akello', 'age' => 24, 'profile_photo' => null, 'biography' => 'Psychology graduate who founded Uganda\'s first youth mental health helpline.'],
            ['full_name' => 'Star Nakawunde', 'age' => 21, 'profile_photo' => null, 'biography' => 'Champion swimmer who represented Uganda at the 2023 All-Africa Games.'],
            ['full_name' => 'Queen Nakabugo', 'age' => 25, 'profile_photo' => null, 'biography' => 'International relations graduate and peace negotiator across the Great Lakes region.'],
        ];

        foreach ($contestants as $i => $data) {
            $parish = $parishes->get($i);
            Contestant::updateOrCreate(
                ['competition_id' => $competition->id, 'full_name' => $data['full_name']],
                array_merge($data, [
                    'competition_id' => $competition->id,
                    'parish_id' => $parish?->id,
                    'region_id' => $parish?->region_id,
                    'contestant_number' => str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'total_votes' => rand(100, 3000),
                    'current_round_votes' => rand(10, 500),
                ])
            );
        }

        // Update competition total_votes
        $competition->update(['total_votes' => $competition->contestants()->sum('total_votes')]);

        // Upcoming competition
        $comp2 = Competition::updateOrCreate(
            ['slug' => 'uganda-tourism-ambassador-2025'],
            [
                'name' => 'Uganda Tourism Ambassador 2025',
                'type' => 'tourism',
                'description' => 'Seeking a passionate ambassador to promote Uganda\'s natural wonders and cultural heritage on the world stage.',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(45),
                'status' => 'upcoming',
                'voting_enabled' => false,
                'total_votes' => 0,
            ]
        );

        $comp2->settings()->updateOrCreate(
            ['competition_id' => $comp2->id],
            [
                'number_of_parishes' => 4,
                'contestants_per_parish' => 2,
                'number_of_rounds' => 2,
                'votes_per_user_per_day' => 1,
                'votes_per_contestant_per_day' => 1,
                'require_social_login' => true,
            ]
        );

        $this->command->info('Competitions and contestants seeded!');
    }
}
