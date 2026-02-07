@echo off
REM Deploy MPS Voting to Wasmer - Direct PHP (No Docker Required)
REM Windows PowerShell version

setlocal enabledelayedexpansion

cls
echo.
echo ===============================================
echo   🚀 Deploy MPS Voting to Wasmer PHP
echo   (No Docker Required)
echo ===============================================
echo.

REM Check if Wasmer CLI is installed
wasmer --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Wasmer CLI not found!
    echo.
    echo Install Wasmer from: https://docs.wasmer.io/ecosystem/wasmer/getting-started
    echo.
    echo Or run in PowerShell:
    echo   irm https://get.wasmer.io -outfile install.ps1 ; &$PROFILE
    echo.
    pause
    exit /b 1
)
echo ✅ Wasmer CLI found

REM Check if logged into Wasmer
wasmer whoami >nul 2>&1
if errorlevel 1 (
    echo.
    echo 🔑 Not logged in to Wasmer. Logging in...
    echo.
    wasmer login
    if errorlevel 1 (
        echo ❌ Login failed
        pause
        exit /b 1
    )
)
echo ✅ Logged in to Wasmer

REM Get current username
for /f %%i in ('wasmer whoami 2^>nul') do set WASMER_USER=%%i

echo.
echo ===============================================
echo   Deployment Information
echo ===============================================
echo.
echo   App Name: mps-voting
echo   Runtime: PHP 8.2
echo   Database: MySQL 8.4 (Automatic)
echo   Owner: !WASMER_USER!
echo   URL: https://mps-voting.!WASMER_USER!.wasmer.app
echo.

set /p CONFIRM="Continue? (yes/no): "
if /i "!CONFIRM!" neq "yes" (
    echo Deployment cancelled.
    pause
    exit /b 0
)

echo.
echo ===============================================
echo   🚀 Deploying to Wasmer...
echo ===============================================
echo.

REM Deploy directly to Wasmer
wasmer deploy --name mps-voting

if errorlevel 1 (
    echo.
    echo ⚠️  Initial deployment creation...
    wasmer app create ^
        --name mps-voting ^
        --owner !WASMER_USER! ^
        --runtime php ^
        --version 8.2
    
    if errorlevel 1 (
        echo ❌ Deployment failed
        pause
        exit /b 1
    )
)

echo.
echo ===============================================
echo   ✅ Deployment Successfully Initiated!
echo ===============================================
echo.
echo 🎉 Your PHP application is being deployed
echo.
echo 📋 Next Steps:
echo.
echo 1. Wait 3-5 minutes for deployment
echo.
echo 2. Check deployment status:
echo    wasmer describe --name mps-voting
echo.
echo 3. View live logs:
echo    wasmer logs --name mps-voting --follow
echo.
echo 4. Access your app:
echo    https://mps-voting.!WASMER_USER!.wasmer.app
echo.
echo 5. Configure environment (if needed):
echo    wasmer env set --name mps-voting DATABASE_HOST=mysql.internal
echo.
echo ===============================================
echo.
echo 📚 Documentation: See WASMER_PHP_DIRECT.md
echo.
pause
