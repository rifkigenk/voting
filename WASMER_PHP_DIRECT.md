# 🚀 Wasmer PHP Direct Deployment (No Docker)

Complete guide for deploying MPS Voting without needing Docker locally.

## ✨ What You Need

- ✅ Wasmer CLI installed
- ✅ Wasmer account
- ✅ Git (optional, for auto-deploy)
- ❌ **NO Docker required!**

## 🎯 One-Line Deploy

### Windows:
```powershell
.\deploy-wasmer-direct.bat
```

### Linux/Mac:
```bash
chmod +x deploy-wasmer-direct.sh
./deploy-wasmer-direct.sh
```

---

## 📋 Manual Deployment (CLI)

### Step 1: Login to Wasmer
```bash
wasmer login
```

### Step 2: Deploy
```bash
wasmer deploy --name mps-voting
```

That's it! No Docker required.

### Step 3: Get Your URL
```bash
wasmer describe --name mps-voting
# Your app: https://mps-voting.YOUR_USERNAME.wasmer.app
```

---

## 🌐 Web UI Deployment (No CLI Needed)

### Option 1: Direct Upload
1. Go to https://app.wasmer.io
2. Click "New Project"
3. Select "Upload Files"
4. Choose **PHP** runtime
5. Upload entire project folder
6. Enable MySQL 8.4
7. Click **Deploy**

### Option 2: GitHub Auto-Deploy
1. Push to GitHub: `git push origin main`
2. Go to https://app.wasmer.io
3. Click "Connect GitHub"
4. Select your repository
5. Choose runtime: **PHP**
6. Click **Deploy**
7. Every push auto-deploys!

---

## 🔧 Configuration

### Environment Variables (Set in Wasmer Dashboard)

After deployment, go to Settings → Environment:

```
DATABASE_HOST = mysql.internal
DATABASE_USER = mps_voting
DATABASE_NAME = mps_voting
DATABASE_PORT = 3306
TIMEZONE = Asia/Jakarta
ENVIRONMENT = production
```

### Secrets (Passwords)
```
DATABASE_PASSWORD = [Wasmer auto-generates]
ADMIN_PASSWORD = [Create secure password]
```

---

## ✅ Verification

### Check Status
```bash
# Is it deployed?
wasmer list

# Get full details
wasmer describe --name mps-voting

# See real-time logs
wasmer logs --name mps-voting --follow
```

### Test Homepage
```bash
curl https://mps-voting.YOUR_USERNAME.wasmer.app
```

### Test PHP Endpoint
```bash
curl https://mps-voting.YOUR_USERNAME.wasmer.app/php/results.php

# Test login API
curl -X POST https://mps-voting.YOUR_USERNAME.wasmer.app/php/admin_login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

---

## 🗄️ Database Management

### Automatic Setup
Wasmer automatically creates:
- MySQL 8.4 instance
- Database: `mps_voting`
- User: `mps_voting`
- Secure password

### Manual Connection (If Exposed)
```bash
# Get connection details
wasmer database describe --name mps-voting

# Connect
mysql -h HOST -u mps_voting -p

# Create backup
mysqldump -h HOST -u mps_voting -p mps_voting > backup.sql
```

### View Database Variables
```bash
wasmer env list --name mps-voting | grep DATABASE
```

---

## 📊 Deployment Comparison

| Method | Time | Requirements | Effort |
|--------|------|--------------|--------|
| **CLI Only** | 2 min | Wasmer CLI | Easy |
| **Web UI** | 3 min | Browser | Very Easy |
| **Script** | 1 min | Wasmer CLI | Auto |
| **GitHub** | Auto | GitHub + Wasmer | Setup once |

---

## 🆘 Troubleshooting

### Deployment Fails
```bash
# See detailed error logs
wasmer logs --name mps-voting --follow

# Check deployment status
wasmer describe --name mps-voting

