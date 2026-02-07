<?php
session_start();
require_once 'connection.php';

$voter_id = $_SESSION['voter_id'];

// Mark voter as voted
$sql = "UPDATE voters SET has_voted = 1 WHERE voter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voter_id);
$stmt->execute();

// Destroy session
session_destroy();

// Redirect to thank you page
header("Location: thank_you.php");
exit();
?>
