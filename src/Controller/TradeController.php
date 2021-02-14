<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Repository\TradeRepository;
use App\Service\TraderService;
use App\Service\PokemonService;
use PokePHP\PokeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
    /**
     * @Route("/", name="trade_index", methods={"GET"})
     */
    public function index(TradeRepository $tradeRepository): Response
    {
        return $this->render('trade/index.html.twig', [
            'trades' => $tradeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/trade/create", name="trade_create", methods={"GET"})
     */
    public function create(): Response
    {
        $trade = new Trade();
        $trade->setCreatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();

        return $this->redirectToRoute('trade_show', ['id' => $trade->getId()]);
    }

    /**
     * @Route("/trade/{id}", name="trade_show", methods={"GET"})
     */
    public function show(Trade $trade, Request $request, PokemonService $pokemonService, TraderService $traderService): Response
    {
        $offeredPokemons = $pokemonService->loadInfoFromApi($trade->getOffered());
        $receivedPokemons = $pokemonService->loadInfoFromApi($trade->getReceived());

        $isFair = $traderService->calculateFairTrade($offeredPokemons, $receivedPokemons);

        $offeredTotalXP = $traderService->sumBaseExperience($offeredPokemons);
        $receivedTotalXP = $traderService->sumBaseExperience($receivedPokemons);


        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', null);

        $api = new PokeApi;
        $pokemonsResult = $api->resourceList('pokemon', $limit, $offset);
        $pokemonsResult = json_decode($pokemonsResult);
        
        if ($pokemonsResult->previous) {
            $pokemonsResult->previous = parse_url($pokemonsResult->previous)['query'];
        }

        if ($pokemonsResult->next) {
            $pokemonsResult->next = parse_url($pokemonsResult->next)['query'];
        }

        if ($pokemonsResult->results) {
            // Get ID without call api for detail of each row
            foreach($pokemonsResult->results as $key => $pokemon) {
                $id = $pokemonService->extractIdFromUrl($pokemon->url);
                $pokemonsResult->results[$key]->id = $id;
            }
        }

        return $this->render('trade/show.html.twig', [
            'trade' => $trade,
            'isFair' => $isFair,
            'offeredPokemons' => $offeredPokemons,
            'receivedPokemons' => $receivedPokemons,
            'offeredTotalXP' => $offeredTotalXP,
            'receivedTotalXP' => $receivedTotalXP,
            'pokemons' => $pokemonsResult,
        ]);
    }

    /**
     * @Route("/trade/{id}/offer/{pokemon}", name="trade_offer", methods={"GET"})
     */
    public function offer(Trade $trade, $pokemon, Request $request, TradeRepository $tradeRepository, TraderService $traderService): Response
    {
        $params = array_merge($request->query->all(), ['id' => $trade->getId()]);
        
        try {
            $trade->addOffered($pokemon);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('trade_show', $params);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();
        
        return $this->redirectToRoute('trade_show', $params);
    }

    /**
     * @Route("/trade/{id}/offer/{pokemon}/delete", name="trade_offer_delete", methods={"GET"})
     */
    public function deleteOffer(Trade $trade, $pokemon, Request $request, TradeRepository $tradeRepository, TraderService $traderService): Response
    {
        $params = array_merge($request->query->all(), ['id' => $trade->getId()]);
        
        try {
            $trade->removeOffered($pokemon);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('trade_show', $params);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();
        
        return $this->redirectToRoute('trade_show', $params);
    }

    /**
     * @Route("/trade/{id}/receive/{pokemon}", name="trade_receive", methods={"GET"})
     */
    public function receive(Trade $trade, $pokemon, Request $request, TradeRepository $tradeRepository, TraderService $traderService): Response
    {
        $params = array_merge($request->query->all(), ['id' => $trade->getId()]);
        
        try {
            $trade->addReceived($pokemon);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('trade_show', $params);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();
        
        return $this->redirectToRoute('trade_show', $params);
    }

    /**
     * @Route("/trade/{id}/receive/{pokemon}/delete", name="trade_receive_delete", methods={"GET"})
     */
    public function deleteReceiver(Trade $trade, $pokemon, Request $request, TradeRepository $tradeRepository, TraderService $traderService): Response
    {
        $params = array_merge($request->query->all(), ['id' => $trade->getId()]);
        
        try {
            $trade->removeReceived($pokemon);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('trade_show', $params);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();
        
        return $this->redirectToRoute('trade_show', $params);
    }

    /**
     * @Route("/trade/{id}", name="trade_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trade $trade): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trade->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trade_index');
    }

    /**
     * @Route("/trade/{id}/close", name="trade_close", methods={"GET"})
     */
    public function close(Trade $trade, PokemonService $pokemonService, TraderService $traderService): Response
    {
        //Duble check if the trade is fair
        $offeredPokemons = $pokemonService->loadInfoFromApi($trade->getOffered());
        $receivedPokemons = $pokemonService->loadInfoFromApi($trade->getReceived());
        $isFair = $traderService->calculateFairTrade($offeredPokemons, $receivedPokemons);
        
        if (!$isFair) {
            $this->addFlash('error', 'Essa troca não é justa.');
            return $this->redirectToRoute('trade_show', ['id' => $trade->getId()]);    
        }

        $trade->setTradedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trade);
        $entityManager->flush();
        
        $this->addFlash('success', 'Troca realizada com sucesso!');
        return $this->redirectToRoute('trade_show', ['id' => $trade->getId()]);
    }
}
