<?php
// Database configuration + PDO instance. Import with: require 'db.php';
$host    = 'localhost';
$dbname  = 'blog_site';
$dbuser  = 'root';
$dbpass  = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $dbuser, $dbpass, $options);
} catch (PDOException $e) {
    exit('Database connection failed.');
}
