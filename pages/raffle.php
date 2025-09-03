<?php

use SebastianFeldmann\Raffly\Raffle;

function raffle($path) {
    // Extract raffle ID from path
    $raffleId = Raffle::extractRaffleIdFromPath($path);
    
    if ($raffleId === null) {
        header('HTTP/1.0 404 Not Found');
        exit('Raffle not found.');
    }

    $dataDir = dirname(__DIR__) . '/data';
    $filePath = $dataDir . '/' . $raffleId . '.json';

    if (!file_exists($filePath)) {
        header('HTTP/1.0 404 Not Found');
        exit('Raffle not found.');
    }

    $raffleData = json_decode(file_get_contents($filePath), true);
    
    // Render raffle template
    include dirname(__DIR__) . '/templates/raffle.tpl.php';
}
