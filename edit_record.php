<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$table = $_POST['table'];

// Pobierz informacje o kolumnach
try {
    $stmt = $pdo->prepare("DESCRIBE $table");
    $stmt->execute();
    $columns_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pobierz klucze główne
    $primary_keys = [];
    foreach ($columns_info as $col) {
        if ($col['Key'] === 'PRI') {
            $primary_keys[] = $col['Field'];
        }
    }
    
    // Pobierz aktualne wartości rekordu
    $conditions = [];
    $params = [];
    foreach ($primary_keys as $key) {
        $conditions[] = "$key = ?";
        $params[] = $_POST[$key];
    }
    
    $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $conditions);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Błąd podczas pobierania danych: " . $e->getMessage());
}

// Obsługa aktualizacji rekordu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    try {
        $set_parts = [];
        $params = [];
        
        foreach ($columns_info as $column) {
            $field = $column['Field'];
            if (!in_array($field, $primary_keys)) {
                $set_parts[] = "$field = ?";
                $params[] = $_POST[$field];
            }
        }
        
        foreach ($primary_keys as $key) {
            $params[] = $_POST[$key];
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE " . implode(' AND ', array_map(fn($k) => "$k = ?", $primary_keys));
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        header("Location: table_edit.php?table=$table");
        exit();
    } catch (PDOException $e) {
        die("Błąd podczas aktualizacji rekordu: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja rekordu w tabeli <?php echo htmlspecialchars($table); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .edit-form { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 150px; }
        input { padding: 5px; width: 300px; }
        .btn { padding: 8px 15px; margin-right: 10px; cursor: pointer; }
        .btn-save { background-color: #4CAF50; color: white; border: none; }
        .btn-cancel { background-color: #f44336; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Edycja rekordu w tabeli: <?php echo htmlspecialchars($table); ?></h1>
    
    <form method="post" class="edit-form">
        <input type="hidden" name="table" value="<?php echo $table; ?>">
        
        <?php foreach ($columns_info as $column): ?>
            <div class="form-group">
                <label><?php echo htmlspecialchars($column['Field']); ?>:</label>
                <?php if (in_array($column['Field'], $primary_keys)): ?>
                    <input type="text" name="<?php echo $column['Field']; ?>" value="<?php echo htmlspecialchars($record[$column['Field']]); ?>" readonly>
                <?php else: ?>
                    <input type="text" name="<?php echo $column['Field']; ?>" value="<?php echo htmlspecialchars($record[$column['Field']]); ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" name="update" class="btn btn-save">Zapisz zmiany</button>
        <a href="table_edit.php?table=<?php echo $table; ?>" class="btn btn-cancel">Anuluj</a>
    </form>
</body>
</html>