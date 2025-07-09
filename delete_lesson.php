<?php
require_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id !== null && is_numeric($id)) {
       
        $stmt = $conn->prepare("SELECT file_path, file_type FROM lessons WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($file_path, $file_type);
        $stmt->fetch();
        $stmt->close();

        
        if ($file_path && $file_type !== 'Ylink' && $file_type !== 'Wlink') {
            $full_path = __DIR__ . '/' . $file_path;

            if (file_exists($full_path)) {
                unlink($full_path); 
            }
        }

        //delete the database record
        $stmt = $conn->prepare("DELETE FROM lessons WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "Lesson deleted successfully.";
        } else {
            echo "Error deleting lesson.";
        }

        $stmt->close();
    } else {
        echo "Invalid lesson ID.";
    }
} else {
    echo "Invalid request.";
}
?>


