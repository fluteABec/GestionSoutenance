<?php
$host = 'localhost';
$db   = 'evaluationstages';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$idEtudiant = $_GET['idEtudiant'];
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

// Liste des enseignants
$sql = "SELECT IdEnseignant, nom, prenom FROM Enseignants ORDER BY nom, prenom";
$stmt = $pdo->query($sql);
$listeEnseignant = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            FROM evalstage
            WHERE IdSalle = :salle
            AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
            UNION
            SELECT 'Salle', IdSalle FROM evalanglais
            WHERE IdSalle = :salle
            AND :date BETWEEN dateS AND DATE_ADD(dateS, INTERVAL 1 HOUR)

            UNION
            SELECT 'Etudiant', IdEtudiant FROM evalstage
            WHERE IdEtudiant = :idEtudiant
            AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
            UNION
            SELECT 'Etudiant', IdEtudiant FROM evalanglais
            WHERE IdEtudiant = :idEtudiant
            AND :date BETWEEN dateS AND DATE_ADD(dateS, INTERVAL 1 HOUR)

            UNION
            SELECT 'Tuteur', IdEnseignantTuteur FROM evalstage
            WHERE IdEnseignantTuteur = :tuteur
            AND :date BETWEEN date_h AND DATE_ADD(date_h, INTERVAL 1 HOUR)
            UNION
            SELECT 'Second', IdSecondEnseignant FROM evalstage
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
            $sql = "INSERT INTO evalstage 
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

        $sql = "INSERT INTO evalanglais 
            (dateS, IdEtudiant, IdEnseignant, IdSalle, anneeDebut, note, Statut)
            VALUES (:date, :idEtudiant, :ens, :salle, :annee, NULL, :statut)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'idEtudiant' => $idEtudiant,
            'ens' => $ens,
            'salle' => $salle,
            'annee' => $anneeDebut,
            'statut' => $statut
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
<div class="navbar">
    <div class="brand"><span class="logo"></span><span>Soutenances</span></div>
    <a class="nav-item" href="/projet_sql/back/Partie3.1/3_1_natan.php">Tâches enseignants</a>
    <a class="nav-item" href="/projet_sql/back/Partie3.3/index.php">Évaluations IUT</a>
    <a class="nav-item" href="/projet_sql/back/Partie3.4/index.php">Diffusion résultats</a>
    <a class="nav-item" href="/projet_sql/back/mainAdministration.php">Administration</a>
</div>
<h2>Ajout d'une Soutenance</h2>
<form method="post">
    <label>Nature :</label>
    <select name="NatureSoutenance">
        <option value="portfolio&stage">Portfolio & Stage</option>
        <option value="anglais">Anglais</option>
    </select><br>

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
