<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(PushNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Envoyer une notification broadcast à tous les utilisateurs
     * Endpoint: POST /api/v1/admin/notifications/broadcast
     * Authentification: Requise (admin seulement)
     */
    public function sendBroadcast(Request $request)
    {
        // Vérifier que l'utilisateur est admin (vous pouvez adapter selon votre logique)
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'image' => 'nullable|url',
            'icon' => 'nullable|string',
            'action' => 'nullable|array',
            'target_audience' => 'nullable|in:all,filtered,specific',
            'filters' => 'nullable|array',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:utilisateurs,id',
            'schedule_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $targetAudience = $request->input('target_audience', 'all');
            $scheduleAt = $request->input('schedule_at');

            // Créer la notification
            $notification = PushNotification::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $scheduleAt ? 'scheduled' : 'instant',
                'category' => 'admin',
                'target_audience' => $targetAudience,
                'filters' => $request->filters ? json_encode($request->filters) : null,
                'action' => $request->action ? json_encode($request->action) : null,
                'image' => $request->image,
                'icon' => $request->icon ?? 'announcement',
                'status' => $scheduleAt ? 'scheduled' : 'pending',
                'scheduled_at' => $scheduleAt,
            ]);

            Log::info("Admin broadcast notification created", [
                'notification_id' => $notification->id,
                'admin_id' => $user->id,
                'target_audience' => $targetAudience,
            ]);

            // Si c'est programmé, ne pas envoyer maintenant
            if ($scheduleAt) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification programmée avec succès',
                    'data' => [
                        'notification_id' => $notification->id,
                        'scheduled_at' => $notification->scheduled_at,
                    ],
                ], 201);
            }

            // Envoyer immédiatement selon le type de cible
            if ($targetAudience === 'specific' && $request->user_ids) {
                // Envoyer à des utilisateurs spécifiques
                $users = Utilisateur::whereIn('id', $request->user_ids)
                    ->whereNotNull('onesignal_player_id')
                    ->get();

                dispatch(function () use ($notification, $users) {
                    $service = app(\App\Services\Push\OneSignalService::class);
                    $service->sendToUsers($users->toArray(), $notification);
                })->afterResponse();

                $recipientCount = $users->count();
            } else {
                // Envoyer à tous ou filtrés (en batch)
                dispatch(function () use ($notification) {
                    $this->notificationService->sendNotificationInBatches($notification, 100);
                })->afterResponse();

                // Compter les destinataires potentiels
                $recipientCount = Utilisateur::whereNotNull('onesignal_player_id')
                    ->where('status', true)
                    ->count();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès',
                'data' => [
                    'notification_id' => $notification->id,
                    'potential_recipients' => $recipientCount,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error("Failed to send admin broadcast notification", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi de la notification',
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques des notifications envoyées
     * Endpoint: GET /api/v1/admin/notifications/stats
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        try {
            $stats = [
                'total_sent' => PushNotification::where('status', 'sent')->count(),
                'total_pending' => PushNotification::where('status', 'pending')->count(),
                'total_scheduled' => PushNotification::where('status', 'scheduled')->count(),
                'total_failed' => PushNotification::where('status', 'failed')->count(),
                'total_opens' => PushNotification::sum('opened_count'),
                'total_clicks' => PushNotification::sum('clicked_count'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to fetch notification stats", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
            ], 500);
        }
    }
}
