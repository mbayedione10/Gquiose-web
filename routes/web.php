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

