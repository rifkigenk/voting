#!/bin/bash
# Build script for Wasmer Edge deployment
# This script prepares the frontend files for deployment

set -e

echo "🔨 Building MPS Voting Application for Wasmer Edge..."

# Create or clean dist directory
if [ -d "dist" ]; then
    echo "Cleaning existing dist directory..."
    rm -rf dist/*
else
    echo "Creating dist directory..."
    mkdir -p dist
fi

# Copy frontend files
echo "Copying frontend files..."

# Copy main HTML file
if [ -f "index.html" ]; then
    cp index.html dist/
    echo "  ✓ index.html"
else
    echo "  ⚠️  index.html not found"
fi

# Copy style directory
if [ -d "style" ]; then
    cp -r style dist/
    echo "  ✓ style/"
else
    echo "  ⚠️  style/ not found"
fi

# Copy assets directory
if [ -d "assets" ]; then
    cp -r assets dist/
    echo "  ✓ assets/"
else
    echo "  ⚠️  assets/ not found"
fi

# Create a backend config file for frontend to use
echo "Creating API configuration..."
cat > dist/config.js << 'EOF'
// API Configuration for Wasmer Edge Frontend
// Backend runs separately (Docker, Cloud PHP, XAMPP)

const API_CONFIG = {
  // Update this base URL to point to your PHP backend
  BASE_URL: process.env.REACT_APP_API_URL || 'http://localhost/Projek%20MPS/mps-voting',
  
  // API endpoints
  endpoints: {
    voterLogin: '/php/voter_login.php',
    vote: '/php/vote.php',
    results: '/php/results.php',
    adminLogin: '/php/admin_login.php',
    adminDashboard: '/php/admin_dashboard.php',
    faceRecord: '/style/mps-voting2/record_face.php',
    faceCheck: '/style/mps-voting2/cek_wajah.php',
  },
  
  // Get full API URL
  getUrl: function(endpoint) {
    return this.BASE_URL + endpoint;
  }
};

// Export for use in JavaScript
if (typeof module !== 'undefined' && module.exports) {
  module.exports = API_CONFIG;
}
EOF
echo "  ✓ config.js"

# List final structure
echo ""
echo "📦 Build Output (dist/):"
find dist -type f | head -20

echo ""
echo "✅ Build complete! Ready for Wasmer deployment"
echo ""
echo "📝 Next steps:"
echo "1. Ensure PHP backend is accessible (Docker, Cloud, etc.)"
echo "2. Update API_CONFIG.BASE_URL if needed in dist/config.js"
echo "3. Deploy: wasmer deploy --name mps-voting"
