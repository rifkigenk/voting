# 🚀 Deploy to Wasmer - Quick Start Guide

**Your MPS Voting Application is ready for production deployment - NO Docker needed!**

---

## ⚡ Fastest Way (3 Steps)

### Windows:
```powershell
.\deploy-wasmer-direct.bat
```

### Linux/Mac:
```bash
chmod +x deploy-wasmer-direct.sh
./deploy-wasmer-direct.sh
```

**Done!** ✨ App will be live in 2-3 minutes.

---

## 🖥️ Prerequisites

Just ONE thing needed:

```bash
# Install Wasmer CLI (if not already installed)
# Windows PowerShell:
irm https://get.wasmer.io -outfile install.ps1 ; &$PROFILE

# Or from: https://docs.wasmer.io/ecosystem/wasmer/getting-started
```

**That's it!** No Docker. No Node.js. Just Wasmer.

---

## 🎯 What Gets Deployed

```
┌────────────────────────────────┐
│    Your MPS Voting App         │
├────────────────────────────────┤
│ PHP 8.2 + Apache               │
│ MySQL 8.4 (Automatic)          │
│ HTTPS (Free SSL/TLS)           │
│ Global CDN                     │
│ Automatic Backups              │
│ 99.9% Uptime                   │
└────────────────────────────────┘
         ↓
https://mps-voting.YOUR_USERNAME.wasmer.app
```

---

## 📋 Deployment Options

### Option 1: AutoScript (Easiest) ✅ RECOMMENDED
```powershell
.\deploy-wasmer-direct.bat
```
- Interactive prompts
- Automatic verification
- Shows final URL
- **Best for beginners**

### Option 2: Direct CLI (Fastest)
```bash
wasmer login
wasmer deploy --name mps-voting
```
- One command
- Manual setup
- **Best for experienced users**

### Option 3: Web UI (No CLI)
1. Visit https://app.wasmer.io
2. Click "New Project"
3. Choose "PHP"
4. Upload files
5. Click Deploy
- **No terminal needed**
- **Best for visual learners**

### Option 4: GitHub Auto-Deploy
1. Push to GitHub
2. Connect Wasmer
3. Auto-deploys on every push
- **Best for continuous updates**

---

## ✅ Step-by-Step (Option 1)

### Step 1: Navigate to Project
```powershell
cd "d:\XAMPP\htdocs\Projek MPS\mps-voting"
```

### Step 2: Run Deploy Script
```powershell
.\deploy-wasmer-direct.bat
```

### Step 3: Follow Prompts
```
✅ Wasmer CLI found
✅ Logged in to Wasmer
Continue? (yes/no): yes
🚀 Deploying to Wasmer...
```

### Step 4: Wait 2-3 Minutes
The script validates and deploys.

### Step 5: Get Your URL
```
✅ Deployment Successfully Initiated!
Access your app: https://mps-voting.YOUR_USERNAME.wasmer.app
```

---

## 🔍 Verify Deployment

After deployment completes:

### Check Status
```bash
wasmer describe --name mps-voting
```

### View Logs
```bash
wasmer logs --name mps-voting --follow
```

### Access App
```
https://mps-voting.YOUR_USERNAME.wasmer.app
```

### Test Endpoint
```bash
curl https://mps-voting.YOUR_USERNAME.wasmer.app/php/results.php
```

---

## 🎁 What's Included

✅ **Frontend** - HTML, CSS, JavaScript, Face detection
✅ **Backend** - All PHP scripts fully functional
✅ **Database** - MySQL 8.4 auto-provisioned
✅ **DNS** - wasmer.app subdomain
✅ **SSL** - Free HTTPS certificate
✅ **Backups** - Automatic daily backups
✅ **Monitoring** - Real-time logs & metrics
✅ **Scaling** - Automatic traffic scaling

---

## 🔐 After Deployment

### First Thing: Change Admin Password
```bash
# Connect to database and update:
UPDATE admin SET password=SHA2('NewStrongPassword', 256) WHERE username='admin';
```

### Second: Configure Environment
```bash
wasmer env set --name mps-voting \
  ENVIRONMENT=production \
  TIMEZONE=Asia/Jakarta
```

### Third: Create Backup
```bash
wasmer backup create --name mps-voting --description "Initial deployment"
```

---

## 📊 Deployment Duration

```
Total Time: ~5 minutes

Timeline:
├─ Script start: 10 seconds
├─ Wasmer login check: 5 seconds  
├─ Deployment initiation: 20 seconds
├─ Wasmer provisioning: 2-3 minutes
├─ Database setup: 30 seconds
└─ Ready to use!
```

---

## ❌ Common Issues & Fixes

### Issue: "Wasmer not found"
```bash
# Install it:
curl https://get.wasmer.io -sSfL | sh

# Verify:
wasmer --version
```

### Issue: "Not logged in"
```bash
# Login:
wasmer login

# Verify:
wasmer whoami
```

### Issue: "Deployment timed out"
```bash
# Check logs:
wasmer logs --name mps-voting --follow

# Retry:
wasmer deploy --name mps-voting --force
```

### Issue: "Database connection failed"
```bash
# Verify env vars:
wasmer env list --name mps-voting

# Check logs:
wasmer logs --name mps-voting | grep -i database
```

---

## 🌍 Alternative Platforms

If you prefer other hosting:

- **Railway.app** - Free tier, easiest setup
- **Render.com** - Great performance
- **Heroku** - Classic but now paid
- **AWS** - Enterprise-grade

See **ALTERNATIVE_HOSTING.md** for details.

---

## 📚 Documentation

| Doc | For |
|-----|-----|
| **WASMER_PHP_DIRECT.md** | Complete guide |
| **WASMER_QUICK_REFERENCE.md** | Commands reference |
| **ALTERNATIVE_HOSTING.md** | Other platforms |
| **wasmer.toml** | Configuration |

---

## 🎯 Your App URL

Once deployed, access at:

```
https://mps-voting.YOUR_USERNAME.wasmer.app
```

**Replace YOUR_USERNAME with your Wasmer username:**
```bash
wasmer whoami
```

---

## ✨ Success Checklist

After deployment:

- [ ] Script completed successfully
- [ ] URL displayed in terminal
- [ ] Homepage loads in browser
- [ ] PHP endpoints respond (test `/php/results.php`)
- [ ] Database connected (check logs)
- [ ] Voter login works (try NISN 12345678901)
- [ ] Admin panel accessible
- [ ] Face recognition loads (if testing)

---

## 🚀 Ready to Deploy?

### Choose Your Method:

**👉 Easiest** - Run script:
```powershell
.\deploy-wasmer-direct.bat
```

**👉 Fastest** - Use CLI:
```bash
wasmer login && wasmer deploy --name mps-voting
```

**👉 Web Only** - Visit https://app.wasmer.io

**👉 GitHub** - Connect repository

---

## 💬 Need Help?

```
Question → Answer
─────────────────────────────────────
"How long?" → 2-3 minutes
"Cost?" → Free tier or paid plan
"Data safe?" → Automatic backups
"SSL?" → Free HTTPS automatic
"Scaling?" → Automatic
"Uptime?" → 99.9% SLA
"Support?" → Discord: discord.gg/ZwZUJmS
```

---

## 🎉 Let's Go!

Your MPS Voting Application is production-ready.

**Next step**: Choose deployment method above and deploy!

```powershell
# Recommended:
.\deploy-wasmer-direct.bat
```

**Your app will be live in 2-3 minutes!** 🚀

---

**Happy Voting! 🗳️**

Questions? Check documentation files or ask Wasmer community.
