<?php
require 'db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>