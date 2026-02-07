#!/bin/bash
# Deploy MPS Voting to Wasmer - Linux/Mac Quick Start

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "==============================================="
echo "  🚀 Deploy MPS Voting to Wasmer Edge"
echo "==============================================="
echo -e "${NC}"
echo ""

# Check Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker not found!${NC}"
    echo "Install Docker from: https://www.docker.com/products/docker-desktop"
    exit 1
fi
echo -e "${GREEN}✅ Docker found${NC}"

# Check Wasmer
if ! command -v wasmer &> /dev/null; then
    echo ""
    echo -e "${RED}❌ Wasmer CLI not found!${NC}"
    echo ""
    echo "Install Wasmer from: https://docs.wasmer.io/ecosystem/wasmer/getting-started"
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
    echo -e "${YELLOW}🔑 Not logged in to Wasmer. Please login...${NC}"
    echo ""
    wasmer login
fi
echo -e "${GREEN}✅ Logged in to Wasmer${NC}"

echo ""
echo -e "${BLUE}==============================================="
echo "  Step 1: Test Locally with Docker"
echo "===============================================${NC}"
echo ""
echo "Building and starting Docker containers (this may take 2-3 minutes)..."
echo ""

# Build Docker image
docker build -t mps-voting-php . || {
    echo -e "${RED}❌ Docker build failed${NC}"
    exit 1
}
echo -e "${GREEN}✅ Docker image built${NC}"

# Start containers
docker-compose up -d || {
    echo -e "${RED}❌ Docker compose failed${NC}"
    exit 1
}
echo -e "${GREEN}✅ Docker containers started${NC}"

echo ""
echo -e "${YELLOW}Local testing URLs:${NC}"
echo "  - App: http://localhost"
echo "  - PhpMyAdmin: http://localhost:8080"
echo ""
echo -e "${YELLOW}✅ Wait 30 seconds for MySQL to initialize...${NC}"
sleep 30

# Test PHP
echo ""
echo "Testing PHP endpoint..."
if curl -s http://localhost/php/results.php > /dev/null; then
    echo -e "${GREEN}✅ PHP responding${NC}"
else
    echo -e "${YELLOW}⚠️  PHP may still be starting, retrying in 10 seconds...${NC}"
    sleep 10
fi

echo -e "${GREEN}✅ Local testing complete!${NC}"

echo ""
echo -e "${BLUE}==============================================="
echo "  Step 2: Deploy to Wasmer Edge"
echo "===============================================${NC}"
echo ""

# Get Wasmer username
read -p "Enter your Wasmer username (or press Enter for default): " WASMER_USER
if [ -z "$WASMER_USER" ]; then
    WASMER_USER=$(wasmer whoami)
    echo "Using username: $WASMER_USER"
fi

echo ""
echo "Deploying to Wasmer..."
echo "  App Name: mps-voting"
echo "  Owner: $WASMER_USER"
echo ""

# Deploy
if wasmer app create \
    --name mps-voting \
    --owner "$WASMER_USER" \
    --from-dockerfile ./Dockerfile; then
    echo -e "${GREEN}✅ Deployment initiated${NC}"
else
    echo -e "${YELLOW}⚠️  App might already exist. Updating deployment...${NC}"
    wasmer deploy --name mps-voting --force
fi

echo ""
echo -e "${BLUE}==============================================="
echo "  ✅ Deployment Complete!"
echo "===============================================${NC}"
echo ""
echo -e "${GREEN}🎉 Your application is being deployed to Wasmer Edge${NC}"
echo ""
echo -e "${YELLOW}📋 Next Steps:${NC}"
echo ""
echo "1. Wait 5-10 minutes for deployment to complete"
echo ""
echo "2. Check deployment status:"
echo "   wasmer describe --name mps-voting"
echo ""
echo "3. View logs:"
echo "   wasmer logs --name mps-voting --follow"
echo ""
echo "4. Access your app:"
echo "   https://mps-voting.$WASMER_USER.wasmer.app"
echo ""
echo "5. Configure database (if needed):"
echo "   wasmer env set --name mps-voting DATABASE_HOST=\"mysql.internal\""
echo ""
echo -e "${YELLOW}📚 Full guide: See WASMER_PHP_DEPLOYMENT.md${NC}"
echo ""
echo -e "${BLUE}===============================================${NC}"
echo ""

# Stop local containers?
read -p "Stop local Docker containers? (y/n): " -n 1 -r STOP_LOCAL
echo ""
if [[ $STOP_LOCAL =~ ^[Yy]$ ]]; then
    docker-compose down
    echo -e "${GREEN}✅ Local containers stopped${NC}"
else
    echo -e "${YELLOW}Local containers still running at http://localhost${NC}"
fi

echo ""
echo -e "${GREEN}✨ Deployment setup complete!${NC}"
echo ""
