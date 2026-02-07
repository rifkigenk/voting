<?php
$url = 'http://localhost/Projek%20MPS/mps-voting/style/mps-voting2/record_face.php';

// Sample descriptor (128 floats)
$descriptor = array_fill(0, 128, 0.012345);
$data = [
    'nisn' => '12345678901',
    'descriptor' => $descriptor
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
        'timeout' => 10
    ]
];

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "Request failed\n";
    if (isset($http_response_header)) {
        echo implode("\n", $http_response_header);
    }
} else {
    echo "Response:\n" . $result . "\n";
}
?>