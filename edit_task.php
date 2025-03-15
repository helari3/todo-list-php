<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['id'] ?? null;
    $task_name = $_POST['task_name'] ?? null;

    if ($task_id && $task_name) {
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ? WHERE id = ?");
        $stmt->execute([$task_name, $task_id]);

        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "missing";
    }
}
?>
