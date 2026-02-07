<?php
session_start();
require_once 'connection.php';

// Check if voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];
$error = '';
$success = '';

// Handle voting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = $_POST['candidate_id'];
    $position = $_POST['position'];
    
    // Check if voter has already voted for this position
    $check_sql = "SELECT * FROM votes WHERE voter_id = ? AND position = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $voter_id, $position);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // Insert vote
        $insert_sql = "INSERT INTO votes (voter_id, candidate_id, position) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $voter_id, $candidate_id, $position);
        
        // Update candidate votes
        $update_sql = "UPDATE candidates SET votes = votes + 1 WHERE candidate_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $candidate_id);
        
        if ($insert_stmt->execute() && $update_stmt->execute()) {
            $success = "Suara Anda telah dicatat untuk " . htmlspecialchars($position);
        } else {
            $error = "Terjadi kesalahan saat mencatat suara.";
        }
    } else {
        $error = "Anda sudah melakukan voting untuk posisi ini.";
    }
}

// Get all positions
$positions = [];
$positions_sql = "SELECT DISTINCT position FROM candidates ORDER BY position";
$positions_result = $conn->query($positions_sql);
while ($row = $positions_result->fetch_assoc()) {
    $positions[] = $row['position'];
}

// Get candidates by position
$candidates_by_position = [];
foreach ($positions as $position) {
    $sql = "SELECT * FROM candidates WHERE position = ? ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $position);
    $stmt->execute();
    $candidates_by_position[$position] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Check if voter has voted
$voted_count_sql = "SELECT COUNT(*) as count FROM votes WHERE voter_id = ?";
$voted_count_stmt = $conn->prepare($voted_count_sql);
$voted_count_stmt->bind_param("i", $voter_id);
$voted_count_stmt->execute();
$voted_count_result = $voted_count_stmt->get_result();
$voted_count = $voted_count_result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting - MPS Elections 2026</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .voting-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .voter-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        
        .info-item {
            color: #666;
            font-size: 14px;
        }
        
        .info-item strong {
            color: #333;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .candidate-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .candidate-photo {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }
        
        .candidate-info {
            padding: 15px;
        }
        
        .candidate-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .candidate-class {
            font-size: 13px;
            color: #999;
            margin-bottom: 10px;
        }
        
        .candidate-bio {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
            min-height: 40px;
        }
        
        .vote-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .vote-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .vote-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .position-section {
            margin-bottom: 40px;
        }
        
        .position-title {
            background: white;
            padding: 15px 20px;
            border-left: 5px solid #667eea;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="voting-container">
        <div class="header">
            <h1>🗳️ Sistem Voting MPS Elections 2026</h1>
            <div class="voter-info">
                <div class="info-item">
                    <strong>Nama:</strong> <?php echo htmlspecialchars($_SESSION['voter_name']); ?>
                </div>
                <div class="info-item">
                    <strong>NISN:</strong> <?php echo htmlspecialchars($_SESSION['voter_nisn']); ?>
                </div>
                <div class="info-item">
                    <strong>Kelas:</strong> <?php echo htmlspecialchars($_SESSION['voter_class']); ?>
                </div>
                <div class="info-item">
                    <strong>Posisi Terbaru:</strong> <?php echo count($positions); ?> Posisi
                </div>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php foreach ($positions as $position): ?>
            <div class="position-section">
                <div class="position-title"><?php echo htmlspecialchars($position); ?></div>
                
                <div class="candidates-grid">
                    <?php foreach ($candidates_by_position[$position] as $candidate): ?>
                        <div class="candidate-card">
                            <div class="candidate-photo">👤</div>
                            <div class="candidate-info">
                                <div class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></div>
                                <div class="candidate-class"><?php echo htmlspecialchars($candidate['class']); ?></div>
                                <div class="candidate-bio"><?php echo htmlspecialchars($candidate['bio']); ?></div>
                                
                                <form method="POST" action="">
                                    <input type="hidden" name="candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
                                    <input type="hidden" name="position" value="<?php echo htmlspecialchars($position); ?>">
                                    <button type="submit" class="vote-btn">Pilih</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <a href="logout.php" class="logout-btn">Selesai Voting & Logout</a>
    </div>
</body>
</html>
