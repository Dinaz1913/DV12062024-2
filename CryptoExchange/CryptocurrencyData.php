<?php

namespace Reelz222z\CryptoExchange;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CryptocurrencyData
{
    private Client $client;
    private string $apiUrl;
    private string $apiKey;
    private array $cryptocurrencies;

    public function __construct(Client $client, string $apiUrl, string $apiKey)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->cryptocurrencies = $this->fetchTopCryptocurrencies();
    }

    /**
     * @throws GuzzleException
     */
    public function fetchTopCryptocurrencies(): array
    {
        $response = $this->client->request('GET', $this->apiUrl, [
            'headers' => [
                'X-CMC_PRO_API_KEY' => $this->apiKey,
                'Accept' => 'application/json'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        $cryptocurrencies = [];
        foreach ($data['data'] as $cryptoData) {
            $quote = new Quote(
                $cryptoData['quote']['USD']['price'],
                $cryptoData['quote']['USD']['volume_24h'],
                $cryptoData['quote']['USD']['volume_change_24h'],
                $cryptoData['quote']['USD']['percent_change_1h'],
                $cryptoData['quote']['USD']['percent_change_24h'],
                $cryptoData['quote']['USD']['percent_change_7d'],
                $cryptoData['quote']['USD']['market_cap'],
                $cryptoData['quote']['USD']['market_cap_dominance'],
                $cryptoData['quote']['USD']['fully_diluted_market_cap'],
                $cryptoData['quote']['USD']['last_updated']
            );

            $cryptocurrencies[] = new Cryptocurrency(
                $cryptoData['id'],
                $cryptoData['name'],
                $cryptoData['symbol'],
                $cryptoData['slug'],
                $cryptoData['cmc_rank'],
                $cryptoData['num_market_pairs'],
                $cryptoData['circulating_supply'],
                $cryptoData['total_supply'],
                $cryptoData['max_supply'],
                $cryptoData['infinite_supply'] ?? '',
                $quote
            );
        }

        return $cryptocurrencies;
    }

    /**
     * @throws GuzzleException
     */
    public function getCryptocurrencyBySymbol(string $symbol): ?Cryptocurrency
    {
        foreach ($this->fetchTopCryptocurrencies() as $crypto) {
            if (trim(strtolower($crypto->getSymbol())) === trim(strtolower($symbol))) {
                return $crypto;
            }
        }
        return null;
    }
    public function getCryptocurrencyBySymbolSecond(string $symbol): ?Cryptocurrency
    {
        foreach ($this->cryptocurrencies as $crypto) {
            if ($crypto->getSymbol() === $symbol) {
                return $crypto;
            }
        }
        return null;
    }
}
