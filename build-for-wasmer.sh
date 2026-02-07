#!/bin/bash
# Build MPS Voting Application for Wasmer Deployment
# Linux/Mac version

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo ""
echo -e "${BLUE}==============================================="
echo "  🔨 Building App for Wasmer Deployment"
echo "===============================================${NC}"
echo ""

# Check Git
if command -v git &> /dev/null; then
    echo -e "${GREEN}✅ Git found${NC}"
    
    if git rev-parse --git-dir > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Already in Git repository${NC}"
    else
        echo ""
        echo -e "${YELLOW}🔑 Initializing Git repository...${NC}"
        git init
        git remote add origin https://github.com/rifkigenk/voting.git
        echo -e "${GREEN}✅ Git repository initialized${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Git not found - skipping repository check${NC}"
fi

echo ""
echo -e "${BLUE}==============================================="
echo "  📋 Verifying Required Files"
echo "===============================================${NC}"
echo ""

FILES_OK=1

# Check essential files
if [ -f "index.html" ]; then
    echo -e "${GREEN}✅ index.html${NC}"
else
    echo -e "${RED}❌ index.html MISSING${NC}"
    FILES_OK=0
fi

if [ -f "php/connection.php" ]; then
    echo -e "${GREEN}✅ php/connection.php${NC}"
else
    echo -e "${RED}❌ php/connection.php MISSING${NC}"
    FILES_OK=0
fi

if [ -f "php/voter_login.php" ]; then
    echo -e "${GREEN}✅ php/voter_login.php${NC}"
else
    echo -e "${RED}❌ php/voter_login.php MISSING${NC}"
    FILES_OK=0
fi

if [ -f "php/vote.php" ]; then
    echo -e "${GREEN}✅ php/vote.php${NC}"
else
    echo -e "${RED}❌ php/vote.php MISSING${NC}"
    FILES_OK=0
fi

if [ -f "style/style.css" ]; then
    echo -e "${GREEN}✅ style/style.css${NC}"
else
    echo -e "${RED}❌ style/style.css MISSING${NC}"
    FILES_OK=0
fi

if [ -f "wasmer.toml" ]; then
    echo -e "${GREEN}✅ wasmer.toml${NC}"
else
    echo -e "${YELLOW}⚠️  wasmer.toml MISSING - Creating...${NC}"
    cat > wasmer.toml << 'EOF'
[app]
name = "voting"
version = "1.0.0"
description = "MPS Elections 2026 Voting Application"

[runtime]
name = "php"
version = "8.2"

[env]
ENVIRONMENT = "production"
DATABASE_HOST = "mysql.internal"
DATABASE_USER = "mps_voting"
DATABASE_NAME = "mps_voting"
EOF
    echo -e "${GREEN}✅ Created wasmer.toml${NC}"
fi

echo ""
if [ $FILES_OK -eq 0 ]; then
    echo -e "${RED}❌ Some files are missing! Check above.${NC}"
    exit 1
fi

echo -e "${BLUE}==============================================="
echo "  🧹 Cleaning Build Artifacts"
echo "===============================================${NC}"
echo ""

if [ -d "dist" ]; then
    echo "Removing old dist folder..."
    rm -rf dist
fi

if [ -d "build" ]; then
    echo "Removing old build folder..."
    rm -rf build
fi

echo -e "${GREEN}✅ Cleaned${NC}"

echo ""
echo -e "${BLUE}==============================================="
echo "  📦 Preparing Deployment Package"
echo "===============================================${NC}"
echo ""

echo "Files to be deployed:"
echo ""
find . -type f ! -path './.*' ! -path './node_modules/*' ! -path './.git/*' | sort
echo ""

echo -e "${BLUE}==============================================="
echo "  ✅ Build Status"
echo "===============================================${NC}"
echo ""

echo -e "${GREEN}Your app is ready for deployment!${NC}"
echo ""
echo -e "${YELLOW}📝 Deployment Instructions:${NC}"
echo ""
echo "1. Push to GitHub:"
echo "   git add ."
echo "   git commit -m \"Ready for Wasmer deployment\""
echo "   git push origin master"
echo ""
echo "2. Deploy to Wasmer (option A - CLI):"
echo "   wasmer login"
echo "   wasmer deploy --name voting --verbose"
echo ""
echo "3. Deploy to Wasmer (option B - Web):"
echo "   Visit: https://app.wasmer.io"
echo "   Click: New Project"
echo "   Select: PHP 8.2 (64-bit)"
echo "   Enable: MySQL Database"
echo "   Click: Deploy"
echo ""
echo -e "${BLUE}===============================================${NC}"
echo ""
echo -e "${GREEN}🎉 Your app will be live at:${NC}"
echo "   https://voting.rifkigenk.wasmer.app"
echo ""
echo -e "${YELLOW}📚 Documentation:${NC}"
echo "   - DEPLOY_QUICK_START.md"
echo "   - WASMER_PHP_DIRECT.md"
echo ""
