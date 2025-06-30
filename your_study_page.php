<?php
session_start();
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Study Material</title>

  <style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  padding: 20px;
  background: #f9f9f9;
}

.header h1 {
  font-size: 18px;
  margin-bottom: 8px;
}

.header h2 {
  font-size: 16px;
  margin-bottom: 16px;
  border-bottom: 1px solid #aaa;
  padding-bottom: 6px;
}

.sets-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.set-box {
  border: 2px solid #23538b;
  padding: 10px;
  width: 200px;
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.set-label {
  font-size: 14px;
  margin-bottom: 12px;
  border-bottom: 1px solid #23538b;
  padding-bottom: 4px;
  font-weight: bold;
  color: #23538b;
}

.items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item {
  position: relative;
  width: 80px;
  height: 100px;
}

.scroll-icon {
  width: 100%;
  height: auto;
  display: block;
}

.item-text {
  position: absolute;
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 14px;
  color: #1a2e44;
}
  </style>
</head><body>
<div class="header">
  <h1>WELCOME, <?php echo htmlspecialchars($username); ?></h1>
  <h2>STUDY MATERIAL</h2>
</div>

<div class="sets-container">
  <!-- Set 10 -->
  <div class="set-box">
    <div class="set-label">SET : 10</div>
    <div class="items">
      <div class="item">
        <img src="scroll-blue.png" alt="scroll" class="scroll-icon">
        <span class="item-text">MATHS</span>
      </div>
      <div class="item">
        <img src="scroll-blue.png" alt="scroll" class="scroll-icon">
      </div>
      <div class="item">
        <img src="scroll-blue.png" alt="scroll" class="scroll-icon">
      </div>
    </div>
  </div>

  <!-- Set 11 -->
  <div class="set-box">
    <div class="set-label">SET : 11 – SCI, COM, ARTS</div>
    <div class="items">
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
    </div>
  </div>

  <!-- Set 12 -->
  <div class="set-box">
    <div class="set-label">SET : 12 – SCI, COM, ARTS</div>
    <div class="items">
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
      <div class="item"><img src="scroll-blue.png" alt="scroll" class="scroll-icon"></div>
    </div>
  </div>
</div>

</body></html>
