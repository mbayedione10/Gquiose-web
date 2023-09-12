<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('thematiques', ThematiqueController::class);
        Route::resource('questions', QuestionController::class);
        Route::resource('responses', ResponseController::class);
        Route::resource('rubriques', RubriqueController::class);
        Route::resource('articles', ArticleController::class);
        Route::resource('type-alertes', TypeAlerteController::class);
        Route::resource('alertes', AlerteController::class);
        Route::resource('villes', VilleController::class);
        Route::resource('type-structures', TypeStructureController::class);
        Route::resource('structures', StructureController::class);
        Route::resource('suivis', SuiviController::class);
        Route::resource('utilisateurs', UtilisateurController::class);
    });
