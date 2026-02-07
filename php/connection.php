<?php
// Database Configuration
// Support environment variables for cloud deployment (Wasmer, Docker, etc.)
// Fall back to local development settings if not set

define('DB_HOST', getenv('DATABASE_HOST') ?: 'localhost');
define('DB_USER', getenv('DATABASE_USER') ?: 'root');
define('DB_PASS', getenv('DATABASE_PASSWORD') ?: '');
define('DB_NAME', getenv('DATABASE_NAME') ?: 'mps_voting');
define('DB_PORT', getenv('DATABASE_PORT') ?: 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

?>
