<?php
require_once 'connection.php';

// Get all positions
$positions = [];
$positions_sql = "SELECT DISTINCT position FROM candidates ORDER BY position";
$positions_result = $conn->query($positions_sql);
while ($row = $positions_result->fetch_assoc()) {
    $positions[] = $row['position'];
}

// Get results by position
$results_by_position = [];
foreach ($positions as $position) {
    $sql = "SELECT c.*, COUNT(v.vote_id) as vote_count 
            FROM candidates c 
            LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
            WHERE c.position = ? 
            GROUP BY c.candidate_id 
            ORDER BY vote_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $position);
    $stmt->execute();
    $results_by_position[$position] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get total votes
$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes_result = $conn->query($total_votes_sql);
$total_votes = $total_votes_result->fetch_assoc()['total'];

// Get total voters
$total_voters_sql = "SELECT COUNT(*) as total FROM voters";
$total_voters_result = $conn->query($total_voters_sql);
$total_voters = $total_voters_result->fetch_assoc()['total'];

$participation_rate = $total_voters > 0 ? round(($total_votes / $total_voters) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Voting - MPS Elections 2026</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .results-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 36px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .position-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .position-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        
        .results-list {
            display: grid;
            gap: 15px;
        }
        
        .result-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .result-item.first {
            border-left-color: #ffd700;
            background: #fffef0;
        }
        
        .result-item.second {
            border-left-color: #c0c0c0;
            background: #fafbfc;
        }
        
        .result-item.third {
            border-left-color: #cd7f32;
            background: #fefcf9;
        }
        
        .candidate-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .candidate-class {
            font-size: 12px;
            color: #999;
            margin-top: 3px;
        }
        
        .vote-info {
            text-align: right;
        }
        
        .vote-count {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
        }
        
        .vote-percentage {
            font-size: 12px;
            color: #999;
            margin-top: 3px;
        }
        
        .progress-bar {
            width: 200px;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.3s;
        }
        
        .no-votes {
            color: #999;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #764ba2;
        }
        
        .refresh-note {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <div class="header">
            <h1>📊 Hasil Voting Sementara</h1>
            <p style="color: #666; font-size: 14px; margin-top: 10px;">MPS Elections 2026</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_votes; ?></div>
                    <div class="stat-label">Total Suara Masuk</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_voters; ?></div>
                    <div class="stat-label">Total Pemilih</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $participation_rate; ?>%</div>
                    <div class="stat-label">Tingkat Partisipasi</div>
                </div>
            </div>
        </div>
        
        <?php foreach ($positions as $position): ?>
            <div class="position-section">
                <div class="position-title"><?php echo htmlspecialchars($position); ?></div>
                
                <?php 
                $position_total_votes = 0;
                foreach ($results_by_position[$position] as $result) {
                    $position_total_votes += $result['vote_count'];
                }
                ?>
                
                <?php if (count($results_by_position[$position]) > 0): ?>
                    <div class="results-list">
                        <?php 
                        $rank = 1;
                        foreach ($results_by_position[$position] as $result): 
                            $percentage = $position_total_votes > 0 ? round(($result['vote_count'] / $position_total_votes) * 100, 2) : 0;
                            $rank_class = $rank === 1 ? 'first' : ($rank === 2 ? 'second' : ($rank === 3 ? 'third' : ''));
                        ?>
                            <div class="result-item <?php echo $rank_class; ?>">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <?php if ($rank === 1): ?>
                                            <span style="font-size: 24px;">🥇</span>
                                        <?php elseif ($rank === 2): ?>
                                            <span style="font-size: 24px;">🥈</span>
                                        <?php elseif ($rank === 3): ?>
                                            <span style="font-size: 24px;">🥉</span>
                                        <?php else: ?>
                                            <span style="font-size: 20px;"><?php echo $rank . '.'; ?></span>
                                        <?php endif; ?>
                                        <div>
                                            <div class="candidate-name"><?php echo htmlspecialchars($result['name']); ?></div>
                                            <div class="candidate-class"><?php echo htmlspecialchars($result['class']); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="vote-info">
                                    <div class="vote-count"><?php echo $result['vote_count']; ?></div>
                                    <div class="vote-percentage"><?php echo $percentage; ?>%</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            $rank++;
                        endforeach; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="no-votes">Belum ada suara masuk untuk posisi ini.</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <a href="admin_dashboard.php" class="back-link">← Kembali</a>
        
        <div class="refresh-note">
            💡 Halaman hasil ini menampilkan data real-time. Refresh halaman untuk melihat update terbaru.
        </div>
    </div>
</body>
</html>
