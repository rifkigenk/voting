<?php
require_once 'connection.php';

echo "<h2>Adding Sample Data...</h2>";

// Add sample voters
$voters = [
    ['12345678901', 'Budi Santoso', 'X A'],
    ['12345678902', 'Siti Nurhaliza', 'X B'],
    ['12345678903', 'Ahmad Wijaya', 'X C'],
    ['12345678904', 'Rina Wijaya', 'X A'],
    ['12345678905', 'Doni Sutrisno', 'X B'],
];

$sql = "INSERT IGNORE INTO voters (nisn, name, class) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

foreach ($voters as $voter) {
    $stmt->bind_param("sss", $voter[0], $voter[1], $voter[2]);
    $stmt->execute();
}
echo "✓ Added " . count($voters) . " sample voters<br>";

// Add sample candidates
$candidates = [
    ['Rini Wijaya', 'Ketua Organisasi', 'XI A', 'Siap memimpin dengan integritas dan dedikasi penuh'],
    ['Doni Sutrisno', 'Ketua Organisasi', 'XI B', 'Inovasi untuk kemajuan organisasi bersama'],
    ['Ani Suryanto', 'Wakil Ketua', 'XI A', 'Mendukung penuh kepemimpinan yang solid'],
    ['Budi Raharjo', 'Wakil Ketua', 'XI C', 'Komitmen penuh untuk kesuksesan organisasi'],
    ['Siti Munawaroh', 'Sekretaris', 'XI A', 'Profisional dalam administrasi dan dokumentasi'],
    ['Ahmad Fauzi', 'Bendahara', 'XI B', 'Transparan dan akuntabel dalam keuangan'],
];

$sql = "INSERT IGNORE INTO candidates (name, position, class, bio) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

foreach ($candidates as $candidate) {
    $stmt->bind_param("ssss", $candidate[0], $candidate[1], $candidate[2], $candidate[3]);
    $stmt->execute();
}
echo "✓ Added " . count($candidates) . " sample candidates<br>";

echo "<br><strong>✅ Sample data added successfully!</strong><br>";
echo "<hr>";
echo "<h3>🎉 Your Voting Application is Ready!</h3>";
echo "<p><strong>Login Credentials:</strong></p>";
echo "<ul>";
echo "<li><strong>Admin Username:</strong> admin</li>";
echo "<li><strong>Admin Password:</strong> admin123</li>";
echo "<li><strong>Test Voter NISN:</strong> 12345678901</li>";
echo "<li><strong>Test Voter Name:</strong> Budi Santoso</li>";
echo "</ul>";
echo "<hr>";
echo "<p><strong>Quick Links:</strong></p>";
echo "<ul>";
echo "<li><a href='index.php' target='_blank'>📱 Home</a></li>";
echo "<li><a href='voter_login.php' target='_blank'>🗳️ Voter Login</a></li>";
echo "<li><a href='results.php' target='_blank'>📊 View Results</a></li>";
echo "<li><a href='admin_login.php' target='_blank'>🔐 Admin Dashboard</a></li>";
echo "</ul>";

$conn->close();
?>
