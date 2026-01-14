<?php

namespace App\Services\SMS;

interface SMSInterface
{
    /**
     * Envoie un SMS
     *
     * @param  string  $to  Numéro de téléphone au format international
     * @param  string  $message  Contenu du message
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(string $to, string $message): array;

    /**
     * Vérifie le statut d'un message envoyé
     *
     * @param  string  $messageId  ID du message
     * @return array ['status' => string, 'delivered' => bool]
     */
    public function getStatus(string $messageId): array;
}
