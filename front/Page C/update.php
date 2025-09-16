<?php
/*
include("config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $idEtudiant = $_POST["idEtudiant"];
    $note = $_POST["note"];
    $commentaire = $_POST["commentaireJury"];
    $action = $_POST["action"];

    // Déterminer le nouveau statut
    if ($action == "valider") {
        $statut = "VALIDEE";
    } else {
        $statut = "SAISIE";
    }

    switch ($type) {
        case "portfolio":
            $id = $_POST["id"];
            $stmt = $mysqli->prepare("UPDATE EvalPortFolio SET note=?, commentaireJury=?, Statut=? WHERE IdEvalPortfolio=?");
            $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
            break;

        case "rapport":
            $id = $_POST["id"];
            $stmt = $mysqli->prepare("UPDATE EvalRapport SET note=?, commentaireJury=?, Statut=? WHERE IdEvalRapport=?");
            $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
            break;

        case "soutenance":
            $id = $_POST["id"];
            $stmt = $mysqli->prepare("UPDATE EvalSoutenance SET note=?, commentaireJury=?, Statut=? WHERE IdEvalSoutenance=?");
            $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
            break;

        case "anglais":
            $id = $_POST["id"];
            $stmt = $mysqli->prepare("UPDATE EvalAnglais SET note=?, commentaireJury=?, Statut=? WHERE IdEvalAnglais=?");
            $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
            break;

        case "stage":
            $id = $_POST["id"];
            $stmt = $mysqli->prepare("UPDATE EvalStage SET note=?, commentaireJury=?, Statut=? WHERE IdEvalStage=?");
            $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
            break;
    }

    // Peut etre enlevé?
    if ($stmt->execute()) {
        echo "Mise à jour réussie pour $type !";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    header("Location: index.php");
    exit();
}
*/

include("config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $idEtudiant = intval($_POST["idEtudiant"]);
    $note = isset($_POST["note"]) ? floatval($_POST["note"]) : null;
    $commentaire = isset($_POST["commentaireJury"]) ? $_POST["commentaireJury"] : "";
    $action = $_POST["action"];
    $id = intval($_POST["id"]);

    $statut = "SAISIE"; // valeur par défaut

    try {
        switch ($type) {
            case "portfolio":
                if ($action === "valider") {
                    $statut = "BLOQUEE"; // règle spécifique
                } elseif ($action === "debloquer") {
                    $statut = "SAISIE"; // repasse en saisie
                }
                $stmt = $mysqli->prepare("UPDATE EvalPortFolio SET note=?, commentaireJury=?, Statut=? WHERE IdEvalPortfolio=?");
                $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
                break;

            case "rapport":
                if ($action === "valider") {
                    $statut = "VALIDEE";
                } elseif ($action === "debloquer") {
                    $statut = "SAISIE";
                }
                $stmt = $mysqli->prepare("UPDATE EvalRapport SET note=?, commentaireJury=?, Statut=? WHERE IdEvalRapport=?");
                $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
                break;

            case "soutenance":
                if ($action === "valider") {
                    $statut = "VALIDEE";
                } elseif ($action === "debloquer") {
                    $statut = "SAISIE";
                }
                $stmt = $mysqli->prepare("UPDATE EvalSoutenance SET note=?, commentaireJury=?, Statut=? WHERE IdEvalSoutenance=?");
                $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
                break;

            case "anglais":
                if ($action === "valider") {
                    $statut = "VALIDEE";
                } elseif ($action === "debloquer") {
                    $statut = "SAISIE";
                }
                $stmt = $mysqli->prepare("UPDATE EvalAnglais SET note=?, commentaireJury=?, Statut=? WHERE IdEvalAnglais=?");
                $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
                break;

            case "stage":
                if ($action === "valider") {
                    // Vérifier que rapport et soutenance sont validés
                    $res1 = $mysqli->query("SELECT Statut FROM EvalRapport WHERE IdEtudiant=$idEtudiant");
                    $res2 = $mysqli->query("SELECT Statut FROM EvalSoutenance WHERE IdEtudiant=$idEtudiant");

                    $rapport = $res1->fetch_assoc()["Statut"] ?? "";
                    $soutenance = $res2->fetch_assoc()["Statut"] ?? "";

                    if ($rapport === "VALIDEE" && $soutenance === "VALIDEE") {
                        $statut = "BLOQUEE";

                        // Bloquer aussi rapport et soutenance
                        $mysqli->query("UPDATE EvalRapport SET Statut='BLOQUEE' WHERE IdEtudiant=$idEtudiant");
                        $mysqli->query("UPDATE EvalSoutenance SET Statut='BLOQUEE' WHERE IdEtudiant=$idEtudiant");
                    } else {
                        die("❌ Impossible de valider le stage tant que rapport et soutenance ne sont pas validés !");
                    }
                } elseif ($action === "debloquer") {
                    $statut = "SAISIE";

                    // Débloquer aussi rapport et soutenance
                    $mysqli->query("UPDATE EvalRapport SET Statut='SAISIE' WHERE IdEtudiant=$idEtudiant AND Statut='BLOQUEE'");
                    $mysqli->query("UPDATE EvalSoutenance SET Statut='SAISIE' WHERE IdEtudiant=$idEtudiant AND Statut='BLOQUEE'");
                }

                $stmt = $mysqli->prepare("UPDATE EvalStage SET note=?, commentaireJury=?, Statut=? WHERE IdEvalStage=?");
                $stmt->bind_param("dssi", $note, $commentaire, $statut, $id);
                break;

            default:
                die("❌ Type de grille inconnu !");
        }

        if ($stmt->execute()) {
            echo "✅ Mise à jour réussie pour $type ($action) !";
        } else {
            echo "❌ Erreur : " . $stmt->error;
        }

    } catch (Exception $e) {
        echo "❌ Exception : " . $e->getMessage();
    }

    // Retour vers index
    header("Location: index.php");
    exit();
}




?>
