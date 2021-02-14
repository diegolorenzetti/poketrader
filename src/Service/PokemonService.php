<?php

namespace App\Service;

use PokePHP\PokeApi;

class PokemonService
{
    public function __construct(PokeApi $pokeApi)
    {
        $this->pokeApi = $pokeApi;
    }

    public function loadInfoFromApi($pokemonNames)
    {
        $pokemons = [];
        foreach ($pokemonNames as $pokemonName) {
            $pokemons[] = json_decode($this->pokeApi->pokemon($pokemonName));
        }

        return $pokemons;
    }

    public function extractIdFromUrl($pokemonUrl)
    {
        $urlParts = explode('/', $pokemonUrl);
        return $urlParts[count($urlParts) - 2];
    }

}