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
    
    // Przygotuj dane do wstawienia
    $columns = [];
    $values = [];
    $placeholders = [];
    
    foreach ($columns_info as $column) {
        $field = $column['Field'];
        if ($column['Key'] !== 'PRI' || strpos($column['Extra'], 'auto_increment') === false) {
            if (isset($_POST[$field])) {
                $columns[] = $field;
                $values[] = $_POST[$field];
                $placeholders[] = '?';
            }
        }
    }
    
    // Wstaw nowy rekord
    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    header("Location: table_edit.php?table=$table");
    exit();
    
} catch (PDOException $e) {
    die("Błąd podczas dodawania rekordu: " . $e->getMessage());
}
?>