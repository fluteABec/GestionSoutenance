<?php
    include("config/db.php");

    // Pour le deboggage
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Variables
    session_start();
    $infoEtud = null;
    // Prefer GET over session: when a link from Page A provides IdEtudiant, use it (avoids stale session values)
    $IdEtudiant = 0;
    if (isset($_GET['IdEtudiant']) && $_GET['IdEtudiant'] !== '') {
        $IdEtudiant = (int)$_GET['IdEtudiant'];
        // keep it in session so subsequent actions keep context
        $_SESSION['idEtudiant'] = $IdEtudiant;
    } elseif (isset($_GET['id']) && $_GET['id'] !== '') {
        $IdEtudiant = (int)$_GET['id'];
        $_SESSION['idEtudiant'] = $IdEtudiant;
    } else {
        $IdEtudiant = isset($_SESSION['idEtudiant']) ? (int)$_SESSION['idEtudiant'] : 0;
    }

    $idEnseignant = isset($_SESSION['professeur_id']) ? (int)$_SESSION['professeur_id'] : 0;
    $nature_Soutenance = $_GET['nature'] ?? '';

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
        echo "<br> La requête a échoué. <br>";
        return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
            echo "<br> La requete à échoué. <br>";
            return;
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
        echo "<p>Type de grille inconnu.</p>";
        return;
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
    $existingCommentaire = "";
    $existingNoteMain = null;
    if (!empty($idEval) && isset($mainTables[$typeEval])) {
        $mTable = $mainTables[$typeEval]['table'];
        $mCol = $mainTables[$typeEval]['col'];
        // Also fetch commentaireJury and note so the form can show/edit them
        $stm = $mysqli->prepare("SELECT Statut, commentaireJury, note FROM $mTable WHERE $mCol = ? LIMIT 1");
        if ($stm) {
            $stm->bind_param('i', $idEval);
            $stm->execute();
            $r = $stm->get_result()->fetch_assoc();
            if ($r) {
                if (isset($r['Statut'])) $statut = $r['Statut'];
                if (isset($r['commentaireJury'])) $existingCommentaire = $r['commentaireJury'];
                if (isset($r['note'])) $existingNoteMain = $r['note'];
            }
        }
    }

    // Charger la grille
    $sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idGrille);
    $stmt->execute();
    $grille = $stmt->get_result()->fetch_assoc();

    if (!$grille) {
        echo "<p>⚠️ Modèle de grille introuvable pour IdModeleEval = " . htmlspecialchars($idGrille) . "</p>";
        return;
    }

    echo "<h2>Grille : ".htmlspecialchars($grille['nomModuleGrilleEvaluation'])."</h2>";

    // Sections
    $sql = "SELECT s.IdSection, sc.titre, sc.description
            FROM sectionseval s
            JOIN sectioncritereeval sc ON s.IdSection = sc.IdSection
            WHERE s.IdModeleEval = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idGrille);
    $stmt->execute();
    $sections = $stmt->get_result();

    echo "<form method='POST' action='update.php'>";
    echo "<input type='hidden' name='type' value='$typeEval'>";
    echo "<input type='hidden' name='id' value='$idEval'>";
    echo "<input type='hidden' name='idEtudiant' value='$idEtudiant'>";
    echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
    echo "<tr><th>Section</th><th>Description</th><th>Critère</th><th>Note</th><th>Max</th></tr>";

    while ($sec = $sections->fetch_assoc()) {
        $id_section = $sec['IdSection'];
        echo "<tr>";
        echo "<td>".htmlspecialchars($sec['titre'])."</td>";
        echo "<td>".htmlspecialchars($sec['description'])."</td>";
        echo "<td colspan='3'>";

        // Critères de la section
        $sql_crit = "SELECT c.IdCritere, c.descCourte, c.descLongue, sc.ValeurMaxCritereEVal as valeurMaxCritereEval
                     FROM critereseval c
                     JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
                     WHERE sc.IdSection = ?";
        $stmt2 = $mysqli->prepare($sql_crit);
        $stmt2->bind_param("i", $id_section);
        $stmt2->execute();
        $crit_res = $stmt2->get_result();

        echo "<table border='1' width='100%'>";
        echo "<tr><th>Desc Courte</th><th>Desc Longue</th><th>Note</th><th>Max</th></tr>";
        while ($crit = $crit_res->fetch_assoc()) {
            $idCrit = $crit['IdCritere'];

            // Note déjà attribuée ?
            $sql_note = "SELECT $noteCol as note FROM $tablePivot WHERE $colEval=? AND IdCritere=?";
            $stmt3 = $mysqli->prepare($sql_note);
            $stmt3->bind_param("ii", $idEval, $idCrit);
            $stmt3->execute();
            $resNote = $stmt3->get_result()->fetch_assoc();
            $noteExistante = $resNote ? $resNote['note'] : "";

            echo "<tr>";
            echo "<td>".htmlspecialchars($crit['descCourte'])."</td>";
            echo "<td>".htmlspecialchars($crit['descLongue'])."</td>";
            $ro = readonlyIfLocked($statut);
            echo "<td><input type='number' step='0.01' name='notes[$idCrit]' value='".htmlspecialchars($noteExistante)."' min='0' max='".$crit['valeurMaxCritereEval']."' $ro></td>";
            echo "<td>".$crit['valeurMaxCritereEval']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    // Afficher le champ commentaire (éditable si non bloqué)
    echo "<div style='margin-top:10px;'>";
    echo "<label for='commentaireJury'>Commentaire du jury</label><br>";
    $commentEsc = htmlspecialchars($existingCommentaire ?? '', ENT_QUOTES);
    $roArea = readonlyIfLocked($statut) ? 'readonly' : '';
    echo "<textarea name='commentaireJury' rows='4' cols='80' $roArea>" . $commentEsc . "</textarea>";
    echo "</div>";
    // Afficher les actions adaptées au statut courant (Enregistrer/Valider ou Débloquer/Non modifiable)
    echo renderActions($statut);
    echo "</form>";
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
        <link rel="stylesheet" href="../../../stylee.css">
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