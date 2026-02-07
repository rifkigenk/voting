@echo off
REM Build script for Wasmer Edge deployment (Windows)
REM This script prepares the frontend files for deployment

setlocal enabledelayedexpansion

echo.
echo 🔨 Building MPS Voting Application for Wasmer Edge...
echo.

REM Create or clean dist directory
if exist dist (
    echo Cleaning existing dist directory...
    rmdir /s /q dist
)

echo Creating dist directory...
mkdir dist

REM Copy frontend files
echo Copying frontend files...

if exist index.html (
    copy index.html dist\
    echo   ✓ index.html
) else (
    echo   ⚠️  index.html not found
)

if exist style (
    xcopy style dist\style /E /I /Y >nul
    echo   ✓ style/
) else (
    echo   ⚠️  style/ not found
)

if exist assets (
    xcopy assets dist\assets /E /I /Y >nul
    echo   ✓ assets/
) else (
    echo   ⚠️  assets/ not found
)

REM Create API configuration file
echo Creating API configuration...
(
    echo // API Configuration for Wasmer Edge Frontend
    echo // Backend runs separately (Docker, Cloud PHP, XAMPP^)
    echo.
    echo const API_CONFIG = {
    echo   // Update this base URL to point to your PHP backend
    echo   BASE_URL: process.env.REACT_APP_API_URL ^|^| 'http://localhost/Projek%%20MPS/mps-voting',
    echo.
    echo   // API endpoints
    echo   endpoints: {
    echo     voterLogin: '/php/voter_login.php',
    echo     vote: '/php/vote.php',
    echo     results: '/php/results.php',
    echo     adminLogin: '/php/admin_login.php',
    echo     adminDashboard: '/php/admin_dashboard.php',
    echo     faceRecord: '/style/mps-voting2/record_face.php',
    echo     faceCheck: '/style/mps-voting2/cek_wajah.php',
    echo   },
    echo.
    echo   // Get full API URL
    echo   getUrl: function(endpoint^) {
    echo     return this.BASE_URL + endpoint;
    echo   }
    echo };
    echo.
    echo // Export for use in JavaScript
    echo if (typeof module !== 'undefined' ^&^& module.exports^) {
    echo   module.exports = API_CONFIG;
    echo }
) > dist\config.js
echo   ✓ config.js

echo.
echo 📦 Build Output (dist\):
dir /s /b dist | findstr /v "^$"

echo.
echo ✅ Build complete! Ready for Wasmer deployment
echo.
echo 📝 Next steps:
echo 1. Ensure PHP backend is accessible (Docker, Cloud, etc.^)
echo 2. Update API_CONFIG.BASE_URL if needed in dist\config.js
echo 3. Deploy: wasmer deploy --name mps-voting
echo.
