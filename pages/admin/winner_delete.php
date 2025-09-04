<?php

function delete_winner($path) {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    header('Content-Type: application/json');
    
    // Get POST data
    $raffleId = $_POST['raffleID'] ?? null;
    $winner = $_POST['winner'] ?? null;
    $backToParticipants = $_POST['backToParticipants'] ?? 'true';
    
    // Convert string to boolean
    $backToParticipants = ($backToParticipants === 'true' || $backToParticipants === true);
    
    if (!$raffleId || !$winner) {
        echo json_encode(['error' => 'Missing raffleID or winner']);
        exit;
    }
    
    // Validate raffle ID format
    if (!preg_match('/^[a-z0-9]{5}$/', $raffleId)) {
        echo json_encode(['error' => 'Invalid raffle ID']);
        exit;
    }

    $filePath = DATA_DIR . $raffleId . '.json';

    if (!file_exists($filePath)) {
        echo json_encode(['error' => 'Raffle not found']);
        exit;
    }

    $raffleData = json_decode(file_get_contents($filePath), true);
    
    if (!$raffleData) {
        echo json_encode(['error' => 'Invalid raffle data']);
        exit;
    }

    // Initialize winners array if not exists
    if (!isset($raffleData['winners'])) {
        $raffleData['winners'] = [];
    }

    // Find and remove winner
    $winnerIndex = null;
    foreach ($raffleData['winners'] as $index => $currentWinner) {
        if ($currentWinner === $winner) {
            $winnerIndex = $index;
            break;
        }
    }
    
    if ($winnerIndex === null) {
        echo json_encode(['error' => 'Winner not found']);
        exit;
    }
    
    // Remove winner from winners array
    unset($raffleData['winners'][$winnerIndex]);
    $raffleData['winners'] = array_values($raffleData['winners']);
    
    // Add winner back to participants array only if requested
    if ($backToParticipants) {
        $raffleData['participants'][] = $winner;
    }
    
    // Update lastUpdated timestamp
    $raffleData['lastUpdated'] = time();
    
    // Save updated data
    if (file_put_contents($filePath, json_encode($raffleData, JSON_PRETTY_PRINT))) {
        $message = $backToParticipants ? 'Winner restored to participants successfully' : 'Winner deleted permanently';
        echo json_encode([
            'success' => true,
            'message' => $message,
            'remainingWinners' => count($raffleData['winners']),
            'totalParticipants' => count($raffleData['participants'])
        ]);
    } else {
        echo json_encode(['error' => 'Failed to save changes']);
    }
}
