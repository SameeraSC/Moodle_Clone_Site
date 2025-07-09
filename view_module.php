<?php
session_start();
include_once 'dbconn.php';

if (isset($_GET['code']) && isset($_GET['name'])) {
    $_SESSION['module_code'] = $_GET['code'];
    $_SESSION['module_name'] = $_GET['name'];
}

if (!isset($_SESSION['module_code'])) {
    die("No module selected. Please go back to the dashboard.");
}

$module_code = $_SESSION['module_code'];
$module_name = $_SESSION['module_name'];


function txtfileprocces($filepath) {
    if (!file_exists($filepath)) {
        return "<p><em>File not found.</em></p>";
    }

    $raw = file_get_contents($filepath);
    $lines = explode("\n", trim($raw));
    $html = '';
    $inList = false;

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if (preg_match('/^[A-Z\s]+$/', $trimmed) || preg_match('/:$/', $trimmed)) {
            if ($inList) {
                $html .= "</ul>";
                $inList = false;
            }
            $html .= "<h5>" . htmlspecialchars($trimmed) . "</h5>";
        } elseif (preg_match('/^[-*•]\s+/', $trimmed)) {
            if (!$inList) {
                $html .= "<ul>";
                $inList = true;
            }
            $item = preg_replace('/^[-*•]\s+/', '', $trimmed);
            $html .= "<li>" . htmlspecialchars($item) . "</li>";
        } elseif (!empty($trimmed)) {
            if ($inList) {
                $html .= "</ul>";
                $inList = false;
            }
            $html .= "<p>" . htmlspecialchars($trimmed) . "</p>";
        } else {
            if ($inList) {
                $html .= "</ul>";
                $inList = false;
            }
        }
    }

    if ($inList) {
        $html .= "</ul>";
    }

    return "<div class='txt-content bg-light p-3 rounded mb-3'>" . $html . "</div>";
}

