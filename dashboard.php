
<?php session_start();
require_once 'dbconn.php';

function renderModules($conn, $year, $semester) {
  $stmt = $conn->prepare("SELECT module_code, module_name, image_path FROM modules WHERE year = ? AND semester = ?");
  $stmt->bind_param("ii", $year, $semester);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    echo '<div class="card module-card text-center">';
    echo '<img src="uploads/' . htmlspecialchars($row['image_path'] ?: 'default.jpg') . '" class="card-img-top module-img" alt="Module Image">';
    echo '<div class="card-body text-center">';
    echo '<a href="view_module.php?code=' . urlencode($row['module_code']) . '&name=' . urlencode($row['module_name']) . '" class="stretched-link text-decoration-none">';
    echo htmlspecialchars($row['module_code']) . ' - ' . htmlspecialchars($row['module_name']);
    echo '</a></div></div>';
  }

  $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Horizontal Levels + Vertical Semesters</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      overflow-y: hidden;
      
    }
    

    /* Container holds all levels horizontally */
    .levels-wrapper {
      display: flex;
      transition: transform 0.5s ease-in-out;
      width: 300vw; /* 100vw x 3 levels */
    }

    .level-section {
      width: 100vw;
      height: 100vh;
      flex-shrink: 0;
      overflow-y: auto;
      padding: 5rem;
    }

    .semester-block {
      
      margin-bottom: 3rem;
    }

    .semester-title {
      font-weight: bold;
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 columns */
        gap: 1.5rem; /* More space between cards */
      }
    .module-card {
      height: 220px; 
      width: 280px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem;
      transition: transform 0.2s ease;
    }
      .module-card:hover {
      transform: scale(1.03);
    
    
    }.module-img {
     
      width: 280px;
      object-fit: contain;
    }
     
    
    .object-fit: cover;{
      height: 200px;
      width: 50%;
      
    
    }


    .arrow-btn {
      background: blue;
      border: none;
      font-size: 1.8rem;
      color: #555;
      cursor: pointer;
    }

    .top-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 70px;
      background-color: #f8f9fa;
      padding: 10px 15px;
      z-index: 1080;
      display: flex;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      align-items: center;
      padding: 1rem 2rem 0 2rem;
    }
    .semester-title{
    color : darkblue;
     border: 2px;  

    }
    
  .arrow-btn {
  background: none;
  border: none;
  font-size: 2rem;
  color: #333;
 
  cursor: pointer;
}
.arrow-btn:disabled {
  color: #ccc;
  cursor: not-allowed;
}

  </style>
</head>
<body>


    
<!-- TOP LEVEL SCROLL CONTROL -->

<div class="top-bar">
  <div> <h5>Dashboard</h5></div>

<button class="arrow-btn" onclick="scrollLevel(-1)"> <i class="fa-solid fa-chevron-left" ></i></button>
  <h4 id="levelLabel">Level 1</h4>
  <button class="arrow-btn" onclick="scrollLevel(1)"><i class="fa-solid fa-chevron-right"></i></button>

  <div><a href="add_module.php" class="btn btn-success me-2">Add Module</a>
      <a href="lessonlistview.php" class="btn btn-primary">Add Lesson</a></div>
</div>

<!-- LEVELS HORIZONTAL WRAPPER -->
<div id="levelsWrapper" class="levels-wrapper">

 
  <!-- LEVEL 1 -->
<div class="level-section bg-light">
  <div class="semester-block">
    <div class="semester-title">Semester 1</div>
    <div class="module-grid">
      <?php renderModules($conn, 1, 1); ?>
    </div>
  </div>

  <div class="semester-block">
    <div class="semester-title">Semester 2</div>
    <div class="module-grid">
      <?php renderModules($conn, 1, 2); ?>
    </div>
  </div>
</div> 

  <!-- LEVEL 2 -->
 <div class="level-section bg-light">
  <div class="semester-block">
    <div class="semester-title">Semester 1</div>
    <div class="module-grid">
      <?php renderModules($conn, 2, 1); ?>
    </div>
  </div>

  <div class="semester-block">
    <div class="semester-title">Semester 2</div>
    <div class="module-grid">
      <?php renderModules($conn, 2, 2); ?>
    </div>
  </div>
</div> 

  <!-- LEVEL 3 -->
  <div class="level-section bg-light">
  <div class="semester-block">
    <div class="semester-title">Semester 1</div>
    <div class="module-grid">
      <?php renderModules($conn, 3, 1); ?>
    </div>
  </div>

  <div class="semester-block">
    <div class="semester-title">Semester 2</div>
    <div class="module-grid">
      <?php renderModules($conn, 3, 2); ?>
    </div>
  </div>
</div> 
<script>
  let currentLevel = 0;
  const totalLevels = document.querySelectorAll('.level-section').length;
  const levelNames = ['Level 1', 'Level 2', 'Level 3'];

  function scrollLevel(direction) {
    currentLevel += direction;
    if (currentLevel < 0) currentLevel = 0;
    if (currentLevel >= totalLevels) currentLevel = totalLevels - 1;

    document.getElementById('levelsWrapper').style.transform = `translateX(-${currentLevel * 100}vw)`;
    document.getElementById('levelLabel').innerText = levelNames[currentLevel];
  }
</script>

</body>
</html>
