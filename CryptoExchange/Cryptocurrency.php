<?php

namespace Reelz222z\CryptoExchange;

class Cryptocurrency extends Entity
{
    private int $id;
    private string $name;
    private string $symbol;
    private string $slug;
    private int $cmcRank;
    private int $numMarketPairs;
    private float $circulatingSupply;
    private float $totalSupply;
    private ?float $maxSupply;
    protected string $infiniteSupply; // Adjusted access level to match parent class
    private Quote $quote;

    public function __construct(
        int $id,
        string $name,
        string $symbol,
        string $slug,
        int $cmcRank,
        int $numMarketPairs,
        float $circulatingSupply,
        float $totalSupply,
        ?float $maxSupply,
        ?string $infiniteSupply,
        Quote $quote
    ) {
        parent::__construct($infiniteSupply ?? ''); // Call to the parent constructor
        $this->id = $id;
        $this->name = $name;
        $this->symbol = $symbol;
        $this->slug = $slug;
        $this->cmcRank = $cmcRank;
        $this->numMarketPairs = $numMarketPairs;
        $this->circulatingSupply = $circulatingSupply;
        $this->totalSupply = $totalSupply;
        $this->maxSupply = $maxSupply;
        $this->infiniteSupply = $infiniteSupply ?? '';
        $this->quote = $quote;
    }

    // Getter methods

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCmcRank(): int
    {
        return $this->cmcRank;
    }

    public function getNumMarketPairs(): int
    {
        return $this->numMarketPairs;
    }

    public function getCirculatingSupply(): float
    {
        return $this->circulatingSupply;
    }

    public function getTotalSupply(): float
    {
        return $this->totalSupply;
    }

    public function getMaxSupply(): ?float
    {
        return $this->maxSupply;
    }

    public function getInfiniteSupply(): string
    {
        return $this->infiniteSupply;
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }
}
