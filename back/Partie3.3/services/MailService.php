<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Service pour l'envoi de mails
 */
class MailService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Envoie un mail
     */
    public function envoieMail($mailDestinataire, $sujet, $message, $fichierJoint = null) {
        $mail = new PHPMailer(true);

        try {
            // Config serveur SMTP Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'u1840518965@gmail.com';
            $mail->Password   = 'ooeo bavi hozw pndl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('u1840518965@gmail.com', 'IUT - Administration');
            $mail->addAddress($mailDestinataire);

            if ($fichierJoint && file_exists($fichierJoint)) {
                $mail->addAttachment($fichierJoint);
            }

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi du mail : {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Envoie un mail de rappel
     */
    public function rappelMail($mail) {
        if ($mail) {
            $sujet = "Rappel : évaluations doivent être validées";
            $message = "<p>Bonjour,<br>La soutenance de l'un de vos élèves est passée mais ses évaluations sont encore en <b>SAISIE</b>.<br>
            Merci de contacter votre enseignant référent.<br>Cordialement.</p>";
            return $this->envoieMail($mail, $sujet, $message);
        }
        return false;
    }

    /**
     * Récupère les mails des administrateurs
     */
    public function recupererMailsAdmin() {
        $stmt = $this->pdo->query("SELECT mail FROM `utilisateursbackoffice`");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}