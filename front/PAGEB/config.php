<?php
// Connexion PDO
$dsn = "mysql:host=localhost;dbname=evaluationstages;charset=utf8";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}