<?php
require_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_number = $_POST['lesson_number'] ?? null;
    $week = $_POST['week'] ?? null;
    $module_code = $_POST['module_code'] ?? null;

    if ($lesson_number !== null && $week !== null && $module_code !== null) {
        $stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_number = ? AND week = ? AND module_code = ?");
        $stmt->bind_param("iis", $lesson_number, $week, $module_code);

        if ($stmt->execute()) {
            echo "Lesson deleted successfully.";
        } else {
            echo "Error deleting lesson.";
        }
    } else {
        echo "Invalid data received.";
    }
}
?>
