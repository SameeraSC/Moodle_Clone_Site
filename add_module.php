<?php
require_once 'dbconn.php';

if (isset($_POST['submit'])) {
    $module_code = trim($_POST['module_code']);
    $module_name = trim($_POST['module_name']);
    $year=$_POST['year'];
    $semester=$_POST['semester'];
    if (empty($module_code) || empty($module_name)) {
        $message = "All fields are required.";
    } else {
        // Handle file upload
        $image_path = null;
        if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $original_name = basename($_FILES['image_path']['name']);
            $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_file)) {
                $image_path = $new_filename;
            } else {
                $message = "File upload failed.";
            }
        }

        if (!isset($message)) {
            // Insert into modules table (adjust SQL if image_path column exists)
            $stmt = $conn->prepare("INSERT INTO modules (module_code, module_name,year,sem, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $module_code, $module_name,$year,$semester, $image_path);

            if ($stmt->execute()) {
                $message = "Module added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
            $conn->close();
        }
    }
}
?>
 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Module</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
   
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Add Module</h3>
    <div>
      
      <a href="dashboard.php" class="btn btn-danger">Dashboard</a>
    </div>
  </div>
  
  
  
  <?php if (isset($message)): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form action="add_module.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="module_code" class="form-label">Module Code</label>
      <input type="text" class="form-control" id="module_code" name="module_code" placeholder="e.g.ITE2217">
    </div>

    <div class="mb-3">
      <label for="module_name" class="form-label">Module Name</label>
      <input type="text" class="form-control" id="module_name" name="module_name" placeholder="Module Name">
    </div>
    
    <div class="mb-3">
      <label for="year" class="form-label">Year</label>
      <select class="form-control" id="year" name="year" >
         <option>Select Level</option>
      <option value="1">Level 1</option>
        <option value="2">Level 2 </option>
        <option value="3">Level 3</option></select>
    </div>

<div class="mb-3">
      <label for="semester" class="form-label">Semester</label>
      <select class="form-control" id="semester" name="semester" >
      <option >Select Semester</option>  
      <option value="1">Semester 1</option>
        <option value="2">Semester 2</option></select>
        
    </div>
    <div class="mb-3">
      <label for="image_path" class="form-label">Choose File</label>
      <input type="file" class="form-control" id="image_path" name="image_path">
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Add Module</button>
  </form>
</div>
</body>
</html>
