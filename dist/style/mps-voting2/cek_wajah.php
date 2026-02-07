<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// include DB connection
require_once '../../php/connection.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['descriptor'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$descriptor = $data['descriptor'];
if (!is_array($descriptor)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid descriptor']);
    exit;
}

$threshold = 0.6;
$bestMatch = null;
$bestDist = INF;

$sql = "SELECT voter_id, nisn, name, has_voted, face_descriptor FROM voters WHERE face_descriptor IS NOT NULL AND face_descriptor != ''";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stored = json_decode($row['face_descriptor'], true);
        if (!is_array($stored) || count($stored) !== count($descriptor)) continue;

        $sum = 0.0;
        for ($i=0; $i<count($descriptor); $i++) {
            $diff = floatval($descriptor[$i]) - floatval($stored[$i]);
            $sum += $diff * $diff;
        }
        $dist = sqrt($sum);
        if ($dist < $bestDist) {
            $bestDist = $dist;
            $bestMatch = $row;
        }
    }
}

if ($bestMatch && $bestDist <= $threshold) {
    // Store voter info in session for vote.php
    $_SESSION['voter_id'] = intval($bestMatch['voter_id']);
    $_SESSION['nisn'] = $bestMatch['nisn'];
    $_SESSION['name'] = $bestMatch['name'];
    
    // Get voter class from database
    $sql_class = "SELECT class FROM voters WHERE voter_id = ?";
    $stmt = $conn->prepare($sql_class);
    $stmt->bind_param("i", $_SESSION['voter_id']);
    $stmt->execute();
    $result_class = $stmt->get_result();
    if ($row_class = $result_class->fetch_assoc()) {
        $_SESSION['class'] = $row_class['class'];
    }
    
    // return matched voter info
    echo json_encode([
        'status' => 'match',
        'voter_id' => intval($bestMatch['voter_id']),
        'nisn' => $bestMatch['nisn'],
        'name' => $bestMatch['name'],
        'has_voted' => intval($bestMatch['has_voted']),
        'distance' => $bestDist
    ]);
} else {
    echo json_encode(['status' => 'no_match']);
}

$conn->close();
?>