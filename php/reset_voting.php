<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_reset']) && $_POST['confirm_reset'] == 'yes') {
        // Reset all votes
        $reset_votes_sql = "DELETE FROM votes";
        $reset_candidates_sql = "UPDATE candidates SET votes = 0";
        $reset_voters_sql = "UPDATE voters SET has_voted = 0";
        
        if ($conn->query($reset_votes_sql) && 
            $conn->query($reset_candidates_sql) && 
            $conn->query($reset_voters_sql)) {
            $success = "Voting telah di-reset. Semua data suara dan status pemilih telah dihapus.";
        } else {
            $error = "Terjadi kesalahan saat mereset voting.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Voting - MPS Elections 2026</title>
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
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 12px;
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
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .info {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            line-height: 1.6;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Reset Voting</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="admin_dashboard.php" style="padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">Kembali ke Dashboard</a>
            </div>
        <?php else: ?>
            <div class="warning">
                <strong>⚠️ PERHATIAN!</strong><br>
                Operasi ini akan:<br>
                • Menghapus semua data suara yang masuk<br>
                • Mereset jumlah suara semua kandidat ke 0<br>
                • Menandai semua pemilih belum melakukan voting<br>
                <br>
                <strong>Tindakan ini TIDAK DAPAT DIBATALKAN!</strong>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="confirm_reset" value="yes">
                <div class="actions">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin mereset semua voting? Tindakan ini tidak dapat dibatalkan!')">Ya, Reset Voting</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
            
            <div class="info">
                <strong>💡 Tips:</strong><br>
                Gunakan fitur ini jika Anda ingin memulai ulang stemming ke awal, atau menghapus data voting yang salah. Pastikan backup data sebelum melakukan reset.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
