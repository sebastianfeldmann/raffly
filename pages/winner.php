<?php

use SebastianFeldmann\Raffly\Raffle;

function winner($path) {
    // Extract raffle ID from path
    $raffleId = Raffle::extractRaffleIdFromPath($path);
    
    if ($raffleId === null) {
        header('HTTP/1.0 404 Not Found');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Raffle not found']);
        exit;
    }

    $filePath = DATA_DIR . $raffleId . '.json';

    if (!file_exists($filePath)) {
        header('HTTP/1.0 404 Not Found');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Raffle not found']);
        exit;
    }

    $raffleData = json_decode(file_get_contents($filePath), true);

    // Handle AJAX requests for processing winner
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        
        if (empty($raffleData['participants'])) {
            echo json_encode(['error' => 'No participants to pick from!']);
            exit;
        }
        
        // Get winner sent from frontend
        $winner = $_POST['winner'] ?? null;

        if (!$winner) {
            echo json_encode(['error' => 'No winner specified!']);
            exit;
        }
        
        // Find the winner index by looping through participants
        $winnerIndex = null;
        foreach ($raffleData['participants'] as $index => $participant) {
            if ($participant === $winner) {
                $winnerIndex = $index;
                break;
            }
        }
        
        // Validate that winner was found in participants
        if ($winnerIndex === null) {
            echo json_encode(['error' => 'Winner not found in participants list!']);
            exit;
        }
        
        // Move winner from participants to winners
        $raffleData['winners'][] = $winner;
        unset($raffleData['participants'][$winnerIndex]);
        $raffleData['participants'] = array_values($raffleData['participants']); // Re-index array
        
        // Save updated data
        file_put_contents($filePath, json_encode($raffleData, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'winner' => $winner,
            'remainingParticipants' => count($raffleData['participants'])
        ]);
        exit;
    } else {
        // Only POST requests are allowed for this endpoint
        header('HTTP/1.0 405 Method Not Allowed');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
}
