# 🚀 Wasmer Edge Deployment Guide

## Prerequisites
- [Wasmer CLI](https://docs.wasmer.io/ecosystem/wasmer/getting-started) installed
- Wasmer account (https://wasmer.io)
- Docker (optional, for local testing)

## Deployment Steps

### 1. Install Wasmer CLI
```bash
curl https://get.wasmer.io -sSfL | sh
```

### 2. Login to Wasmer
```bash
wasmer login
```

### 3. Prepare Application Name
```bash
# Replace YOUR_USERNAME with your Wasmer username
export WASMER_USER=YOUR_USERNAME
export APP_NAME=mps-voting
```

### 4. Deploy to Wasmer Edge
```bash
cd "d:\XAMPP\htdocs\Projek MPS\mps-voting"

# Option A: Deploy as static site with API proxy
wasmer deploy --name $APP_NAME

# Option B: Deploy with custom registry
wasmer deploy --name $APP_NAME --owner $WASMER_USER
```

### 5. Set Environment Variables
```bash
wasmer config set --name $APP_NAME \
  DATABASE_HOST="your-db-host.com" \
  DATABASE_USER="mps_voting_user" \
  DATABASE_NAME="mps_voting" \
  DATABASE_PORT="3306"

# Store secrets securely
wasmer secret set --name $APP_NAME \
  DATABASE_PASSWORD="your_secure_password"
```

### 6. Access Your App
Your application will be available at:
```
https://$APP_NAME.$WASMER_USER.wasmer.app
```

## Database Configuration

### Option 1: Cloud Database (Recommended)
- Use **AWS RDS**, **PlanetScale**, or **Clever Cloud**
- Update connection string in `php/connection.php`

### Option 2: Local MySQL Connection
- For local development, keep using XAMPP
- For production, migrate to cloud database

### Update Connection String
Edit `php/connection.php`:
```php
<?php
$host = getenv('DATABASE_HOST') ?: 'localhost';
$user = getenv('DATABASE_USER') ?: 'root';
$password = getenv('DATABASE_PASSWORD') ?: '';
$database = getenv('DATABASE_NAME') ?: 'mps_voting';
$port = getenv('DATABASE_PORT') ?: 3306;

$conn = new mysqli($host, $user, $password, $database, $port);
```

## Deployment Architecture

```
┌─────────────────────────────────────┐
│    Wasmer Edge (Static Assets)      │
│  ├─ /index.html                     │
│  ├─ /style/                         │
│  └─ /assets/                        │
└────────────┬────────────────────────┘
             │
             ↓ API Calls
┌─────────────────────────────────────┐
│   API Proxy / Handler Layer         │
│  (Routes to PHP Backend)            │
└────────────┬────────────────────────┘
             │
             ↓
┌─────────────────────────────────────┐
│   Cloud Database                    │
│  (RDS / PlanetScale / Clever Cloud) │
└─────────────────────────────────────┘
```

## Development Locally

### Run with Wasmer Locally
```bash
wasmer run . --net --env DATABASE_HOST=localhost
```

### Build Distribution Package
```bash
npm run build
# OR
php -S localhost:8000 -t .
```

## Advanced Configuration

### Add Custom Domain
```bash
wasmer domain add --name $APP_NAME yourdomain.com
```

### Enable Cache Headers
```bash
# Add to wasmer.toml
[cache]
max_age = 3600
public = true
```

### Enable CORS
Create `.wasmer/cors.toml`:
```toml
[cors]
allowed_origins = ["*"]
allowed_methods = ["GET", "POST", "PUT", "DELETE"]
```

## Troubleshooting

### 404 on Routes
- Verify all files are in deployment package
- Check route configuration in `wasmer.toml`

### Database Connection Failed
- Verify DATABASE_HOST environment variable
- Check firewall allows connection from Wasmer IPs
- Test connection: `mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME`

### PHP Errors
- Check logs: `wasmer logs --name $APP_NAME`
- Verify PHP runtime compatibility

## Next Steps

1. **Set up monitoring**: Add Application Performance Monitoring (APM)
2. **Enable HTTPS**: Automatic with Wasmer Edge
3. **Backup database**: Configure automated backups
4. **Set up CI/CD**: Automate deployments

## Support
- [Wasmer Documentation](https://docs.wasmer.io)
- [Wasmer Community Discord](https://discord.gg/ZwZUJmS)
