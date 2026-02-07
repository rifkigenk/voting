@echo off
REM Build MPS Voting Application for Wasmer Deployment
REM Windows version

setlocal enabledelayedexpansion

cls
echo.
echo ===============================================
echo   🔨 Building App for Wasmer Deployment
echo ===============================================
echo.

REM Check if Git is installed
git --version >nul 2>&1
if errorlevel 1 (
    echo ⚠️  Git not found - skipping repository check
) else (
    echo ✅ Git found
    
    REM Check if in git repository
    git rev-parse --git-dir >nul 2>&1
    if errorlevel 1 (
        echo.
        echo 🔑 Initializing Git repository...
        git init
        git remote add origin https://github.com/rifkigenk/voting.git
        echo ✅ Git repository initialized
    ) else (
        echo ✅ Already in Git repository
    )
)

echo.
echo ===============================================
echo   📋 Verifying Required Files
echo ===============================================
echo.

setlocal EnableDelayedExpansion
set FILES_OK=1

REM Check essential files
if exist index.html (
    echo ✅ index.html
) else (
    echo ❌ index.html MISSING
    set FILES_OK=0
)

if exist php\connection.php (
    echo ✅ php/connection.php
) else (
    echo ❌ php/connection.php MISSING
    set FILES_OK=0
)

if exist php\voter_login.php (
    echo ✅ php/voter_login.php
) else (
    echo ❌ php/voter_login.php MISSING
    set FILES_OK=0
)

if exist php\vote.php (
    echo ✅ php/vote.php
) else (
    echo ❌ php/vote.php MISSING
    set FILES_OK=0
)

if exist style\style.css (
    echo ✅ style/style.css
) else (
    echo ❌ style/style.css MISSING
    set FILES_OK=0
)

if exist wasmer.toml (
    echo ✅ wasmer.toml
) else (
    echo ❌ wasmer.toml MISSING - Creating...
    echo [app] > wasmer.toml
    echo name = "voting" >> wasmer.toml
    echo version = "1.0.0" >> wasmer.toml
    echo description = "MPS Elections 2026 Voting Application" >> wasmer.toml
)

echo.
if !FILES_OK!==0 (
    echo ❌ Some files are missing! Check above.
    pause
    exit /b 1
)

echo ===============================================
echo   🧹 Cleaning Build Artifacts
echo ===============================================
echo.

if exist dist (
    echo Removing old dist folder...
    rmdir /s /q dist >nul 2>&1
)

if exist build (
    echo Removing old build folder...
    rmdir /s /q build >nul 2>&1
)

echo ✅ Cleaned

echo.
echo ===============================================
echo   📦 Preparing Deployment Package
echo ===============================================
echo.

REM List all files that will be deployed
echo Files to be deployed:
echo.
dir /s /b | findstr /v "^\." | findstr /v "node_modules" | findstr /v ".git" | sort
echo.

echo ===============================================
echo   ✅ Build Status
echo ===============================================
echo.

echo Your app is ready for deployment!
echo.
echo 📝 Deployment Instructions:
echo.
echo 1. Push to GitHub:
echo    git add .
echo    git commit -m "Ready for Wasmer deployment"
echo    git push origin master
echo.
echo 2. Deploy to Wasmer (option A - CLI):
echo    wasmer login
echo    wasmer deploy --name voting --verbose
echo.
echo 3. Deploy to Wasmer (option B - Web):
echo    Visit: https://app.wasmer.io
echo    Click: New Project
echo    Select: PHP 8.2 (64-bit)
echo    Enable: MySQL Database
echo    Click: Deploy
echo.
echo ===============================================
echo.
echo 🎉 Your app will be live at:
echo    https://voting.rifkigenk.wasmer.app
echo.
echo 📚 Documentation:
echo    - DEPLOY_QUICK_START.md
echo    - WASMER_PHP_DIRECT.md
echo.
pause
