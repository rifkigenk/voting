# ⚡ Wasmer PHP Deployment - Quick Reference

## 🚀 One-Line Deploy

### Windows (PowerShell)
```powershell
.\deploy-wasmer-php.bat
```

### Linux/Mac (Bash)
```bash
chmod +x deploy-wasmer-php.sh
./deploy-wasmer-php.sh
```

---

## 📋 Manual Deployment Steps

### 1️⃣ Test Locally with Docker
```bash
# Build image
docker build -t mps-voting-php .

# Start containers
docker-compose up -d

# Access at:
# App: http://localhost
# PhpMyAdmin: http://localhost:8080
# MySQL: localhost:3306
```

### 2️⃣ Deploy to Wasmer

#### Option A: CLI
```bash
wasmer login
wasmer deploy --name mps-voting
```

#### Option B: From Dockerfile
```bash
wasmer app create \
  --name mps-voting \
  --from-dockerfile ./Dockerfile
```

#### Option C: Web UI
1. Go to https://app.wasmer.io
2. Click "New Project"
3. Choose "Deploy from Git"
4. Select repository
5. Choose template: **PHP**
6. Configure MySQL database
7. Click **Deploy**

### 3️⃣ Get Your URL
```bash
wasmer list
wasmer describe --name mps-voting
```

Your app: `https://mps-voting.YOUR_USERNAME.wasmer.app`

---

## 🔧 Configuration

### Environment Variables
```bash
wasmer env list --name mps-voting
wasmer env set --name mps-voting \
  DATABASE_HOST=mysql.internal \
  DATABASE_USER=mps_voting \
  ENVIRONMENT=production
```

### Secrets (Passwords)
```bash
wasmer secret set --name mps-voting \
  DATABASE_PASSWORD=your_secure_password
```

---

## ✅ Verification

### Check Status
```bash
# See if app is deployed
wasmer list

# Get full details
wasmer describe --name mps-voting

# Real-time logs
wasmer logs --name mps-voting --follow
```

### Test Endpoints
```bash
# Test homepage
curl https://mps-voting.rifkigenk.wasmer.app

# Test PHP
curl https://mps-voting.rifkigenk.wasmer.app/php/results.php

# Test API
curl -X POST https://mps-voting.rifkigenk.wasmer.app/php/admin_login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

---

## 🗄️ Database

### Automatic Setup
Wasmer creates MySQL 8.4 automatically when you deploy

### Manual Connection
```bash
# Get connection details
wasmer database describe --name mps-voting

# Connect (if exposed)
mysql -h HOST -u mps_voting -p mps_voting

# Create backup
mysqldump -h HOST -u mps_voting -p mps_voting > backup.sql
```

---

## 🆘 Troubleshooting

### Deployment Failed
```bash
# Check logs
wasmer logs --name mps-voting --follow

# Verbose output
wasmer deploy --verbose

# Rebuild
wasmer deploy --force
```

### PHP Not Working
```bash
# View errors
wasmer logs --name mps-voting | grep -i error

# Restart app
wasmer restart --name mps-voting

# Check health
wasmer health --name mps-voting
```

### Database Issues
```bash
# Test connection
mysql -h mysql.internal -u mps_voting -e "SELECT 1"

# View env vars
wasmer env list --name mps-voting

# Reset database
wasmer database reset --name mps-voting
```

---

## 📊 Monitoring

```bash
# View logs
wasmer logs --name mps-voting --follow

# Get metrics
wasmer stats --name mps-voting

# List backups
wasmer backup list --name mps-voting

# Create backup
wasmer backup create --name mps-voting
```

---

## 🔄 Updates & Rollback

### Deploy New Version
```bash
# After code changes
git push origin main
# (Auto-deploy if connected to GitHub)

# OR manually
wasmer deploy --force
```

### View Deployment History
```bash
wasmer deployments list --name mps-voting
```

### Rollback to Previous
```bash
wasmer rollback --name mps-voting --version 1
```

---

## 🎯 Production Checklist

- [ ] App is live and accessible
- [ ] Database connected
- [ ] All PHP endpoints working
- [ ] Login functions correctly
- [ ] Voting flows end-to-end
- [ ] Results display properly
- [ ] Face recognition works (if enabled)
- [ ] Backups configured
- [ ] Error logging enabled
- [ ] Monitoring alerts set up
- [ ] SSL/HTTPS working (automatic)
- [ ] Admin password changed from default

---

## 📚 Full Documentation

- **WASMER_PHP_DEPLOYMENT.md** - Complete guide with all options
- **wasmer.toml** - Configuration reference
- **Dockerfile** - PHP + Apache container definition
- **docker-compose.yml** - Local testing setup

---

## 🔗 Useful Links

- **Wasmer**: https://wasmer.io
- **Wasmer Docs**: https://docs.wasmer.io
- **Wasmer Shell**: https://shell.wasmer.io
- **Wasmer Community**: https://discord.gg/ZwZUJmS

---

## 💡 Tips

1. **Local testing first** - Always test with Docker before deploying
2. **Keep secrets safe** - Use `wasmer secret set` for passwords
3. **Monitor logs** - Watch logs during first deployment
4. **Backup often** - Create manual backups before major changes
5. **Test endpoints** - Verify all APIs work after deployment

---

## ✨ Done!

Your app is now production-ready on Wasmer Edge!

**Share your app**: https://mps-voting.rifkigenk.wasmer.app
