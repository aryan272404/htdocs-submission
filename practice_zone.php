<?php include 'header.php'; ?>

<div style="padding: 30px; background: #f7f9fb;">
  <h1 style="color: #23538b; margin-bottom: 10px;">📝 Practice Zone</h1>
  <p style="color: #555;">Sharpen your skills by practicing questions from different subjects</p>

  <!-- Practice Cards -->
  <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px;">
    
    <div style="flex: 1; min-width: 240px; padding: 20px; background: #337ab7; color: white; border-radius: 10px;">
      <h3>📐 Mathematics</h3>
      <p>Algebra, Geometry, Trigonometry</p>
      <a href="quiz.php?subject=math" style="display: inline-block; margin-top: 10px; background: white; color: #337ab7; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Start Quiz</a>
    </div>

    <div style="flex: 1; min-width: 240px; padding: 20px; background: #5cb85c; color: white; border-radius: 10px;">
      <h3>🔬 Science</h3>
      <p>Physics, Chemistry, Biology</p>
      <a href="quiz.php?subject=science" style="display: inline-block; margin-top: 10px; background: white; color: #5cb85c; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Start Quiz</a>
    </div>

    <div style="flex: 1; min-width: 240px; padding: 20px; background: #f0ad4e; color: white; border-radius: 10px;">
      <h3>🌍 Social Science</h3>
      <p>History, Civics, Geography</p>
      <a href="quiz.php?subject=social" style="display: inline-block; margin-top: 10px; background: white; color: #f0ad4e; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Start Quiz</a>
    </div>

    <div style="flex: 1; min-width: 240px; padding: 20px; background: #6f42c1; color: white; border-radius: 10px;">
      <h3>📘 English</h3>
      <p>Grammar & Comprehension</p>
      <a href="quiz.php?subject=english" style="display: inline-block; margin-top: 10px; background: white; color: #6f42c1; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Start Quiz</a>
    </div>

    <div style="flex: 1; min-width: 240px; padding: 20px; background: #d9534f; color: white; border-radius: 10px;">
      <h3>💡 General Knowledge</h3>
      <p>Current affairs & logical reasoning</p>
      <a href="quiz.php?subject=gk" style="display: inline-block; margin-top: 10px; background: white; color: #d9534f; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Start Quiz</a>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
