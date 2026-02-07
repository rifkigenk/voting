<?php
/**
 * MPS VOTING SYSTEM - PHP Native with MySQL
 * 
 * This is a complete native PHP application for MPS Elections 2026
 * Features:
 * - Voter registration and login
 * - Real-time voting system
 * - Results display with statistics
 * - Admin dashboard and management
 * - Database with MySQL
 * 
 * Database: mps_voting
 * Created: 2026
 * 
 * Quick Start:
 * 1. Run php/database.php to create tables
 * 2. Access voter_login.php to vote
 * 3. Access admin_login.php for admin panel
 */

// THIS FILE SERVES AS APPLICATION DOCUMENTATION
// All functionality is in the individual PHP files

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPS Elections 2026 - Aplikasi Voting</title>
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
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .welcome-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .welcome-box h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .welcome-box p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .link-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            text-align: center;
        }
        
        .link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .link-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .link-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .link-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .link-btn {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .link-card:hover .link-btn {
            transform: scale(1.05);
        }
        
        .features {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .features h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .feature-item {
            padding: 15px;
            background: #f5f7fa;
            border-radius: 5px;
        }
        
        .feature-item strong {
            color: #667eea;
        }
        
        .setup-section {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .setup-section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        ol {
            color: #666;
            line-height: 1.8;
            margin-left: 20px;
        }
        
        li {
            margin-bottom: 10px;
        }
        
        code {
            background: #f5f7fa;
            padding: 2px 6px;
            border-radius: 3px;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <h1>🗳️ MPS Elections 2026</h1>
            <p>Sistem Voting Digital Native PHP dengan MySQL</p>
        </div>
        
        <div class="quick-links">
            <a href="face.php" class="link-card">
                <div class="link-icon">🗳️</div>
                <div class="link-title">Voting</div>
                <div class="link-description">Lakukan voting sekarang dengan memasukkan NISN dan nama Anda</div>
                <div class="link-btn">Mulai Voting</div>
            </a>
            
            <a href="admin_login.php" class="link-card">
                <div class="link-icon">🔐</div>
                <div class="link-title">Admin Panel</div>
                <div class="link-description">Kelola data voting, kandidat, dan pemilih</div>
                <div class="link-btn">Login Admin</div>
            </a>
        </div>
        
        <div class="features">
            <h2>Fitur Aplikasi</h2>
            <div class="features-grid">
                <div class="feature-item">✅ <strong>Voter Authentication</strong><br>Sistem login berbasis NISN dan nama</div>
                <div class="feature-item">🔒 <strong>Secure Voting</strong><br>Setiap pemilih hanya bisa voting 1x</div>
                <div class="feature-item">📊 <strong>Real-time Results</strong><br>Hasil voting update otomatis</div>
                <div class="feature-item">👥 <strong>Candidate Management</strong><br>Admin bisa menambah/hapus kandidat</div>
                <div class="feature-item">📈 <strong>Statistics</strong><br>Laporan lengkap dan grafik voting</div>
                <div class="feature-item">🗄️ <strong>MySQL Database</strong><br>Data tersimpan aman di database</div>
                <div class="feature-item">💾 <strong>Export Data</strong><br>Export hasil voting ke CSV</div>
                <div class="feature-item">🔄 <strong>Reset Function</strong><br>Reset voting untuk dimulai ulang</div>
            </div>
        </div>
        
        <div class="setup-section">
            <h2>Langkah Awal - Setup Database</h2>
            <ol>
                <li>Buka phpMyAdmin di <code>http://localhost/phpmyadmin</code></li>
                <li>Buat database baru dengan nama <code>mps_voting</code></li>
                <li>Buka URL: <code>http://localhost/Projek%20MPS/mps-voting/php/database.php</code></li>
                <li>Tunggu hingga semua tabel berhasil dibuat</li>
                <li>Buat admin user dengan membuka SETUP_GUIDE.txt</li>
                <li>Siap digunakan! Login menggunakan halaman voter atau admin</li>
            </ol>
        </div>
    </div>
</body>
</html>
