<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$pdo = get_pdo_connection();
function h($s){return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');}
$year = (int)$pdo->query('SELECT COALESCE(MAX(anneeDebut), YEAR(CURDATE())) FROM AnneesUniversitaires')->fetchColumn();
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$action = isset($_POST['action'])?$_POST['action']:'';
	try{
		if ($action==='add_enseignant'){
			$stmt=$pdo->prepare('INSERT INTO Enseignants(nom,prenom,mail,mdp) VALUES(?,?,?,?)');
			$stmt->execute([trim($_POST['nom']),trim($_POST['prenom']),trim($_POST['mail']),trim($_POST['mdp'])]);
			$msg='OK';
		} elseif ($action==='update_enseignant'){
			$stmt=$pdo->prepare('UPDATE Enseignants SET nom=?, prenom=?, mail=?, mdp=? WHERE IdEnseignant=?');
			$stmt->execute([trim($_POST['nom']),trim($_POST['prenom']),trim($_POST['mail']),trim($_POST['mdp']),(int)$_POST['IdEnseignant']]);
			$msg='OK';
		} elseif ($action==='delete_enseignant'){
			$id=(int)$_POST['IdEnseignant'];
			$cnt=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE IdEnseignantTuteur='.$id.' OR IdSecondEnseignant='.$id)->fetchColumn();
			$cnt2=(int)$pdo->query('SELECT COUNT(*) FROM EvalAnglais WHERE IdEnseignant='.$id)->fetchColumn();
			if($cnt===0 && $cnt2===0){$pdo->prepare('DELETE FROM Enseignants WHERE IdEnseignant=?')->execute([$id]);$msg='OK';} else {$msg='REFUSE';}
		} elseif ($action==='add_salle'){
			$pdo->prepare('INSERT INTO Salles(IdSalle,description) VALUES(?,?)')->execute([trim($_POST['IdSalle']),trim($_POST['description'])]);
			$msg='OK';
		} elseif ($action==='update_salle'){
			$pdo->prepare('UPDATE Salles SET description=? WHERE IdSalle=?')->execute([trim($_POST['description']),trim($_POST['IdSalle'])]);
			$msg='OK';
		} elseif ($action==='delete_salle'){
			$id=trim($_POST['IdSalle']);
			$cnt=(int)$pdo->query("SELECT COUNT(*) FROM EvalStage WHERE IdSalle=".$pdo->quote($id))->fetchColumn();
			$cnt2=(int)$pdo->query("SELECT COUNT(*) FROM EvalAnglais WHERE IdSalle=".$pdo->quote($id))->fetchColumn();
			$cnt3=(int)$pdo->query("SELECT COUNT(*) FROM EvalSoutenance es JOIN EvalStage st ON st.IdEtudiant=es.IdEtudiant AND st.anneeDebut=es.anneeDebut WHERE st.IdSalle=".$pdo->quote($id))->fetchColumn();
			if($cnt===0 && $cnt2===0 && $cnt3===0){$pdo->prepare('DELETE FROM Salles WHERE IdSalle=?')->execute([$id]);$msg='OK';} else {$msg='REFUSE';}
		} elseif ($action==='add_entreprise'){
			$pdo->prepare('INSERT INTO Entreprises(nom,villeE,codePostal) VALUES(?,?,?)')->execute([trim($_POST['nom']),trim($_POST['villeE']),trim($_POST['codePostal'])]);
			$msg='OK';
		} elseif ($action==='update_entreprise'){
			$pdo->prepare('UPDATE Entreprises SET nom=?, villeE=?, codePostal=? WHERE IdEntreprise=?')->execute([trim($_POST['nom']),trim($_POST['villeE']),trim($_POST['codePostal']),(int)$_POST['IdEntreprise']]);
			$msg='OK';
		} elseif ($action==='delete_entreprise'){
			$id=(int)$_POST['IdEntreprise'];
			$cnt=(int)$pdo->query('SELECT COUNT(*) FROM AnneeStage WHERE IdEntreprise='.$id)->fetchColumn();
			if($cnt===0){$pdo->prepare('DELETE FROM Entreprises WHERE IdEntreprise=?')->execute([$id]);$msg='OK';} else {$msg='REFUSE';}
		} elseif ($action==='add_etudiant'){
			$pdo->prepare('INSERT INTO EtudiantsBUT2ou3(nom,prenom,mail,empreinte) VALUES(?,?,?,?)')->execute([trim($_POST['nom']),trim($_POST['prenom']),trim($_POST['mail']),trim($_POST['empreinte'])]);
			$msg='OK';
		} elseif ($action==='update_etudiant'){
			$pdo->prepare('UPDATE EtudiantsBUT2ou3 SET nom=?, prenom=?, mail=?, empreinte=? WHERE IdEtudiant=?')->execute([trim($_POST['nom']),trim($_POST['prenom']),trim($_POST['mail']),trim($_POST['empreinte']),(int)$_POST['IdEtudiant']]);
			$msg='OK';
		} elseif ($action==='delete_etudiant'){
			$id=(int)$_POST['IdEtudiant'];
			$cnt=(int)$pdo->query('SELECT COUNT(*) FROM AnneeStage WHERE IdEtudiant='.$id)->fetchColumn();
			if($cnt===0){$pdo->prepare('DELETE FROM EtudiantsBUT2ou3 WHERE IdEtudiant=?')->execute([$id]);$msg='OK';} else {$msg='REFUSE';}
		} elseif ($action==='add_stage'){
			$pdo->prepare('INSERT INTO AnneeStage(anneeDebut,IdEtudiant,IdEntreprise,but3sinon2,alternanceBUT3,nomMaitreStageApp,sujet,noteEntreprise,typeMission,cadreMission) VALUES(?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE IdEntreprise=VALUES(IdEntreprise), but3sinon2=VALUES(but3sinon2), alternanceBUT3=VALUES(alternanceBUT3), nomMaitreStageApp=VALUES(nomMaitreStageApp), sujet=VALUES(sujet), typeMission=VALUES(typeMission), cadreMission=VALUES(cadreMission)')->execute([$year,(int)$_POST['IdEtudiant'],($_POST['IdEntreprise']!=='')?(int)$_POST['IdEntreprise']:null,(int)$_POST['but3sinon2'],(int)$_POST['alternanceBUT3'],trim($_POST['nomMaitreStageApp']),trim($_POST['sujet']),null,trim($_POST['typeMission']),trim($_POST['cadreMission'])]);
			if(isset($_POST['creer_grilles'])&&$_POST['creer_grilles']==='1'){
				$midS=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='STAGE' AND anneeDebut=? LIMIT 1");$midS->execute([$year]);$midS=$midS->fetchColumn();
				$midR=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='RAPPORT' AND anneeDebut=? LIMIT 1");$midR->execute([$year]);$midR=$midR->fetchColumn();
				$midP=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='PORTFOLIO' AND anneeDebut=? LIMIT 1");$midP->execute([$year]);$midP=$midP->fetchColumn();
				$midSu=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='SOUTENANCE' AND anneeDebut=? LIMIT 1");$midSu->execute([$year]);$midSu=$midSu->fetchColumn();
				$et=(int)$_POST['IdEtudiant'];
				if($midS){$pdo->prepare('INSERT IGNORE INTO EvalStage(anneeDebut,IdModeleEval,IdEtudiant,Statut) VALUES(?,?,? , "SAISIE")')->execute([$year,$midS,$et]);}
				if($midR){$pdo->prepare('INSERT IGNORE INTO EvalRapport(anneeDebut,IdModeleEval,IdEtudiant,Statut) VALUES(?,?,? , "SAISIE")')->execute([$year,$midR,$et]);}
				if($midP){$pdo->prepare('INSERT IGNORE INTO EvalPortFolio(anneeDebut,IdModeleEval,IdEtudiant,Statut) VALUES(?,?,? , "SAISIE")')->execute([$year,$midP,$et]);}
				if($midSu){$pdo->prepare('INSERT IGNORE INTO EvalSoutenance(anneeDebut,IdModeleEval,IdEtudiant,Statut) VALUES(?,?,? , "SAISIE")')->execute([$year,$midSu,$et]);}
			}
			$msg='OK';
		} elseif ($action==='set_tuteur'){
			$et=(int)$_POST['IdEtudiant'];$ens=(int)$_POST['IdEnseignant'];
			$mid=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='STAGE' AND anneeDebut=? LIMIT 1");$mid->execute([$year]);$mid=$mid->fetchColumn();
			if($mid){$pdo->prepare('INSERT IGNORE INTO EvalStage(anneeDebut,IdModeleEval,IdEtudiant,Statut) VALUES(?,?,?,"SAISIE")')->execute([$year,$mid,$et]);}
			$pdo->prepare('UPDATE EvalStage SET IdEnseignantTuteur=? WHERE anneeDebut=? AND IdEtudiant=?')->execute([$ens,$year,$et]);
			$c1=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdEnseignantTuteur='.$ens)->fetchColumn();
			$c2=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdSecondEnseignant='.$ens)->fetchColumn();
			$c3=(int)$pdo->query('SELECT COUNT(*) FROM EvalAnglais WHERE anneeDebut='.$year.' AND IdEnseignant='.$ens)->fetchColumn();
			$msg='TUTEUR:'.$c1+'|SECOND:'.$c2+'|ANGLAIS:'.$c3;
		} elseif ($action==='set_second'){
			$et=(int)$_POST['IdEtudiant'];$ens=(int)$_POST['IdEnseignant'];
			$pdo->prepare('UPDATE EvalStage SET IdSecondEnseignant=? WHERE anneeDebut=? AND IdEtudiant=?')->execute([$ens,$year,$et]);
			$c1=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdEnseignantTuteur='.$ens)->fetchColumn();
			$c2=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdSecondEnseignant='.$ens)->fetchColumn();
			$c3=(int)$pdo->query('SELECT COUNT(*) FROM EvalAnglais WHERE anneeDebut='.$year.' AND IdEnseignant='.$ens)->fetchColumn();
			$msg='TUTEUR:'.$c1+'|SECOND:'.$c2+'|ANGLAIS:'.$c3;
		} elseif ($action==='set_anglais'){
			$et=(int)$_POST['IdEtudiant'];$ens=(int)$_POST['IdEnseignant'];
			$mid=$pdo->prepare("SELECT IdModeleEval FROM ModelesGrilleEval WHERE natureGrille='ANGLAIS' AND anneeDebut=? LIMIT 1");$mid->execute([$year]);$mid=$mid->fetchColumn();
			if($mid){$pdo->prepare('INSERT IGNORE INTO EvalAnglais(anneeDebut,IdModeleEval,IdEtudiant,Statut,IdEnseignant) VALUES(?,?,?,?,?)')->execute([$year,$mid,$et,'SAISIE',$ens]);}
			$pdo->prepare('UPDATE EvalAnglais SET IdEnseignant=? WHERE anneeDebut=? AND IdEtudiant=?')->execute([$ens,$year,$et]);
			$c1=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdEnseignantTuteur='.$ens)->fetchColumn();
			$c2=(int)$pdo->query('SELECT COUNT(*) FROM EvalStage WHERE anneeDebut='.$year.' AND IdSecondEnseignant='.$ens)->fetchColumn();
			$c3=(int)$pdo->query('SELECT COUNT(*) FROM EvalAnglais WHERE anneeDebut='.$year.' AND IdEnseignant='.$ens)->fetchColumn();
			$msg='TUTEUR:'.$c1+'|SECOND:'.$c2+'|ANGLAIS:'.$c3;
		}
	} catch(Throwable $e){$msg='ERR';}
}
$ensList=$pdo->query('SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom, prenom')->fetchAll();
$salles=$pdo->query('SELECT IdSalle, description FROM Salles ORDER BY IdSalle')->fetchAll();
$entList=$pdo->query('SELECT IdEntreprise, nom FROM Entreprises ORDER BY nom')->fetchAll();
$etudList=$pdo->query('SELECT IdEtudiant, nom, prenom FROM EtudiantsBUT2ou3 ORDER BY nom, prenom')->fetchAll();
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestion ressources</title>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../../stylee.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
  <?php include '../navbar.php'; ?>
