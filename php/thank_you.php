<?php
require_once 'connection.php';

// Get voting statistics
$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes_result = $conn->query($total_votes_sql);
$total_votes = $total_votes_result->fetch_assoc()['total'];

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
    <title>Terima Kasih - MPS Elections 2026</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .thank-you-container {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
        }
        
        .thank-you-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 1s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 15px;
        }
        
        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 2px solid #f0f0f0;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .message {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            color: #667eea;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="thank-you-icon">✅</div>
        <h1>Terima Kasih!</h1>
        <p>Suara Anda telah berhasil dicatat dalam Pemilihan MPS 2026. Satu suara yang Anda berikan akan membantu membentuk masa depan yang lebih baik.</p>
        
        <div class="stats">
            <div class="stat">
                <div class="stat-number"><?php echo $total_votes; ?></div>
                <div class="stat-label">Total Suara</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?php echo $total_voters; ?></div>
                <div class="stat-label">Pemilih</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?php echo $participation_rate; ?>%</div>
                <div class="stat-label">Partisipasi</div>
            </div>
        </div>
        
        <div class="message">
            <strong>ℹ️ Informasi Penting:</strong><br>
            Setiap siswa hanya dapat melakukan voting sekali. Data Anda telah tersimpan dan tidak dapat diubah lagi.
        </div>
        
        <div class="action-buttons">
            <a href="index.php" class="btn btn-secondary">Kembali Ke Beranda</a>
        </div>
    </div>
</body>
</html>
