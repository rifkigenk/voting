# Wasmer Edge Deployment Script for MPS Voting Application (Windows)
# Usage: powershell -ExecutionPolicy Bypass -File deploy-wasmer.ps1 -AppName mps-voting -WasmerUser your_username

param(
    [string]$AppName = "mps-voting",
    [string]$WasmerUser = ""
)

# Colors
$Host.UI.RawUI.BackgroundColor = "Black"
Clear-Host

Write-Host "🚀 MPS Voting Application - Wasmer Edge Deployment" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Check if Wasmer CLI is installed
try {
    $wasmerVersion = wasmer --version 2>$null
    Write-Host "✅ Wasmer CLI found: $wasmerVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Wasmer CLI not found!" -ForegroundColor Red
    Write-Host "Install it from: https://docs.wasmer.io/ecosystem/wasmer/getting-started" -ForegroundColor Yellow
    exit 1
}

# Get Wasmer username if not provided
if ([string]::IsNullOrWhiteSpace($WasmerUser)) {
    Write-Host "📝 Enter your Wasmer username:" -ForegroundColor Yellow
    $WasmerUser = Read-Host
}

# Check if logged in
try {
    $whoami = wasmer whoami 2>$null
} catch {
    Write-Host "🔑 Not logged in. Please login to Wasmer..." -ForegroundColor Yellow
    wasmer login
}

# Prepare deployment
Write-Host ""
Write-Host "📦 Preparing Application..." -ForegroundColor Cyan

# Create build directory
if (-not (Test-Path "dist")) {
    New-Item -ItemType Directory -Path "dist" -Force | Out-Null
}

# Copy frontend files
Write-Host "Copying static files..." -ForegroundColor Gray
if (Test-Path "index.html") {
    Copy-Item "index.html" "dist\" -Force
} else {
    Write-Host "  ⚠️  index.html not found" -ForegroundColor Yellow
}

if (Test-Path "style") {
    Copy-Item "style" "dist\" -Recurse -Force
} else {
    Write-Host "  ⚠️  style/ not found" -ForegroundColor Yellow
}

if (Test-Path "assets") {
    Copy-Item "assets" "dist\" -Recurse -Force
} else {
    Write-Host "  ⚠️  assets/ not found" -ForegroundColor Yellow
}

# List deployment contents
Write-Host ""
Write-Host "📋 Deployment Contents:" -ForegroundColor Cyan
Get-ChildItem -Path "dist" -Recurse -File | ForEach-Object { Write-Host "  - $($_.FullName.Substring((Get-Item "dist").FullName.Length))" -ForegroundColor Gray } | Select-Object -First 20

# Confirmation
Write-Host ""
Write-Host "⚠️  Deployment Summary:" -ForegroundColor Yellow
Write-Host "  App Name: $AppName"
Write-Host "  Username: $WasmerUser"
Write-Host "  Registry: $WasmerUser/$AppName"
Write-Host "  URL: https://$AppName.$WasmerUser.wasmer.app"
Write-Host ""

$confirm = Read-Host "Continue with deployment? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "❌ Deployment cancelled" -ForegroundColor Red
    exit 1
}

# Deploy
Write-Host ""
Write-Host "🚀 Deploying to Wasmer Edge..." -ForegroundColor Cyan
wasmer deploy --name $AppName --owner $WasmerUser

# Success message
Write-Host ""
Write-Host "✅ Deployment successful!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1️⃣  Set environment variables:" -ForegroundColor Cyan
Write-Host "   wasmer config set --name $AppName \`" -ForegroundColor Gray
Write-Host "     DATABASE_HOST=""your-database.com"" \`" -ForegroundColor Gray
Write-Host "     DATABASE_USER=""mps_voting"" \`" -ForegroundColor Gray
Write-Host "     DATABASE_NAME=""mps_voting""" -ForegroundColor Gray
Write-Host ""
Write-Host "2️⃣  Set database password (secret):" -ForegroundColor Cyan
Write-Host "   wasmer secret set --name $AppName DATABASE_PASSWORD=""your_secure_password""" -ForegroundColor Gray
Write-Host ""
Write-Host "3️⃣  Access your app:" -ForegroundColor Cyan
Write-Host "   https://$AppName.$WasmerUser.wasmer.app" -ForegroundColor Gray
Write-Host ""
Write-Host "📚 Documentation: See WASMER_DEPLOYMENT.md for detailed setup" -ForegroundColor Yellow
