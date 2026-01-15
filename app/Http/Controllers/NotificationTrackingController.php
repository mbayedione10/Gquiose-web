<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationTrackingController extends Controller
{
    /**
     * Marquer une notification comme ouverte
     *
     * POST /api/notifications/opened
     * Body: { "notification_id": 123, "log_id": 456 }
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Marquer une notification comme cliquée
     *
     * POST /api/notifications/clicked
     * Body: { "notification_id": 123, "log_id": 456 }
     *
     * @return \Illuminate\Http\JsonResponse
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
}
