<?php
// db.php
// Secure PDO database connection for the Grasshopper Clone
// Uses prepared statements and error handling. Assumes MySQL.
 
$host = 'localhost'; // Change if remote DB
$dbname = 'dbylwgda5pjr2y';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
 
// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
