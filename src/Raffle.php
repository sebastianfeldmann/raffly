<?php

namespace SebastianFeldmann\Raffly;

/**
 * Raffle utility class
 * 
 * Contains static methods for handling raffle ID operations
 */
class Raffle {
    
    /**
     * Extract raffle ID from URL path
     * 
     * @param string $path The URL path (e.g., '/raffle/abc123')
     * @return string|null The extracted raffle ID or null if invalid/missing
     */
    public static function extractRaffleIdFromPath($path) {
        $pathParts = explode('/', trim($path, '/'));
        $raffleId = end($pathParts);

        if (empty($raffleId) || !preg_match('/^[a-z0-9]{5}$/', $raffleId)) {
            return null;
        }

        return $raffleId;
    }
    
    /**
     * Generate a unique raffle ID
     * 
     * Creates a 5-character alphanumeric ID that doesn't conflict with existing raffles
     * 
     * @param string|null $dataDir Custom data directory path (optional)
     * @return string The generated unique raffle ID
     */
    public static function generateRaffleId($dataDir = null) {
        if ($dataDir === null) {
            $dataDir = dirname(__DIR__) . '/data';
        }
        
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = 5;
        
        do {
            $raffleId = '';
            for ($i = 0; $i < $length; $i++) {
                $raffleId .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $filePath = $dataDir . '/' . $raffleId . '.json';
        } while (file_exists($filePath)); // Ensure unique ID
        
        return $raffleId;
    }
}
