<?php
require_once 'connection.php';

$sql = "ALTER TABLE voters ADD COLUMN IF NOT EXISTS face_descriptor TEXT NULL";
// MySQL does not support IF NOT EXISTS for ADD COLUMN pre-8.0, so check first
$colCheck = $conn->query("SHOW COLUMNS FROM voters LIKE 'face_descriptor'");
if ($colCheck->num_rows == 0) {
    if ($conn->query("ALTER TABLE voters ADD COLUMN face_descriptor TEXT NULL") === TRUE) {
        echo "Column 'face_descriptor' added successfully.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'face_descriptor' already exists.<br>";
}

$conn->close();
?>