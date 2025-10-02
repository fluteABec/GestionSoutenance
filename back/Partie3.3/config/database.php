<?php
/**
 * Configuration de la base de données
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'EvaluationStages';
    private $username = 'root';
    private $password = '';
    private $pdo;

    /**
     * Obtenir la connexion PDO
     */
    public function getPDO() {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return $this->pdo;
    }
}