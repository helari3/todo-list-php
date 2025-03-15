<?php
require 'db_connect.php';

$pendingTasks = $conn->query("SELECT * FROM tasks WHERE is_completed = 0 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$completedTasks = $conn->query("SELECT * FROM tasks WHERE is_completed = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css?v=1">
    <title>To-Do List</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>
<body>
    <div class="container full-width">
        <h2>TO-DO</h2>
        <form action="add_task.php" method="POST">
            <input type="text" name="task_name" class="task-input" placeholder="Enter a new task" required>
            <button type="submit" class="add-btn">ADD TASK</button>
        </form>
        
        <h3>TASK LIST</h3>
        <div id="taskList">
            <?php if (empty($pendingTasks)): ?>
                <p class="no-tasks">No tasks available.</p>
            <?php else: ?>
                <?php foreach ($pendingTasks as $row): ?>
                    <div class="task-box" id="task-<?php echo $row['id']; ?>">
                        <span class="task-name" onclick="enableEdit(<?php echo $row['id']; ?>)">
                            <?php echo htmlspecialchars($row['task_name']); ?>
                        </span>
                        <input type="text" class="edit-input" value="<?php echo htmlspecialchars($row['task_name']); ?>" 
                               style="display: none;">
                        <div>
                            <button class="edit-btn" onclick="editTask(<?php echo $row['id']; ?>)">EDIT</button>
                            <button class="save-btn" onclick="saveTask(<?php echo $row['id']; ?>)">SAVE</button>
                            <a href="complete_task.php?id=<?php echo $row['id']; ?>" class="complete-btn">COMPLETE</a>
                            <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="delete-btn">DELETE</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <h3>COMPLETED TASKS</h3>
        <div id="completedTasks">
            <?php if (empty($completedTasks)): ?>
                <p class="no-tasks">No completed tasks.</p>
            <?php else: ?>
                <?php foreach ($completedTasks as $row): ?>
                    <div class="task-box completed" id="completed-task-<?php echo $row['id']; ?>">
                        <span class="task-name"><?php echo htmlspecialchars($row['task_name']); ?></span>
                        <button class="remove-btn" onclick="removeTask(<?php echo $row['id']; ?>)">REMOVE</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function editTask(id) {
            let taskBox = document.getElementById(`task-${id}`);
            let span = taskBox.querySelector(".task-name");
            let input = taskBox.querySelector(".edit-input");
            let editBtn = taskBox.querySelector(".edit-btn");
            let saveBtn = taskBox.querySelector(".save-btn");

            input.style.display = "inline"; 
            input.style.width = span.offsetWidth + "px"; 
            input.value = span.textContent.trim();
            input.focus();
            span.style.display = "none"; 
            editBtn.style.display = "none"; 
            saveBtn.style.display = "inline"; 
        }

        function saveTask(id) {
            let taskBox = document.getElementById(`task-${id}`);
            let input = taskBox.querySelector(".edit-input");
            let span = taskBox.querySelector(".task-name");
            let editBtn = taskBox.querySelector(".edit-btn");
            let saveBtn = taskBox.querySelector(".save-btn");

            let taskName = input.value;

            $.post("edit_task.php", { id: id, task_name: taskName }, function(response) {
                if (response === "success") {
                    span.textContent = taskName;
                    span.style.display = "inline"; 
                    input.style.display = "none"; 
                    editBtn.style.display = "inline"; 
                    saveBtn.style.display = "none"; 
                } else {
                    alert("Error updating task!");
                }
            });
        }

        function removeTask(id) {
            $.post("remove_task.php", { id: id }, function(response) {
                if (response === "success") {
                    document.getElementById(`completed-task-${id}`).remove();
                    
                    // If no completed tasks remain, show "No completed tasks" message
                    if (document.getElementById("completedTasks").children.length === 0) {
                        document.getElementById("completedTasks").innerHTML = '<p class="no-tasks">No completed tasks.</p>';
                    }
                } else {
                    alert("Error removing task!");
                }
            });
        }
    </script>
</body>
</html>
