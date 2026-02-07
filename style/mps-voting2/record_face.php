<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../php/connection.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['descriptor']) || !isset($data['nisn'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
    exit;
}

$descriptor = $data['descriptor'];
$nisn = $data['nisn'];

if (!is_array($descriptor) || empty($nisn)) {
    echo json_encode(['status'=>'error','message'=>'Invalid data']);
    exit;
}

$descriptor_json = json_encode($descriptor);

$sql = "UPDATE voters SET face_descriptor = ? WHERE nisn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $descriptor_json, $nisn);
if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Face recorded']);
} else {
    echo json_encode(['status'=>'error','message'=>$conn->error]);
}

$conn->close();
?>