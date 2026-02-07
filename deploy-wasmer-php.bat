@echo off
REM Deploy MPS Voting to Wasmer - Windows Quick Start

setlocal enabledelayedexpansion

cls
echo.
echo ===============================================
echo   🚀 Deploy MPS Voting to Wasmer Edge
echo ===============================================
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker not found! Install Docker Desktop from: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)
echo ✅ Docker found

REM Check if Wasmer CLI is installed
wasmer --version >nul 2>&1
if errorlevel 1 (
    echo.
    echo ❌ Wasmer CLI not found!
    echo.
    echo Install Wasmer from: https://docs.wasmer.io/ecosystem/wasmer/getting-started
    echo.
    echo Quick install (PowerShell):
    echo   irm https://get.wasmer.io -outfile install.ps1; `$PROFILE = $PROFILE -replace '.ps1$', '_wasmer.ps1'; `&$PROFILE
    echo.
    pause
    exit /b 1
)
echo ✅ Wasmer CLI found

REM Check if logged into Wasmer
wasmer whoami >nul 2>&1
if errorlevel 1 (
    echo.
    echo 🔑 Not logged in to Wasmer. Please login...
    echo.
    wasmer login
    if errorlevel 1 (
        echo ❌ Login failed
        pause
        exit /b 1
    )
)
echo ✅ Logged in to Wasmer

echo.
echo ===============================================
echo   Step 1: Test Locally with Docker
echo ===============================================
echo.
echo Building and starting Docker containers (this may take 2-3 minutes)...
echo.

REM Build Docker image
docker build -t mps-voting-php .
if errorlevel 1 (
    echo ❌ Docker build failed
    pause
    exit /b 1
)
echo ✅ Docker image built

REM Start containers
docker-compose up -d
if errorlevel 1 (
    echo ❌ Docker compose failed
    pause
    exit /b 1
)
echo ✅ Docker containers started

echo.
echo   Local testing URLs:
echo   - App: http://localhost
echo   - PhpMyAdmin: http://localhost:8080
echo.
echo ✅ Wait 30 seconds for MySQL to initialize...
timeout /t 30 /nobreak

REM Test PHP endpoint
echo.
echo Testing PHP endpoint...
curl -s http://localhost/php/results.php >nul
if errorlevel 1 (
    echo ⚠️  PHP may still be starting, retrying in 10 seconds...
    timeout /t 10 /nobreak
)

echo ✅ Local testing complete!

echo.
echo ===============================================
echo   Step 2: Deploy to Wasmer Edge
echo ===============================================
echo.

set /p WASMER_USER="Enter your Wasmer username (or press Enter for default): "
if "!WASMER_USER!"=="" (
    echo ⚠️  No username provided. Getting from: wasmer whoami
    for /f %%i in ('wasmer whoami 2^>nul') do set WASMER_USER=%%i
)

echo.
echo Deploying to Wasmer...
echo   App Name: mps-voting
echo   Owner: !WASMER_USER!
echo.

REM Deploy to Wasmer
wasmer app create ^
    --name mps-voting ^
    --owner !WASMER_USER! ^
    --from-dockerfile ./Dockerfile

if errorlevel 1 (
    echo.
    echo ⚠️  App might already exist. Updating deployment...
    wasmer deploy --name mps-voting --force
)

echo.
echo ===============================================
echo   ✅ Deployment Complete!
echo ===============================================
echo.
echo 🎉 Your application is being deployed to Wasmer Edge
echo.
echo 📋 Next Steps:
echo.
echo 1. Wait 5-10 minutes for deployment to complete
echo.
echo 2. Check deployment status:
echo    wasmer describe --name mps-voting
echo.
echo 3. View logs:
echo    wasmer logs --name mps-voting --follow
echo.
echo 4. Access your app:
echo    https://mps-voting.!WASMER_USER!.wasmer.app
echo.
echo 5. Configure database (if needed):
echo    wasmer env set --name mps-voting DATABASE_HOST="mysql.internal"
echo.
echo 📚 Full guide: See WASMER_PHP_DEPLOYMENT.md
echo.
echo ===============================================
echo.

REM Stop local containers
echo.
set /p STOP_LOCAL="Stop local Docker containers? (y/n): "
if /i "!STOP_LOCAL!"=="y" (
    docker-compose down
    echo ✅ Local containers stopped
) else (
    echo Local containers still running at http://localhost
)

echo.
echo ✨ Deployment setup complete!
echo.
pause
