<?php

use App\Http\Controllers\APIArticleController;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\APIQuizController;
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
    ->group(function(){

        /*************************  AUTH    ***************************/

        Route::post('login', [APIAuthController::class, 'login']);

        Route::post('register', [APIAuthController::class, 'register']);

        Route::post('send-code-update-password', [APIAuthController::class, 'codePasswordUpdate']);
        Route::post('update-password', [APIAuthController::class, 'updatePassword']);

        Route::post('code-confirmation', [APIAuthController::class, 'codeConfirmation']);

        /*************************  CONFIG    ***************************/
        Route::get('config', [APIArticleController::class, 'config']);

        /*************************  ARTICLES    ***************************/
        Route::get('articles', [APIArticleController::class, 'index']);
        Route::get('articles/{slug}', [APIArticleController::class, 'show']);

        Route::get('articles/categorie/{articleId}', [APIArticleController::class, 'showByRubrique']);

        /*************************  SYNC Quiz    ***************************/

        Route::post('sync-quiz', [APIQuizController::class, 'sync']);

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
