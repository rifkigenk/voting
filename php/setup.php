<?php
// Create database first, without selecting it
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS mps_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "Database 'mps_voting' created successfully.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

$conn->close();

// Now include the regular database setup
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
    echo "✓ Voters table created successfully.<br>";
} else {
    echo "Error creating voters table: " . $conn->error . "<br>";
}

if ($conn->query($sql_candidates) === TRUE) {
    echo "✓ Candidates table created successfully.<br>";
} else {
    echo "Error creating candidates table: " . $conn->error . "<br>";
}

if ($conn->query($sql_votes) === TRUE) {
    echo "✓ Votes table created successfully.<br>";
} else {
    echo "Error creating votes table: " . $conn->error . "<br>";
}

if ($conn->query($sql_admin) === TRUE) {
    echo "✓ Admin table created successfully.<br>";
} else {
    echo "Error creating admin table: " . $conn->error . "<br>";
}

// Insert default admin user
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$sql_insert_admin = "INSERT IGNORE INTO admin (username, password, email) VALUES ('admin', ?, 'admin@school.com')";
$stmt = $conn->prepare($sql_insert_admin);
$stmt->bind_param("s", $admin_hash);
if ($stmt->execute()) {
    echo "✓ Default admin account created (username: admin, password: admin123)<br>";
} else {
    echo "Admin account already exists or error: " . $conn->error . "<br>";
}

echo "<br><strong>✅ Database setup completed successfully!</strong><br>";
echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to Admin Panel: <a href='admin_login.php' target='_blank'>Login</a></li>";
echo "<li>Or Start Voting: <a href='voter_login.php' target='_blank'>Voter Login</a></li>";
echo "<li>View Results: <a href='results.php' target='_blank'>Results</a></li>";
echo "</ol>";

$conn->close();
?>
