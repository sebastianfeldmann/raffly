<?php

function delete_participant($path) {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    header('Content-Type: application/json');
    
    // Get POST data
    $raffleId    = $_POST['raffleID'] ?? null;
    $participant = $_POST['participant'] ?? null;
    
    if (!$raffleId || !$participant) {
        echo json_encode(['error' => 'Missing raffleID or participant']);
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

    // Find and remove participant
    $participantIndex = null;
    foreach ($raffleData['participants'] as $index => $currentParticipant) {
        if ($currentParticipant === $participant) {
            $participantIndex = $index;
            break;
        }
    }
    
    if ($participantIndex === null) {
        echo json_encode(['error' => 'Participant not found']);
        exit;
    }
    
    // Remove participant and re-index array
    unset($raffleData['participants'][$participantIndex]);
    $raffleData['participants'] = array_values($raffleData['participants']);
    
    // Save updated data
    if (file_put_contents($filePath, json_encode($raffleData, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Participant deleted successfully',
            'remainingParticipants' => count($raffleData['participants'])
        ]);
    } else {
        echo json_encode(['error' => 'Failed to save changes']);
    }
}
