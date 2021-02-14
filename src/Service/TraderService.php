<?php

namespace App\Service;

class TraderService
{
    const FAIR_PERCENT_DIFF = 10;

    public function calculateFairTrade($player1Pokemons = [], $player2Pokemons = [])
    {
        $player1Xp = $this->sumBaseExperience($player1Pokemons);
        $player2Xp = $this->sumBaseExperience($player2Pokemons);

        $diff = $this->calcPercentDiff($player1Xp, $player2Xp);

        return $this->isFair($diff);
    }

    public function sumBaseExperience($pokemons)
    {
        $experienceTotal = 0;
        foreach ($pokemons as $pokemon) {
            $experienceTotal += (int)$pokemon->base_experience;
        }
        
        return $experienceTotal;
    }

    private function calcPercentDiff($number1, $number2)
    {
        if (empty($number1) || empty($number2)) {
            return 100;
        }
        
        return ($number1 / $number2 -1) * 100;
    }

    private function isFair($percent)
    {
        //10% of difference is a fair trade
        return ($percent < -self::FAIR_PERCENT_DIFF || $percent > self::FAIR_PERCENT_DIFF) ? false : true;
    }

}