<?php
// Page d'accueil simple pour tester le système
// Partie 3.4 - Outils de diffusion des résultats

require_once 'config.php';

// Statistiques simples
$stats = [];

// Nombre d'étudiants candidats à la diffusion
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.IdEtudiant) as candidats
    FROM EtudiantsBUT2ou3 e
    INNER JOIN AnneeStage an ON e.IdEtudiant = an.IdEtudiant AND an.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalPortFolio ep ON e.IdEtudiant = ep.IdEtudiant AND ep.anneeDebut = YEAR(CURDATE())
    LEFT JOIN EvalAnglais ea ON e.IdEtudiant = ea.IdEtudiant AND ea.anneeDebut = YEAR(CURDATE())
    WHERE es.Statut = 'REMONTEE' 
    AND ep.Statut = 'REMONTEE'
    AND (an.but3sinon2 = FALSE OR ea.Statut = 'REMONTEE')
");
$stmt->execute();
$stats['candidats'] = $stmt->fetchColumn();

// Nombre d'étudiants déjà diffusés
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.IdEtudiant) as diffuses
    FROM EtudiantsBUT2ou3 e
    INNER JOIN EvalStage es ON e.IdEtudiant = es.IdEtudiant AND es.anneeDebut = YEAR(CURDATE())
    WHERE es.Statut = 'DIFFUSEE'
");
$stmt->execute();
$stats['diffuses'] = $stmt->fetchColumn();

// Nombre total d'étudiants en stage
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM AnneeStage 
    WHERE anneeDebut = YEAR(CURDATE())
");
$stmt->execute();
$stats['total'] = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partie 3.4 - Test du système</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header { 
            text-align: center; 
            margin-bottom: 40px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #007bff; 
        }
        .header h1 { 
            color: #333; 
            margin-bottom: 10px; 
            font-size: 2.5em;
        }
        .header p { 
            color: #666; 
            font-size: 1.2em; 
        }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin: 30px 0; 
        }
        .stat-card { 
            background: #f8f9fa; 
            padding: 25px; 
            border-radius: 10px; 
            text-align: center; 
            border: 2px solid #e9ecef;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-number { 
            font-size: 3em; 
            font-weight: bold; 
            color: #007bff; 
            margin-bottom: 10px; 
        }
        .stat-label { 
            color: #6c757d; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        .actions { 
            text-align: center; 
            margin: 40px 0; 
        }
        .btn { 
            display: inline-block; 
            background: #007bff; 
            color: white; 
            padding: 15px 30px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 18px; 
            text-decoration: none; 
            margin: 10px; 
            transition: all 0.3s ease;
        }
        .btn:hover { 
            background: #0056b3; 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        .btn-success { 
            background: #28a745; 
        }
        .btn-success:hover { 
            background: #218838; 
        }
        .btn-info { 
            background: #17a2b8; 
        }
        .btn-info:hover { 
            background: #138496; 
        }
        .info-section { 
            background: #e7f3ff; 
            padding: 25px; 
            border-radius: 10px; 
            margin: 30px 0; 
            border-left: 5px solid #007bff; 
        }
        .info-section h3 { 
            margin-top: 0; 
            color: #495057; 
        }
        .info-section ul { 
            margin: 15px 0; 
        }
        .info-section li { 
            margin: 8px 0; 
        }
        .test-section { 
            background: #fff3cd; 
            padding: 25px; 
            border-radius: 10px; 
            margin: 30px 0; 
            border-left: 5px solid #ffc107; 
        }
        .test-section h3 { 
            margin-top: 0; 
            color: #856404; 
        }
        .footer { 
            text-align: center; 
            margin-top: 40px; 
            padding-top: 20px; 
            border-top: 2px solid #e9ecef; 
            color: #6c757d; 
        }
        .demo-links { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0; 
        }
        .demo-links h4 { 
            margin-top: 0; 
            color: #495057; 
        }
        .demo-links a { 
            display: inline-block; 
            background: #6c757d; 
            color: white; 
            padding: 8px 16px; 
            border-radius: 5px; 
            text-decoration: none; 
            margin: 5px; 
            font-size: 14px; 
        }
        .demo-links a:hover { 
            background: #5a6268; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Partie 3.4 - Outils de diffusion des résultats</h1>
            <p>Test du système de diffusion des résultats aux étudiants</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Étudiants en stage</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['candidats'] ?></div>
                <div class="stat-label">Candidats à la diffusion</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['diffuses'] ?></div>
                <div class="stat-label">Déjà diffusés</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="partie_3_4_simple.php" class="btn btn-success">📧 Gérer la diffusion</a>
        </div>
        
        <div class="info-section">
            <h3>📋 Règles de diffusion</h3>
            <ul>
                <li><strong>BUT2 :</strong> Grilles de stage ET portfolio remontées</li>
                <li><strong>BUT3 :</strong> Grilles de stage ET portfolio ET anglais remontées</li>
                <li><strong>Action irréversible :</strong> Une fois diffusée, la grille ne peut plus être modifiée</li>
                <li><strong>Email automatique :</strong> Chaque étudiant reçoit un lien de consultation</li>
            </ul>
        </div>
        

            <p><a href="../mainAdministration.php">← Retour</a></p>

        
        
    </div>
</body>
</html>