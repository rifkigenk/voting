# 🚀 Deploy to Wasmer - Quick Start

## What Was Fixed:

1. ✅ **wasmer.toml** - Changed from WASM handler config to static file deployment
2. ✅ **dist/ folder** - Built with all frontend files ready
3. ✅ **CORS enabled** - API calls to backend will work
4. ✅ **Cache configured** - Static assets cached efficiently

## Deployment Steps (Choose One):

### Option 1: Command Line (Recommended)

```powershell
# Step 1: Login to Wasmer
wasmer login

# Step 2: Deploy to Wasmer
wasmer deploy --name mps-voting

# Your app will be at:
# https://mps-voting.YOUR_USERNAME.wasmer.app
```

### Option 2: Using Deploy Script

```powershell
# Run the PowerShell deploy script
powershell -ExecutionPolicy Bypass -File deploy-wasmer.ps1 -AppName mps-voting -WasmerUser YOUR_USERNAME
```

### Option 3: Direct CLI Command

```bash
wasmer deploy \
  --name mps-voting \
  --owner YOUR_USERNAME \
  --visibility public
```

## ⚡ Quick Deployment

```powershell
# 1. Verify wasmer is installed
wasmer --version

# 2. Login (if not already logged in)
wasmer login

# 3. Deploy (this is the command that fixes your error)
wasmer deploy --name mps-voting

# 4. Get your URL
wasmer list
```

## 🔗 Backend Connection

Your Wasmer Edge frontend is now deployed, but it needs a backend to call.

### Option A: Keep Using XAMPP (Local Testing)
```javascript
// In dist/config.js, set:
BASE_URL = 'http://localhost/Projek%20MPS/mps-voting'
```

### Option B: Deploy Backend to Docker

```bash
# Start Docker containers
docker-compose up -d

# Then update config.js to point to Docker backend
BASE_URL = 'http://your-docker-host:80'
```

### Option C: Deploy to Cloud (Production)

For production, deploy PHP backend separately:

1. **Railway.app** (Recommended - Easy)
   - Sign up: https://railway.app
   - Connect GitHub repo
   - Deploy
   - Get URL: `https://mps-voting-prod.up.railway.app`

2. **Heroku** (Legacy but works)
   - Sign up: https://heroku.com
   - Deploy PHP app
   - Get URL

3. **AWS Lightsail**
   - Create MySQL instance
   - Deploy PHP app
   - Get URL

Then update `dist/config.js`:

```javascript
const API_CONFIG = {
  BASE_URL: 'https://your-backend-url.railway.app',  // Update this!
  // ... rest
};
```

## ✅ Verify Deployment

### Check Frontend
```bash
# Open in browser:
https://mps-voting.YOUR_USERNAME.wasmer.app
```

You should see the voting homepage!

### Test API Connection
```bash
# Open browser console (F12) and run:
fetch(API_CONFIG.getUrl('/php/results.php'))
  .then(r => r.json())
  .then(data => console.log(data))
```

### Check Wasmer Status
```powershell
wasmer list
wasmer describe --name mps-voting
wasmer logs --name mps-voting --follow
```

## 🆘 If Deployment Still Fails

```powershell
# 1. Verify dist folder exists
dir dist
# Should show: config.js, index.html, style/

# 2. Check wasmer.toml is correct
type wasmer.toml
# Should NOT have [module] or handler references

# 3. Deploy with verbose output
wasmer deploy --name mps-voting --verbose

# 4. Check logs
wasmer logs --name mps-voting

# 5. If still failing, try rebuild
.\build.bat
wasmer deploy --name mps-voting --force
```

## 📊 Monitor Your Deployment

```powershell
# View active deployments
wasmer list

# See full details
wasmer describe --name mps-voting

# Follow logs in real-time
wasmer logs --name mps-voting --follow

# View specific deployment
wasmer package --name YOUR_USERNAME/mps-voting

# Get metrics
wasmer stats --name mps-voting
```

## 🎉 You're Done!

Your MPS Voting application is now:
- ✅ Frontend deployed to Wasmer Edge
- ✅ Available at `https://mps-voting.YOUR_USERNAME.wasmer.app`
- ✅ Ready to connect to PHP backend
- ✅ Automatically HTTPS with free SSL

Next: Connect the backend and test the voting flow!

---

**Need Help?**
- Check WASMER_FIXED_DEPLOYMENT.md for detailed setup
- Read Wasmer docs: https://docs.wasmer.io
- Join community: https://discord.gg/ZwZUJmS
