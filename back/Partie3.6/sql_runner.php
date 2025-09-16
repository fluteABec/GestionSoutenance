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

	// Normalize and pre-process known plain-text markers that break SQL parsing
	$sql = preg_replace("/^\xEF\xBB\xBF/", '', $sql); // strip BOM
	$sql = str_replace(["\r\n", "\r"], "\n", $sql);
	// Comment any marker line like: "data pour les tables entreprises , anneestage"
	$lines = explode("\n", $sql);
	foreach ($lines as $i => $line) {
		if (preg_match('/^\s*data\s+pour\s+les\s+tables/i', $line)) {
			$lines[$i] = '-- ' . $line;
		}
	}
	$sql = implode("\n", $lines);

	$pdo = get_pdo_connection();
	// Split on semicolons at end of statements; keep DELIMITER-sensitive simple approach
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
			// Ignore benign "already exists" errors on CREATE TABLE (even if comments precede it)
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


