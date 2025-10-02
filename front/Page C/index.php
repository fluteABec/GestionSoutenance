<?php
    include("config/db.php");

    // Pour le deboggage
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Variables
    session_start();
    $idEnseignant;
    $infoEtud; 
    $IdEtudiant = $_SESSION['idEtudiant'] ?? 0;
    if (isset($_SESSION["professeur_id"])) {
        $idEnseignant = $_SESSION["professeur_id"];
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
            echo "<td><input type='number' step='0.01' name='notes[$idCrit]' value='".htmlspecialchars($noteExistante)."' min='0' max='".$crit['valeurMaxCritereEval']."'></td>";
            echo "<td>".$crit['valeurMaxCritereEval']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<button type='submit' name='action' value='enregistrer'>Enregistrer</button>";
    echo "<button type='submit' name='action' value='valider'>Valider</button>";
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
    <h2>Grilles de <?=$nature_Soutenance?> de l'étudiant <?= $nom_etudient?> <?= $prenom_etudient?> </h2>
    
    
    <div class="student-block">

    <!-- Portfolio -->
   <div class="card">
    <h3><?= $title ?></h3>
    <?php foreach ($rows as $etu): ?>
        <?php
            // On appelle la fonction qui affiche la grille avec ses critères
            // ⚠️ Ici tu dois passer l'IdModeleEval correspondant
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
                echo "<p>⚠️ Aucun modèle de grille trouvé pour la nature : $type</p>";
            }
        ?>
    <?php endforeach; ?>
</div>


</div>

    </body>
</html>