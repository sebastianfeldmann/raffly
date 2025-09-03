<?php

namespace SebastianFeldmann\Raffly;

/**
 * Simple Router for Raffle App
 * 
 * Handles routing requests to appropriate page handlers based on URL patterns.
 */
class Router {
    private $routes = [];
    
    /**
     * Add a route to the router
     * 
     * @param string $pattern URL pattern (e.g., '/raffle/*')
     * @param string $file Path to the file containing the handler function
     * @param string $function Name of the handler function
     */
    public function addRoute($pattern, $file, $function) {
        $this->routes[] = [
            'pattern' => $pattern,
            'file' => $file,
            'function' => $function
        ];
    }
    
    /**
     * Handle the current request by matching against registered routes
     */
    public function handleRequest() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';
        
        foreach ($this->routes as $route) {
            if ($this->matchPattern($route['pattern'], $path)) {
                require_once $route['file'];
                $function = $route['function'];
                return $function($path);
            }
        }
        
        // 404 if no route matches
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page Not Found';
        exit;
    }
    
    /**
     * Check if a URL path matches a route pattern
     * 
     * @param string $pattern Route pattern with * wildcards
     * @param string $path URL path to match
     * @return bool True if pattern matches
     */
    private function matchPattern($pattern, $path) {
        // Convert pattern to regex
        $pattern = str_replace('*', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $path);
    }
}
