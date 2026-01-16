<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Enregistrer le Player ID
        $user->update([
            'onesignal_player_id' => $request->player_id,
            'platform' => $request->platform,
        ]);

        // Lier le Player ID à l'External User ID sur OneSignal
        try {
            $oneSignalService = app(\App\Services\Push\OneSignalService::class);
            $oneSignalService->setExternalUserId($request->player_id, (string) $user->id);
            
            \Illuminate\Support\Facades\Log::info("OneSignal External User ID set", [
                'user_id' => $user->id,
                'player_id' => $request->player_id,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to set OneSignal External User ID", [
                'user_id' => $user->id,
                'player_id' => $request->player_id,
                'error' => $e->getMessage(),
            ]);
            // Ne pas bloquer l'enregistrement si ça échoue
        }

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
     * Tracker l'ouverture d'une notification (avec body notification_id + log tracking)
     */
    public function markAsOpened(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:push_notifications,id',
            'log_id' => 'nullable|integer|exists:notification_logs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Trouver le log correspondant
            if ($request->log_id) {
                $log = NotificationLog::where('id', $request->log_id)
                    ->where('utilisateur_id', $user->id)
                    ->first();
            } else {
                // Trouver le dernier log pour cette notification et cet utilisateur
                $log = NotificationLog::where('notification_schedule_id', $request->notification_id)
                    ->where('utilisateur_id', $user->id)
                    ->orderBy('sent_at', 'desc')
                    ->first();
            }

            if (! $log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification log not found',
                ], 404);
            }

            // Marquer comme ouvert
            $log->markAsOpened();

            // Mettre à jour les stats de la notification parent
            $notification = PushNotification::find($request->notification_id);
            if ($notification) {
                $notification->increment('opened_count');
            }

            Log::info("Notification {$request->notification_id} marked as opened by user {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as opened',
                'data' => [
                    'log_id' => $log->id,
                    'opened_at' => $log->opened_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as opened: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while tracking notification',
            ], 500);
        }
    }

    /**
     * Tracker le clic sur une notification (avec body notification_id + log tracking)
     */
    public function markAsClicked(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:push_notifications,id',
            'log_id' => 'nullable|integer|exists:notification_logs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Trouver le log correspondant
            if ($request->log_id) {
                $log = NotificationLog::where('id', $request->log_id)
                    ->where('utilisateur_id', $user->id)
                    ->first();
            } else {
                // Trouver le dernier log pour cette notification et cet utilisateur
                $log = NotificationLog::where('notification_schedule_id', $request->notification_id)
                    ->where('utilisateur_id', $user->id)
                    ->orderBy('sent_at', 'desc')
                    ->first();
            }

            if (! $log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification log not found',
                ], 404);
            }

            // Marquer comme cliqué (et ouvert si pas déjà fait)
            $log->markAsClicked();

            // Mettre à jour les stats de la notification parent
            $notification = PushNotification::find($request->notification_id);
            if ($notification) {
                $notification->increment('clicked_count');

                // Incrémenter opened_count si pas déjà fait
                if (! $log->opened_at) {
                    $notification->increment('opened_count');
                }
            }

            Log::info("Notification {$request->notification_id} marked as clicked by user {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as clicked',
                'data' => [
                    'log_id' => $log->id,
                    'opened_at' => $log->opened_at,
                    'clicked_at' => $log->clicked_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as clicked: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while tracking notification',
            ], 500);
        }
    }

    /**
     * Tracker l'ouverture d'une notification (méthode simple avec ID dans URL)
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
     * Tracker le clic sur une notification (méthode simple avec ID dans URL)
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

    /**
     * Récupérer l'historique des notifications de l'utilisateur connecté
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:100',
            'type' => 'string|in:automatic,manual,scheduled',
            'category' => 'string',
            'status' => 'string|in:pending,sent,delivered,opened,clicked,failed',
            'from_date' => 'date',
            'to_date' => 'date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = $request->input('per_page', 20);

        $query = NotificationLog::where('utilisateur_id', $user->id)
            ->orderBy('sent_at', 'desc');

        // Filtres optionnels
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('sent_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('sent_at', '<=', $request->to_date);
        }

        $notifications = $query->paginate($perPage);

        // Enrichir les données avec la notification parent pour avoir related_type, related_id, category
        $enrichedData = $notifications->getCollection()->map(function ($log) {
            $notification = PushNotification::find($log->notification_schedule_id);
            
            $data = $log->toArray();
            
            // Ajouter les informations de deep linking depuis la notification parent
            if ($notification) {
                $data['related_type'] = $notification->related_type;
                $data['related_id'] = $notification->related_id;
                $data['category'] = $notification->category;
                $data['action'] = $notification->action;
            }
            
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $enrichedData,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }
}
