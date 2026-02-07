<?php
// Database Setup - Run this file once to create tables
require_once 'connection.php';

// Create Voters Table
$sql_voters = "CREATE TABLE IF NOT EXISTS voters (
    voter_id INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    class VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    has_voted INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Create Candidates Table
$sql_candidates = "CREATE TABLE IF NOT EXISTS candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    class VARCHAR(20),
    photo VARCHAR(255),
    bio TEXT,
    votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Create Votes Table
$sql_votes = "CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id INT NOT NULL,
    candidate_id INT NOT NULL,
    position VARCHAR(100),
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE
)";

// Create Admin Table
$sql_admin = "CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute queries
if ($conn->query($sql_voters) === TRUE) {
    echo "Voters table created successfully.<br>";
} else {
    echo "Error creating voters table: " . $conn->error . "<br>";
}

if ($conn->query($sql_candidates) === TRUE) {
    echo "Candidates table created successfully.<br>";
} else {
    echo "Error creating candidates table: " . $conn->error . "<br>";
}

if ($conn->query($sql_votes) === TRUE) {
    echo "Votes table created successfully.<br>";
} else {
    echo "Error creating votes table: " . $conn->error . "<br>";
}

if ($conn->query($sql_admin) === TRUE) {
    echo "Admin table created successfully.<br>";
} else {
    echo "Error creating admin table: " . $conn->error . "<br>";
}

echo "<br><strong>Database setup completed!</strong><br>";
echo "You can now delete this file or comment out the execution.";

$conn->close();
?>
