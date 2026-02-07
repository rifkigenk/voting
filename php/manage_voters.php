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

// Handle add voter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nisn = trim($_POST['nisn']);
    $name = trim($_POST['name']);
    $class = trim($_POST['class']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    $sql = "INSERT INTO voters (nisn, name, class, email, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nisn, $name, $class, $email, $phone);
    
    if ($stmt->execute()) {
        $success = "Pemilih berhasil ditambahkan.";
    } else {
        if ($conn->errno == 1062) {
            $error = "NISN sudah terdaftar.";
        } else {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}

// Handle delete voter
if (isset($_GET['delete'])) {
    $voter_id = $_GET['delete'];
    $sql = "DELETE FROM voters WHERE voter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voter_id);
    
    if ($stmt->execute()) {
        $success = "Pemilih berhasil dihapus.";
    } else {
        $error = "Terjadi kesalahan saat menghapus.";
    }
}

// Get all voters
$voters_sql = "SELECT * FROM voters ORDER BY name";
$voters_result = $conn->query($voters_sql);
$voters = $voters_result->fetch_all(MYSQLI_ASSOC);

// Get statistics
$total_voters = count($voters);
$voted_voters = 0;
foreach ($voters as $voter) {
    if ($voter['has_voted'] == 1) {
        $voted_voters++;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemilih - MPS Elections 2026</title>
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
        
        .back-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
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
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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
        
        .btn-danger {
            background: #dc3545;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-voted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-not-voted {
            background: #fff3cd;
            color: #856404;
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
        
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #f5f7fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>👥 Kelola Pemilih</h1>
        <a href="admin_dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    </div>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="section">
            <div class="section-title">Tambah Pemilih Baru</div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" name="nisn" required placeholder="Nomor Induk Siswa Nasional">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama Lengkap">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kelas</label>
                        <input type="text" name="class" required placeholder="Contoh: X A">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Email (opsional)">
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="phone" placeholder="Nomor Telepon (opsional)">
                </div>
                <button type="submit" class="btn btn-primary">Tambah Pemilih</button>
            </form>
        </div>
        
        <div class="section">
            <div class="section-title">Daftar Pemilih</div>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_voters; ?></div>
                    <div class="stat-label">Total Pemilih</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $voted_voters; ?></div>
                    <div class="stat-label">Sudah Vote</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_voters - $voted_voters; ?></div>
                    <div class="stat-label">Belum Vote</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voters as $voter): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($voter['name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['class']); ?></td>
                            <td>
                                <?php if ($voter['has_voted'] == 1): ?>
                                    <span class="status-badge status-voted">✓ Sudah Vote</span>
                                <?php else: ?>
                                    <span class="status-badge status-not-voted">⊘ Belum Vote</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $voter['voter_id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
