<?php

use SebastianFeldmann\Raffly\Raffle;

function admin($path) {
    $successMessage = '';

    // Handle raffle deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_raffle'])) {
        $raffleId = $_POST['raffle_id'];
        if (preg_match('/^[a-z0-9]{5}$/', $raffleId)) {
            $filePath = DATA_DIR . $raffleId . '.json';
            if (file_exists($filePath)) {
                unlink($filePath);
                $successMessage = "Raffle '{$raffleId}' has been deleted.";
            }
        }
    }

    // Get all raffle files
    $raffles = [];
    if (is_dir(DATA_DIR)) {
        $files = glob(DATA_DIR . '*.json');
        foreach ($files as $file) {
            $raffleId = basename($file, '.json');
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                $raffles[] = [
                    'id'           => $raffleId,
                    'title'        => $data['title'],
                    'participants' => count($data['participants'] ?? []),
                    'winners'      => count($data['winners'] ?? []),
                    'lastUpdated'  => filemtime($file)
                ];
            }
        }
    }

    // Sort by creation date (newest first)
    usort($raffles, function($a, $b) {
        return $b['lastUpdated'] - $a['lastUpdated'];
    });
    
    // Render admin template
    include TPL_DIR . 'admin/raffles.tpl.php';
}
