<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Keep session_start() at the very top

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'header.php'; // header.php will now handle session_start() and database connection via config.php
include 'config.php'; // Retained for now as per original code, include_once in header.php handles redundancy.

$total = $conn->query("SELECT COUNT(*) AS c FROM study_materials")->fetch_assoc()['c'];
$latest = $conn->query("SELECT subject, file_name, uploaded_at FROM study_materials ORDER BY uploaded_at DESC LIMIT 1")->fetch_assoc();
?>

<div class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-all-smooth">
  <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
    
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-4 sm:p-6 rounded-xl shadow-lg mb-6 sm:mb-8 transition-all-smooth">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <span class="text-2xl sm:text-3xl">📊</span>
          </div>
          <div>
            <h2 id="easyStudyLogo" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white cursor-pointer hover:text-blue-100 transition-colors">
              Easy Study
            </h2>
            <p class="text-blue-100 text-sm sm:text-base opacity-90">Gujarat Board Study Platform</p>
          </div>
        </div>
        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2 sm:px-6 sm:py-3">
          <p class="text-blue-100 text-sm sm:text-base font-medium">
            Welcome back, <span class="font-bold text-white"><?= htmlspecialchars($display_username) ?></span>!
          </p>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl transition-all-smooth">
      
      <!-- Dashboard Header -->
      <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
              Dashboard Overview
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1 sm:mt-2">Your personalized study hub for academic success</p>
          </div>
          <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span id="currentTime"></span>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="p-4 sm:p-6 lg:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-10">
          
          <!-- Total Materials Card -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
              <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-bold"><?= htmlspecialchars($total) ?></p>
                <p class="text-blue-100 text-xs sm:text-sm font-medium">+12% from last month</p>
              </div>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold">Total Materials</h3>
            <p class="text-blue-100 text-sm opacity-90">Available study resources</p>
          </div>

          <!-- Latest Upload Card -->
          <div class="bg-gradient-to-br from-yellow-500 to-orange-500 dark:from-yellow-600 dark:to-orange-600 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
              <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-xs sm:text-sm text-yellow-100 font-medium">Recently Added</p>
              </div>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2">Latest Upload</h3>
            <div class="space-y-1">
              <p class="text-sm sm:text-base font-medium truncate"><?= htmlspecialchars($latest['subject'] ?? 'No uploads yet') ?></p>
              <p class="text-xs sm:text-sm text-yellow-100 truncate"><?= htmlspecialchars($latest['file_name'] ?? 'N/A') ?></p>
              <p class="text-xs text-yellow-200"><?= htmlspecialchars($latest['uploaded_at'] ?? 'N/A') ?></p>
            </div>
          </div>

          <!-- Progress Card -->
          <div class="bg-gradient-to-br from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-3">
              <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-bold">75%</p>
                <p class="text-green-100 text-xs sm:text-sm font-medium">+5% this week</p>
              </div>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2">Study Progress</h3>
            <div class="w-full bg-white bg-opacity-20 rounded-full h-2 mb-2">
              <div class="bg-white h-2 rounded-full" style="width: 75%"></div>
            </div>
            <p class="text-green-100 text-sm opacity-90">Overall completion rate</p>
          </div>
        </div>

        <!-- Quick Access Section -->
        <div class="mb-8 sm:mb-10">
          <div class="flex items-center mb-4 sm:mb-6">
            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-3">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
              </svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Quick Access</h2>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <a href="study_materials.php" class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <div class="flex flex-col items-center text-center space-y-3">
                <div class="p-3 bg-white bg-opacity-20 rounded-full group-hover:bg-opacity-30 transition-all">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold">Study Materials</h3>
                  <p class="text-blue-100 text-sm opacity-90">Access textbooks & resources</p>
                </div>
              </div>
            </a>

            <a href="practice_zone.php" class="group bg-gradient-to-br from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <div class="flex flex-col items-center text-center space-y-3">
                <div class="p-3 bg-white bg-opacity-20 rounded-full group-hover:bg-opacity-30 transition-all">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold">Practice Zone</h3>
                  <p class="text-yellow-100 text-sm opacity-90">Tests & assignments</p>
                </div>
              </div>
            </a>

            <a href="downloads.php" class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <div class="flex flex-col items-center text-center space-y-3">
                <div class="p-3 bg-white bg-opacity-20 rounded-full group-hover:bg-opacity-30 transition-all">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold">Downloads</h3>
                  <p class="text-purple-100 text-sm opacity-90">Downloaded files</p>
                </div>
              </div>
            </a>

            <a href="notes.php" class="group bg-gradient-to-br from-teal-500 to-cyan-600 hover:from-teal-600 hover:to-cyan-700 p-4 sm:p-6 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <div class="flex flex-col items-center text-center space-y-3">
                <div class="p-3 bg-white bg-opacity-20 rounded-full group-hover:bg-opacity-30 transition-all">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold">My Notes</h3>
                  <p class="text-teal-100 text-sm opacity-90">Personal study notes</p>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="mb-8 sm:mb-10">
          <div class="flex items-center justify-between mb-4 sm:mb-6">
            <div class="flex items-center">
              <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg mr-3">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Recent Activity</h2>
            </div>
            <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium transition-colors">
              View All
            </button>
          </div>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 sm:p-6">
            <div class="space-y-4">
              <div class="flex items-center space-x-4 p-3 bg-white dark:bg-gray-600 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">Downloaded Mathematics Chapter 5</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4 p-3 bg-white dark:bg-gray-600 rounded-lg">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">Completed Physics Practice Test</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Score: 85% • Yesterday</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4 p-3 bg-white dark:bg-gray-600 rounded-lg">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">Added new study notes for Chemistry</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">2 days ago</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Study Banner -->
        <div class="mt-8 sm:mt-10">
          <div class="relative overflow-hidden rounded-xl shadow-lg">
            <img src="banner-study.jpg" alt="Study Banner" class="w-full h-48 sm:h-64 lg:h-80 object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-75"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center text-white p-4">
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">Ready to Excel?</h3>
                <p class="text-lg sm:text-xl opacity-90 mb-4">Your success journey starts here</p>
                <button class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                  Start Studying Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Admin panel access functionality
  let clickCount = 0;
  const easyStudyLogo = document.getElementById('easyStudyLogo');

  if (easyStudyLogo) {
    easyStudyLogo.addEventListener('click', () => {
      clickCount++;
      if (clickCount >= 5) {
        const password = prompt("Enter admin password:");
        if (password === "AARY@n") {
          window.location.href = "admin_panel.php";
        } else {
          alert("Incorrect password!");
        }
        clickCount = 0; // reset counter after attempt
      }
    });
  }

  // Current time display
  function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleString('en-US', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
      timeElement.textContent = timeString;
    }
  }

  // Update time immediately and then every minute
  updateTime();
  setInterval(updateTime, 60000);

  // Add scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Apply scroll animations to cards
  document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.grid > div, .bg-gradient-to-br');
    animatedElements.forEach((el, index) => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
      observer.observe(el);
    });
  });
</script>

<?php include 'footer.php'; ?>