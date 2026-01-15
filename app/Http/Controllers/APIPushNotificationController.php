<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIPushNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(PushNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Enregistrer le player_id OneSignal d'un utilisateur
     * Authentification requise - l'utilisateur connecté enregistre son propre player_id
     */
    public function registerToken(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'player_id' => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'onesignal_player_id' => $request->player_id,
            'platform' => $request->platform,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player ID OneSignal enregistré avec succès',
            'utilisateur' => $user->only(['id', 'onesignal_player_id', 'platform']),
        ]);
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function updatePreferences(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'notifications_enabled' => 'boolean',
            'cycle_notifications' => 'boolean',
            'content_notifications' => 'boolean',
            'forum_notifications' => 'boolean',
            'health_tips_notifications' => 'boolean',
            'admin_notifications' => 'boolean',
            'quiet_start' => 'nullable|date_format:H:i',
            'quiet_end' => 'nullable|date_format:H:i',
            'do_not_disturb' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $preferences = $user->notificationPreferences()->updateOrCreate(
            ['utilisateur_id' => $user->id],
            $request->only([
                'notifications_enabled',
                'cycle_notifications',
                'content_notifications',
                'forum_notifications',
                'health_tips_notifications',
                'admin_notifications',
                'quiet_start',
                'quiet_end',
                'do_not_disturb',
            ])
        );

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
            'message' => 'Préférences mises à jour avec succès',
        ]);
    }

    /**
     * Récupérer les préférences de notification
     */
    public function getPreferences(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $preferences = $user->notificationPreferences;

        // Si pas de préférences, retourner les valeurs par défaut
        if (! $preferences) {
            $preferences = [
                'notifications_enabled' => true,
                'cycle_notifications' => true,
                'content_notifications' => true,
                'forum_notifications' => true,
                'health_tips_notifications' => true,
                'admin_notifications' => true,
                'quiet_start' => null,
                'quiet_end' => null,
                'do_not_disturb' => false,
            ];
        }

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Tracker l'ouverture d'une notification
     */
    public function trackOpened(Request $request, $notificationId)
    {
        $notification = PushNotification::find($notificationId);

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $notification->increment('opened_count');

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Tracker le clic sur une notification
     */
    public function trackClicked(Request $request, $notificationId)
    {
        $notification = PushNotification::find($notificationId);

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $notification->increment('clicked_count');

        return response()->json([
            'success' => true,
        ]);
    }
}
