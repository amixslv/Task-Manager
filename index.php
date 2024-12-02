<?php

$tasks = [];

// Uzdevumu pievienošana
function addTask(&$tasks, $task) {
    $tasks[] = [
        'task' => $task,
        'completed' => false
    ];
}

// Noņemt uzdevumu
function removeTask(&$tasks, $index) {
    if (isset($tasks[$index])) {
        unset($tasks[$index]);
        $tasks = array_values($tasks); // Pārkārto masīvu
    }
}

// Atzīmēt uzdevumu kā pabeigtu/nepabeigtu
function toggleTaskStatus(&$tasks, $index) {
    if (isset($tasks[$index])) {
        $tasks[$index]['completed'] = !$tasks[$index]['completed'];
    }
}

// Kārtotu uzdevumus pēc statusa
function sortTasksByStatus(&$tasks) {
    usort($tasks, function($a, $b) {
        return $a['completed'] - $b['completed'];
    });
}

// Saglabāt uzdevumus failā
function saveTasksToFile($tasks, $filename) {
    file_put_contents($filename, serialize($tasks));
}

// Ielādēt uzdevumus no faila
function loadTasksFromFile($filename) {
    if (file_exists($filename)) {
        return unserialize(file_get_contents($filename));
    }
    return [];
}

// Ielādē uzdevumus no faila
$tasks = loadTasksFromFile('tasks.txt');

// Apstrādā formu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        addTask($tasks, $_POST['task']);
    } elseif (isset($_POST['remove'])) {
        removeTask($tasks, $_POST['index']);
    } elseif (isset($_POST['toggle'])) {
        toggleTaskStatus($tasks, $_POST['index']);
    }
    sortTasksByStatus($tasks);
    saveTasksToFile($tasks, 'tasks.txt');
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uzdevumi</title>
</head>
<body>
    <h1>Uzdevumu apstrādes rīks</h1>
    <form method="post">
        <label for="task">Jauns uzdevums:</label>
        <input type="text" id="task" name="task" required>
        <button type="submit" name="add">Pievienot</button>
    </form>
    <h2>Uzdevumu saraksts</h2>
    <ul>
        <?php foreach ($tasks as $index => $task): ?>
            
            <li>
            <form method="post" style="display:inline;">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <button type="submit" name="toggle">Pārslēgt statusu</button>
                    <button type="submit" name="remove">Dzēst</button>
            </form>
                <?php echo htmlspecialchars($task['task']); ?>
                <?php if ($task['completed']): ?>
                    (Pabeigts)
                <?php else: ?>
                    (Nepabeigts)
                <?php endif; ?>                
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
