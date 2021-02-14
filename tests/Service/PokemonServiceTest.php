<?php

namespace App\Tests\Service;

use App\Service\PokemonService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PokemonServiceTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $pokeApi;


    protected function setUp(): void
    {
        self::bootKernel();
        $this->pokeApi = self::$container->get('PokePHP\PokeApi');
    }

    public function testLoadInfoFromApi()
    {
        $pokemonService = new PokemonService($this->pokeApi);
        
        $pokemonNames = ['bulbasaur'];
        $pokemons = $pokemonService->loadInfoFromApi($pokemonNames);
        
        $pokemon = $pokemons[0];

        $this->assertInstanceOf(\StdClass::class, $pokemon, 'Load should return a StdClass Object');
        $this->assertObjectHasAttribute('id', $pokemon, 'Should have id property');
        $this->assertObjectHasAttribute('name', $pokemon, 'Should have name property');
        $this->assertObjectHasAttribute('base_experience', $pokemon, 'Should have base_experience property');
        $this->assertObjectHasAttribute('sprites', $pokemon, 'Should have sprites property');
    }

    public function testExtractIdFromUrl()
    {
        $pokemonService = new PokemonService($this->pokeApi);
        
        $url = 'https://pokeapi.co/api/v2/pokemon/1/';
        $id = $pokemonService->extractIdFromUrl($url);
        
        $this->assertEquals(1, $id, 'Extract from URL should return 1');
    }
}
