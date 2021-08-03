<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Http\Request;

class SmulationController extends Controller
{
    public $higher_rated_team = 1.148698355;
    public $lower_rated_team = 0.9705505633;

    public function teams()
    {
        $teamModel = new Team();
        $teams = $teamModel->all();

        return response()->json([
            "success" => true,
            "message" => "Student List",
            "data" => $teams
        ]);
    }

    public function generateFixture()
    {
        $teamModel = new Team();
        //Get all teams different indexes each generate fixture time
        $teams = $teamModel->all();
        $teamCount = $teams->count();
        $teamArray = $teams->toArray();

        $matches = [];
        for ($i = 1; $i < ($teamCount); $i++) {
            $tempTeamArray = $teamArray;
            shuffle($tempTeamArray);
            for ($j = 0; $j <= ($teamCount-2)/2; $j++) {
                $a = array_pop($tempTeamArray);
                $b = array_pop($tempTeamArray);
                $matches[$i][$j] = [
                    "home" => $a,
                    "away" => $b
                ];
                $matches[$i+($teamCount-1)][$j] = [
                    "home" => $b,
                    "away" => $a
                ];
//                $matches[$i][] = $home["team_name"]." - ".$away["team_name"];
//                $matches[($i+3)][] = $away["team_name"]." - ".$home["team_name"];
            }
        }
        ksort($matches);

        //clear fixture data
        Fixture::truncate();

        //write fixture data
        foreach ($matches as $week=>$weekMatches) {
            foreach ($weekMatches as $match){
                Fixture::create([
                    "week" => $week,
                    "home_team_id" => $match["home"]["id"],
                    "away_team_id" => $match["away"]["id"],
                    "home_team_score" => 0,
                    "away_team_score" => 0,
                    "played" => 0
                ]);
            }
        }

        //return created fixture
        return response()->json([
            "success" => true,
            "message" => "Fixture",
            "data" => $matches
        ]);
    }

    public function playWeek()
    {
        $playResults = $this->play();

        return response()->json([
            "success" => true,
            "message" => "PlayWeek",
            "data" => $playResults
        ]);
    }

    public function playAllWeek()
    {
        $playResults  = $this->play(true);

        return response()->json([
            "success" => true,
            "message" => "PlayAllWeek",
            "data" => $playResults
        ]);
    }

    private function play($allWeek = false)
    {
        //play weeek
        $fixtureModel = new Fixture();
        $minWeek = $fixtureModel->where('played','=',0)->min('week');

        $where = [];
        if($allWeek) {
            $where = [
                ['played', '=', "0"],
            ];
        }else{
            $where = [
                ['week', '=', $minWeek],
                ['played', '=', "0"],
            ];
        };

        $currentWeekMatches = $fixtureModel
            ->leftJoin('teams as home_team', 'home_team.id', '=', 'fixture.home_team_id')
            ->leftJoin('teams as away_team', 'away_team.id', '=', 'fixture.away_team_id')
            ->select(
                'fixture.*',
                "home_team.team_name as ht_name",
                "home_team.defensive_power as ht_dp",
                "home_team.attack_power as ht_ap",
                "away_team.team_name as at_name",
                "away_team.defensive_power as at_dp",
                "away_team.attack_power as at_ap"
            )->where($where)->orderBy('week', 'asc')->get()->toArray();

        foreach ($currentWeekMatches as $key=>$match){
            $homeScore = $this->home_score($match);
            $awayScore = $this->away_score($match);

            $currentWeekMatches[$key]["home_score"] = $homeScore;
            $currentWeekMatches[$key]["away_score"] = $awayScore;
            $currentWeekMatches[$key]["played"] = 1;

            $fixtureRecord = $fixtureModel->find($match["id"]);
            $fixtureRecord->home_team_score = $homeScore;
            $fixtureRecord->away_team_score = $awayScore;
            $fixtureRecord->played = 1;
            $fixtureRecord->save();
        }

        return $currentWeekMatches;
    }

    public function scoreSheet()
    {

    }

    public function resetData()
    {

    }


    private function home_score($match)
    {
        $homeSkill = $match["ht_ap"] / 3;
        $awaySkill = $match["at_dp"] / 3;

        if ($homeSkill == $awaySkill)
            return 0;

        if ($homeSkill > $awaySkill){
            $homeGoals = 0;
            $lambHome = pow($this->higher_rated_team , ($homeSkill - $awaySkill));
            $z = (float)rand() / (float)getrandmax();
            while ($z > 0) {

                $z = $z - (((pow($lambHome , $homeGoals)) * exp(-1 * $lambHome)) / floatval(gmp_fact($homeGoals)));
                $homeGoals += 1;
            }
            return ($homeGoals - 1);
        }

        if ($homeSkill < $awaySkill){
            $homeGoals = 0;
            $lambHome = pow($this->higher_rated_team , ($homeSkill - $awaySkill));
            $z = (float)rand() / (float)getrandmax();
            while ($z > 0){
                $z = $z - (((pow($lambHome, $homeGoals)) * exp(-1 * $lambHome)) / floatval(gmp_fact($homeGoals)));
                $homeGoals += 1;
            }
            return ($homeGoals - 1);
        }
    }

    private function away_score($match)
    {
        $homeSkill = $match["ht_dp"] / 3;
        $awaySkill = $match["at_ap"] / 3;

        if ($homeSkill == $awaySkill)
            return 0;

        if ($awaySkill > $homeSkill){
            $awayGoals = 0;
            $lambAway = pow($this->lower_rated_team , ($homeSkill - $awaySkill));
            $x = (float)rand() / (float)getrandmax();
            while ($x > 0) {
                $x = $x - (((pow($lambAway, $awayGoals)) * exp(-1 * $lambAway)) / floatval(gmp_fact($awayGoals)));
                $awayGoals += 1;
                return ($awayGoals - 1);
            }
        }

        if ($awaySkill < $homeSkill) {
            $awayGoals = 0;
            $lambAway = pow($this->lower_rated_team, ($homeSkill - $awaySkill));
            $x = (float)rand() / (float)getrandmax();
            while ($x > 0) {
                $x = $x - (((pow($lambAway, $awayGoals)) * exp(-1 * $lambAway)) / floatval(gmp_fact($awayGoals)));
                $awayGoals += 1;
           }
           return ($awayGoals - 1);
        }
    }
}
