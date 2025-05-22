<?php
// Dane do połączenia z bazą danych
$host = '127.0.0.1:3307';
$dbname = 'dom_weselny';
$username = 'root'; // Zmień na swojego użytkownika
$password = ''; // Zmień na swoje hasło

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>