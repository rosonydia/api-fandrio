<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Retourne une réponse JSON standardisée pour tous les contrôleurs
     *
     * @param int $errorCode Code d'erreur (0 pour succès, autre pour erreur)
     * @param mixed $response Données à retourner ou message d'erreur
     * @param string|null $customMessage Message personnalisé (optionnel)
     * @return JsonResponse
     */

    protected function ApiResponse(int $errorCode, $response = null, string $customMessage = null): JsonResponse {

        $successful = $errorCode === 0;

        // Message par défaut selon code d'erreur
        $defaultMessages = [
            0   => 'Opération réussie',
            400 => 'Requête invalide',
            401 => 'Non autorisé',
            403 => 'Accès refusé',
            404 => 'Ressource non trouvée',
            422 => 'Données de validation invalides',
            500 => 'Erreur interne du serveur',
            501 => 'Erreur de validation'
        ];

        // Déterminer le message
        if($customMessage) {
            $message = $customMessage;
        } else {
            $message = $defaultMessages[$errorCode] ?? 'Erreur inconnue';
        }

        // Structurer la réponse
        $responseData = [
            'successfull'   => $successful,
            'errorCode'     => $errorCode,
            'message'       => $message,
            'data'          => $successful ? $response : null
        ];

        // Déterminer le code du statut HTTP
        $httpStatusCode = $successful ? 200 : $errorCode;

        // Pour certains codes d'erreur, 
        if($errorCode >= 400 && $errorCode <= 599) {
            $httpStatusCode = $errorCode;
        } elseif(!$successful && $errorCode !== 0){
            $httpStatusCode = 500; // Erreur générique si code non standard
        }

        return response()->json($responseData, $httpStatusCode);
    }
}
