<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EventController; // Contrôleur public
use App\Http\Controllers\Admin\EventController as AdminEventController; // Contrôleur admin
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController; // Contrôleur catégories admin
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// ROUTE RACINE - REDIRECTION
// ============================================
Route::get('/', function () {
    return redirect()->route('events.index');
});

// ============================================
// ROUTES PUBLIQUES - ACCESSIBLES À TOUS
// ============================================
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// ============================================
// ROUTES AUTHENTIFICATION (Breeze)
// ============================================
require __DIR__.'/auth.php';

// ============================================
// ROUTES UTILISATEUR CONNECTÉ
// ============================================
Route::middleware(['auth'])->group(function () {
    // Profil utilisateur (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboard utilisateur
    
    
    // Inscriptions aux événements
    Route::post('/events/{event}/register', [EventController::class, 'register'])
        ->name('events.register');
    
    Route::delete('/events/{event}/unregister', [EventController::class, 'unregister'])
        ->name('events.unregister');
    
    // Mes inscriptions
    Route::get('/profile/registrations', [EventController::class, 'registrations'])
        ->name('profile.registrations');
});

// ============================================
// ROUTES ADMIN - PROTÉGÉES PAR MIDDLEWARE ADMIN
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Gestion des événements (CRUD complet)
    Route::resource('events', AdminEventController::class);
    
    // Gestion des catégories (CRUD complet)
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
});