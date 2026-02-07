# Wasmer Edge Deployment - Fixed Setup Guide

## ⚠️ Important: Wasmer Edge is for Frontend Only

**Wasmer Edge** is a static file hosting service. For PHP applications with dynamic backends, use this architecture:

```
┌──────────────────────────┐
│   Wasmer Edge            │
│   (Frontend - Static)    │
│  ⠿─────────────────────⠿ │
│  ├─ index.html          │
│  ├─ style/              │
│  └─ assets/             │
└───────────┬──────────────┘
            │ API Calls (HTTPS)
            ↓
┌──────────────────────────┐
│   PHP Backend            │
│  (Docker/Cloud/XAMPP)    │
│ ⠿─────────────────────⠿  │
│  ├─ voter_login.php     │
│  ├─ vote.php            │
│  ├─ results.php         │
│  └─ admin_login.php     │
└───────────┬──────────────┘
            │
            ↓
┌──────────────────────────┐
│   Cloud Database         │
│  (RDS/PlanetScale)       │
└──────────────────────────┘
```

## 🔧 Step 1: Fix wasmer.toml

✅ **Already Updated** - Your wasmer.toml now uses the correct static file configuration.

## 🏗️ Step 2: Build the Frontend

### Option A: Windows (PowerShell)
```powershell
# Run the build script
$setupUtility = Get-ExecutionPolicy
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
.\build.bat
Set-ExecutionPolicy -ExecutionPolicy $setupUtility -Scope Process
```

### Option B: Windows (Command Prompt)
```batch
build.bat
```

### Option C: Linux/Mac (Bash)
```bash
bash build.sh
chmod +x build.sh
```

This creates a `dist/` folder with:
- `index.html` - Homepage
- `style/` - CSS and face recognition UI
- `assets/` - Images and resources
- `config.js` - API configuration

## 🌐 Step 3: Set Up PHP Backend

Choose one of these options:

### Option A: Docker (Recommended)
```bash
# Start backend with Docker
docker-compose up -d

# Backend will be at: http://localhost
```

### Option B: XAMPP (Local Development)
```bash
# Already running on: http://localhost/Projek%20MPS/mps-voting
```

### Option C: Cloud Server (Production)

**Option C1: Heroku (Free tier ending, but still available)**
```bash
# Will not use - Heroku free tier ended

# Use alternatives instead
```

**Option C2: Railway.app (Recommended - Easy PHP)**
1. Sign up: https://railway.app
2. Create new project
3. Deploy from GitHub or upload
4. Connect MySQL database
5. Get public URL (e.g., `https://mps-voting-prod.up.railway.app`)

**Option C3: PaaS with PHP Support**
- **Render.com** - Free PHP hosting
- **Fly.io** - Flexible deployment
- **AWS Lightsail** - Managed hosting
- **DigitalOcean** - VPS or App Platform

## 🚀 Step 4: Deploy Frontend to Wasmer

### Step 4a: Prepare for Deployment
```bash
# Build the frontend
.\build.bat  # Windows
# OR
bash build.sh  # Linux/Mac

# This creates dist/ folder
```

### Step 4b: Verify dist/ folder
```powershell
# Check Windows
dir dist/

# Check Linux
ls -la dist/
```

Expected contents:
```
dist/
├── index.html
├── config.js
├── style/
│   ├── style.css
│   └── mps-voting2/
└── assets/
```

### Step 4c: Deploy to Wasmer
```bash
# Install Wasmer CLI (if not installed)
curl https://get.wasmer.io -sSfL | sh

# Login
wasmer login

# Deploy
wasmer deploy --name mps-voting --owner YOUR_USERNAME

# Your app will be at:
# https://mps-voting.YOUR_USERNAME.wasmer.app
```

## 🔗 Step 5: Connect Frontend to Backend

After both frontend and backend are deployed:

### Update Frontend Configuration

Edit `dist/config.js`:

