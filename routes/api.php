<?php

use App\Http\Controllers\APIAlertController;
use App\Http\Controllers\APIArticleController;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\APIForumController;
use App\Http\Controllers\APIQuizController;
use App\Http\Controllers\APIStructureController;
use App\Http\Controllers\APIVideoController;
use App\Http\Controllers\NotificationTrackingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->middleware('log.route')
    ->group(function(){

        /*************************  AUTH    ***************************/

        Route::post('login', [APIAuthController::class, 'login']);
        Route::post('register', [APIAuthController::class, 'register']);
        Route::post('code-confirmation', [APIAuthController::class, 'codeConfirmation']);

        // Password reset
        Route::post('send-password-reset-code', [APIAuthController::class, 'sendPasswordResetCode']);
        Route::post('reset-password', [APIAuthController::class, 'resetPassword']);

        // Routes protégées par authentification
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [APIAuthController::class, 'logout']);
            Route::post('update-profile', [APIAuthController::class, 'updateProfile']);
            Route::post('change-password', [APIAuthController::class, 'changePassword']);
            Route::post('delete-account', [APIAuthController::class, 'deleteAccount']);

            // Notification tracking
            Route::prefix('notifications')->group(function () {
                Route::post('opened', [NotificationTrackingController::class, 'markAsOpened']);
                Route::post('clicked', [NotificationTrackingController::class, 'markAsClicked']);
                Route::get('history', [NotificationTrackingController::class, 'getHistory']);
            });

            // Push Notifications Routes
            Route::prefix('notifications')->group(function () {
                Route::post('/register-token', [App\Http\Controllers\APIPushNotificationController::class, 'registerToken']);
                Route::post('/preferences', [App\Http\Controllers\APIPushNotificationController::class, 'updatePreferences']);
                Route::get('/preferences/{userId}', [App\Http\Controllers\APIPushNotificationController::class, 'getPreferences']);
                Route::post('/{notificationId}/opened', [App\Http\Controllers\APIPushNotificationController::class, 'trackOpened']);
                Route::post('/{notificationId}/clicked', [App\Http\Controllers\APIPushNotificationController::class, 'trackClicked']);
            });
        });

        /*************************  CONFIG    ***************************/
        Route::get('config', [APIArticleController::class, 'config']);

        /*************************  ARTICLES    ***************************/
        Route::get('articles', [APIArticleController::class, 'index']);
        Route::get('articles/{slug}', [APIArticleController::class, 'show']);

        Route::get('articles/categorie/{articleId}', [APIArticleController::class, 'showByRubrique']);

        /*************************  SYNC Quiz    ***************************/
        Route::post('sync-quiz', [APIQuizController::class, 'sync']);

        /*************************  SYNC STRUCTURE    ***************************/
        Route::get('structures', [APIStructureController::class, 'list']);
        Route::get('structures/nearby', [APIStructureController::class, 'nearby']);

        /*************************  SYNC ALERTE    ***************************/
        Route::post('alert-sync', [APIAlertController::class, 'sync']);

        /*************************  WORKFLOW MULTI-ÉCRANS ALERTE VBG    ***************************/
        Route::prefix('alertes')->group(function () {
            // Récupérer les options pour le formulaire
            Route::get('workflow-options', [App\Http\Controllers\APIAlertWorkflowController::class, 'getWorkflowOptions']);

            // Workflow étapes
            Route::post('step1', [App\Http\Controllers\APIAlertWorkflowController::class, 'step1']);
            Route::post('step2', [App\Http\Controllers\APIAlertWorkflowController::class, 'step2']);
            Route::post('step3', [App\Http\Controllers\APIAlertWorkflowController::class, 'step3']);
            Route::get('step4/{alerte_id}', [App\Http\Controllers\APIAlertWorkflowController::class, 'step4']);
            Route::get('step5/{alerte_id}', [App\Http\Controllers\APIAlertWorkflowController::class, 'step5']);
            Route::post('step6', [App\Http\Controllers\APIAlertWorkflowController::class, 'step6']);
        });

        // Routes VBG amélioré - Téléchargement sécurisé des preuves et gestion conseils
        Route::middleware('auth:sanctum')->prefix('alertes')->group(function () {
            Route::get('{alerte}/evidence/{index}', [App\Http\Controllers\Api\AlerteController::class, 'downloadEvidence'])
                ->name('alertes.download-evidence');
            Route::post('{alerte}/mark-advice-read', [App\Http\Controllers\Api\AlerteController::class, 'markAdviceAsRead'])
                ->name('alertes.mark-advice-read');
        });

        /*************************  SYNC MESSAGE    ***************************/
        Route::post('message-sync', [APIForumController::class, 'syncMessage']);

        /*************************  SYNC CHAT    ***************************/
        Route::post('chat-sync', [APIForumController::class, 'syncChat']);

        /*************************  DELETE CHAT    ***************************/
        Route::post('chat-delete/{id}', [APIForumController::class, 'delete']);

        /*************************  FORUM    ***************************/
        Route::get('forum', [APIForumController::class, 'forum']);

        /*************************  VIDEO    ***************************/
        Route::get('videos', [APIVideoController::class, 'videos']);

        // Routes pour les évaluations
        Route::prefix('evaluations')->group(function () {
            Route::get('/questions', [App\Http\Controllers\APIEvaluationController::class, 'getQuestions']);
            Route::post('/submit', [App\Http\Controllers\APIEvaluationController::class, 'submit']);
            Route::get('/statistics', [App\Http\Controllers\APIEvaluationController::class, 'statistics']);
            Route::get('/user/{userId}', [App\Http\Controllers\APIEvaluationController::class, 'userEvaluations']);
        });

        // Forum routes
        Route::post('/forum/message/sync', [APIForumController::class, 'syncMessage']);
        Route::post('/forum/chat/sync', [APIForumController::class, 'syncChat']);
        Route::get('/forum', [APIForumController::class, 'forum']);
        Route::delete('/forum/chat/{id}', [APIForumController::class, 'delete']);

        // Cycle menstruel routes
        Route::prefix('cycle')->group(function () {
            Route::post('/start', [APICycleController::class, 'startPeriod']);
            Route::post('/end-period', [APICycleController::class, 'endPeriod']);
            Route::post('/log-symptoms', [APICycleController::class, 'logSymptoms']);
            Route::get('/current/{user_id}', [APICycleController::class, 'getCurrentCycle']);
            Route::get('/history/{user_id}', [APICycleController::class, 'getHistory']);
            Route::get('/symptoms/{user_id}', [APICycleController::class, 'getSymptoms']);
            Route::post('/settings', [APICycleController::class, 'updateSettings']);
            Route::post('/reminders', [APICycleController::class, 'configureReminders']);
        });



});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});