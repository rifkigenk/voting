<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get statistics
$total_voters_sql = "SELECT COUNT(*) as total FROM voters";
$total_voters_result = $conn->query($total_voters_sql);
$total_voters = $total_voters_result->fetch_assoc()['total'];

$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes_result = $conn->query($total_votes_sql);
$total_votes = $total_votes_result->fetch_assoc()['total'];

$total_candidates_sql = "SELECT COUNT(*) as total FROM candidates";
$total_candidates_result = $conn->query($total_candidates_sql);
$total_candidates = $total_candidates_result->fetch_assoc()['total'];

$voted_voters_sql = "SELECT COUNT(*) as total FROM voters WHERE has_voted = 1";
$voted_voters_result = $conn->query($voted_voters_sql);
$voted_voters = $voted_voters_result->fetch_assoc()['total'];

$participation_rate = $total_voters > 0 ? round(($voted_voters / $total_voters) * 100, 2) : 0;

// Get top candidates
$top_candidates_sql = "SELECT name, position, votes FROM candidates ORDER BY votes DESC LIMIT 5";
$top_candidates_result = $conn->query($top_candidates_sql);
$top_candidates = $top_candidates_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MPS Elections 2026</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar h1 {
            font-size: 24px;
        }
        
        .logout-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #333;
        }
        
        .stat-label {
            font-size: 14px;
            color: #999;
            margin-top: 5px;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f5f7fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🗳️ Admin Dashboard - MPS Elections 2026</h1>
        <a href="index.php" class="logout-link">Logout</a>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number"><?php echo $total_voters; ?></div>
                <div class="stat-label">Total Pemilih</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?php echo $voted_voters; ?></div>
                <div class="stat-label">Pemilih yang Sudah Vote</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $participation_rate; ?>%</div>
                <div class="stat-label">Tingkat Partisipasi</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-number"><?php echo $total_candidates; ?></div>
                <div class="stat-label">Total Kandidat</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Top 5 Kandidat</div>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Kandidat</th>
                        <th>Posisi</th>
                        <th>Jumlah Suara</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($top_candidates as $candidate): 
                        $percentage = $total_votes > 0 ? round(($candidate['votes'] / $total_votes) * 100, 2) : 0;
                    ?>
                        <tr>
                            <td><?php echo $rank; ?></td>
                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                            <td><?php echo htmlspecialchars($candidate['position']); ?></td>
                            <td><?php echo $candidate['votes']; ?></td>
                            <td>
                                <div><?php echo $percentage; ?>%</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        $rank++;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Manajemen Data</div>
            <div class="action-buttons">
                <a href="manage_voters.php" class="btn btn-primary">Kelola Pemilih</a>
                <a href="manage_candidates.php" class="btn btn-primary">Kelola Kandidat</a>
                <a href="export_results.php" class="btn btn-secondary">Export Hasil</a>
                <a href="reset_voting.php" class="btn btn-secondary">Reset Voting</a>
                <a href="results.php" class="btn btn-secondary">Hasil Vote</a>
            </div>
        </div>
    </div>
</body>
</html>
