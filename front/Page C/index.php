<?php
    include("config/db.php");

    // Pour le deboggage
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Variables
    session_start();

    $infoEtud = null;
    // Prefer GET over session: when a link frdEtudiant, use it (avoids stale session values)
    $IdEtudiant = 0;
    if (isset($_GET['IdEtudiant']) && $_GET['IdEtudiant'] !== '') {
        $IdEtudiant = (int)$_GET['IdEtudiant'];
        // keep it in session so subsequent actions keep context
        $_SESSION['idEtudiant'] = $IdEtudiant;
    } elseif (isset($_GET['id']) && $_GET['id'] !== '') {
        $IdEtudiant = (int)$_GET['id'];
        $_SESSION['idEtudiant'] = $IdEtudiant;
    } else {
        // Cas ou il n'y a pas de idEnseignant dans l'URL
        $idEnseignant = 0; 
    }

    $nature_Soutenance = $_GET['nature'] ?? '';

    // Si l'IdEtudiant n'est pas en session, accepter un paramètre GET id / IdEtudiant
    if (empty($IdEtudiant)) {
        $IdEtudiant = isset($_GET['IdEtudiant']) ? (int)$_GET['IdEtudiant'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    }

    if (empty($IdEtudiant)) {
        die('IdEtudiant manquant. Connectez-vous ou passez ?IdEtudiant=...');
    }

    

    // Création des requetes (qui vont chacune retournée un resultat)
    function getInfoEtud($mysqli, $IdEtudiant)
{
    $stmt = $mysqli->prepare("SELECT nom, prenom FROM etudiantsbut2ou3 WHERE IdEtudiant = ?");
    $stmt->bind_param("i", $IdEtudiant);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("[Page C] getInfoEtud: requete echouee pour IdEtudiant=$IdEtudiant");
        return null;
    }

    // Un seul étudiant attendu
    return $result->fetch_assoc();
}


    

    // Permet de récupérer chaque enseignantTuteur avec leur Etudiant ainsi que l'AnnéeUniversité
    function getEnseiWithTheirEtud($mysqli, $idEnseignant)
    {
        $stmt = $mysqli->prepare("SELECT evalstage.IdEvalStage, enseignants.IdEnseignant, etudiantsbut2ou3.IdEtudiant, anneesuniversitaires.anneeDebut FROM evalstage
        JOIN enseignants ON evalstage.IdEnseignantTuteur = enseignants.IdEnseignant
        JOIN etudiantsbut2ou3 ON  evalstage.IdEtudiant = etudiantsbut2ou3.IdEtudiant
        JOIN anneesuniversitaires ON evalstage.anneeDebut = anneesuniversitaires.anneeDebut
        WHERE enseignants.IdEnseignant = ?;");
        
        $stmt->bind_param("i", $idEnseignant);
        $stmt->execute();
        $result = $stmt->get_result();

        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getEnseiWithTheirEtud: requete echouee pour idEnseignant=$idEnseignant");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    // Liaison de l'enseignant avec letudiant 
    $infoEtud = getEnseiWithTheirEtud($mysqli, $idEnseignant);
    
    function getPortfolioGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("
            SELECT DISTINCT evalportfolio.IdEvalPortfolio, evalportfolio.IdEtudiant, etudiantsbut2ou3.nom, etudiantsbut2ou3.prenom, 
                evalportfolio.note, evalportfolio.commentaireJury, statutseval.Statut, evalportfolio.IdModeleEval
            FROM evalportfolio
            JOIN etudiantsbut2ou3 ON evalportfolio.IdEtudiant = etudiantsbut2ou3.IdEtudiant
            JOIN statutseval ON evalportfolio.Statut = statutseval.Statut
            JOIN evalstage ON evalportfolio.anneeDebut = evalstage.anneeDebut
            WHERE etudiantsbut2ou3.IdEtudiant = ? ;
        ");

        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getPortfolioGrid: requete echouee pour IdEtudiant=$idEtud");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }

    function getEnglishGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT evalanglais.IdEvalAnglais, etudiantsbut2ou3.IdEtudiant, etudiantsbut2ou3.nom, etudiantsbut2ou3.prenom, evalanglais.note, evalanglais.commentaireJury, evalanglais.dateS, statutseval.Statut, evalanglais.IdModeleEval FROM evalanglais
        JOIN etudiantsbut2ou3 ON evalanglais.IdEtudiant = etudiantsbut2ou3.IdEtudiant
        JOIN statutseval ON evalanglais.Statut = statutseval.Statut
        WHERE etudiantsbut2ou3.IdEtudiant = ?");

        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getEnglishGrid: requete echouee pour IdEtudiant=$idEtud");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }

    function getSoutenanceGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT evalsoutenance.IdEvalSoutenance, etudiantsbut2ou3.IdEtudiant, etudiantsbut2ou3.nom, etudiantsbut2ou3.prenom, evalsoutenance.note, evalsoutenance.commentaireJury, statutseval.Statut, evalsoutenance.IdModeleEval FROM evalsoutenance
        JOIN etudiantsbut2ou3 ON evalsoutenance.IdEtudiant = etudiantsbut2ou3.IdEtudiant
        JOIN statutseval ON evalsoutenance.Statut = statutseval.Statut
        WHERE etudiantsbut2ou3.IdEtudiant = ?");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getSoutenanceGrid: requete echouee pour IdEtudiant=$idEtud");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;

    }


    function getRapportGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT evalrapport.IdEvalRapport, etudiantsbut2ou3.IdEtudiant, etudiantsbut2ou3.nom, etudiantsbut2ou3.prenom, evalrapport.note, evalrapport.commentaireJury, statutseval.Statut, evalrapport.IdModeleEval FROM evalrapport
        JOIN etudiantsbut2ou3 ON evalrapport.IdEtudiant = etudiantsbut2ou3.IdEtudiant
        JOIN statutseval ON  evalrapport.Statut = statutseval.Statut
        WHERE etudiantsbut2ou3.IdEtudiant = ?;");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getRapportGrid: requete echouee pour IdEtudiant=$idEtud");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    function getStageGrid($mysqli, $idEtud)
    {
        $stmt = $mysqli->prepare("SELECT evalstage.IdEvalStage, etudiantsbut2ou3.IdEtudiant, etudiantsbut2ou3.nom, etudiantsbut2ou3.prenom, evalstage.note, evalstage.commentaireJury, evalstage.date_h, salles.description, statutseval.Statut, evalstage.IdModeleEval FROM evalstage
        JOIN etudiantsbut2ou3 ON evalstage.IdEtudiant = etudiantsbut2ou3.IdEtudiant
        JOIN salles ON evalstage.IdSalle = salles.IdSalle
        JOIN statutseval ON evalstage.Statut = statutseval.Statut
        WHERE etudiantsbut2ou3.IdEtudiant = ?;");
        
        $stmt->bind_param("i", $idEtud);
        $stmt->execute();
        $result = $stmt->get_result();

        
        // Erreur de la requete
        if (!$result)
        {
            error_log("[Page C] getStageGrid: requete echouee pour IdEtudiant=$idEtud");
            return [];
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    // Fonction générique pour actions
    function renderActions($statut) {
        if ($statut === "BLOQUEE") {
            return "<button type='submit' name='action' value='debloquer'>Débloquer</button>";
        } elseif (in_array($statut, ["REMONTEE", "DIFFUSEE"])) {
            return "⛔ Non modifiable";
        } else {
            return "<button type='submit' name='action' value='enregistrer'>Enregistrer</button>
                    <button type='submit' name='action' value='valider'>Valider</button>";
        }
    }

    function readonlyIfLocked($statut) {
        return in_array($statut, ["BLOQUEE","REMONTEE","DIFFUSEE"]) ? "readonly" : "";
    }

    
    function afficherGrilleAvecNotes($mysqli, $idGrille, $idEtudiant, $idEval, $typeEval) {
    // Nom de la table pivot selon le type (conforme au dump)
    $pivotTables = [
        "portfolio"   => ["table" => "lescriteresnotesportfolio", "colEval" => "IdEvalPortfolio" , "noteCol" => 'noteCritere'],
        "rapport"     => ["table" => "lescriteresnotesrapport",   "colEval" => "IdEvalRapport" , "noteCol" => 'noteCritere'],
        "soutenance"  => ["table" => "lescriteresnotessoutenance","colEval" => "IdEvalSoutenance", "noteCol" => 'noteCritere'],
        "stage"       => ["table" => "lescriteresnotesstage",     "colEval" => "IdEvalStage", "noteCol" => 'noteCritere'],
        "anglais"     => ["table" => "lescriteresnotesanglais",   "colEval" => "IdEvalAnglais", "noteCol" => 'noteCritere']
    ];

    if (!isset($pivotTables[$typeEval])) {
        return '<p>Type de grille inconnu.</p>';
    }
    $tablePivot = $pivotTables[$typeEval]['table'];
    $colEval = $pivotTables[$typeEval]['colEval'];
    $noteCol = $pivotTables[$typeEval]['noteCol'];

    // Déterminer le statut courant de l'évaluation principale (si elle existe)
    $mainTables = [
        "portfolio" => ["table" => "evalportfolio", "col" => "IdEvalPortfolio"],
        "rapport"   => ["table" => "evalrapport",   "col" => "IdEvalRapport"],
        "soutenance"=> ["table" => "evalsoutenance","col" => "IdEvalSoutenance"],
        "stage"     => ["table" => "evalstage",     "col" => "IdEvalStage"],
        "anglais"   => ["table" => "evalanglais",   "col" => "IdEvalAnglais"]
    ];

    $statut = "SAISIE";
    if (!empty($idEval) && isset($mainTables[$typeEval])) {
        $mTable = $mainTables[$typeEval]['table'];
        $mCol = $mainTables[$typeEval]['col'];
        $stm = $mysqli->prepare("SELECT Statut FROM $mTable WHERE $mCol = ? LIMIT 1");
        if ($stm) {
            $stm->bind_param('i', $idEval);
            $stm->execute();
            $r = $stm->get_result()->fetch_assoc();
            if ($r && isset($r['Statut'])) $statut = $r['Statut'];
        }
    }

    // Charger la grille
    $sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idGrille);
    $stmt->execute();
    $grille = $stmt->get_result()->fetch_assoc();

    if (!$grille) {
        return '<p>⚠️ Modèle de grille introuvable pour IdModeleEval = ' . htmlspecialchars($idGrille) . '</p>';
    }

    $html = '<h2>Grille : ' . htmlspecialchars($grille['nomModuleGrilleEvaluation']) . '</h2>';

    // Sections
    $sql = "SELECT s.IdSection, sc.titre, sc.description
            FROM sectionseval s
            JOIN sectioncritereeval sc ON s.IdSection = sc.IdSection
            WHERE s.IdModeleEval = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idGrille);
    $stmt->execute();
    $sections = $stmt->get_result();

    $html .= "<form method='POST' action='update.php'>";
    $html .= "<input type='hidden' name='type' value='" . htmlspecialchars($typeEval) . "'>";
    $html .= "<input type='hidden' name='id' value='" . htmlspecialchars($idEval) . "'>";
    $html .= "<input type='hidden' name='idEtudiant' value='" . htmlspecialchars($idEtudiant) . "'>";
    $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
    $html .= "<tr><th>Section</th><th>Description</th><th>Critère</th><th>Note</th><th>Max</th></tr>";

    while ($sec = $sections->fetch_assoc()) {
        $id_section = $sec['IdSection'];
    $html .= "<tr>";
    $html .= "<td>".htmlspecialchars($sec['titre'])."</td>";
    $html .= "<td>".htmlspecialchars($sec['description'])."</td>";
    $html .= "<td colspan='3'>";

        // Critères de la section
        $sql_crit = "SELECT c.IdCritere, c.descCourte, c.descLongue, sc.ValeurMaxCritereEVal as valeurMaxCritereEval
                     FROM critereseval c
                     JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
                     WHERE sc.IdSection = ?";
        $stmt2 = $mysqli->prepare($sql_crit);
        $stmt2->bind_param("i", $id_section);
        $stmt2->execute();
        $crit_res = $stmt2->get_result();

    $html .= "<table border='1' width='100%'>";
    $html .= "<tr><th>Desc Courte</th><th>Desc Longue</th><th>Note</th><th>Max</th></tr>";
        while ($crit = $crit_res->fetch_assoc()) {
            $idCrit = $crit['IdCritere'];

            // Note déjà attribuée ?
            $sql_note = "SELECT $noteCol as note FROM $tablePivot WHERE $colEval=? AND IdCritere=?";
            $stmt3 = $mysqli->prepare($sql_note);
            $stmt3->bind_param("ii", $idEval, $idCrit);
            $stmt3->execute();
            $resNote = $stmt3->get_result()->fetch_assoc();
            $noteExistante = $resNote ? $resNote['note'] : "";

            $html .= "<tr>";
            $html .= "<td>".htmlspecialchars($crit['descCourte'])."</td>";
            $html .= "<td>".htmlspecialchars($crit['descLongue'])."</td>";
            $ro = readonlyIfLocked($statut);
            $html .= "<td><input type='number' step='0.01' name='notes[$idCrit]' value='".htmlspecialchars($noteExistante)."' min='0' max='".$crit['valeurMaxCritereEval']."' $ro></td>";
            $html .= "<td>".$crit['valeurMaxCritereEval']."</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</td>";
        $html .= "</tr>";
    }
    $html .= "</table>";
    // Afficher les actions adaptées au statut courant (Enregistrer/Valider ou Débloquer/Non modifiable)
    $html .= renderActions($statut);
    $html .= "</form>";

    return $html;
}



   $infoEtud = getInfoEtud($mysqli, $IdEtudiant);

    $nom_etudient = $infoEtud["nom"];
    $prenom_etudient = $infoEtud["prenom"];


switch (strtolower($nature_Soutenance)) {
    case "portfolio":
        $rows = getPortfolioGrid($mysqli, $IdEtudiant);
        $title = "PORTFOLIO";
        $type = "portfolio";
        break;
    case "anglais":
        $rows = getEnglishGrid($mysqli, $IdEtudiant);
        $title = "ANGLAIS";
        $type = "anglais";
        break;
    case "soutenance":
        $rows = getSoutenanceGrid($mysqli, $IdEtudiant);
        $title = "SOUTENANCE";
        $type = "soutenance";
        break;
    case "rapport":
        $rows = getRapportGrid($mysqli, $IdEtudiant);
        $title = "RAPPORT";
        $type = "rapport";
        break;
    case "stage":
        $rows = getStageGrid($mysqli, $IdEtudiant);
        $title = "STAGE";
        $type = "stage";
        break;
    default:
        $rows = [];
        $title = "Aucune grille";
        $type = "";
}


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Grilles <?= $nature_Soutenance?> - <?=$idEnseignant?></title>
        <link rel="stylesheet" href="../../stylee.css">
        <meta charset="UTF-8">
    </head>
    <body>
        <?php include('../headerFront.php'); ?>
    <div class="admin-block" style="max-width:950px;width:96%;margin:80px auto 0 auto;box-sizing:border-box;">
        <h2 class="section-title" style="margin-bottom:24px;">Grilles de <?= htmlspecialchars($nature_Soutenance) ?> de l'étudiant <?= htmlspecialchars($nom_etudient) ?> <?= htmlspecialchars($prenom_etudient) ?></h2>
        <div class="student-block" style="width:100%;">
            <div class="card" style="margin-bottom:32px;">
                <h3 style="margin-bottom:18px;"><?= htmlspecialchars($title) ?></h3>
                <?php 
                    $pageContent = '';
                    foreach ($rows as $etu) {
                        $idEval = null;
                        switch ($type) {
                            case "portfolio": $idEval = $etu['IdEvalPortfolio']; break;
                            case "anglais": $idEval = $etu['IdEvalAnglais']; break;
                            case "soutenance": $idEval = $etu['IdEvalSoutenance']; break;
                            case "rapport": $idEval = $etu['IdEvalRapport']; break;
                            case "stage": $idEval = $etu['IdEvalStage']; break;
                        }
                        $idGrille = null;
                        if (!empty($etu['IdModeleEval'])) {
                            $idGrille = (int)$etu['IdModeleEval'];
                        }
                        if (!$idGrille) {
                            $stmt = $mysqli->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE TRIM(LOWER(natureGrille)) = LOWER(?) LIMIT 1");
                            $stmt->bind_param("s", $type);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            if ($res && $row = $res->fetch_assoc()) {
                                $idGrille = $row['IdModeleEval'];
                            }
                        }
                        if (!$idGrille) {
                            $like = '%' . $type . '%';
                            $stmt2 = $mysqli->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE LOWER(natureGrille) LIKE LOWER(?) LIMIT 1");
                            $stmt2->bind_param('s', $like);
                            $stmt2->execute();
                            $res2 = $stmt2->get_result();
                            if ($res2 && $r2 = $res2->fetch_assoc()) {
                                $idGrille = $r2['IdModeleEval'];
                            }
                        }
                        if (!$idGrille) {
                            if (!file_exists('logs')) mkdir('logs', 0755, true);
                            file_put_contents('logs/actions.log', date('c') . " - Model not found for type: $type - IdEtudiant: {$etu['IdEtudiant']}\n", FILE_APPEND | LOCK_EX);
                        }
                        if ($idGrille) {
                            $pageContent .= afficherGrilleAvecNotes($mysqli, $idGrille, $etu['IdEtudiant'], $idEval, $type);
                        } else {
                            $pageContent .= "<p>⚠️ Aucun modèle de grille trouvé pour la nature : " . htmlspecialchars($type) . "</p>";
                        }
                    }
                ?>
                <?= $pageContent ?>
            </div>
        </div>
                <a href="../PAGEB/index.php?etudiant_id=<?= htmlspecialchars($IdEtudiant) ?>" class="btn-retour mb-3">← Retour</a>
    </div>
    </body>
</html>