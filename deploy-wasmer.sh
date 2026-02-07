#!/bin/bash
# Wasmer Edge Deployment Script for MPS Voting Application
# Usage: ./deploy-wasmer.sh [app-name] [wasmer-username]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 MPS Voting Application - Wasmer Edge Deployment${NC}"
echo "=================================================="

# Check if Wasmer CLI is installed
if ! command -v wasmer &> /dev/null; then
    echo -e "${RED}❌ Wasmer CLI not found!${NC}"
    echo "Install it from: https://docs.wasmer.io/ecosystem/wasmer/getting-started"
    exit 1
fi

# Get app name and username
APP_NAME=${1:-"mps-voting"}
WASMER_USER=${2:-""}

if [ -z "$WASMER_USER" ]; then
    echo -e "${YELLOW}📝 Enter your Wasmer username:${NC}"
    read -r WASMER_USER
fi

# Check if logged in to Wasmer
if ! wasmer whoami &> /dev/null; then
    echo -e "${YELLOW}🔑 Not logged in. Please login to Wasmer...${NC}"
    wasmer login
fi

echo -e "${BLUE}📦 Preparing Application...${NC}"

# Create build directory
mkdir -p dist

# Copy frontend files
echo "Copying static files..."
cp index.html dist/ 2>/dev/null || echo "  ⚠️  index.html not found"
cp -r style/ dist/ 2>/dev/null || echo "  ⚠️  style/ not found"
cp -r assets/ dist/ 2>/dev/null || echo "  ⚠️  assets/ not found"

# List deployment contents
echo -e "${BLUE}📋 Deployment Contents:${NC}"
find dist/ -type f | head -20

# Confirm before deploying
echo ""
echo -e "${YELLOW}Deployment Summary:${NC}"
echo "  App Name: $APP_NAME"
echo "  Username: $WASMER_USER"
echo "  Registry: $WASMER_USER/$APP_NAME"
echo "  URL: https://$APP_NAME.$WASMER_USER.wasmer.app"
echo ""
echo -e "${YELLOW}Continue with deployment? (yes/no)${NC}"
read -r -p "> " confirm

if [ "$confirm" != "yes" ]; then
    echo -e "${RED}❌ Deployment cancelled${NC}"
    exit 1
fi

# Deploy to Wasmer
echo ""
echo -e "${BLUE}🚀 Deploying to Wasmer Edge...${NC}"
wasmer deploy --name "$APP_NAME" --owner "$WASMER_USER"

echo ""
echo -e "${GREEN}✅ Deployment successful!${NC}"
echo ""
echo -e "${BLUE}Next Steps:${NC}"
echo "1. Set environment variables:"
echo "   wasmer config set --name $APP_NAME \\"
echo "     DATABASE_HOST=\"your-database.com\" \\"
echo "     DATABASE_USER=\"mps_voting\" \\"
echo "     DATABASE_NAME=\"mps_voting\""
echo ""
echo "2. Set database password (secret):"
echo "   wasmer secret set --name $APP_NAME DATABASE_PASSWORD=\"your_secure_password\""
echo ""
echo "3. Access your app:"
echo "   https://$APP_NAME.$WASMER_USER.wasmer.app"
echo ""
echo -e "${YELLOW}📚 Documentation:${NC} See WASMER_DEPLOYMENT.md for detailed setup"