// Function to display lesson content
function displayLesson($conn, $module_code, $week_number) {
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE module_code = ? AND week = ? ORDER BY lesson_number ASC");
    if (!$stmt) {
        echo "SQL Error: " . $conn->error;
        return;
    }
    
    $stmt->bind_param("ss", $module_code, $week_number);
    $html = '';
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $filename = htmlspecialchars($row['title']);
                $lesson_no=($row['lesson_number']);
                $ftype = $row['file_type'];
                $filepath = htmlspecialchars($row['file_path']);

                echo "<div class='lesson-item mb-3'>";
               
                 if ($ftype == 'video') {
                      
                      echo "<p><strong>$lesson_no. $filename</strong></p>";
                      echo "<video width='300' height='170' controls preload='none'>";
                      echo "<source src='$filepath' type='video/mp4'>";
                      echo "Your browser does not support the video tag.";
                      echo "</video>";
                      
                } elseif ($ftype == 'Ylink') {
                        echo "<p><strong>$lesson_no $filename</strong></p>";
                        echo "<iframe width='300' height='170' src='$filepath' frameborder='0' allowfullscreen></iframe>";
              } 
              elseif ($ftype == 'pdf'||$ftype == 'Wlink') {
                    echo "<div class='card'> ";
                    echo "<div class='card-body'> ";
                if ($ftype=='pdf'){ 
                    echo "<h6 class='card-title'> PDF Note </h6>";
                    echo "<p class='card-text'>Lesson :$lesson_no $filename </p> ";
                    echo "<a href='$filepath' class='card-link' target='_blank'>📄 View PDF</a>";}
                else{
                    echo "<h6 class='card-title'> Web Link </h6>";
                    echo "<p class='card-text'>Lesson :$lesson_no $filename </p> ";
                    echo "<a href='$filepath' class='card-link' target='_blank'>📄 Open link </a>";}
                    
                    
                    echo "</div>";
                    echo "</div>";


              }elseif ($ftype== 'text'){

                  echo txtfileprocces($filepath);

              }elseif ($ftype== 'image'){

                  echo "<img src='$filepath' width='600' height='350' alt='image'/>";
              }else {
                  echo "<a href='$filepath' target='_blank'>📄 Download $filename</a>";
              }
                echo "</div>";
            }
        } else {
            echo "<p>No content available for Week $week_number.</p>";
        }
    } else {
        echo "Query execution error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo "$module_code - $module_name"; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
   
        html {
        scroll-padding-top: 65px; 
          }
   body {
      scroll-behavior: smooth;
    }

    .sidebar {
      position: fixed;
      top: 0;
      bottom: 0;
      width: 160px;
      background-color: #fff;
      padding-top: 60px;
      overflow-y: auto;
    }

    .header {
      position: fixed;
      top: 0;
      left: 160px;
      right: 0;
      height: 60px;
      background-color: #f8f9fa;
      padding: 10px 15px;
      z-index: 1080;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    } 

   
    .content {
      margin-left: 160px;
      padding: 20px;
      padding-top: 80px;
    }

    .section {
      padding: 20px 10px;
      border-bottom: 1px solid #ccc;
     
    }

    .lesson-item {
      margin-bottom: 20px;
    }

    .nav-link.active {
      font-weight: bold;
      color: #0d6efd;
    }

    @media (max-width: 991.98px) {
      .sidebar {
        display: none;
      }
      .content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<!-- Small screen nav -->
<nav class="navbar bg-light d-lg-none">
  <div class="container-fluid">
    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
      ☰ Menu
    </button>
  </div>
</nav>

<!-- Offcanvas for small screen -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Weeks</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="toggleAll(true)">Expand All</button>
      <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="toggleAll(false)">Collapse All</button>
         <ul class="nav flex-column">
          <?php for ($i = 1; $i <= 14; $i++): ?>
        <li class="nav-item"><a class="nav-link" href="#week<?php echo $i; ?>" data-bs-dismiss="offcanvas">Week <?php echo $i; ?></a></li>
      <?php endfor; ?>
    </ul>
  </div>
</div>

<!-- Sidebar for large screen -->
<div class="sidebar d-none d-lg-block p-2">
  <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="toggleAll(true)">Expand All</button>
  <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="toggleAll(false)">Collapse All</button>
  <ul class="nav flex-column">
    <?php for ($i = 1; $i <= 14; $i++): ?>
      <li class="nav-item"><a class="nav-link" href="#week<?php echo $i; ?>">Week <?php echo $i; ?></a></li>
    <?php endfor; ?>
  </ul>
</div>

<!-- Top header -->
<div class="header">
  <h5><?php echo $module_code . " - " . $module_name; ?></h5>
  <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
</div>

<!-- Main content -->

<div class="content">
<?php for ($week = 1; $week <= 14; $week++): ?>
<section id="week<?php echo $week; ?>" class="section">
  <h4>
        <button class="btn btn-link text-decoration-none week-toggle"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#collapseWeek<?= $week ?>"
        aria-expanded="false"
        aria-controls="collapseWeek<?= $week ?>">
        <i class="fa-solid fa-chevron-right"></i> Week <?= $week ?>
      </button>

  </h4>
  <div class="collapse" id="collapseWeek<?php echo $week; ?>">
    <?php displayLesson($conn, $module_code, $week); ?>
  </div>
</section>
  <?php endfor; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  function toggleAll(expand = true) {
    for (let i = 1; i <= 14; i++) {
      const collapse = document.getElementById('collapseWeek' + i);
      const bsCollapse = new bootstrap.Collapse(collapse, {
        toggle: false
      });
      expand ? bsCollapse.show() : bsCollapse.hide();
    }
  }


//Collapse + Expand Script

  document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.week-toggle');

  buttons.forEach(button => {
    const targetId = button.getAttribute('data-bs-target');
    const target = document.querySelector(targetId);
    const icon = button.querySelector('i.fa-solid'); // get the icon <i>

    // Set initial icon state
    if (target.classList.contains('show')) {
      icon.classList.remove('fa-chevron-right');
      icon.classList.add('fa-chevron-down');
    } else {
      icon.classList.remove('fa-chevron-down');
      icon.classList.add('fa-chevron-right');
    }

    target.addEventListener('shown.bs.collapse', () => {
      icon.classList.remove('fa-chevron-right');
      icon.classList.add('fa-chevron-down');
    });

    target.addEventListener('hidden.bs.collapse', () => {
      icon.classList.remove('fa-chevron-down');
      icon.classList.add('fa-chevron-right');
    });
  });

  document.getElementById('collapseAll').addEventListener('click', () => {
    buttons.forEach(button => {
      const targetId = button.getAttribute('data-bs-target');
      const target = document.querySelector(targetId);
      const icon = button.querySelector('i.fa-solid');
      const instance = bootstrap.Collapse.getOrCreateInstance(target);
      instance.hide();
      icon.classList.remove('fa-chevron-down');
      icon.classList.add('fa-chevron-right');
    });
  });

  document.getElementById('expandAll').addEventListener('click', () => {
    buttons.forEach(button => {
      const targetId = button.getAttribute('data-bs-target');
      const target = document.querySelector(targetId);
      const icon = button.querySelector('i.fa-solid');
      const instance = bootstrap.Collapse.getOrCreateInstance(target);
      instance.show();
      icon.classList.remove('fa-chevron-right');
      icon.classList.add('fa-chevron-down');
    });
  });
});

</script>
</body>
</html>


