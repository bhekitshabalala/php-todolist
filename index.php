<?php
require_once 'Database.php';
require_once 'Task.php';

$db = new Database();
$conn = $db->getConnection();

// Handle form submission for adding a new task
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['description'])) {
    $description = htmlspecialchars(strip_tags($_POST['description']));
    $task = new Task($description);

    $query = "INSERT INTO tasks(description, completed) VALUES (:description, :completed)";
    $stmt = $conn->prepare($query);
    $descriptionParam = $task->getDescription();
    $completedParam = $task->isCompleted();

    $stmt->bindParam(':description', $descriptionParam);
    $stmt->bindParam(':completed', $completedParam, PDO::PARAM_BOOL);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Task added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Unable to add task.</p>";
    }
}

// Handle form submission for updating task completion status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['completed'])) {
    // First, reset all tasks to not completed
    $query = "UPDATE tasks SET completed = 0";
    $conn->exec($query);

    // Then, mark the selected tasks as completed
    $completedTasks = $_POST['completed'];
    $query = "UPDATE tasks SET completed = 1 WHERE id = :id";
    $stmt = $conn->prepare($query);

    foreach ($completedTasks as $taskId) {
        $taskIdParam = (int) $taskId;
        $stmt->bindParam(':id', $taskIdParam, PDO::PARAM_INT);
        $stmt->execute();
    }

    echo "<p style='color: green;'>Tasks updated successfully!</p>";
}

// Fetch tasks
$query = "SELECT * FROM tasks";
$stmt = $conn->query($query);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: calc(100% - 110px);
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            color: #fff;
            background-color: #007bff;
            cursor: pointer;
        }
        button[type="reset"] {
            background-color: #6c757d;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .status {
            font-weight: bold;
        }
        .status.completed {
            color: green;
        }
        .status.not-completed {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <form method="POST" action="index.php">
            <label for="description">Task:</label>
            <input type="text" id="description" name="description" required>
            <button type="submit">Add Task</button>
            <button type="reset">Clear</button>
        </form>

        <h2>Your Tasks</h2>
        <form method="POST" action="index.php">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Completed</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['id']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td class="status <?php echo $task['completed'] ? 'completed' : 'not-completed'; ?>">
                            <?php echo $task['completed'] ? 'Yes' : 'No'; ?>
                        </td>
                        <td>
                            <input type="checkbox" name="completed[]" value="<?php echo htmlspecialchars($task['id']); ?>" <?php if ($task['completed']) echo 'checked'; ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit">Update Completed Status</button>
        </form>
    </div>
</body>
</html>
