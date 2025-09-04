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
            if (!is_dir(DATA_DIR)) {
                mkdir(DATA_DIR, 0755, true);
            }
            
            // Save to JSON file
            file_put_contents(DATA_DIR . $raffleId . '.json', json_encode($raffleData, JSON_PRETTY_PRINT));
            
            // Redirect to signup page
            header('Location: /admin/raffle/' . $raffleId);
            exit;
        }
    }
    
    // Render homepage template
    include TPL_DIR . 'homepage.tpl.php';
}
