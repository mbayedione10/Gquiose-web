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
     * Enregistrer le token FCM d'un utilisateur
     */
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'fcm_token' => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Utilisateur::find($request->user_id);
        $user->update([
            'fcm_token' => $request->fcm_token,
            'platform' => $request->platform,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM enregistré avec succès'
        ]);
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
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
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Utilisateur::find($request->user_id);
        
        $preferences = $user->notificationPreferences()->updateOrCreate(
            ['utilisateur_id' => $user->id],
            $request->except('user_id')
        );

        return response()->json([
            'success' => true,
            'preferences' => $preferences
        ]);
    }

    /**
     * Récupérer les préférences de notification
     */
    public function getPreferences($userId)
    {
        $user = Utilisateur::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $preferences = $user->notificationPreferences;

        return response()->json([
            'success' => true,
            'preferences' => $preferences
        ]);
    }

    /**
     * Tracker l'ouverture d'une notification
     */
    public function trackOpened(Request $request, $notificationId)
    {
        $notification = PushNotification::find($notificationId);
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée'
            ], 404);
        }

        $notification->increment('opened_count');

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Tracker le clic sur une notification
     */
    public function trackClicked(Request $request, $notificationId)
    {
        $notification = PushNotification::find($notificationId);
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée'
            ], 404);
        }

        $notification->increment('clicked_count');

        return response()->json([
            'success' => true
        ]);
    }
}
