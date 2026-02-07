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

// Handle add candidate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $class = trim($_POST['class']);
    $bio = trim($_POST['bio']);
    
    $sql = "INSERT INTO candidates (name, position, class, bio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $position, $class, $bio);
    
    if ($stmt->execute()) {
        $success = "Kandidat berhasil ditambahkan.";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

// Handle delete candidate
if (isset($_GET['delete'])) {
    $candidate_id = $_GET['delete'];
    $sql = "DELETE FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);
    
    if ($stmt->execute()) {
        $success = "Kandidat berhasil dihapus.";
    } else {
        $error = "Terjadi kesalahan saat menghapus.";
    }
}

// Get all candidates
$candidates_sql = "SELECT * FROM candidates ORDER BY position, name";
$candidates_result = $conn->query($candidates_sql);
$candidates = $candidates_result->fetch_all(MYSQLI_ASSOC);

// Get positions
$positions = [];
$positions_sql = "SELECT DISTINCT position FROM candidates ORDER BY position";
$positions_result = $conn->query($positions_sql);
while ($row = $positions_result->fetch_assoc()) {
    $positions[] = $row['position'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kandidat - MPS Elections 2026</title>
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
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
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
        
        .position-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
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
            grid-template-columns: repeat(2, 1fr);
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
        <h1>🎯 Kelola Kandidat</h1>
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
            <div class="section-title">Tambah Kandidat Baru</div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Kandidat</label>
                        <input type="text" name="name" required placeholder="Nama lengkap kandidat">
                    </div>
                    <div class="form-group">
                        <label>Posisi</label>
                        <input type="text" name="position" required placeholder="Contoh: Ketua Organisasi" list="positions">
                    </div>
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <input type="text" name="class" placeholder="Contoh: XI A">
                </div>
                <div class="form-group">
                    <label>Biodata / Visi Misi</label>
                    <textarea name="bio" placeholder="Deskripsikan visi dan misi kandidat ini"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Tambah Kandidat</button>
            </form>
            
            <datalist id="positions">
                <?php foreach ($positions as $position): ?>
                    <option value="<?php echo htmlspecialchars($position); ?>">
                <?php endforeach; ?>
            </datalist>
        </div>
        
        <div class="section">
            <div class="section-title">Daftar Kandidat</div>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($candidates); ?></div>
                    <div class="stat-label">Total Kandidat</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($positions); ?></div>
                    <div class="stat-label">Total Posisi</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>Kelas</th>
                        <th>Suara</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                            <td><span class="position-badge"><?php echo htmlspecialchars($candidate['position']); ?></span></td>
                            <td><?php echo htmlspecialchars($candidate['class']); ?></td>
                            <td><?php echo $candidate['votes']; ?> suara</td>
                            <td>
                                <a href="?delete=<?php echo $candidate['candidate_id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
