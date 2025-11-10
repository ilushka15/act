<?php
// DB connect + minimal schema ensure (users table)
$servername = "localhost";
$username = "root";
$password = "";

// Define schema helper locally (no external include needed)
if (!function_exists('ensure_schema')) {
  function ensure_schema(PDO $conn): void {
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) NOT NULL UNIQUE,
      email VARCHAR(190) NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      role ENUM('user','admin') NOT NULL DEFAULT 'user',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
  }
}

try {
  $conn = new PDO("mysql:host=$servername;dbname=activities_app;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  ensure_schema($conn);
} catch (PDOException $e) {
  http_response_code(500);
  die('Databaseverbindingsfout.');
}
?>