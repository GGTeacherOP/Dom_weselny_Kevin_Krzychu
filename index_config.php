<?php
// config.php
$servername = "localhost";
$username = "root";  // domyślnie w XAMPP to 'root'
$password = "";      // domyślnie puste w XAMPP
$dbname = "dom_weselny";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>