<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Reelz222z\CryptoExchange\User;
use Reelz222z\CryptoExchange\CryptocurrencyData;
use Reelz222z\CryptoExchange\TransactionHistory;

$client = new Client();
$apiUrl = 'https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
$apiKey = 'b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c';

$usersFilePath = 'users.json';
$transactionHistoryFilePath = 'transactions.json';

$users = User::loadUsers($usersFilePath);

// Get the username from the command line
$username = readline("Enter your username: ");

// Find the user
$user = null;
foreach ($users as $u) {
    if ($u->getName() === $username) {
        $user = $u;
        break;
    }
}

if ($user === null) {
    echo "User not found.\n";
    exit;
}
    echo "User found: " . $user->getName() . " with wallet balance: " . $user->getWallet()->getBalance() . " USD\n";


// Fetch top cryptocurrencies
$cryptoData = new CryptocurrencyData($client, $apiUrl, $apiKey);
$topCryptos = $cryptoData->fetchTopCryptocurrencies();
echo "Top cryptocurrencies fetched successfully.\n";

$transactionHistory = new TransactionHistory($transactionHistoryFilePath);

function displayMenu(): void
{
    echo "Choose an option:\n";
    echo "1. List top cryptocurrencies\n";
    echo "2. Search cryptocurrency by symbol\n";
    echo "3. Buy cryptocurrency\n";
    echo "4. Sell cryptocurrency\n";
    echo "5. Display wallet state\n";
    echo "6. Display transaction history\n";
    echo "7.exit\n";
}

while (true) {
    displayMenu();
    $choice = (int) readline("Enter your choice: ");

    switch ($choice) {
        case 1:
            echo "Available Cryptocurrencies:\n";
            foreach ($topCryptos as $crypto) {
                echo "Name: " . $crypto->getName() . " - Symbol: " . $crypto->getSymbol() . "\n";
                echo "Market Cap Dominance: " . $crypto->getQuote()->getMarketCapDominance() . "\n"; // Accessing specific data
                echo "Price: " . $crypto->getQuote()->getPrice() . " Dol". "\n";
            }
            break;

        case 2:
            $symbol = readline("Enter the cryptocurrency symbol: ");
            $crypto = $cryptoData->getCryptocurrencyBySymbol($symbol);
            if ($crypto == null) {
                echo "Cryptocurrency not found.\n";
                exit(1);
            }
                echo "Name: " . $crypto->getName() . "\n";
                echo "Symbol: " . $crypto->getSymbol() . "\n";
                echo "Market Cap: $" . $crypto->getQuote()->getMarketCap() . "\n";
                echo "Price: $" . $crypto->getQuote()->getPrice() . "\n";
                echo "Market Cap Dominance: " . $crypto->getQuote()->getMarketCapDominance() . "\n"; // Accessing specific data
            break;

        case 3:
            $symbol = readline("Enter the cryptocurrency symbol to buy: ");
            $crypto = $cryptoData->getCryptocurrencyBySymbol($symbol);
            if ($crypto == null) {
                echo "Cryptocurrency not found.\n";
                exit(1);
            }
            echo "Name: " . $crypto->getName() . "\n";
            echo "Symbol: " . $crypto->getSymbol() . "\n";
            echo "Price: $" . $crypto->getQuote()->getPrice() . "\n";
            $Choice =(string) readline("Do you want to purchase this Value? (yes/no):\n");
            switch ($Choice) {
                case trim(strtolower('yes')):
                    $amount = (float) readline("Enter the amount to buy: ");
                    $user->buyCryptocurrency($crypto, $amount);
                    $transactionHistory->addTransaction($username, date('Y-m-d H:i:s'), 'buy', $crypto->getSymbol(), $amount, $crypto->getQuote()->getPrice(), $crypto->getQuote()->getPrice() * $amount);
                    echo "Bought $amount of " . $crypto->getName() . "\n";
                    User::saveUsers($usersFilePath, $users);
                    $transactionHistory->saveTransactions();
                    break;
                    case trim(strtolower('no')):
                        break;
                default:
                    exit(1);
            }
            break;

        case 4:
            $symbol = readline("Enter the cryptocurrency symbol to sell: ");
            $crypto = $cryptoData->getCryptocurrencyBySymbolSecond($symbol);
            if ($crypto == null) {
                echo "Cryptocurrency not found.\n";
            }
                $amount = (float) readline("Enter the amount to sell: ");
                $user->sellCryptocurrency($crypto, $amount);
                $transactionHistory->addTransaction($username, date('Y-m-d H:i:s'), 'sell', $crypto->getSymbol(), $amount, $crypto->getQuote()->getPrice(), $crypto->getQuote()->getPrice() * $amount);
                echo "Sold $amount of " . $crypto->getName() . "\n";
                User::saveUsers($usersFilePath, $users);
                $transactionHistory->saveTransactions();

                echo "Cryptocurrency not found.\n";

            break;

        case 5:
            echo "Current Wallet State:\n";
            echo "Balance: " . $user->getWallet()->getBalance() . " USD\n";
            echo "Portfolio: \n";
            foreach ($user->getPortfolio() as $symbol => $amount) {
                echo "$symbol: $amount\n";
            }
            break;

        case 6:
            echo "Transaction History:\n";
            foreach ($transactionHistory->getTransactions() as $transaction) {
                echo $transaction['date'] . ": "
                    . $transaction['type'] . " "
                    . $transaction['amount']
                    . " of " . $transaction['symbol']
                    . " at $" . $transaction['price']
                    . " each. Total: $" . $transaction['total'] . "\n";
            }
            break;
        case 7:
            exit(2);

        default:
            echo "Invalid choice. Please try again.\n";
            break;
    }
}
