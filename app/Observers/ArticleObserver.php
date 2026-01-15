<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    /**
     * Gérer l'événement "created" pour les articles.
     * Envoyer une notification push lorsqu'un nouvel article est publié.
     */
    public function created(Article $article)
    {
        // Vérifier que l'article est publié (status = true)
        if (!$article->status) {
            return;
        }

        // Envoyer la notification de manière asynchrone
        $this->sendNewArticleNotification($article);
    }

    /**
     * Gérer l'événement "updated" pour les articles.
     * Envoyer une notification si l'article vient d'être publié.
     */
    public function updated(Article $article)
    {
        // Vérifier si l'article vient d'être publié (status passé de false à true)
        if ($article->status && $article->isDirty('status') && $article->getOriginal('status') == false) {
            $this->sendNewArticleNotification($article);
        }
    }

    /**
     * Envoyer une notification push pour un nouvel article
     */
    protected function sendNewArticleNotification(Article $article)
    {
        try {
            // Créer la notification
            $notification = PushNotification::create([
                'title' => '📰 Nouvel article publié',
                'message' => $article->title,
                'type' => 'instant',
                'category' => 'content',
                'target_audience' => 'filtered',
                'filters' => json_encode([
                    'content_notifications' => true, // Uniquement les utilisateurs avec content_notifications activé
                ]),
                'action' => json_encode([
                    'type' => 'article',
                    'article_id' => $article->id,
                    'slug' => $article->slug,
                ]),
                'image' => $article->image ? asset('storage/' . $article->image) : null,
                'icon' => 'article',
                'status' => 'pending',
            ]);

            Log::info("New article notification created", [
                'article_id' => $article->id,
                'notification_id' => $notification->id,
            ]);

            // Envoyer la notification en arrière-plan (batch)
            dispatch(function () use ($notification) {
                $service = app(PushNotificationService::class);
                $service->sendNotificationInBatches($notification, 100);
            })->afterResponse();

        } catch (\Exception $e) {
            Log::error("Failed to send notification for new article", [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
