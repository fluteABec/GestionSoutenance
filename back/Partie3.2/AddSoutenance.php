<?php

require_once "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Liste des enseignants
$sql = "SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom, prenom";
$stmt = $pdo->query($sql);
$listeEnseignant = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT e.IdEtudiant, e.nom, e.prenom
        FROM EtudiantsBUT2ou3 e
        JOIN AnneeStage a ON e.IdEtudiant = a.IdEtudiant
        WHERE a.but3sinon2 = TRUE";
$etudiantsBUT3 = $pdo->query($sql)->fetchAll();

$idEtudiant = $_GET["idEtudiant"] ?? NULL;
$type = $_GET["type"] ?? NULL;

if (!in_array($type, ['stage', 'anglais'])) {
    die("Erreur : type de soutenance invalide.");
}


// Vérifier si c'est un étudiant BUT3
$estBut3 = false;
foreach ($etudiantsBUT3 as $e) {
    if ($e["IdEtudiant"] == $idEtudiant) {
        $estBut3 = true;
        break;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nature = $_GET['type'];
    $date   = $_POST['DateSoutenance'];
    $salle  = $_POST['Salle'];
    $anneeDebut = 2025;
    $statut = 'SAISIE';

    if ($nature === 'stage') {
        $idTuteur = $_POST['Tuteur'];
        $secondEns = $_POST['SecondEnseignant'];


        if ($idTuteur == $secondEns) {
            echo "<h2 style='color:red'>Le tuteur et le second enseignant doivent être différents.<h2>";
        }
        else
        {
            // Vérifier conflits
            $sqlConflits = "
                SELECT 'Salle' AS type, IdSalle AS ressource
                FROM EvalStage
                WHERE IdSalle = :salle
                AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
                UNION 
                SELECT 'Salle', IdSalle FROM EvalAnglais
                WHERE IdSalle = :salle
                AND :date BETWEEN dateS AND DATE_ADD(dateS, INTERVAL 1 HOUR)

                UNION
                SELECT 'Etudiant', IdEtudiant FROM EvalStage
                WHERE IdEtudiant = :idEtudiant
                AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
                UNION
                SELECT 'Etudiant', IdEtudiant FROM EvalAnglais
                WHERE IdEtudiant = :idEtudiant
                AND :date BETWEEN dateS AND DATE_ADD(dateS, INTERVAL 1 HOUR)

                UNION
                SELECT 'Tuteur', IdEnseignantTuteur FROM EvalStage
                WHERE IdEnseignantTuteur = :tuteur
                AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
                UNION
                SELECT 'Second', IdSecondEnseignant FROM EvalStage
                WHERE IdSecondEnseignant = :second
                AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
            ";

            $stmt = $pdo->prepare($sqlConflits);
            $stmt->execute([
                'date' => $date,
                'salle' => $salle,
                'idEtudiant' => $idEtudiant,
                'tuteur' => $idTuteur,
                'second' => $secondEns
            ]);
            $conflits = $stmt->fetchAll();

            if ($conflits) {
                echo "<p style='color:red'>⚠️ Conflit détecté :<br>";
                foreach ($conflits as $c) {
                    echo htmlspecialchars($c['type']) . " (" . htmlspecialchars($c['ressource']) . ") déjà occupé.<br>";
                }
                echo "</p>";
            } else {
                // Déterminer IdModeleEval le plus récent pour la nature 'STAGE' (normaliser espaces/casse)
                $stmtModel = $pdo->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE TRIM(LOWER(natureGrille)) LIKE LOWER('%stage%') ORDER BY anneeDebut DESC, IdModeleEval DESC LIMIT 1");
                $stmtModel->execute();
                $modelRow = $stmtModel->fetch(PDO::FETCH_ASSOC);
                $idModeleEval = $modelRow ? (int)$modelRow['IdModeleEval'] : 1; // fallback to 1 if none found

                // INSERT stage + création atomique des autres évaluations pour BUT3
                try {
                    $pdo->beginTransaction();

                    $sql = "INSERT INTO EvalStage 
                    (date_h, IdEtudiant, IdEnseignantTuteur, IdSecondEnseignant, IdSalle, anneeDebut, IdModeleEval, Statut, note, commentaireJury, presenceMaitreStageApp, confidentiel)
                    VALUES (:date, :idEtudiant, :tuteur, :second, :salle, :annee, :idModele, :statut, NULL, NULL, 0, 0)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'date' => $date,
                        'idEtudiant' => $idEtudiant,
                        'tuteur' => $idTuteur,
                        'second' => $secondEns,
                        'salle' => $salle,
                        'annee' => $anneeDebut,
                        'idModele' => $idModeleEval,
                        'statut' => $statut
                    ]);

                    // Si étudiant BUT3 : créer aussi les évaluations portfolio, rapport et soutenance (pas anglais)
                    if ($estBut3) {
                        // mapping of additional eval tables
                        $toCreate = [
                            'portfolio' => ['table' => 'evalportfolio', 'like' => 'portfolio'],
                            'rapport'   => ['table' => 'evalrapport',   'like' => 'rapport'],
                            'soutenance'=> ['table' => 'evalsoutenance','like' => 'soutenance']
                        ];

                        foreach ($toCreate as $key => $info) {
                            // find latest model for this nature
                            $stmtM = $pdo->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE TRIM(LOWER(natureGrille)) LIKE LOWER(:needle) ORDER BY anneeDebut DESC, IdModeleEval DESC LIMIT 1");
                            $needle = '%' . $info['like'] . '%';
                            $stmtM->execute(['needle' => $needle]);
                            $mRow = $stmtM->fetch(PDO::FETCH_ASSOC);
                            $idModel = $mRow ? (int)$mRow['IdModeleEval'] : 1;

                            // Check existence to avoid duplicate unique key errors
                            $checkSql = "SELECT 1 FROM {$info['table']} WHERE IdEtudiant = :idEt AND anneeDebut = :annee AND IdModeleEval = :idModel LIMIT 1";
                            $chk = $pdo->prepare($checkSql);
                            $chk->execute(['idEt' => $idEtudiant, 'annee' => $anneeDebut, 'idModel' => $idModel]);
                            $exists = $chk->fetchColumn();
                            if ($exists) continue;

                            // Insert accordingly
                            if ($info['table'] === 'evalportfolio') {
                                $ins = $pdo->prepare("INSERT INTO evalportfolio (note, commentaireJury, anneeDebut, IdModeleEval, IdEtudiant, Statut) VALUES (NULL, NULL, :annee, :idModel, :idEt, :statut)");
                                $ins->execute(['annee' => $anneeDebut, 'idModel' => $idModel, 'idEt' => $idEtudiant, 'statut' => $statut]);
                            } elseif ($info['table'] === 'evalrapport') {
                                $ins = $pdo->prepare("INSERT INTO evalrapport (note, commentaireJury, Statut, anneeDebut, IdModeleEval, IdEtudiant) VALUES (NULL, NULL, :statut, :annee, :idModel, :idEt)");
                                $ins->execute(['statut' => $statut, 'annee' => $anneeDebut, 'idModel' => $idModel, 'idEt' => $idEtudiant]);
                            } elseif ($info['table'] === 'evalsoutenance') {
                                $ins = $pdo->prepare("INSERT INTO evalsoutenance (note, commentaireJury, anneeDebut, IdModeleEval, IdEtudiant, Statut) VALUES (NULL, NULL, :annee, :idModel, :idEt, :statut)");
                                $ins->execute(['annee' => $anneeDebut, 'idModel' => $idModel, 'idEt' => $idEtudiant, 'statut' => $statut]);
                            }
                        }
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    error_log('Error creating stage and related evals: ' . $e->getMessage());
                    echo "<p style='color:red'>Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage()) . "</p>";
                    exit;
                }
                header("Location: ../mainAdministration.php?added=1");
                exit;
            }
        }
        
    }

    if ($nature === 'anglais') {
        // For 'anglais' the visible select is the Tuteur select (label changed to "Enseignant").
        // Use the value from 'Tuteur' if present, otherwise fall back to 'SecondEnseignant'.
        if (isset($_POST['Tuteur']) && $_POST['Tuteur'] !== '') {
            $ens = (int)$_POST['Tuteur'];
        } elseif (isset($_POST['SecondEnseignant']) && $_POST['SecondEnseignant'] !== '') {
            $ens = (int)$_POST['SecondEnseignant'];
        } else {
            echo "<p style='color:red'>Erreur : aucun enseignant sélectionné pour la soutenance d'anglais.</p>";
            exit;
        }

        // Déterminer IdModeleEval le plus récent pour la nature 'ANGLAIS'
        $stmtModel = $pdo->prepare("SELECT IdModeleEval FROM modelesgrilleeval WHERE TRIM(LOWER(natureGrille)) LIKE LOWER('%anglais%') ORDER BY anneeDebut DESC, IdModeleEval DESC LIMIT 1");
        $stmtModel->execute();
        $modelRow = $stmtModel->fetch(PDO::FETCH_ASSOC);
        $idModeleEval = $modelRow ? (int)$modelRow['IdModeleEval'] : 1;

        $sql = "INSERT INTO EvalAnglais 
            (dateS, IdEtudiant, IdEnseignant, IdSalle, anneeDebut, note, Statut, IdModeleEval)
            VALUES (:date, :idEtudiant, :ens, :salle, :annee, NULL, :statut, :idModele)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'idEtudiant' => $idEtudiant,
            'ens' => $ens,
            'salle' => $salle,
            'annee' => $anneeDebut,
            'statut' => $statut,
            'idModele' => $idModeleEval
        ]);
        header("Location: ../mainAdministration.php?added=1");
        exit;
    }
}


