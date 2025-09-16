<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$pdo = get_pdo_connection();
$annee = isset($_GET['annee']) ? trim($_GET['annee']) : '';
$enseignant = isset($_GET['enseignant']) ? trim($_GET['enseignant']) : '';
$years = $pdo->query('SELECT anneeDebut FROM AnneesUniversitaires ORDER BY anneeDebut DESC')->fetchAll(PDO::FETCH_COLUMN);
function query_view(PDO $pdo, string $sql, array $params = []): array { $stmt = $pdo->prepare($sql); $stmt->execute($params); return $stmt->fetchAll(); }
$whereAnnee = $annee !== '' ? ' WHERE anneeDebut = :annee ' : '';
$paramsAnnee = $annee !== '' ? [':annee' => $annee] : [];
$fiche = query_view($pdo, 'SELECT * FROM v_fiche_etudiant' . ($annee !== '' ? ' WHERE anneeDebut = :annee' : '' ) . ' ORDER BY nom, prenom, anneeDebut DESC', $paramsAnnee);
$promos = query_view($pdo, 'SELECT * FROM v_moyenne_promotion' . $whereAnnee . ' ORDER BY anneeDebut DESC', $paramsAnnee);
$paramsEns = $paramsAnnee;
$sqlEns = 'SELECT * FROM v_moyennes_par_enseignant' . $whereAnnee;
if ($enseignant !== '') { $sqlEns .= ($whereAnnee === '' ? ' WHERE ' : ' AND ') . '(nom LIKE :ens OR prenom LIKE :ens)'; $paramsEns[':ens'] = '%' . $enseignant . '%'; }
$sqlEns .= ' ORDER BY anneeDebut DESC, nom, prenom';
$ens = query_view($pdo, $sqlEns, $paramsEns);
$alertesS = query_view($pdo, 'SELECT * FROM v_alertes_soutenances_non_validees' . $whereAnnee . ' ORDER BY date_h DESC', $paramsAnnee);
$alertesP = query_view($pdo, 'SELECT * FROM v_alertes_portfolio_soutenance_non_validees' . $whereAnnee . ' ORDER BY date_h DESC', $paramsAnnee);
$repartDept = query_view($pdo, 'SELECT * FROM v_repartition_departements' . $whereAnnee . ' ORDER BY anneeDebut DESC, departement', $paramsAnnee);
$repartVilles = query_view($pdo, 'SELECT * FROM v_repartition_villes' . $whereAnnee . ' ORDER BY anneeDebut DESC, villeE', $paramsAnnee);
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Analyses soutenances BUT2/BUT3</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background-color:#006666;margin:0}
header{position:sticky;top:0;background:#005e5e;padding:12px 16px;z-index:2;color:#fff}
h1{font-size:20px;margin:0;font-style:italic}
.container{padding:16px;display:flex;flex-direction:column;align-items:center}
.panel{background:#fff;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,.2);padding:16px;max-width:1200px;width:100%;margin:10px auto}
.panel h2{font-size:16px;margin:0 0 8px 0;color:#006666}
table{width:100%;border-collapse:collapse}
th,td{padding:8px;border:1px solid #ddd;text-align:left}
th{background:#006666;color:#fff}
tr:nth-child(even){background:#f2f2f2}
.filters{display:flex;gap:8px;align-items:center;margin-left:auto}
input,select,button{background:#fff;border:1px solid #ccc;color:#222;border-radius:5px;padding:8px 10px}
button{background:#006666;color:#fff;border:none;cursor:pointer}
button:hover{background:#005555}
.actions a{color:#006666;text-decoration:none;margin-right:10px}
.grid{display:grid;gap:16px}
@media(min-width:900px){.grid{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<header>
  <div style="display:flex;gap:12px;align-items:center">
    <h1>Analyses soutenances BUT2/BUT3</h1>
    <form method="get" class="filters">
      <label>Année
        <select name="annee">
          <option value="">Toutes</option>
          <?php foreach ($years as $y): ?>
          <option value="<?php echo h($y); ?>" <?php echo ($annee===(string)$y?'selected':''); ?>><?php echo h($y); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Enseignant
        <input type="text" name="enseignant" value="<?php echo h($enseignant); ?>" placeholder="Nom/prénom">
      </label>
      <button type="submit">Filtrer</button>
      <span class="actions">
        <a href="init.php?action=schema" target="_blank">Initialiser schéma+données</a>
        <a href="init.php?action=views" target="_blank">Rafraîchir vues</a>
      </span>
    </form>
  </div>
</header>
<div class="container">
  <div class="panel">
    <h2>Fiche étudiant (v_fiche_etudiant)</h2>
      <table>
        <thead>
          <tr>
            <th>Id</th><th>Nom</th><th>Prénom</th><th>Année</th>
            <th>Note Rapport</th><th>Note Soutenance</th><th>Note Stage</th><th>Note Entreprise</th>
            <th>Statut Rapport</th><th>Statut Soutenance</th><th>Statut Stage</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($fiche as $r): ?>
          <tr>
            <td><?php echo h($r['IdEtudiant']); ?></td>
            <td><?php echo h($r['nom']); ?></td>
            <td><?php echo h($r['prenom']); ?></td>
            <td><?php echo h($r['anneeDebut']); ?></td>
            <td><?php echo h($r['noteRapport']); ?></td>
            <td><?php echo h($r['noteSoutenance']); ?></td>
            <td><?php echo h($r['noteStage']); ?></td>
            <td><?php echo h($r['noteEntreprise']); ?></td>
            <td><?php echo h($r['statutRapport']); ?></td>
            <td><?php echo h($r['statutSoutenance']); ?></td>
            <td><?php echo h($r['statutStage']); ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
  </div>

  <div class="grid">
    <div class="panel">
      <h2>Moyenne promotion (v_moyenne_promotion)</h2>
        <table>
          <thead>
            <tr><th>Année</th><th>Moyenne stage</th><th>Nb stages</th></tr>
          </thead>
          <tbody>
          <?php foreach ($promos as $r): ?>
            <tr>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['moyenneStage']); ?></td>
              <td><?php echo h($r['nbStages']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>

    <div class="panel">
      <h2>Moyennes par enseignant (v_moyennes_par_enseignant)</h2>
        <table>
          <thead>
            <tr><th>Id</th><th>Nom</th><th>Prénom</th><th>Année</th><th>Moyenne stage</th><th>Nb stages</th></tr>
          </thead>
          <tbody>
          <?php foreach ($ens as $r): ?>
            <tr>
              <td><?php echo h($r['IdEnseignant']); ?></td>
              <td><?php echo h($r['nom']); ?></td>
              <td><?php echo h($r['prenom']); ?></td>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['moyenneStage']); ?></td>
              <td><?php echo h($r['nbStages']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>
  </div>

  <div class="grid">
    <div class="panel">
      <h2>Alertes soutenances non validées (v_alertes_soutenances_non_validees)</h2>
        <table>
          <thead>
            <tr><th>Id</th><th>Nom</th><th>Prénom</th><th>Année</th><th>Date</th><th>Manque rapport</th><th>Manque soutenance</th></tr>
          </thead>
          <tbody>
          <?php foreach ($alertesS as $r): ?>
            <tr>
              <td><?php echo h($r['IdEtudiant']); ?></td>
              <td><?php echo h($r['nom']); ?></td>
              <td><?php echo h($r['prenom']); ?></td>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['date_h']); ?></td>
              <td><?php echo h($r['manqueRapport']); ?></td>
              <td><?php echo h($r['manqueSoutenance']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>

    <div class="panel">
      <h2>Alertes portfolio/soutenance non validées (v_alertes_portfolio_soutenance_non_validees)</h2>
        <table>
          <thead>
            <tr><th>Id</th><th>Nom</th><th>Prénom</th><th>Année</th><th>Date</th><th>Manque soutenance</th><th>Manque portfolio</th></tr>
          </thead>
          <tbody>
          <?php foreach ($alertesP as $r): ?>
            <tr>
              <td><?php echo h($r['IdEtudiant']); ?></td>
              <td><?php echo h($r['nom']); ?></td>
              <td><?php echo h($r['prenom']); ?></td>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['date_h']); ?></td>
              <td><?php echo h($r['manqueSoutenance']); ?></td>
              <td><?php echo h($r['manquePortfolio']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>
  </div>

  <div class="grid">
    <div class="panel">
      <h2>Répartition par département (v_repartition_departements)</h2>
        <table>
          <thead>
            <tr><th>Année</th><th>Département</th><th>Nb</th></tr>
          </thead>
          <tbody>
          <?php foreach ($repartDept as $r): ?>
            <tr>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['departement']); ?></td>
              <td><?php echo h($r['nb']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>

    <div class="panel">
      <h2>Répartition par villes (v_repartition_villes)</h2>
        <table>
          <thead>
            <tr><th>Année</th><th>Ville</th><th>Nb</th></tr>
          </thead>
          <tbody>
          <?php foreach ($repartVilles as $r): ?>
            <tr>
              <td><?php echo h($r['anneeDebut']); ?></td>
              <td><?php echo h($r['villeE']); ?></td>
              <td><?php echo h($r['nb']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
    </div>
  </div>

</div>
</body>
</html>