<div>
  <div><?php echo h($msg); ?></div>
  <h2>Enseignants</h2>
  <form method="post">
    <input type="hidden" name="action" value="add_enseignant">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="mail" placeholder="Mail" required>
    <input type="text" name="mdp" placeholder="Mot de passe" required>
    <button type="submit">Ajouter</button>
  </form>
  <form method="post">
    <input type="hidden" name="action" value="update_enseignant">
    <select name="IdEnseignant">
      <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
    </select>
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="mail" placeholder="Mail" required>
    <input type="text" name="mdp" placeholder="Mot de passe" required>
    <button type="submit">Modifier</button>
  </form>
  <form method="post">
    <input type="hidden" name="action" value="delete_enseignant">
    <select name="IdEnseignant">
      <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
    </select>
    <button type="submit">Supprimer</button>
  </form>

<div class="admin-block">
  <div class="mb-3"><?php echo h($msg); ?></div>

  <div class="section-card">
    <h2>Enseignants</h2>
    <div class="form-group-row">
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="add_enseignant">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="mail" placeholder="Mail" required>
        <input type="text" name="mdp" placeholder="Mot de passe" required>
        <button type="submit" class="btn btn-ajouter">Ajouter</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="update_enseignant">
        <select name="IdEnseignant">
          <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
        </select>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="mail" placeholder="Mail" required>
        <input type="text" name="mdp" placeholder="Mot de passe" required>
        <button type="submit" class="btn">Modifier</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="delete_enseignant">
        <select name="IdEnseignant">
          <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn btn-supprimer">Supprimer</button>
      </form>
    </div>
  </div>

  <hr class="mb-4 mt-4">

  <div class="section-card">
    <h2>Salles</h2>
    <div class="form-group-row">
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="add_salle">
        <input type="text" name="IdSalle" placeholder="IdSalle" required>
        <input type="text" name="description" placeholder="Description">
        <button type="submit" class="btn btn-ajouter">Ajouter</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="update_salle">
        <select name="IdSalle">
          <?php foreach($salles as $s){echo '<option value="'.h($s['IdSalle']).'">'.h($s['IdSalle']).'</option>'; }?>
        </select>
        <input type="text" name="description" placeholder="Description">
        <button type="submit" class="btn">Modifier</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="delete_salle">
        <select name="IdSalle">
          <?php foreach($salles as $s){echo '<option value="'.h($s['IdSalle']).'">'.h($s['IdSalle']).'</option>'; }?>
        </select>
        <button type="submit" class="btn btn-supprimer">Supprimer</button>
      </form>
    </div>
  </div>

  <hr class="mb-4 mt-4">

  <div class="section-card">
    <h2>Entreprises</h2>
    <div class="form-group-row">
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="add_entreprise">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="villeE" placeholder="Ville" required>
        <input type="text" name="codePostal" placeholder="Code postal" required>
        <button type="submit" class="btn btn-ajouter">Ajouter</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="update_entreprise">
        <select name="IdEntreprise">
          <?php foreach($entList as $s){echo '<option value="'.h($s['IdEntreprise']).'">'.h($s['nom']).'</option>'; }?>
        </select>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="villeE" placeholder="Ville" required>
        <input type="text" name="codePostal" placeholder="Code postal" required>
        <button type="submit" class="btn">Modifier</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="delete_entreprise">
        <select name="IdEntreprise">
          <?php foreach($entList as $s){echo '<option value="'.h($s['IdEntreprise']).'">'.h($s['nom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn btn-supprimer">Supprimer</button>
      </form>
    </div>
  </div>

  <hr class="mb-4 mt-4">

  <div class="section-card">
    <h2>Étudiants</h2>
    <div class="form-group-row">
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="add_etudiant">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="mail" placeholder="Mail" required>
        <input type="text" name="empreinte" placeholder="Empreinte" required>
        <button type="submit" class="btn btn-ajouter">Ajouter</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="update_etudiant">
        <select name="IdEtudiant">
          <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
        </select>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="mail" placeholder="Mail" required>
        <input type="text" name="empreinte" placeholder="Empreinte" required>
        <button type="submit" class="btn">Modifier</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="delete_etudiant">
        <select name="IdEtudiant">
          <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn btn-supprimer">Supprimer</button>
      </form>
    </div>
  </div>

  <hr class="mb-4 mt-4">

  <div class="section-card">
    <h2>Ajouter/Mettre à jour un stage (<?php echo h($year); ?>)</h2>
    <form method="post" class="card mb-2">
      <input type="hidden" name="action" value="add_stage">
      <select name="IdEtudiant">
        <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
      </select>
      <select name="IdEntreprise">
        <option value="">(aucune)</option>
        <?php foreach($entList as $s){echo '<option value="'.h($s['IdEntreprise']).'">'.h($s['nom']).'</option>'; }?>
      </select>
      <select name="but3sinon2"><option value="0">BUT2</option><option value="1">BUT3</option></select>
      <select name="alternanceBUT3"><option value="0">Initial</option><option value="1">Alternance</option></select>
      <input type="text" name="nomMaitreStageApp" placeholder="Maitre de stage">
      <input type="text" name="sujet" placeholder="Sujet" required>
      <input type="text" name="typeMission" placeholder="Type de mission">
      <input type="text" name="cadreMission" placeholder="Cadre de mission">
      <label><input type="checkbox" name="creer_grilles" value="1">Créer les grilles</label>
      <button type="submit" class="btn btn-ajouter">Valider</button>
    </form>
  </div>

  <hr class="mb-4 mt-4">

  <div class="section-card">
    <h2>Affectations Enseignants</h2>
    <div class="form-group-row">
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="set_tuteur">
        <select name="IdEtudiant">
          <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
        </select>
        <select name="IdEnseignant">
          <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn">Définir tuteur</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="set_second">
        <select name="IdEtudiant">
          <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
        </select>
        <select name="IdEnseignant">
          <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn">Définir second</button>
      </form>
      <form method="post" class="card mb-2">
        <input type="hidden" name="action" value="set_anglais">
        <select name="IdEtudiant">
          <?php foreach($etudList as $s){echo '<option value="'.h($s['IdEtudiant']).'">'.h($s['nom'].' '.$s['prenom']).'</option>'; }?>
        </select>
        <select name="IdEnseignant">
          <?php foreach($ensList as $e){echo '<option value="'.h($e['IdEnseignant']).'">'.h($e['nom'].' '.$e['prenom']).'</option>'; }?>
        </select>
        <button type="submit" class="btn">Définir anglais</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>




