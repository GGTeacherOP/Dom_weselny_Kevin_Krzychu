<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php'; // Plik z połączeniem do bazy danych

$table = $_POST['table'];

// Pobierz dane z tabeli
try {
    $stmt = $pdo->prepare("SELECT * FROM $table");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pobierz informacje o kolumnach
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
} catch (PDOException $e) {
    die("Błąd podczas pobierania danych: " . $e->getMessage());
}

// Obsługa usuwania rekordu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    try {
        $conditions = [];
        $params = [];
        foreach ($primary_keys as $key) {
            $conditions[] = "$key = ?";
            $params[] = $_POST[$key];
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $conditions);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        header("Location: table_edit.php?table=$table");
        exit();
    } catch (PDOException $e) {
        die("Błąd podczas usuwania rekordu: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja tabeli <?php echo htmlspecialchars($table); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .action-btn { padding: 5px 10px; margin: 2px; cursor: pointer; }
        .back-btn { margin-top: 20px; padding: 10px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .add-form { margin-top: 30px; padding: 20px; background-color: #f5f5f5; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Edycja tabeli: <?php echo htmlspecialchars($table); ?></h1>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <?php foreach ($columns_info as $column): ?>
                        <th><?php echo htmlspecialchars($column['Field']); ?></th>
                    <?php endforeach; ?>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($columns_info as $column): ?>
                            <td><?php echo htmlspecialchars($row[$column['Field']] ?? ''); ?></td>
                        <?php endforeach; ?>
                        <td>
                            <form method="post" action="edit_record.php" style="display: inline;">
                                <?php foreach ($primary_keys as $key): ?>
                                    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($row[$key]); ?>">
                                <?php endforeach; ?>
                                <input type="hidden" name="table" value="<?php echo $table; ?>">
                                <button type="submit" class="action-btn" style="background-color: #2196F3; color: white;">Edytuj</button>
                            </form>
                            <form method="post" style="display: inline;">
                                <?php foreach ($primary_keys as $key): ?>
                                    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($row[$key]); ?>">
                                <?php endforeach; ?>
                                <input type="hidden" name="table" value="<?php echo $table; ?>">
                                <button type="submit" name="delete" class="action-btn" style="background-color: #f44336; color: white;">Usuń</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="add-form">
        <h2>Dodaj nowy rekord</h2>
        <form method="post" action="add_record.php">
            <input type="hidden" name="table" value="<?php echo $table; ?>">
            <?php foreach ($columns_info as $column): ?>
                <?php if ($column['Key'] !== 'PRI' || strpos($column['Extra'], 'auto_increment') === false): ?>
                    <div style="margin-bottom: 10px;">
                        <label><?php echo htmlspecialchars($column['Field']); ?>:</label>
                        <input type="text" name="<?php echo $column['Field']; ?>" style="width: 100%; padding: 5px;">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" style="padding: 10px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Dodaj</button>
        </form>
    </div>
    
    <button class="back-btn" onclick="window.location.href='panel.php'">Powrót do panelu</button>
</body>
</html>