<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

function get_pdo_connection(): PDO {
	$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
	try {
		$pdo = new PDO($dsn, DB_USER, DB_PASS, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		]);
		return $pdo;
	} catch (PDOException $e) {
		die('DB connection failed: ' . $e->getMessage());
	}
}


