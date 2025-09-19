<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

function get_mysqli_connection(): mysqli {
	$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS);
	if ($mysqli->connect_errno) {
		die('DB connection failed: ' . $mysqli->connect_error);
	}
	// Ensure database exists
	$mysqli->query('CREATE DATABASE IF NOT EXISTS `' . $mysqli->real_escape_string(DB_NAME) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
	$mysqli->select_db(DB_NAME);
	return $mysqli;
}

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


