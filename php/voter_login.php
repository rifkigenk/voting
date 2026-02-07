<?php
session_start();
require_once 'connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'login') {
        $nisn = trim($_POST['nisn']);
        $name = trim($_POST['name']);
        
        // Check if voter exists
        $sql = "SELECT * FROM voters WHERE nisn = ? AND name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nisn, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $voter = $result->fetch_assoc();
            
            if ($voter['has_voted'] == 0) {
                $_SESSION['voter_id'] = $voter['voter_id'];
                $_SESSION['voter_name'] = $voter['name'];
                $_SESSION['voter_nisn'] = $voter['nisn'];
                $_SESSION['voter_class'] = $voter['class'];
                header("Location: vote.php");
                exit();
            } else {
                $error = "Anda sudah melakukan voting. Terima kasih!";
            }
        } else {
            $error = "NISN atau Nama tidak ditemukan dalam daftar pemilih.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemilih - MPS Elections 2026</title>
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
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .login-container p {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
        
        .info-text {
            background: #f0f4ff;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #555;
            margin-top: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🗳️ Login Pemilih</h1>
        <p>Masukkan informasi Anda untuk melakukan voting</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label for="nisn">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" id="nisn" name="nisn" required placeholder="Masukkan NISN Anda">
            </div>
            
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required placeholder="Masukkan Nama Lengkap">
            </div>
            
            <button type="submit" class="btn-login">Login & Voting</button>
            
            <div class="info-text">
                <strong>ℹ️ Informasi:</strong><br>
                Pastikan NISN dan nama yang Anda masukkan sesuai dengan data di sekolah. Setiap siswa hanya dapat melakukan voting sekali.
            </div>
        </form>
    </div>
</body>
</html>
