<?php
require_once 'dbconn.php';

$modules = $conn->query("SELECT module_code, module_name FROM modules");

$message = '';
$alertType = 'danger';


function convertYouTubeLinkToEmbed($url) {
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    return $url;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $filename = trim($_POST['filename']);
    $week = $_POST['week'];
    $ftype = $_POST['ftype'];
    $lesson = trim($_POST['lesson']);
    $module_code = $_POST['module_code'] ?? '';

    if (empty($module_code)) {
        $message = "Please select a module.";

    } elseif (empty($filename) || empty($week) || empty($lesson)) {
        if ($ftype !== 'Ylink' && $ftype !== 'Wlink') {
            $message = "Please fill in all required fields.";
        }

    } elseif ($ftype !== 'Ylink' && $ftype !== 'Wlink' && (!isset($_FILES['fileupload']) || $_FILES['fileupload']['error'] !== 0)) {
        $message = "No file selected or upload error.";

    } else {
        if ($ftype === 'Ylink' || $ftype === 'Wlink') {
            $weblink = trim($_POST['weblink'] ?? '');

            if (empty($weblink)) {
                $message = "Please enter a link.";
            } else { if ($ftype === 'Ylink') {
                    $weblink = convertYouTubeLinkToEmbed($weblink);
                }
                $stmt = $conn->prepare("INSERT INTO lessons (title, week, file_type, file_path, lesson_number, module_code) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $filename, $week, $ftype, $weblink, $lesson, $module_code);

                if ($stmt->execute()) {
                    $message = "Lesson link saved successfully!";
                    $alertType = "success";
                } else {
                    $message = "Database error: " . $stmt->error;
                }

                $stmt->close();
            }

        } else {
            $original_name = basename($_FILES['fileupload']['name']);
            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            $allowed_extensions = [
                'pdf' => ['pdf'],
                'video' => ['mp4', 'avi', 'mov'],
                'ppt' => ['ppt', 'pptx'],
                'image' => ['jpg', 'jpeg', 'png', 'gif'],
                'text' => ['txt'],
                'folder'=>['zip','rar'],
                
            ];

            if (!in_array($file_ext, $allowed_extensions[$ftype])) {
                $message = "Invalid file type for selected file format.";
            } else {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_filename = uniqid() . '.' . $file_ext;
                $target_file = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['fileupload']['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO lessons (title, week, file_type, file_path, lesson_number, module_code) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $filename, $week, $ftype, $target_file, $lesson, $module_code);

                    if ($stmt->execute()) {
                        $message = "Lesson uploaded successfully!";
                        $alertType = "success";
                    } else {
                        $message = "Database error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $message = "File upload failed.";
                }
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lesson Upload</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<body >

<br>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h6>Upload Lesson </h6>
    
  </div>

  <?php if (!empty($message)): ?>
    <div id="uploadMessage" class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">

      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <form id="lessonForm" class="form-horizontal" method="post" enctype="multipart/form-data">
    <div class="content">
      <label for="ftype" class="form-label">File Type</label>
      <select class="form-select" onchange="toggleInputs()" name="ftype" id="ftype">
        <option value="pdf">PDF</option>
        <option value="video">Video</option>
        <option value="ppt">Presentation</option>
        <option value="image">Image</option>
        <option value="Ylink">YouTube Link</option>
        <option value="Wlink">Web Link</option>
        <option value="text">Text</option>
        <option value="folder">Folder</option>
        <option> value="plain txt"Plain Text</option>
      </select>
    </div>

    <div class="content">
      <label for="module" class="form-label">Select Module</label>
          <select class="form-select" name="module_code" id="module">
            <option>Select Module</option>
            <?php while ($row = $modules->fetch_assoc()): ?>
              <option value="<?= $row['module_code'] ?>"><?= $row['module_code'] ?> - <?= $row['module_name'] ?></option>
            <?php endwhile; ?>
          </select>
    </div>

    <div class="content">
        <label for="filename" class="form-label">File Name</label>
        <input type="text" class="form-control" id="filename" name="filename" placeholder="Enter file name">
    </div>

    <div class="content">
      <label for="week" class="form-label">Select Week</label>
      <select class="form-select" name="week" id="week">
        <?php for ($i = 1; $i <= 14; $i++): ?>
          <option value="<?= $i ?>">Week <?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div id="weblinkdiv" class="content">
      <label for="weblink" class="form-label">Link</label>
      <input type="text" class="form-control" id="weblink" name="weblink" placeholder="Paste the Link">
    </div>

    <div id="fileuploaddiv" class="content">
      <label for="fileupload" class="form-label">Choose File</label>
      <input type="file" class="form-control" id="fileupload" name="fileupload">
    </div>

    <div class="content">
      <label for="lesson" class="form-label">Lesson Number</label>
      <input type="text" class="form-control" id="lesson" name="lesson" placeholder="Enter lesson number">
    </div>

    <br>
    <div>
      <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    
    
   <button type="button" class="btn btn-secondary ms-2" id="clearUploadFormBtn">
  Add Another Lesson
</button>
 </div>

</form>
</div>




<script>
function toggleInputs() {
  const ftype = document.getElementById('ftype').value;
  document.getElementById('fileuploaddiv').style.display = (ftype === 'Ylink' || ftype === 'Wlink') ? 'none' : 'block';
  document.getElementById('weblinkdiv').style.display = (ftype === 'Ylink' || ftype === 'Wlink') ? 'block' : 'none';
}
window.onload = toggleInputs;

document.getElementById('clearUploadFormBtn').addEventListener('click', function () {
  // 1. Clear the form
  document.getElementById('lessonForm').reset();

  // 2. Toggle inputs visibility after reset
  toggleInputs();

  const msgDiv = document.getElementById('uploadMessage');
  if (msgDiv) msgDiv.remove()

  setTimeout(() => {
  const msgDiv = document.getElementById('uploadMessage');
  if (msgDiv) msgDiv.remove();
}, 5000);

  // 3. Refresh lesson list if a module is selected
  const moduleCode = document.getElementById('module').value;
  if (moduleCode) {
    fetch("", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "selected=" + encodeURIComponent(moduleCode)
    })
    .then(res => res.text())
    .then(html => {
      document.getElementById('output').innerHTML = html;
    });
  }
});
</script>















