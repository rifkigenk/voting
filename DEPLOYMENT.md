# 🗳️ MPS Elections 2026 - Deployment Guide

Complete setup and deployment instructions for the MPS Voting Application.

## 📋 Quick Start

### Local Development (XAMPP)
```bash
# 1. Copy project to XAMPP
# Already done: D:\XAMPP\htdocs\Projek MPS\mps-voting

# 2. Start XAMPP (Apache + MySQL)
# Click XAMPP Control Panel → Start Apache & MySQL

# 3. Access application
# http://localhost/Projek%20MPS/mps-voting/index.html
```

### Docker Deployment (Local)
```bash
# 1. Install Docker Desktop
# https://www.docker.com/products/docker-desktop

# 2. Start containers
docker-compose up -d

# 3. Access application
# App:      http://localhost
# PhpMyAdmin: http://localhost:8080
# Database: localhost:3306
```

### Wasmer Edge Deployment (Production)
```bash
# 1. Install Wasmer CLI
curl https://get.wasmer.io -sSfL | sh

# 2. Login to Wasmer
wasmer login

# 3. Deploy (PowerShell on Windows)
powershell -ExecutionPolicy Bypass -File deploy-wasmer.ps1 -AppName mps-voting -WasmerUser your_username

# 3. Deploy (Bash on Linux/Mac)
bash deploy-wasmer.sh mps-voting your_username

# 4. Configure environment
wasmer config set --name mps-voting \
  DATABASE_HOST="your-db.com" \
  DATABASE_USER="mps_voting" \
  DATABASE_NAME="mps_voting"

# 5. Set password
wasmer secret set --name mps-voting DATABASE_PASSWORD="your_password"
```

## 🏗️ Deployment Architecture

```
┌─────────────────────────────────────────────────────┐
│                   Wasmer Edge                       │
│  ├─ Static Files (HTML, CSS, JS, Images)           │
│  └─ API Routes (/php/*, /sql/*)                    │
└────────────────┬────────────────────────────────────┘
                 │ HTTPS Requests
                 ↓
┌─────────────────────────────────────────────────────┐
│              Cloud Database (RDS/PlanetScale)       │
│  ├─ voters                                          │
│  ├─ candidates                                      │
│  ├─ votes                                           │
│  └─ admin                                           │
└─────────────────────────────────────────────────────┘
```

## 🗄️ Database Setup

### Development (MySQL on XAMPP)
```bash
# Already initialized via php/database.php
# Access: http://localhost/phpmyadmin
```

### Docker Database
```bash
# Credentials from docker-compose.yml
Host:     mysql
User:     mps_voting
Password: mps_voting_2026
Database: mps_voting
Port:     3306
```

### Production Database (Recommended Options)

#### Option 1: AWS RDS
1. Create RDS MySQL 8.0 instance
2. Allow inbound traffic on port 3306
3. Create database: `mps_voting`
4. Create user: `mps_voting` with password
5. Update connection variables

#### Option 2: PlanetScale
1. Sign up: https://planetscale.com
2. Create database: `mps_voting`
3. Get connection string
4. Update DATABASE_HOST and credentials

#### Option 3: Clever Cloud
1. Sign up: https://www.clever-cloud.com
2. Create MySQL add-on
3. Link to application
4. Get connection details

## 🔐 Environment Variables

### Local Development (.env)
```bash
DATABASE_HOST=localhost
DATABASE_USER=root
DATABASE_PASSWORD=
DATABASE_NAME=mps_voting
DATABASE_PORT=3306
ENVIRONMENT=development
```

### Production (.env.production)
```bash
DATABASE_HOST=prod-db-instance.region.rds.amazonaws.com
DATABASE_USER=mps_voting_prod
DATABASE_PASSWORD=<secure-password>
DATABASE_NAME=mps_voting
DATABASE_PORT=3306
ENVIRONMENT=production
TIMEZONE=Asia/Jakarta
```

## 🚀 Deployment Checklist

- [ ] Environment variables configured
- [ ] Database migrated to production
- [ ] Admin credentials changed from default
- [ ] CORS headers configured
- [ ] SSL/TLS certificates installed (Wasmer handles this)
- [ ] Database backups enabled
- [ ] Monitoring/logging configured
- [ ] Rate limiting enabled on API endpoints
- [ ] Session timeout configured
- [ ] Email notifications tested (if enabled)

## 📊 Monitoring & Maintenance

### View Application Logs
```bash
# Wasmer
wasmer logs --name mps-voting

# Docker
docker-compose logs php
docker-compose logs mysql
```

### Database Backup
```bash
# Local backup
mysqldump -h localhost -u mps_voting -p mps_voting > backup.sql

# Docker backup
docker-compose exec mysql mysqldump -u mps_voting -pmps_voting_2026 mps_voting > backup.sql
```

### Restore Database
```bash
mysql -h localhost -u mps_voting -p mps_voting < backup.sql
```

## 🔄 Deployment Pipeline (CI/CD)

### GitHub Actions Example
```yaml
name: Deploy to Wasmer
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: wasmerio/setup-wasmer@v1
      - run: wasmer deploy --name mps-voting
```

## 🛠️ Troubleshooting

### Deployment Issues
```bash
# Check Wasmer status
wasmer whoami
wasmer list

# View deployment logs
wasmer logs --name mps-voting

# Redeploy
wasmer deploy --name mps-voting --force
```

### Database Connection Issues
```bash
# Test connection
mysql -h <HOST> -u <USER> -p<PASSWORD> <DATABASE>

# From PHP
php -r "new mysqli('host', 'user', 'pass', 'db');"
```

### PHP Errors
1. Check Apache error logs: `/var/log/apache2/error.log`
2. Check PHP error logs: `/var/log/php.log`
3. Enable debug mode: Set `ENVIRONMENT=development`

## 📞 Support & Resources

- **Wasmer Docs**: https://docs.wasmer.io
- **Wasmer Community**: https://discord.gg/ZwZUJmS
- **Docker Docs**: https://docs.docker.com
- **MySQL Documentation**: https://dev.mysql.com/doc

## 📝 Project Structure

```
mps-voting/
├── index.html                 # Homepage
├── php/
│   ├── connection.php         # DB connection (env-aware)
│   ├── database.php           # Schema creation
│   ├── setup.php              # Initial setup
│   ├── voter_login.php        # Voter authentication
│   ├── vote.php               # Voting interface
│   ├── results.php            # Results display
│   └── admin_login.php        # Admin authentication
├── style/
│   └── mps-voting2/           # Face recognition UI
│       ├── face.html
│       ├── js/face.js
│       ├── cek_wajah.php      # Face matching API
│       └── record_face.php    # Face registration API
├── assets/                    # Images, icons, etc.
├── docker-compose.yml         # Docker configuration
├── wasmer.toml               # Wasmer configuration
├── deploy-wasmer.sh          # Bash deploy script
├── deploy-wasmer.ps1         # PowerShell deploy script
├── .env.example              # Environment template
└── WASMER_DEPLOYMENT.md      # Detailed Wasmer guide
```

---

**Current Status**: ✅ Ready for Production Deployment

**Supported Platforms**:
- ✅ XAMPP (Local Development)
- ✅ Docker (Local/Cloud)
- ✅ Wasmer Edge (Serverless)
- ✅ AWS, GCP, Azure (with Docker/Custom VMs)

**Next Steps**: 
1. Choose your deployment platform
2. Configure database connection
3. Set environment variables
4. Run deployment script
5. Test application endpoints
