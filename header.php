<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'config.php';

$display_username = 'Guest';

if (isset($_SESSION['user_id']) && isset($conn) && $conn instanceof mysqli) {
    $user_id = $_SESSION['user_id'];

    // Since 'userid' is your actual column for user identification
    $stmt = $conn->prepare("SELECT userid FROM users WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id); // assuming 'id' is integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $display_username = htmlspecialchars($row['userid']);
        }

        $stmt->close();
    } else {
        error_log("Failed to prepare user query in header.php: " . $conn->error);
        $display_username = 'Error';
    }
}

$username = $_SESSION['username'] ?? $display_username;
?>
<!DOCTYPE html>
<html lang="en" id="htmlRoot" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Easy Study</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            'inter': ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    
    /* Custom scrollbar for sidebar */
    .sidebar-scroll::-webkit-scrollbar {
      width: 4px;
    }
    .sidebar-scroll::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 2px;
    }
    
    /* Smooth transitions */
    .transition-all-smooth {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Mobile menu animation */
    .mobile-menu-enter {
      transform: translateX(-100%);
      opacity: 0;
    }
    .mobile-menu-enter-active {
      transform: translateX(0);
      opacity: 1;
      transition: all 0.3s ease-out;
    }
    
    /* Dark mode toggle styles */
    .toggle-checkbox:checked + .toggle-label .toggle-ball {
      transform: translateX(1.5rem);
      background-color: #10b981;
    }
    .toggle-label {
      background-color: #d1d5db;
    }
    .dark .toggle-label {
      background-color: #374151;
    }
    .toggle-checkbox:checked + .toggle-label {
      background-color: #065f46;
    }
  </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-full transition-all-smooth">
  <!-- Mobile menu overlay -->
  <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
  
  <!-- Mobile Header -->
  <div class="lg:hidden bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
    <div class="flex items-center justify-between px-4 py-3">
      <div class="flex items-center space-x-3">
        <button id="mobileMenuBtn" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <h1 class="text-xl font-bold text-blue-600 dark:text-blue-400">Easy Study</h1>
      </div>
      
      <div class="flex items-center space-x-3">
        <!-- Mobile Dark Mode Toggle -->
        <div class="flex items-center">
          <input type="checkbox" id="mobileDarkToggle" class="toggle-checkbox sr-only">
          <label for="mobileDarkToggle" class="toggle-label relative w-12 h-6 rounded-full cursor-pointer transition-all-smooth">
            <div class="toggle-ball absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all-smooth"></div>
          </label>
        </div>
        
        <!-- Mobile User Avatar -->
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
            <?= strtoupper(substr($display_username, 0, 1)) ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile Sidebar -->
  <aside id="mobileSidebar" class="fixed inset-y-0 left-0 w-64 bg-gray-900 dark:bg-gray-800 text-white z-50 transform -translate-x-full transition-transform duration-300 ease-out lg:hidden">
    <div class="flex flex-col h-full">
      <!-- Mobile Sidebar Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-700 dark:border-gray-600">
        <h2 class="text-xl font-bold text-white">Easy Study</h2>
        <button id="closeMobileMenu" class="p-2 rounded-md text-gray-300 hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <!-- Mobile User Info -->
      <div class="p-4 border-b border-gray-700 dark:border-gray-600">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
            <?= strtoupper(substr($display_username, 0, 1)) ?>
          </div>
          <div>
            <p class="text-sm font-medium text-white">Welcome back!</p>
            <p class="text-xs text-blue-300 truncate max-w-32"><?= $display_username ?></p>
          </div>
        </div>
      </div>
      
      <!-- Mobile Navigation -->
      <nav class="flex-1 p-4 sidebar-scroll overflow-y-auto">
        <ul class="space-y-2">
          <li>
            <a href="dashboard.php" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
              </svg>
              <span class="font-medium">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="study_materials.php" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span class="font-medium">Study Materials</span>
            </a>
          </li>
          <li>
            <a href="practice_zone.php" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-medium">Practice Zone</span>
            </a>
          </li>
          <li>
            <a href="downloads.php" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-medium">Downloads</span>
            </a>
          </li>
          <li>
            <a href="notes.php" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <span class="font-medium">Notes</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Mobile Logout -->
      <div class="p-4 border-t border-gray-700 dark:border-gray-600">
        <a href="login.html" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-red-300 hover:bg-red-600 hover:text-white transition-colors group">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span class="font-medium">Logout</span>
        </a>
      </div>
    </div>
  </aside>

  <!-- Desktop Sidebar -->
  <aside class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:block bg-gray-900 dark:bg-gray-800 text-white shadow-xl z-30">
    <div class="flex flex-col h-full">
      <!-- Desktop Sidebar Header -->
      <div class="flex items-center px-6 py-6 border-b border-gray-700 dark:border-gray-600">
        <h2 class="text-2xl font-bold text-white">Easy Study</h2>
      </div>
      
      <!-- Desktop User Info -->
      <div class="px-6 py-4 border-b border-gray-700 dark:border-gray-600">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-semibold">
            <?= strtoupper(substr($display_username, 0, 1)) ?>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-white">Welcome back!</p>
            <p class="text-sm text-blue-300 truncate"><?= $display_username ?></p>
          </div>
        </div>
      </div>
      
      <!-- Desktop Navigation -->
      <nav class="flex-1 px-4 py-6 sidebar-scroll overflow-y-auto">
        <ul class="space-y-2">
          <li>
            <a href="dashboard.php" class="flex items-center space-x-3 px-3 py-3 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
              </svg>
              <span class="font-semibold">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="study_materials.php" class="flex items-center space-x-3 px-3 py-3 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span class="font-semibold">Study Materials</span>
            </a>
          </li>
          <li>
            <a href="practice_zone.php" class="flex items-center space-x-3 px-3 py-3 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-semibold">Practice Zone</span>
            </a>
          </li>
          <li>
            <a href="downloads.php" class="flex items-center space-x-3 px-3 py-3 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-semibold">Downloads</span>
            </a>
          </li>
          <li>
            <a href="notes.php" class="flex items-center space-x-3 px-3 py-3 rounded-lg text-white hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors group">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <span class="font-semibold">Notes</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Desktop Dark Mode Toggle & Logout -->
      <div class="px-6 py-4 space-y-4 border-t border-gray-700 dark:border-gray-600">
        <!-- Dark Mode Toggle -->
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-300">Dark Mode</span>
          <div class="flex items-center">
            <input type="checkbox" id="darkToggle" class="toggle-checkbox sr-only">
            <label for="darkToggle" class="toggle-label relative w-12 h-6 rounded-full cursor-pointer transition-all-smooth">
              <div class="toggle-ball absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all-smooth"></div>
            </label>
          </div>
        </div>
        
        <!-- Logout -->
        <a href="login.html" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-red-300 hover:bg-red-600 hover:text-white transition-colors group w-full">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span class="font-semibold">Logout</span>
        </a>
      </div>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="lg:ml-64 min-h-screen">
    <!-- Mobile menu and dark mode toggle script -->
    <script>
      // Mobile menu functionality
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
      const closeMobileMenu = document.getElementById('closeMobileMenu');

      function openMobileMenu() {
        mobileSidebar.classList.remove('-translate-x-full');
        mobileMenuOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      }

      function closeMobileMenuFunc() {
        mobileSidebar.classList.add('-translate-x-full');
        mobileMenuOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }

      if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileMenu);
      if (closeMobileMenu) closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
      if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenuFunc);

      // Dark mode functionality
      const darkToggle = document.getElementById('darkToggle');
      const mobileDarkToggle = document.getElementById('mobileDarkToggle');
      const html = document.getElementById('htmlRoot');

      function applyTheme(isDark) {
        if (isDark) {
          html.classList.add('dark');
          if (darkToggle) darkToggle.checked = true;
          if (mobileDarkToggle) mobileDarkToggle.checked = true;
        } else {
          html.classList.remove('dark');
          if (darkToggle) darkToggle.checked = false;
          if (mobileDarkToggle) mobileDarkToggle.checked = false;
        }
      }

      function toggleTheme() {
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        
        // Sync both toggles
        if (darkToggle) darkToggle.checked = isDark;
        if (mobileDarkToggle) mobileDarkToggle.checked = isDark;
      }

      // Initialize theme on load
      const savedTheme = localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      applyTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));

      // Add event listeners
      if (darkToggle) darkToggle.addEventListener('change', toggleTheme);
      if (mobileDarkToggle) mobileDarkToggle.addEventListener('change', toggleTheme);

      // Handle system theme changes
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
          applyTheme(e.matches);
        }
      });

      // Close mobile menu on window resize
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
          closeMobileMenuFunc();
        }
      });
    </script>