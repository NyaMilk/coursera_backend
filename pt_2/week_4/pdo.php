<?php
$dsn = 'mysql:host=localhost;dbname=misc';
$user = 'super';
$password = '1234';

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error creating database: " . $e->getMessage() . "\n");
}
