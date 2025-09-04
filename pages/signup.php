<?php

use SebastianFeldmann\Raffly\Raffle;

function sanitizeName($name) {
    // Remove leading/trailing whitespace
    $name = trim($name);
    
    // Remove dangerous special characters but keep international characters
    // Allow: letters (including accented), numbers, spaces, hyphens, apostrophes
    // Remove: quotes, dollar signs, percent, section sign, and other special chars
    $name = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', $name);
    
    // Replace multiple spaces with single space
    $name = preg_replace('/\s+/', ' ', $name);
    
    // Remove leading/trailing spaces again after cleanup
    $name = trim($name);
    
    return $name;
}

function signup($path) {
    // Extract raffle ID from path
    $raffleId = Raffle::extractRaffleIdFromPath($path);
    
    if ($raffleId === null) {
        header('HTTP/1.0 404 Not Found');
        exit('Raffle not found.');
    }

    $filePath = DATA_DIR . $raffleId . '.json';

    if (!file_exists($filePath)) {
        header('HTTP/1.0 404 Not Found');
        exit('Raffle not found.');
    }

    $raffleData = json_decode(file_get_contents($filePath), true);
    
    $error = '';
    $success = '';
    $rawName = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rawName = trim($_POST['name'] ?? '');
        $name = sanitizeName($rawName);
        
        if (empty($name)) {
            $error = 'Please enter a valid name. Only letters, numbers, spaces, and hyphens are allowed.';
        } elseif ($name !== $rawName && !empty($rawName)) {
            $error = 'Name contains invalid characters. Only letters, numbers, spaces, and hyphens are allowed. Your name would become: "' . htmlspecialchars($name) . '"';
        } elseif (strlen($name) > 20) {
            $error = 'Name must be 20 characters or less.';
        } elseif (in_array($name, $raffleData['participants'])) {
            $error = 'This name is already registered for this raffle.';
        } else {
            // Add participant
            $raffleData['participants'][] = $name;
            
            // Save updated data
            file_put_contents($filePath, json_encode($raffleData, JSON_PRETTY_PRINT));
            
            $success = 'Successfully registered for the raffle!';
            $rawName = '';
        }
    }
    
    // Render signup template
    include TPL_DIR . 'signup.tpl.php';
}
