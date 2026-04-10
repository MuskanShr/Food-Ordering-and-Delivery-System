<?php

$host = 'localhost';
$database   = 'foodbyte';
$username = 'root';
$password = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO(
        "mysql:host=$host; port=3307; dbname=$database",
        $username,
        $password,
        $options
    );
    // echo("Successful Connection");
} catch (PDOException $e) {
    die('Database connection failed.');
}
?>