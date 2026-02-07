#!/bin/bash
# Deploy MPS Voting to Wasmer - Direct PHP (No Docker Required)
# Linux/Mac version

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}"
echo "==============================================="
echo "  🚀 Deploy MPS Voting to Wasmer PHP"
echo "     (No Docker Required)"
echo "==============================================="
echo -e "${NC}"
echo ""

# Check Wasmer
if ! command -v wasmer &> /dev/null; then
    echo -e "${RED}❌ Wasmer CLI not found!${NC}"
    echo ""
    echo "Install Wasmer from:"
    echo "  https://docs.wasmer.io/ecosystem/wasmer/getting-started"
    echo ""
    echo "Quick install:"
    echo "  curl https://get.wasmer.io -sSfL | sh"
    echo ""
    exit 1
fi
echo -e "${GREEN}✅ Wasmer CLI found${NC}"

# Check login
if ! wasmer whoami &> /dev/null; then
    echo ""
    echo -e "${YELLOW}🔑 Not logged in. Please login...${NC}"
    echo ""
    wasmer login
fi
echo -e "${GREEN}✅ Logged in to Wasmer${NC}"

# Get username
WASMER_USER=$(wasmer whoami)

echo ""
echo -e "${BLUE}==============================================="
echo "  Deployment Information"
echo "===============================================${NC}"
echo ""
echo "  App Name: mps-voting"
echo "  Runtime: PHP 8.2"
echo "  Database: MySQL 8.4 (Automatic)"
echo "  Owner: $WASMER_USER"
echo "  URL: https://mps-voting.$WASMER_USER.wasmer.app"
echo ""

read -p "Continue? (yes/no): " -r CONFIRM
if [[ ! $CONFIRM =~ ^[Yy][Ee][Ss]$ ]]; then
    echo "Deployment cancelled."
    exit 0
fi

echo ""
echo -e "${BLUE}==============================================="
echo "  🚀 Deploying to Wasmer..."
echo "===============================================${NC}"
echo ""

# Deploy
if wasmer deploy --name mps-voting; then
    DEPLOY_OK=1
else
    echo ""
    echo -e "${YELLOW}⚠️  Creating app for first time...${NC}"
    
    if wasmer app create \
        --name mps-voting \
        --owner "$WASMER_USER" \
        --runtime php \
        --version 8.2; then
        DEPLOY_OK=1
    else
        DEPLOY_OK=0
    fi
fi

echo ""
echo -e "${BLUE}==============================================="
echo "  ✅ Deployment Successfully Initiated!"
echo "===============================================${NC}"
echo ""
echo -e "${GREEN}🎉 Your PHP application is being deployed${NC}"
echo ""
echo -e "${YELLOW}📋 Next Steps:${NC}"
echo ""
echo "1. Wait 3-5 minutes for deployment"
echo ""
echo "2. Check deployment status:"
echo "   wasmer describe --name mps-voting"
echo ""
echo "3. View live logs:"
echo "   wasmer logs --name mps-voting --follow"
echo ""
echo "4. Access your app:"
echo "   https://mps-voting.$WASMER_USER.wasmer.app"
echo ""
echo "5. Configure environment (if needed):"
echo "   wasmer env set --name mps-voting DATABASE_HOST=mysql.internal"
echo ""
echo -e "${BLUE}===============================================${NC}"
echo ""
echo -e "${YELLOW}📚 Documentation: See WASMER_PHP_DIRECT.md${NC}"
echo ""
