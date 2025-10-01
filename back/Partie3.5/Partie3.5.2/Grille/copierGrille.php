<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "evaluationstages";    

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}


///////////////////////////////////////////////// COPIER UNE GRILLE ////////////////////////////////////////////////////////////


// Vérifier qu'on a bien reçu l'id de la grille à copier
if (!isset($_GET['id_grille'])) {
    die("Erreur : aucune grille spécifiée.");
}

$id_grille_source = intval($_GET['id_grille']);


////////////////////////////////////////////////////////
// Étape 1 : Récupérer les infos de la grille source
////////////////////////////////////////////////////////


$sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = $id_grille_source";
$res = $conn->query($sql);

if ($res->num_rows === 0) {
    die("Erreur : Grille source introuvable.");
}
$grille_source = $res->fetch_assoc();


////////////////////////////////////////////////////////
// Étape 2 : Déterminer la nature de la nouvelle grille
////////////////////////////////////////////////////////


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    ?>
    <h2>Copier la grille : <?php echo htmlspecialchars($grille_source['nomModuleGrilleEvaluation']); ?></h2>
    <form method="POST">
        <label>Choisir la nature de la nouvelle grille :</label>
        <select name="nouvelle_nature" required>
            <option value="anglais">ANGLAIS</option>
            <option value="rapport">RAPPORT</option>
            <option value="soutenance">SOUTENANCE</option>
            <option value="stage">STAGE</option>
            <option value="portfolio">PORTFOLIO</option>
        </select>
        <button type="submit">📋 Copier</button>
    </form>

    <?php echo "<a href='../Grille.php'>📂 Retour aux grilles</a>"; ?>
    <?php
    exit;
}

$nouvelle_nature = $_POST['nouvelle_nature'];

// ⚡ Forcer l’année courante
$anneeCourante = date("Y");


////////////////////////////////////////////////////////
// Étape 3 : Créer une copie de la grille
////////////////////////////////////////////////////////


$baseName = $grille_source['nomModuleGrilleEvaluation'] . " (copie)";
$newName  = $baseName;
$i = 1;
while (true) {
    $check = $conn->query("SELECT 1 FROM modelesgrilleeval WHERE nomModuleGrilleEvaluation = '" . $conn->real_escape_string($newName) . "'");
    if ($check->num_rows == 0) break; // nom dispo
    $newName = $baseName . " #" . $i;
    $i++;
}

$sql_new = "INSERT INTO modelesgrilleeval (natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut) 
            VALUES ('" . $conn->real_escape_string($nouvelle_nature) . "',
                    '" . $grille_source['noteMaxGrille'] . "',
                    '" . $conn->real_escape_string($newName) . "',
                    '$anneeCourante')";
if (!$conn->query($sql_new)) {
    die("Erreur copie grille : " . $conn->error);
}
$id_grille_new = $conn->insert_id;


////////////////////////////////////////////////////////
// Étape 4 : Copier les sections de la grille //////////
////////////////////////////////////////////////////////


$sql_sections = "SELECT s.* 
                 FROM sectioncritereeval s
                 INNER JOIN sectionseval se ON se.IdSection = s.IdSection
                 WHERE se.IdModeleEval = $id_grille_source";
$res_sections = $conn->query($sql_sections);

while ($section = $res_sections->fetch_assoc()) {
    // Copier la section
    $sql_insert_section = "INSERT INTO sectioncritereeval (titre, description) 
                           VALUES ('" . $conn->real_escape_string($section['titre']) . "', 
                                   '" . $conn->real_escape_string($section['description']) . "')";
    if (!$conn->query($sql_insert_section)) {
        die("Erreur copie section : " . $conn->error);
    }
    $id_section_new = $conn->insert_id;

    // Lier la nouvelle section à la nouvelle grille
    $sql_link = "INSERT INTO sectionseval (IdSection, IdModeleEval) 
                 VALUES ($id_section_new, $id_grille_new)";
    $conn->query($sql_link);


    ////////////////////////////////////////////////////////
    // Étape 5 : Copier les critères de la section
    ////////////////////////////////////////////////////////


    $sql_criteres = "SELECT c.*, sc.valeurMaxCritereEval 
                     FROM critereseval c
                     INNER JOIN sectioncontenircriteres sc ON sc.IdCritere = c.IdCritere
                     WHERE sc.IdSection = " . $section['IdSection'];
    $res_criteres = $conn->query($sql_criteres);

    while ($critere = $res_criteres->fetch_assoc()) {
        // Copier le critère
        $sql_insert_crit = "INSERT INTO critereseval (descLongue, descCourte) 
                            VALUES ('" . $conn->real_escape_string($critere['descLongue']) . "', 
                                    '" . $conn->real_escape_string($critere['descCourte']) . "')";
        if (!$conn->query($sql_insert_crit)) {
            die("Erreur copie critère : " . $conn->error);
        }
        $id_critere_new = $conn->insert_id;

        // Lier le critère à la nouvelle section avec valeur max
        $sql_link_crit = "INSERT INTO sectioncontenircriteres (IdSection, IdCritere, valeurMaxCritereEval) 
                          VALUES ($id_section_new, $id_critere_new, " . intval($critere['valeurMaxCritereEval']) . ")";
        $conn->query($sql_link_crit);
    }
}

header("Location: ../Grille.php");

?>