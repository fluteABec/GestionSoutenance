<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';

function run_sql_file(string $filePath, bool $stopOnError = false): array {
	$result = [
		'success' => true,
		'errors' => [],
		'count' => 0,
	];
	if (!is_file($filePath)) {
		$result['success'] = false;
		$result['errors'][] = 'File not found: ' . $filePath;
		return $result;
	}
	$sql = file_get_contents($filePath);
	if ($sql === false) {
		$result['success'] = false;
		$result['errors'][] = 'Unable to read file: ' . $filePath;
		return $result;
	}


	$sql = preg_replace("/^\xEF\xBB\xBF/", '', $sql);
	$sql = str_replace(["\r\n", "\r"], "\n", $sql);

	$lines = explode("\n", $sql);
	foreach ($lines as $i => $line) {
		if (preg_match('/^\s*data\s+pour\s+les\s+tables/i', $line)) {
			$lines[$i] = '-- ' . $line;
		}
	}
	$sql = implode("\n", $lines);

	$pdo = get_pdo_connection();

	$statements = preg_split('/;\s*(?:\r?\n|$)/m', $sql);
	foreach ($statements as $stmt) {
		$statement = trim($stmt);
		if ($statement === '') { continue; }
		try {
			$pdo->exec($statement);
			$result['count']++;
		} catch (Throwable $e) {
			$errMsg = $e->getMessage();
			$lowerMsg = strtolower($errMsg);
			$lowerStmt = strtolower($statement);

			if (strpos($lowerStmt, 'create table') !== false && (strpos($lowerMsg, 'already exists') !== false || strpos($lowerMsg, '1050') !== false)) {
				continue;
			}
			$result['success'] = false;
			$result['errors'][] = $errMsg . ' | SQL: ' . substr($statement, 0, 2000);
			if ($stopOnError) { break; }
		}
	}
	return $result;
}


