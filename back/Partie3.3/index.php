<!-- Désoler le code est un vrai bordel mais il fonctionne en 3 parties :
le html et le CSS
les fonction php avec requête SQL et les fonctionnalités (c'est tout en fonction)
l'affichage des résultats sous forme de tableau -->




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Évaluations - IUT</title>

<!--------------------------------------------------------
 Tout le CSS de la page (générer par IA)
------------------------------------------------------ -->

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            color: #333;
        }
        header {
            background-color: #003087;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header img {
            height: 50px;
        }
        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h2 {
            color: #003087;
            border-bottom: 2px solid #003087;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #e6ecf5;
            color: #003087;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            color: #005b99;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .no-data {
            color: #777;
            font-style: italic;
        }
        footer {
            background-color: #003087;
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Évaluations - IUT</h1>
    </header>
    <div class="container">
        <?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




        // Connexion
        function getPDO() {
            return new PDO('mysql:host=localhost;dbname=EvaluationStages;charset=utf8', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
//------------------------------------------------------
// Fonctions SQL et fonctionnement du site
//------------------------------------------------------
        
function getEtudiantsBUT2($pdo){ // récupère les étudiants BUT2 prêts à la remontée
    $stmd = $pdo->query("
        SELECT e.IdEtudiant, e.nom, e.prenom,
               s.Statut AS statut_stage,
               p.Statut AS statut_portfolio
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = FALSE
          AND s.Statut = 'BLOQUEE'
          AND p.Statut = 'BLOQUEE'
    ");
    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}

function getEtudiantsBUT3($pdo){ // récupère les étudiants BUT3 prêts à la remontée
    $stmd = $pdo->query("
        SELECT e.IdEtudiant, e.nom, e.prenom,
               s.Statut AS statut_stage,
               p.Statut AS statut_portfolio,
               a.Statut AS statut_anglais
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = TRUE
          AND s.Statut = 'BLOQUEE'
          AND p.Statut = 'BLOQUEE'
          AND a.Statut = 'BLOQUEE'
    ");
    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}
function getEtudiantsNonBloques($pdo){ //récupère les étudiant avec au moin 1 statut saisi
    $stmd = $pdo->query("
        SELECT e.IdEtudiant, e.nom, e.prenom,
               s.Statut AS statut_stage,
               p.Statut AS statut_portfolio,
               COALESCE(a.Statut, 'NON CONCERNE') AS statut_anglais,
               s.date_h AS date_soutenance
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        LEFT JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE s.date_h < NOW()
          AND (s.Statut = 'SAISIE' OR p.Statut = 'SAISIE' OR (ast.but3sinon2 = TRUE AND a.Statut = 'SAISIE'))
    ");
    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}
function remonterNotes($pdo, $idEtudiant, $isBUT3 = false) { // remonte les notes d'un étudiant et envoie un mail
    $stmd = $pdo->prepare("UPDATE EvalStage SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
    $stmd->execute([$idEtudiant]);
    $stmd = $pdo->prepare("UPDATE EvalPortfolio SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
    $stmd->execute([$idEtudiant]);
    if ($isBUT3) {
        $stmd = $pdo->prepare("UPDATE EvalAnglais SET Statut = 'REMONTEE' WHERE IdEtudiant = ? AND Statut = 'BLOQUEE'");
        $stmd->execute([$idEtudiant]);
    }
    $mail = getMailEtudiant($pdo, $idEtudiant);
    if ($mail) {
        $sujet = "Vos évaluations ont été remonté";
        $message = "<p>Bonjour,<br>Vos notes ont été <b>remontées</b> par l'administration.<br>Cordialement.</p>";
        envoieMail($mail, $sujet, $message);
    }
}
function getEtudiantRemonter2A($pdo) // récupère les étudiants BUT2 déjà remontés
{
    $stmd = $pdo->query("SELECT e.IdEtudiant, e.nom, e.prenom,
               s.Statut AS statut_stage,
               p.Statut AS statut_portfolio
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = FALSE
          AND s.Statut = 'REMONTEE'
          AND p.Statut = 'REMONTEE';");
    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}
function getEtudiantRemonter3A($pdo) // récupère les étudiants BUT3 déjà remontés
{
    $stmd = $pdo->query("SELECT e.IdEtudiant, e.nom, e.prenom,
               s.Statut AS statut_stage,
               p.Statut AS statut_portfolio
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = TRUE
          AND s.Statut = 'REMONTEE'
          AND p.Statut = 'REMONTEE';");
    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}
function bloquerNotes($pdo, $idEtudiant, $isBUT3 = false) { // rebloque les notes d'un étudiant et envoie un mail
    $stmd = $pdo->prepare("UPDATE EvalStage SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
    $stmd->execute([$idEtudiant]);
    $stmd = $pdo->prepare("UPDATE EvalPortfolio SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
    $stmd->execute([$idEtudiant]);
    if ($isBUT3) {
        $stmd = $pdo->prepare("UPDATE EvalAnglais SET Statut = 'BLOQUEE' WHERE IdEtudiant = ? AND Statut = 'REMONTEE'");
        $stmd->execute([$idEtudiant]);
    }
    $mail = getMailEtudiant($pdo, $idEtudiant);
    if ($mail) {
        $sujet = "Vos évaluations ont été bloqué";
        $message = "<p>Bonjour,<br>Vos notes ont été <b>bloquées</b> par l'administration.<br>Cordialement.</p>";
        envoieMail($mail, $sujet, $message);
    }
}

function envoieMail($mail_destinataire, $sujet, $message) {
    $mail = new PHPMailer(true);

    try {
        // Config serveur SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // Serveur SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'u1840518965@gmail.com';
        $mail->Password   = 'ooeo bavi hozw pndl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Expéditeur
        $mail->setFrom('u1840518965@gmail.com', 'IUT - Administration');

        // Destinataire
        $mail->addAddress($mail_destinataire);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        //echo "Mail envoyé avec succès à $mail_destinataire<br>";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi du mail : {$mail->ErrorInfo}";
    }
}

function getMailEtudiant($pdo, $idEtudiant) {  // récupère le mail d'un étudiant via son ID pour l'envoi de mail
    $stmd = $pdo->prepare("SELECT mail FROM EtudiantsBUT2ou3 WHERE IdEtudiant = ?");
    $stmd->execute([$idEtudiant]);
    return $stmd->fetchColumn();
}
function rappelMail($pdo, $idEtudiant) { // plus ou moins la même fonction que remonterNotes mais pour envoyer un mail de rappel
    $mail = getMailEtudiant($pdo, $idEtudiant);
    if ($mail) {
        $sujet = "Rappel : vos évaluations doivent être validé";
        $message = "<p>Bonjour,<br>Votre soutenance est passée mais vos évaluations sont encore en <b>SAISIE</b>.<br>
        Merci de contacter votre enseignant référent.<br>Cordialement.</p>";
        envoieMail($mail, $sujet, $message);
    }
}

function get_liste_eleve_remonter3A($pdo) {
    $stmd = $pdo->query("
    SELECT e.IdEtudiant, e.nom, e.prenom, e.mail,  a.note AS note_angalis, p.note AS note_portfolio, s.note AS note_stage
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN EvalAnglais a ON e.IdEtudiant = a.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = TRUE
          AND s.Statut = 'REMONTEE'
          AND p.Statut = 'REMONTEE'
          AND a.Statut = 'REMONTEE'
    ");


    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}

function get_liste_eleve_remonter2A($pdo) {
    $stmd = $pdo->query("
    SELECT e.IdEtudiant, e.nom, e.prenom, e.mail,  p.note AS note_portfolio, s.note AS note_stage
        FROM EtudiantsBUT2ou3 e
        JOIN EvalStage s ON e.IdEtudiant = s.IdEtudiant
        JOIN EvalPortfolio p ON e.IdEtudiant = p.IdEtudiant
        JOIN AnneeStage ast ON e.IdEtudiant = ast.IdEtudiant
        WHERE ast.but3sinon2 = FALSE
          AND s.Statut = 'REMONTEE'
          AND p.Statut = 'REMONTEE';
    ");

    return $stmd->fetchAll(PDO::FETCH_ASSOC);
}



//------------------------------------------------------
// CODE PRINCIPALE
//------------------------------------------------------

        $pdo = getPDO();

        if (isset($_GET['action']) && isset($_GET['id'])) {
            $idEtudiant = (int)$_GET['id'];
            $isBUT3 = isset($_GET['but3']) && $_GET['but3'] == 1;

            if ($_GET['action'] === 'remonter') {
                remonterNotes($pdo, $idEtudiant, $isBUT3);
                echo "<div class='message'>Statuts remontés et mail envoyé à l'étudiant ID $idEtudiant</div>";
            }
            if ($_GET['action'] === 'bloquer') {
                bloquerNotes($pdo, $idEtudiant, $isBUT3);
                echo "<div class='message'>Statuts re-bloqués et mail envoyé à l'étudiant ID $idEtudiant</div>";
            }
            if ($_GET['action'] === 'rappel') {
                rappelMail($pdo, $idEtudiant);
                echo "<div class='message'>Mail de rappel envoyé à l'étudiant ID $idEtudiant</div>";
            }
        }

        if (isset($_POST['export_csv'])) {
            if ($_POST['export_csv'] === 'but2') {
                $liste = get_liste_eleve_remonter2A($pdo);
                $nom_fichier = "export_remontee_BUT2.csv";
            } else {
                $liste = get_liste_eleve_remonter3A($pdo);
                $nom_fichier = "export_remontee_BUT3.csv";
            }

            // Nettoie le tampon de sortie pour éviter d'inclure du HTML dans le CSV
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$nom_fichier\"");

            $output = fopen("php://output", "w");
            if (!empty($liste)) {
                fputcsv($output, array_keys($liste[0])); // en-têtes
                foreach ($liste as $ligne) {
                    fputcsv($output, $ligne);
                }
            }
            fclose($output);
            exit;
        }


        echo "<h2>Étudiants BUT2 prêts à la remontée :</h2>";
        $etudiantsBUT2 = getEtudiantsBUT2($pdo);
        if ($etudiantsBUT2) {
            echo "<table><tr>
                <th>Prénom</th><th>Nom</th><th>ID</th>
                <th>Stage</th><th>Portfolio</th><th>Action</th></tr>";
            foreach ($etudiantsBUT2 as $etudiant) {
                echo "<tr>
                    <td>{$etudiant['prenom']}</td>
                    <td>{$etudiant['nom']}</td>
                    <td>{$etudiant['IdEtudiant']}</td>
                    <td>{$etudiant['statut_stage']}</td>
                    <td>{$etudiant['statut_portfolio']}</td>
                    <td><a href='?action=remonter&id={$etudiant['IdEtudiant']}&but3=0'>Remonter</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-data'>Aucun étudiant BUT2 prêt.</p>";
        }

        echo "<h2>Étudiants BUT3 prêts à la remontée :</h2>";
        $etudiantsBUT3 = getEtudiantsBUT3($pdo);
        if ($etudiantsBUT3) {
            echo "<table><tr>
                <th>Prénom</th><th>Nom</th><th>ID</th>
                <th>Stage</th><th>Portfolio</th><th>Anglais</th><th>Action</th></tr>";
            foreach ($etudiantsBUT3 as $etudiant) {
                echo "<tr>
                    <td>{$etudiant['prenom']}</td>
                    <td>{$etudiant['nom']}</td>
                    <td>{$etudiant['IdEtudiant']}</td>
                    <td>{$etudiant['statut_stage']}</td>
                    <td>{$etudiant['statut_portfolio']}</td>
                    <td>{$etudiant['statut_anglais']}</td>
                    <td><a href='?action=remonter&id={$etudiant['IdEtudiant']}&but3=1'>Remonter</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-data'>Aucun étudiant BUT3 prêt.</p>";
        }

        echo "<h2>Étudiants en retard (soutenance passée, statut SAISIE) :</h2>";
        $etudiantsNonBloques = getEtudiantsNonBloques($pdo);
        if ($etudiantsNonBloques) {
            echo "<table><tr>
                <th>Prénom</th><th>Nom</th><th>ID</th>
                <th>Stage</th><th>Portfolio</th><th>Anglais</th><th>Date Soutenance</th><th>Action</th></tr>";
            foreach ($etudiantsNonBloques as $etudiant) {
                echo "<tr>
                    <td>{$etudiant['prenom']}</td>
                    <td>{$etudiant['nom']}</td>
                    <td>{$etudiant['IdEtudiant']}</td>
                    <td>{$etudiant['statut_stage']}</td>
                    <td>{$etudiant['statut_portfolio']}</td>
                    <td>{$etudiant['statut_anglais']}</td>
                    <td>{$etudiant['date_soutenance']}</td>
                    <td><a href='?action=rappel&id={$etudiant['IdEtudiant']}'>Envoyer mail</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-data'>Aucun étudiant en retard.</p>";
        }

        echo "<h2>Étudiants BUT2 déjà remontés :</h2>";
        $etudiantsRemonteeBUT2 = getEtudiantRemonter2A($pdo);
        if ($etudiantsRemonteeBUT2) {
            echo "<table><tr>
                <th>Prénom</th><th>Nom</th><th>ID</th>
                <th>Stage</th><th>Portfolio</th><th>Action</th></tr>";
            foreach ($etudiantsRemonteeBUT2 as $etudiant) {
                echo "<tr>
                    <td>{$etudiant['prenom']}</td>
                    <td>{$etudiant['nom']}</td>
                    <td>{$etudiant['IdEtudiant']}</td>
                    <td>{$etudiant['statut_stage']}</td>
                    <td>{$etudiant['statut_portfolio']}</td>
                    <td><a href='?action=bloquer&id={$etudiant['IdEtudiant']}&but3=0'>Bloquer</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-data'>Aucun étudiant BUT2 remonté.</p>";
        }

        echo "<h2>Étudiants BUT3 déjà remontés :</h2>";
        $etudiantsRemonteeBUT3 = getEtudiantRemonter3A($pdo);
        if ($etudiantsRemonteeBUT3) {
            echo "<table><tr>
                <th>Prénom</th><th>Nom</th><th>ID</th>
                <th>Stage</th><th>Portfolio</th><th>Action</th></tr>";
            foreach ($etudiantsRemonteeBUT3 as $etudiant) {
                echo "<tr>
                    <td>{$etudiant['prenom']}</td>
                    <td>{$etudiant['nom']}</td>
                    <td>{$etudiant['IdEtudiant']}</td>
                    <td>{$etudiant['statut_stage']}</td>
                    <td>{$etudiant['statut_portfolio']}</td>
                    <td><a href='?action=bloquer&id={$etudiant['IdEtudiant']}&but3=1'>Bloquer</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-data'>Aucun étudiant BUT3 remonté.</p>";
        }

        // Bouton de récupération des données 

        echo "<form method='post'>
        <button type='submit' name='export_csv' value='but2'>Exporter BUT2 en CSV</button>
        <button type='submit' name='export_csv' value='but3'>Exporter BUT3 en CSV</button> </form>";


        ?>

            <p><a href="../mainAdministration.php">← Retour</a></p>

    </div>
    <!-- test branch -->
</body>
</html>