?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Ajout Soutenance</title>
        <link rel="stylesheet" href="../../stylee.css">
    </head>
    
<body>
<?php include '../navbar.php'; ?>


<div class="admin-block" style="max-width:650px;width:96%;margin:80px auto 0 auto;box-sizing:border-box;">
    <h2 class="section-title">Ajout d'une Soutenance</h2>
    <form method="post" class="card" style="padding:32px 24px;">
        <?php if (!$estBut3 || $estBut3 && $type === 'stage'): ?> 
            <div class="form-group" style="margin-bottom:18px;">
                <label value="portfolio&stage">Nature : Portfolio & Stage</label>
            </div>
        <?php endif; ?> 
        <?php if ($estBut3 && $type === 'anglais'): ?> 
            <div class="form-group" style="margin-bottom:18px;">
                <label value="anglais">Nature : Anglais</label>
            </div>
        <?php endif; ?> 
        <div class="form-group" style="margin-bottom:18px;">
            <label for="DateSoutenance">Date et heure :</label>
            <input type="datetime-local" name="DateSoutenance" id="DateSoutenance" class="input-text">
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label for="salleSelect">Salle :</label>
            <select name="Salle" id="salleSelect" class="input-text">
                <option value="">Choisir...</option>
            </select>
        </div>
        <div id="tuteurGroup" class="form-group" style="margin-bottom:18px;">
            <label id="tuteurLabel" for="tuteurSelect">Tuteur (Stage/Portfolio) :</label>
            <select name="Tuteur" id="tuteurSelect" class="input-text">
                <?php foreach ($listeEnseignant as $e): ?>
                    <option value="<?= $e['IdEnseignant'] ?>">
                        <?= htmlspecialchars($e['nom']." ".$e['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="secondEnsGroup" class="form-group" style="margin-bottom:18px;">
            <label for="secondEnsSelect">Second enseignant :</label>
            <select name="SecondEnseignant" id="secondEnsSelect" class="input-text">
                <?php foreach ($listeEnseignant as $e): ?>
                    <option value="<?= $e['IdEnseignant'] ?>">
                        <?= htmlspecialchars($e['nom']." ".$e['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
        <a href="../mainAdministration.php" class="btn-retour mb-3">← Retour</a>
</div>

</body>
</html>




<script>
const type = "<?= htmlspecialchars($type) ?>"; // récupéré depuis $_GET en PHP

function chargerSalles() {
    const dateSoutenance = document.getElementById('DateSoutenance').value;
    const selectSalle = document.getElementById('salleSelect');

    if (!dateSoutenance) {
        selectSalle.innerHTML = '<option value="">Indiquez une heure de soutenance</option>';
        return;
    }

    fetch("get_salles.php?date=" + encodeURIComponent(dateSoutenance))
        .then(response => response.json())
        .then(data => {
            selectSalle.innerHTML = "";
            if (!data || data.length === 0) {
                selectSalle.innerHTML = '<option value="">Aucune salle disponible</option>';
            } else {
                data.forEach(salle => {
                    const opt = document.createElement("option");
                    opt.value = salle.IdSalle;
                    opt.textContent = salle.description;
                    selectSalle.appendChild(opt);
                });
            }
        })
        .catch(err => {
            console.error("Erreur:", err);
            selectSalle.innerHTML = '<option value="">Erreur de chargement</option>';
        });
}

// Adapter les champs si anglais sélectionné
function adapterChampsNature() {
    const tuteurLabel = document.getElementById("tuteurLabel");
    const secondGroup = document.getElementById("secondEnsGroup");

    if (type === "anglais") {
        tuteurLabel.textContent = "Enseignant :";
        secondGroup.style.display = "none"; // on cache le champ second enseignant
    } else {
        tuteurLabel.textContent = "Tuteur (Stage/Portfolio) :";
        secondGroup.style.display = "block";
    }
}

// événements
document.getElementById('DateSoutenance').addEventListener('change', chargerSalles);

// appel initial
adapterChampsNature();
</script>