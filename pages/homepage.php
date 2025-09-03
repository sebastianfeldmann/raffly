<?php

use SebastianFeldmann\Raffly\Raffle;

function homepage($path) {
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        
        if (empty($title)) {
            $error = 'Please enter a title for your raffle.';
        } else {
            // Generate 5-character random URL
            $raffleId = Raffle::generateRaffleId();
            
            // Create raffle data
            $raffleData = [
                'title' => $title,
                'participants' => [],
                'winners' => []
            ];
            
            // Ensure data directory exists
            $dataDir = dirname(__DIR__) . '/data';
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            
            // Save to JSON file
            file_put_contents($dataDir . '/' . $raffleId . '.json', json_encode($raffleData, JSON_PRETTY_PRINT));
            
            // Redirect to signup page
            header('Location: /raffle/' . $raffleId);
            exit;
        }
    }
    
    // Render homepage template
    include dirname(__DIR__) . '/templates/homepage.tpl.php';
}
