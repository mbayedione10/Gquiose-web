<?php

use App\Http\Controllers\DeleteAccountController;
use App\Mail\NotificationEmail;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\RubriqueController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ThematiqueController;
use App\Http\Controllers\TypeAlerteController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\TypeStructureController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect(url('/admin'));
});

Route::redirect('/login', '/admin/login')->name('login');

Route::get('remove-my-account', [DeleteAccountController::class, 'form'])->name('remove.form');

Route::post('remove-my-account', [DeleteAccountController::class, 'remove'])->name('remove.account');

// Facebook Data Deletion Callback
Route::post('facebook/data-deletion', [DeleteAccountController::class, 'facebookDataDeletion'])
    ->name('facebook.data.deletion')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Route pour visualiser/télécharger les preuves depuis Filament (admin)
Route::get('preuves/alertes/{alerte}/{index}', function (App\Models\Alerte $alerte, int $index) {
    // Vérifier que l'utilisateur est connecté
    if (!auth()->check()) {
        abort(401);
    }

    $preuves = $alerte->preuves ?? [];

    if (!isset($preuves[$index])) {
        abort(404, 'Preuve introuvable');
    }

    $preuve = $preuves[$index];
    $evidenceService = app(App\Services\VBG\EvidenceSecurityService::class);
    $decryptedContent = $evidenceService->retrieveEvidence($preuve['path']);

    if (!$decryptedContent) {
        abort(500, 'Erreur de déchiffrement');
    }

    // Log d'accès
    \Log::info('Accès preuve Filament', [
        'alerte_id' => $alerte->id,
        'user_id' => auth()->id(),
        'evidence_index' => $index,
    ]);

    // Afficher directement dans le navigateur (inline) au lieu de télécharger
    return response($decryptedContent)
        ->header('Content-Type', $preuve['type'])
        ->header('Content-Disposition', 'inline; filename="' . $preuve['original_name'] . '"');
})->middleware(['auth', 'web'])->name('admin.alertes.preuve.download');

// Routes pour l'invitation admin
Route::get('admin/invitation/accept/{token}', [App\Http\Controllers\AdminInvitationController::class, 'showAcceptForm'])
    ->name('admin.invitation.accept');
Route::post('admin/invitation/accept/{token}', [App\Http\Controllers\AdminInvitationController::class, 'accept'])
    ->name('admin.invitation.accept.submit');
