<?php
/**
 * Gestionnaire de messages pour l'interface utilisateur
 */
class MessageManager {
    private $messages = [];

    /**
     * Ajoute un message de succès
     */
    public function addSuccess($message) {
        $this->messages[] = ['type' => 'success', 'text' => $message];
    }

    /**
     * Ajoute un message d'erreur
     */
    public function addError($message) {
        $this->messages[] = ['type' => 'error', 'text' => $message];
    }

    /**
     * Ajoute un message d'information
     */
    public function addInfo($message) {
        $this->messages[] = ['type' => 'info', 'text' => $message];
    }

    /**
     * Récupère tous les messages
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Vide la liste des messages
     */
    public function clearMessages() {
        $this->messages = [];
    }

    /**
     * Vérifie s'il y a des messages
     */
    public function hasMessages() {
        return !empty($this->messages);
    }
}