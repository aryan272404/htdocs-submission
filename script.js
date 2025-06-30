// Chart.js - Study Analytics
// This script now only contains the Chart.js initialization.
// Dark Mode toggle and section switching logic moved to index.php for better integration.
document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('studyChart');
  if (ctx) { // Ensure the canvas element exists before initializing the chart
      new Chart(ctx.getContext('2d'), {
          type: 'bar',
          data: {
              labels: ['Math', 'Science', 'English', 'History', 'Coding'],
              datasets: [{
                  label: 'Hours Studied',
                  data: [5, 3, 4, 2, 6], // Enhancement: Replace with dynamic data from backend
                  backgroundColor: [
                      '#4F46E5', '#22C55E', '#EC4899', '#F59E0B', '#06B6D4'
                  ],
                  borderRadius: 8
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: { display: false } // Hide legend for cleaner look
              },
              scales: {
                  y: {
                      beginAtZero: true,
                      grid: {
                          color: 'rgba(200, 200, 200, 0.2)' // Lighter grid lines
                      },
                      ticks: {
                          color: '#666' // Axis label color
                      }
                  },
                  x: {
                      grid: {
                          display: false // No vertical grid lines
                      },
                      ticks: {
                          color: '#666' // Axis label color
                      }
                  }
              }
          }
      });

      // Dark mode adjustments for chart (conceptual, might need more specific CSS overrides or JS logic)
      const updateChartColors = () => {
          const isDarkMode = document.body.classList.contains('dark-mode');
          const textColor = isDarkMode ? '#f1f5f9' : '#333';
          const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(200, 200, 200, 0.2)';

          if (Chart.instances && Object.keys(Chart.instances).length > 0) {
              const chartInstance = Object.values(Chart.instances)[0]; // Get the first chart instance
              if (chartInstance) {
                  chartInstance.options.scales.y.grid.color = gridColor;
                  chartInstance.options.scales.y.ticks.color = textColor;
                  chartInstance.options.scales.x.ticks.color = textColor;
                  chartInstance.update();
              }
          }
      };

      // Initial chart color update
      updateChartColors();

      // Listen for dark mode changes to update chart colors
      const darkModeToggle = document.getElementById('darkModeToggle');
      if (darkModeToggle) {
          darkModeToggle.addEventListener('change', updateChartColors);
      }
  }
});