# Force redeploy
wasmer deploy --name mps-voting --force
```

### PHP Not Responding
```bash
# Check if app is healthy
wasmer health --name mps-voting

# View error logs
wasmer logs --name mps-voting | grep -i error

# Restart app
wasmer restart --name mps-voting
```

### Can't Connect to Database
```bash
# Verify environment variables
wasmer env list --name mps-voting

# Check MySQL is running
mysql -h mysql.internal -u mps_voting -e "SELECT 1"

# View connection logs
wasmer logs --name mps-voting | grep -i database
```

---

## 🔐 After Deployment

### 1. Change Admin Password (IMPORTANT!)
```bash
# Connect to database and run:
UPDATE admin SET password=SHA2('New_Secure_Password_Here', 256) WHERE username='admin';
```

### 2. Update Test Voter Password
```bash
UPDATE admin SET password=SHA2('new_secure_pass', 256);
```

### 3. Enable Backups
```bash
# Automatic backups
wasmer backup enable --name mps-voting

# Create manual backup
wasmer backup create --name mps-voting
```

---

## 📈 Monitoring

### Real-Time Logs
```bash
wasmer logs --name mps-voting --follow
```

### Performance Metrics
```bash
wasmer stats --name mps-voting
```

### Deployment History
```bash
wasmer deployments list --name mps-voting
```

### Rollback to Previous
```bash
wasmer rollback --name mps-voting --version 1
```

---

## 🚀 Advanced Features

### Custom Domain
```bash
# Add custom domain (if registered)
wasmer domain add --name mps-voting yourdomain.com
```

### Environment-Specific Config
```bash
# Production settings
wasmer env set --name mps-voting ENVIRONMENT=production

# Development settings
wasmer env set --name mps-voting ENVIRONMENT=development
```

### Rate Limiting
```bash
# Prevent abuse
wasmer ratelimit set --name mps-voting --requests 100 --interval 60s
```

---

## 📝 Deployment Commands Reference

```bash
# Deploy/Update
wasmer deploy --name mps-voting
wasmer deploy --name mps-voting --force

# Create new app
wasmer app create --name mps-voting --runtime php --version 8.2

# View apps
wasmer list
wasmer describe --name mps-voting

# Environment
wasmer env list --name mps-voting
wasmer env set --name mps-voting KEY=VALUE
wasmer env unset --name mps-voting KEY

# Backups
wasmer backup list --name mps-voting
wasmer backup create --name mps-voting
wasmer backup restore --name mps-voting

# Logs & Monitoring
wasmer logs --name mps-voting --follow
wasmer logs --name mps-voting --tail 100
wasmer stats --name mps-voting
wasmer health --name mps-voting

# Management
wasmer restart --name mps-voting
wasmer stop --name mps-voting
wasmer delete --name mps-voting
```

---

## ✨ Your App is Live!

```
https://mps-voting.YOUR_USERNAME.wasmer.app
```

### Next Steps:
1. ✅ Share URL with voters
2. ✅ Test login (NISN: 12345678901)
3. ✅ Test voting flow
4. ✅ Monitor logs during election
5. ✅ View results in real-time

---

## 📞 Support

- **Wasmer Docs**: https://docs.wasmer.io
- **Wasmer Discord**: https://discord.gg/ZwZUJmS
- **My App**: https://app.wasmer.io

---

## 🎯 Architecture (No Docker)

```
Your Files (PHP, HTML, JS)
         ↓
    wasmer.toml (config)
         ↓
   Wasmer Edge
    ├─ PHP 8.2 Runtime
    ├─ MySQL 8.4 Database
    └─ HTTPS (Automatic)
         ↓
https://mps-voting.YOUR_USERNAME.wasmer.app
```

---

**Status**: ✅ Ready for Production

**Update**: February 7, 2026
**Method**: Direct PHP (No Docker)
**Runtime**: PHP 8.2
**Database**: MySQL 8.4
