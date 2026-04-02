<?php
/**
 * Database Configuration
 * Business Listing & Rating System
 * 
 * Supports both Docker and local environments.
 * Set environment variables in docker-compose.yml or use defaults.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'mysql');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'root_password');
define('DB_NAME', getenv('DB_NAME') ?: 'business_listing');

/**
 * Get database connection
 * @return mysqli
 */
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Send JSON response
 * @param mixed $data
 * @param int $status
 */
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
