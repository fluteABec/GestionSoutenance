<?php

require_once "/opt/lampp/htdocs/projet_sql/db.php";

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
    $nature = $_POST['NatureSoutenance'];
    $date   = $_POST['DateSoutenance'];
    $salle  = $_POST['Salle'];
    $anneeDebut = 2025;
    $statut = 'SAISIE';

    if ($nature === 'portfolio&stage') {
        $idTuteur = $_POST['Tuteur'];
        $secondEns = $_POST['SecondEnseignant'];

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
            // INSERT stage
            $sql = "INSERT INTO EvalStage 
                (date_h, IdEtudiant, IdEnseignantTuteur, IdSecondEnseignant, IdSalle, anneeDebut, IdModeleEval, Statut, note, commentaireJury, presenceMaitreStageApp, confidentiel)
                VALUES (:date, :idEtudiant, :tuteur, :second, :salle, :annee, 1, :statut, NULL, NULL, 0, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'date' => $date,
                'idEtudiant' => $idEtudiant,
                'tuteur' => $idTuteur,
                'second' => $secondEns,
                'salle' => $salle,
                'annee' => $anneeDebut,
                'statut' => $statut
            ]);
            header("Location: ../mainAdministration.php?added=1");
            exit;
        }
    }

    if ($nature === 'anglais') {
        $ens = $_POST['SecondEnseignant'];

        
        $sql = "INSERT INTO EvalAnglais 
            (dateS, IdEtudiant, IdEnseignant, IdSalle, anneeDebut, note, Statut, IdModeleEval)
            VALUES (:date, :idEtudiant, :ens, :salle, :annee, NULL, :statut, 1)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'idEtudiant' => $idEtudiant,
            'ens' => $ens,
            'salle' => $salle,
            'annee' => $anneeDebut,
            'statut' => $statut,
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
        <link rel="stylesheet" href="/projet_sql/stylee.css">
    </head>
    
<body>
<?php include '../navbar.php'; ?>
<h2>Ajout d'une Soutenance</h2>
<form method="post">
    <?php if (!$estBut3 || $estBut3 && $type === 'stage'): ?> 
        <h3 value="portfolio&stage">Nature : Portfolio & Stage</h3> 
    <?php endif; ?> 
        
    <?php if ($estBut3 && $type === 'anglais'): ?> 
        <h3 value="anglais">Nature : Anglais</h3> 
    <?php endif; ?> 

   <label>Date et heure :</label>
<input type="datetime-local" name="DateSoutenance" id="DateSoutenance"><br>

<label>Salle :</label>
<select name="Salle" id="salleSelect">
    <option value="">Choisir...</option>
</select><br>

<div id="tuteurGroup">
    <label id="tuteurLabel">Tuteur (Stage/Portfolio) :</label>
    <select name="Tuteur" id="tuteurSelect">
        <?php foreach ($listeEnseignant as $e): ?>
            <option value="<?= $e['IdEnseignant'] ?>">
                <?= htmlspecialchars($e['nom']." ".$e['prenom']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
</div>

<div id="secondEnsGroup">
    <label>Second enseignant :</label>
    <select name="SecondEnseignant" id="secondEnsSelect">
        <?php foreach ($listeEnseignant as $e): ?>
            <option value="<?= $e['IdEnseignant'] ?>">
                <?= htmlspecialchars($e['nom']." ".$e['prenom']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
</div>


    <button type="submit">Enregistrer</button>
</form>

<p><a href="../mainAdministration.php">← Retour</a></p>
</body>
</html>




    <script>
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

// ⚡ Adapter les champs si anglais sélectionné
function adapterChampsNature() {
    const nature = document.querySelector('select[name="NatureSoutenance"]').value;
    const tuteurLabel = document.getElementById("tuteurLabel");
    const secondGroup = document.getElementById("secondEnsGroup");

    if (nature === "anglais") {
        tuteurLabel.textContent = "Enseignant :";
        secondGroup.style.display = "none"; // on cache le champ second enseignant
    } else {
        tuteurLabel.textContent = "Tuteur (Stage/Portfolio) :";
        secondGroup.style.display = "block";
    }
}

// événements
document.getElementById('DateSoutenance').addEventListener('change', chargerSalles);
document.querySelector('select[name="NatureSoutenance"]').addEventListener('change', adapterChampsNature);

// appel initial
adapterChampsNature();
</script>
