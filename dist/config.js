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
if (typeof module == 'undefined' && module.exports) {
  module.exports = API_CONFIG;
}
