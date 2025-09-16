<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'sql_runner.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$response = [ 'ok' => false, 'action' => $action, 'details' => [] ];

if ($action === 'schema') {
	$res = run_sql_file(SQL_SCHEMA_FILE, false);
	$response['ok'] = $res['success'];
	$response['details'] = $res;
} elseif ($action === 'views') {
	$res = run_sql_file(SQL_VIEWS_FILE, false);
	$response['ok'] = $res['success'];
	$response['details'] = $res;
} else {
	$response['details'] = 'Usage: init.php?action=schema or action=views';
}

if ($format === 'json') {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	exit;
}

?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mise à jour base</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background-color:#006666;margin:0}
.wrap{display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:24px}
.panel{background:#fff;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,.2);padding:24px;max-width:900px;width:100%}
h1{margin:0 0 8px 0;color:#006666}
.ok{color:#2e7d32}
.ko{color:#c62828}
pre{white-space:pre-wrap;background:#f7f7f7;border:1px solid #eee;padding:12px;border-radius:8px}
.row{margin:8px 0}
.btns a{display:inline-block;margin-right:8px;background:#006666;color:#fff;text-decoration:none;padding:8px 12px;border-radius:6px}
.btns a:hover{background:#005555}
.muted{color:#666}
</style>
</head>
<body>
<div class="wrap">
  <div class="panel">
    <h1>Mise à jour de la base</h1>
    <div class="row">Action: <strong><?php echo htmlspecialchars($response['action']); ?></strong></div>
    <div class="row">Statut: <strong class="<?php echo $response['ok']?'ok':'ko'; ?>"><?php echo $response['ok']?'Succès':'Échec'; ?></strong></div>
    <?php if (!$response['ok'] && !empty($response['details']['errors'])): ?>
      <div class="row">Erreurs:</div>
      <pre><?php echo htmlspecialchars(implode("\n\n", $response['details']['errors'])); ?></pre>
    <?php else: ?>
      <div class="row">Nombre de statements exécutés: <strong><?php echo (int)($response['details']['count'] ?? 0); ?></strong></div>
      <div class="row muted">Tout est à jour.</div>
    <?php endif; ?>
    <div class="row btns">
      <a href="index.php">← Retour au dashboard</a>
      <a href="init.php?action=schema">Relancer schéma+données</a>
      <a href="init.php?action=views">Rafraîchir vues</a>
      <a href="init.php?action=<?php echo urlencode($action); ?>&format=json">Voir JSON</a>
    </div>
  </div>
</div>
</body>
</html>


