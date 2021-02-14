<?php

namespace App\Entity;

use App\Repository\TradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TradeRepository::class)
 */
class Trade
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $offered = [];

    /**
     * @ORM\Column(type="array")
     */
    private $received = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tradedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffered(): ?array
    {
        return $this->offered;
    }

    public function addOffered(string $pokemon): self
    {
        if (count($this->offered) >= 6) {
            throw new \Exception('Você não pode adicionar mais de seis pokemons em uma troca.');
        }

        if (!in_array($pokemon, $this->offered)) {
            $this->offered[] = $pokemon;
        }

        return $this;
    }

    public function removeOffered(string $pokemon): self
    {
        $key = array_search($pokemon, $this->offered);
        if ($key !== false) {
            unset($this->offered[$key]);
        }

        return $this;
    }

    public function getReceived(): ?array
    {
        return $this->received;
    }

    public function addReceived(string $pokemon): self
    {
        if (count($this->received) >= 6) {
            throw new \Exception('Você não pode adicionar mais de seis pokemons em uma troca.');
        }

        if (!in_array($pokemon, $this->received)) {
            $this->received[] = $pokemon;
        }

        return $this;
    }

    public function removeReceived(string $pokemon): self
    {
        $key = array_search($pokemon, $this->received);
        if ($key !== false) {
            unset($this->received[$key]);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTradedAt(): ?\DateTimeInterface
    {
        return $this->tradedAt;
    }

    public function setTradedAt(?\DateTimeInterface $tradedAt): self
    {
        $this->tradedAt = $tradedAt;

        return $this;
    }

    public function getStatus()
    {
        return $this->tradedAt ? 'Fechada' : 'Aberta';
    }
}
