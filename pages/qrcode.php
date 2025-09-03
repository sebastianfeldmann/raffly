<?php

use SebastianFeldmann\Raffly\Raffle;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function qrcode($path) {
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
    $baseUrl = $_SERVER['REQUEST_SCHEME'] ?? 'http' . '://' . $_SERVER['HTTP_HOST'];
    $url     = $baseUrl . '/signup/' . $raffleId;

    $qrCode  = new QrCode($url);
    $writer  = new PngWriter();    
    $result  = $writer->write($qrCode);

    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
    exit;
}