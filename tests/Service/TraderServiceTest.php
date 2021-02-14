<?php

namespace App\Tests\Service;

use App\Service\TraderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TraderServiceTest extends KernelTestCase
{

    //To test private and protected methods
    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    public function testIsFair()
    {
        $traderService = new TraderService();
        
        $isFair = self::callMethod($traderService, 'isFair', [10]);
        $this->assertTrue($isFair, 'Number less than 10 shoud be fair');

        $isFair = self::callMethod($traderService, 'isFair', [11]);
        $this->assertFalse($isFair, 'Number greater than 10 shoud be fair');
    }

    public function testCalcPercentDiff()
    {
        $traderService = new TraderService();
        
        $percentDiff = self::callMethod($traderService, 'calcPercentDiff', [100, 110]);
        $this->assertGreaterThanOrEqual(-10, $percentDiff, 'Numbers 100 and 110 should have less than 10 percent of difference');
        
        $percentDiff = self::callMethod($traderService, 'calcPercentDiff', [110, 100]);
        $this->assertLessThanOrEqual(10, $percentDiff, 'Numbers 100 and 110 should have less than 10 percent of difference');


        $percentDiff = self::callMethod($traderService, 'calcPercentDiff', [100, 120]);
        $this->assertLessThanOrEqual(-10, $percentDiff, 'Numbers 100 and 120 should have more than 10 percent of difference');
        
        $percentDiff = self::callMethod($traderService, 'calcPercentDiff', [120, 100]);
        $this->assertGreaterThanOrEqual(10, $percentDiff, 'Numbers 100 and 120 should have more than 10 percent of difference');
    }

    public function testSumBaseExperience()
    {
        $traderService = new TraderService();
        
        $pokemon1 = new \StdClass();
        $pokemon1->base_experience = 100;
        $pokemon2 = new \StdClass();
        $pokemon2->base_experience = 100;

        $pokemons = [ $pokemon1, $pokemon2 ];
        $totalXP = $traderService->sumBaseExperience($pokemons);
        $this->assertEquals(200, $totalXP, 'Sum of base experience of 2 pokenons of 100 each, shoud be 200');
    }

    public function testCalculateFairTrade()
    {
        $traderService = new TraderService();
        
        $pokemon1 = new \StdClass();
        $pokemon1->base_experience = 100;
        $pokemon2 = new \StdClass();
        $pokemon2->base_experience = 100;

        $pokemon3 = new \StdClass();
        $pokemon3->base_experience = 110;
        $pokemon4 = new \StdClass();
        $pokemon4->base_experience = 110;

        $offered = [ $pokemon1, $pokemon2 ];
        $received = [ $pokemon3, $pokemon4 ];

        $isFair = $traderService->calculateFairTrade($offered, $received);
        $this->assertTrue($isFair, 'Trade with less than 10 percent of difference of base experience on both sizes, shoud be fair');


        
        $pokemon3 = new \StdClass();
        $pokemon3->base_experience = 120;
        $pokemon4 = new \StdClass();
        $pokemon4->base_experience = 120;

        $received = [ $pokemon3, $pokemon4 ];

        $isFair = $traderService->calculateFairTrade($offered, $received);
        $this->assertFalse($isFair, 'Trade with less than 10 percent of difference of base experience on both sizes, shoud not be fair');
    }
}