```javascript
const API_CONFIG = {
  // Update to your backend URL
  BASE_URL: 'https://mps-voting-prod.up.railway.app',  // Railway example
  // OR
  BASE_URL: 'http://your-backend.com',
  // OR
  BASE_URL: 'http://localhost',  // For local testing
  
  // ... rest of config
};
```

### Update HTML/JS to Use Config

In `index.html` and JavaScript files, reference API_CONFIG:

```javascript
// Before:
fetch('/php/voter_login.php', { ... })

// After:
fetch(API_CONFIG.getUrl('/php/voter_login.php'), { ... })
```

## ✅ Step 6: Verify Deployment

### Check Frontend
```bash
# Open in browser
https://mps-voting.YOUR_USERNAME.wasmer.app
```

### Test Backend Connection
```bash
# Test API endpoint
curl https://your-backend-url/php/results.php
```

### Check Wasmer Status
```bash
wasmer list
wasmer logs --name mps-voting
```

## 🔐 Environment Variables

### Development (.env)
```
BACKEND_URL=http://localhost
API_BASE=/php
```

### Production (.env.production)
```
BACKEND_URL=https://mps-voting-prod.railway.app
API_BASE=/php
```

## 🛠️ Troubleshooting

### Error: "App was not deployed"
**Solutions:**
1. ✅ Ensure `dist/` folder exists with files
2. ✅ Check wasmer.toml syntax (use static config, not WASM)
3. ✅ Run `wasmer deploy --verbose` for detailed logs
4. ✅ Verify Wasmer login: `wasmer whoami`

### Error: "dist folder not found"
```bash
# Build the dist folder first
.\build.bat  # Windows
# OR
bash build.sh  # Linux/Mac

# Then deploy
wasmer deploy --name mps-voting
```

### API Calls Failing (CORS)
1. Enable CORS in wasmer.toml ✅ (already done)
2. Update backend URL in config.js
3. Test CORS with: `curl -i https://mps-voting-prod.railway.app`

### Database Connection Failed
1. Verify backend is running: `curl https://backend-url/php/results.php`
2. Check DATABASE_HOST matches your backend server
3. Test locally first: `docker-compose up -d`

## 📊 Monitoring

### View Logs
```bash
# Wasmer frontend logs
wasmer logs --name mps-voting --follow

# Backend logs (Docker)
docker-compose logs php --follow
docker-compose logs mysql --follow
```

### View Metrics
```bash
# Check deployment status
wasmer describe --name mps-voting
```

## 📝 Deployment Checklist

- [ ] `wasmer.toml` uses static configuration
- [ ] `build.sh` or `build.bat` runs successfully
- [ ] `dist/` folder contains all frontend files
- [ ] PHP backend is running (Docker or Cloud)
- [ ] Database is configured and accessible
- [ ] `config.js` has correct BACKEND_URL
- [ ] `wasmer deploy` completes successfully
- [ ] Frontend loads at `https://mps-voting.*.wasmer.app`
- [ ] API calls work (test in browser console)
- [ ] Login flows work end-to-end
- [ ] Database is backed up

## 🚀 Production Checklist

Before going live:

- [ ] Use cloud database (RDS/PlanetScale) instead of local
- [ ] Enable HTTPS (automatic with Wasmer/Cloud providers)
- [ ] Set up automated backups
- [ ] Configure error logging/monitoring
- [ ] Add rate limiting
- [ ] Use environment variables for sensitive data
- [ ] Test all voting flows
- [ ] Load test with expected number of voters
- [ ] Document deployment process
- [ ] Set up alerting for errors

## 📞 Support

- **Wasmer Docs**: https://docs.wasmer.io
- **Wasmer Community**: https://discord.gg/ZwZUJmS
- **Railway.app Docs**: https://docs.railway.app
- **Docker Docs**: https://docs.docker.com

---

**Summary**: Deploy frontend to Wasmer Edge, backend to separate service. Build with `build.bat`, deploy with `wasmer deploy`.
