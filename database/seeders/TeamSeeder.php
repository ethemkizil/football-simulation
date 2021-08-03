<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    public $teamNames = [
        'Liverpool',
        'Machester City',
        'Chelsea',
        'Arsenal',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        shuffle($this->teamNames);

        while($teamName = array_pop($this->teamNames)){
            Team::create([
                'team_name' => $teamName,
                'defensive_power' => rand(10,20),
                'attack_power' => rand(10,20),
                'coefficient' => rand(10,20),
            ]);
        }
    }
}
