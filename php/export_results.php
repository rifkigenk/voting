<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=voting_results_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// Write BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header
fputcsv($output, array('Nama Kandidat', 'Posisi', 'Kelas', 'Jumlah Suara'));

// Get all candidates
$sql = "SELECT name, position, class, votes FROM candidates ORDER BY position, votes DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['name'],
        $row['position'],
        $row['class'],
        $row['votes']
    ));
}

// Add summary
fputcsv($output, array(''));

// Get statistics
$total_voters_sql = "SELECT COUNT(*) as total FROM voters";
$total_voters_result = $conn->query($total_voters_sql);
$total_voters = $total_voters_result->fetch_assoc()['total'];

$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes_result = $conn->query($total_votes_sql);
$total_votes = $total_votes_result->fetch_assoc()['total'];

fputcsv($output, array('Statistik'));
fputcsv($output, array('Total Pemilih', $total_voters));
fputcsv($output, array('Total Suara Masuk', $total_votes));

$participation_rate = $total_voters > 0 ? round(($total_votes / $total_voters) * 100, 2) : 0;
fputcsv($output, array('Tingkat Partisipasi (%)', $participation_rate));

fputcsv($output, array('Tanggal Export', date('Y-m-d H:i:s')));

fclose($output);
?>
