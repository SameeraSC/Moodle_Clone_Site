<?php 
require_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected'])) {
    $module_code = $_POST['selected'];

    $stmt = $conn->prepare("SELECT week, lesson_number, title, file_type FROM lessons WHERE module_code = ? ORDER BY week ASC, lesson_number ASC");
    $stmt->bind_param("s", $module_code);
    $stmt->execute();
    $result = $stmt->get_result();

   $currentWeek = null;

echo "<ul class='list-group'>";

while ($row = $result->fetch_assoc()) {
    // Group by week
    if ($currentWeek !== $row['week']) {
        $currentWeek = $row['week'];
        
        echo "<li class='list-group-item active'><strong>Week {$currentWeek}</strong></li>";
    } 

    $lessonNumber = htmlspecialchars($row['lesson_number']);
    $title = htmlspecialchars($row['title']);
    $type = htmlspecialchars($row['file_type']);

    echo "<li class='list-group-item'>Lesson {$lessonNumber}: {$title} <span class='text-muted'>({$type})</span></li>";
}
echo "</ul>";
 exit;
}

$modules = $conn->query("SELECT module_code, module_name FROM modules");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lesson List View</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
html, body {
    height: 100%;
    margin: 0;
}
.parent {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-template-rows: repeat(5, 1fr);
    gap: 8px;
    height: 100vh; 
    width: 100%;
}
    
.div1 {
    grid-column: span 2 / span 2;
    grid-row: span 5 / span 5;
}

.div2 {
    grid-column: span 3/ span 3;
    grid-row: span 5 / span 5;
    grid-column-start: 3;

    overflow-y: auto; /* enable vertical scroll */
    padding: 1rem;
    border: 1px solid #dee2e6;  
}
   .dashbtn  {
   align :left;

   } 

</style>

</head>
<body>


<div class="parent">
         
    <div class="div1">

    <?php include 'lesson_upload.php';?>
    
  </div>
    
    


    
           
          
     <div class="div2">
            
            <div class="container">
             <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 id="selected-module" class="mb-3 text-primary"></h6>
                <a href="dashboard.php" class="btn btn-danger">Dashboard</a>
              </div> 
                        
               <div class="mb-3">
                  
                    <?php while ($row = $modules->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['module_code']) ?>">
                        <?= htmlspecialchars($row['module_code']) ?> - <?= htmlspecialchars($row['module_name']) ?>
                        </option>
                    <?php endwhile; ?>
                   </select>
                </div>
            </div>

           
        <div id="output" class="mt-3 text-primary"></div>


    </div>


    




<script>
document.getElementById("module").addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedValue = selectedOption.value;
  var selectedText = selectedOption.text;

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("output").innerHTML = xhr.responseText;

      // Display module name
      document.getElementById("selected-module").innerText = "Module: " + selectedText;
    }
  };

  xhr.send("selected=" + encodeURIComponent(selectedValue));
});

</script>

</body>
</html>