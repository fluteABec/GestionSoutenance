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

    

    // CrÃ©ation des requetes (qui vont chacune retournÃ©e un resultat)
    function getInfoEtud($mysqli, $IdEtudiant)
{
    $stmt = $mysqli->prepare("SELECT nom, prenom FROM etudiantsbut2ou3 WHERE IdEtudiant = ?");
    $stmt->bind_param("i", $IdEtudiant);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        echo "<br> La requÃªte a Ã©chouÃ©. <br>";
        return;
    }

    // Un seul Ã©tudiant attendu
    return $result->fetch_assoc();
}


    

    // Permet de rÃ©cupÃ©rer chaque enseignantTuteur avec leur Etudiant ainsi que l'AnnÃ©eUniversitÃ©
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
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
            echo "<br> La requete Ã  Ã©chouÃ©. <br>";
            return;
        }

        // Tableau associatif
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    // Fonction gÃ©nÃ©rique pour actions
    function renderActions($statut) {
        if ($statut === "BLOQUEE") {
            return "<button type='submit' name='action' value='debloquer'>DÃ©bloquer</button>";
        } elseif (in_array($statut, ["REMONTEE", "DIFFUSEE"])) {
            return "â›” Non modifiable";
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

    // DÃ©terminer le statut courant de l'Ã©valuation principale (si elle existe)
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
?>
   <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grille d'Ã‰valuation</title>
</head>
<body>

<?php
// Connexion et rÃ©cupÃ©ration des infos
$grille = $mysqli->prepare("SELECT * FROM modelesgrilleeval WHERE IdModeleEval = ?");
$grille->bind_param("i", $idGrille);
$grille->execute();
$grille = $grille->get_result()->fetch_assoc();

if (!$grille) {
    echo "<p>ModÃ¨le de grille introuvable pour IdModeleEval = " . htmlspecialchars($idGrille) . "</p>";
    return;
}

echo "<h2>Grille : " . htmlspecialchars($grille['nomModuleGrilleEvaluation']) . "</h2>";

$sections = $mysqli->prepare("SELECT s.IdSection, sc.titre, sc.description
                              FROM sectionseval s
                              JOIN sectioncritereeval sc ON s.IdSection = sc.IdSection
                              WHERE s.IdModeleEval = ?");
$sections->bind_param("i", $idGrille);
$sections->execute();
$sections = $sections->get_result();
?>

<form method="POST" action="update.php">
    <input type="hidden" name="type" value="<?= htmlspecialchars($typeEval) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($idEval) ?>">
    <input type="hidden" name="idEtudiant" value="<?= htmlspecialchars($idEtudiant) ?>">

    <table>
        <tr>
            <th>Section</th>
            <th>Description</th>
            <th>CritÃ¨re</th>
            <th>Note</th>
            <th>Max</th>
        </tr>

        <?php while ($sec = $sections->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($sec['titre']) ?></td>
                <td><?= htmlspecialchars($sec['description']) ?></td>
                <td colspan="3">
                    <table>
                        <tr>
                            <th>Desc Courte</th>
                            <th>Desc Longue</th>
                            <th>Note</th>
                            <th>Max</th>
                        </tr>
                        <?php
                        $id_section = $sec['IdSection'];
                        $crit_sql = $mysqli->prepare("SELECT c.IdCritere, c.descCourte, c.descLongue, sc.ValeurMaxCritereEVal
                                                      FROM critereseval c
                                                      JOIN sectioncontenircriteres sc ON c.IdCritere = sc.IdCritere
                                                      WHERE sc.IdSection = ?");
                        $crit_sql->bind_param("i", $id_section);
                        $crit_sql->execute();
                        $crit_res = $crit_sql->get_result();

                        while ($crit = $crit_res->fetch_assoc()):
                            $idCrit = $crit['IdCritere'];
                            $note_sql = $mysqli->prepare("SELECT $noteCol as note FROM $tablePivot WHERE $colEval=? AND IdCritere=?");
                            $note_sql->bind_param("ii", $idEval, $idCrit);
                            $note_sql->execute();
                            $resNote = $note_sql->get_result()->fetch_assoc();
                            $noteExistante = $resNote ? $resNote['note'] : "";
                            $ro = readonlyIfLocked($statut);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($crit['descCourte']) ?></td>
                                <td><?= htmlspecialchars($crit['descLongue']) ?></td>
                                <td><input type="number" step="0.01" name="notes[<?= $idCrit ?>]" value="<?= htmlspecialchars($noteExistante) ?>" min="0" max="<?= $crit['ValeurMaxCritereEVal'] ?>" <?= $ro ?>></td>
                                <td><?= $crit['ValeurMaxCritereEVal'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div>
        <label for="commentaireJury">Commentaire du jury</label><br>
        <textarea name="commentaireJury" rows="4" <?= readonlyIfLocked($statut) ? 'readonly' : '' ?>>
            <?= htmlspecialchars($existingCommentaire ?? '', ENT_QUOTES) ?>
        </textarea>
    </div>

    <?= renderActions($statut) ?>
</form>

</body>
</html>

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Grilles <?= $nature_Soutenance?> - <?=$idEnseignant?></title>
        <link rel="stylesheet" href="../../../stylee.css">
        <meta charset="UTF-8">
    </head>
    <body>
    <h2>Grilles de <?=$nature_Soutenance?> de l'Ã©tudiant <?= $nom_etudient?> <?= $prenom_etudient?> </h2>
    
    
    <div class="student-block">

    <!-- Portfolio -->
   <div class="card">
    <h3><?= $title ?></h3>
    <?php if (empty($rows)): ?>
        <p>Aucune Ã©valuation trouvÃ©e pour cet Ã©tudiant et cette nature (<?= htmlspecialchars($title) ?>).</p>
        <?php
            // log empty result for debugging
            if (!file_exists('logs')) mkdir('logs', 0755, true);
            file_put_contents('logs/actions.log', date('c') . " - No rows for type: $type - IdEtudiant: $IdEtudiant\n", FILE_APPEND | LOCK_EX);
        ?>
    <?php endif; ?>
    <?php foreach ($rows as $etu): ?>
        <?php
            // On appelle la fonction qui affiche la grille avec ses critÃ¨res
            // âš ï¸ Ici tu dois passer l'IdModeleEval correspondant
            // -> Pour simplifier on peut le lire directement dans EvalXXX
            $idEval = null;
            switch ($type) {
                case "portfolio": $idEval = $etu['IdEvalPortfolio']; break;
                case "anglais": $idEval = $etu['IdEvalAnglais']; break;
                case "soutenance": $idEval = $etu['IdEvalSoutenance']; break;
                case "rapport": $idEval = $etu['IdEvalRapport']; break;
                case "stage": $idEval = $etu['IdEvalStage']; break;
            }

            // Prefer IdModeleEval provided by the eval record (IdModeleEval), fallback to lookup by nature
            $idGrille = null;
            if (!empty($etu['IdModeleEval'])) {
                $idGrille = (int)$etu['IdModeleEval'];
            }

            if (!$idGrille) {
                // Use TRIM to ignore accidental spaces in the stored enum values (e.g. ' STAGE' in the dump)
                $stmt = $mysqli->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE TRIM(LOWER(natureGrille)) = LOWER(?) LIMIT 1");
                $stmt->bind_param("s", $type);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $row = $res->fetch_assoc()) {
                    $idGrille = $row['IdModeleEval'];
                }
            }

            // Second fallback: look for models containing the word (robuste contre espaces inattendus)
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

            // If still no model, log to local file for diagnosis
            if (!$idGrille) {
                if (!file_exists('logs')) mkdir('logs', 0755, true);
                file_put_contents('logs/actions.log', date('c') . " - Model not found for type: $type - IdEtudiant: {$etu['IdEtudiant']}\n", FILE_APPEND | LOCK_EX);
            }

            if ($idGrille) {
                afficherGrilleAvecNotes($mysqli, $idGrille, $etu['IdEtudiant'], $idEval, $type);
            } else {
                // Second stronger fallback: pick the most recent model that contains the word (order by year desc)
                $like = '%' . $type . '%';
                $stmt3 = $mysqli->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE LOWER(natureGrille) LIKE LOWER(?) ORDER BY anneeDebut DESC, IdModeleEval DESC LIMIT 1");
                if ($stmt3) {
                    $stmt3->bind_param('s', $like);
                    $stmt3->execute();
                    $r3 = $stmt3->get_result()->fetch_assoc();
                    if ($r3 && !empty($r3['IdModeleEval'])) {
                        $idGrille = $r3['IdModeleEval'];
                        afficherGrilleAvecNotes($mysqli, $idGrille, $etu['IdEtudiant'], $idEval, $type);
                    } else {
                        echo "<p>âš ï¸ Aucun modÃ¨le de grille trouvÃ© pour la nature : " . htmlspecialchars($type) . "</p>";
                        // Log available model natures to help debugging
                        if (!file_exists('logs')) mkdir('logs', 0755, true);
                        $resN = $mysqli->query("SELECT DISTINCT natureGrille FROM modelesgrilleeval");
                        $vals = [];
                        if ($resN) {
                            while ($rowN = $resN->fetch_assoc()) $vals[] = $rowN['natureGrille'];
                        }
                        file_put_contents('logs/actions.log', date('c') . " - Model not found for type: $type - IdEtudiant: {$etu['IdEtudiant']} - available: " . implode('|', $vals) . "\n", FILE_APPEND | LOCK_EX);
                    }
                } else {
                    echo "<p>âš ï¸ Erreur lors de la recherche du modÃ¨le de grille.</p>";
                }
            }
        ?>
    <?php endforeach; ?>
</div>


</div>
<?php if (strtolower($type) === 'anglais'): ?>
    <p><a href="../Front_PartieA/public/index.php"> â† Retour</a></p>
<?php else: ?>
    <p><a href="../PAGEB/index.php?etudiant_id=<?php echo $IdEtudiant; ?>"> â† Retour</a></p>
<?php endif; ?>


    </body>
</html>