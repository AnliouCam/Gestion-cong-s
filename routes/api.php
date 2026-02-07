<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EquipeController;
use App\Http\Controllers\Api\EmployeController;
use App\Http\Controllers\Api\CongeController;
use App\Http\Controllers\Api\PaieController;
use App\Http\Controllers\Api\PerformanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Toutes les routes définies ici sont automatiquement préfixées par /api
| Exemple : Route::post('/login') sera accessible via POST /api/login
|
*/

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES (sans authentification)
|--------------------------------------------------------------------------
| Ces routes sont accessibles sans token Sanctum
*/

// Authentification
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (avec authentification Sanctum)
|--------------------------------------------------------------------------
| Ces routes nécessitent un token Sanctum valide dans le header Authorization
| Format: Authorization: Bearer {token}
*/

Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);

    // Récupérer l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (STATISTIQUES)
    |--------------------------------------------------------------------------
    | Route pour récupérer toutes les statistiques du tableau de bord
    */
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | GESTION DES ÉQUIPES
    |--------------------------------------------------------------------------
    | Routes pour gérer les équipes (CRUD complet)
    */
    Route::apiResource('equipes', EquipeController::class);
    // Crée automatiquement les routes :
    // GET    /api/equipes           → index()   (liste)
    // POST   /api/equipes           → store()   (créer)
    // GET    /api/equipes/{id}      → show()    (détails)
    // PUT    /api/equipes/{id}      → update()  (modifier)
    // DELETE /api/equipes/{id}      → destroy() (supprimer)

    /*
    |--------------------------------------------------------------------------
    | GESTION DES EMPLOYÉS
    |--------------------------------------------------------------------------
    | Routes pour gérer les employés (CRUD complet + historique)
    */
    Route::apiResource('employes', EmployeController::class);
    // Crée automatiquement les routes :
    // GET    /api/employes          → index()   (liste)
    // POST   /api/employes          → store()   (créer)
    // GET    /api/employes/{id}     → show()    (détails)
    // PUT    /api/employes/{id}     → update()  (modifier)
    // DELETE /api/employes/{id}     → destroy() (supprimer)

    // Route supplémentaire pour l'historique complet d'un employé
    Route::get('employes/{id}/historique', [EmployeController::class, 'historique']);

    /*
    |--------------------------------------------------------------------------
    | GESTION DES CONGÉS
    |--------------------------------------------------------------------------
    | Routes pour gérer les demandes de congés (annuel, maladie, exceptionnel)
    */
    Route::apiResource('conges', CongeController::class)->only(['index', 'store', 'show']);
    // Crée les routes :
    // GET    /api/conges            → index()   (liste avec filtres)
    // POST   /api/conges            → store()   (créer une demande)
    // GET    /api/conges/{id}       → show()    (détail d'une demande)

    // Routes supplémentaires pour validation/refus
    Route::post('conges/{id}/valider', [CongeController::class, 'valider']);
    Route::post('conges/{id}/refuser', [CongeController::class, 'refuser']);

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PAIES
    |--------------------------------------------------------------------------
    | Routes pour enregistrer et consulter les paiements mensuels
    */
    Route::apiResource('paies', PaieController::class)->except(['update']);
    // Crée les routes :
    // GET    /api/paies             → index()   (liste avec filtres)
    // POST   /api/paies             → store()   (enregistrer un paiement)
    // GET    /api/paies/{id}        → show()    (détail d'un paiement)
    // DELETE /api/paies/{id}        → destroy() (supprimer)
    // Note : pas de update() → pas de modification après création (traçabilité)

    // Routes supplémentaires pour historique et employés non payés
    Route::get('paies/employe/{id}/historique', [PaieController::class, 'historiqueEmploye']);
    Route::get('paies/non-payes/liste', [PaieController::class, 'nonPayes']);

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PERFORMANCES
    |--------------------------------------------------------------------------
    | Routes pour évaluer et consulter les performances des employés
    */
    Route::apiResource('performances', PerformanceController::class);
    // Crée automatiquement les routes :
    // GET    /api/performances          → index()   (liste avec filtres)
    // POST   /api/performances          → store()   (créer une évaluation)
    // GET    /api/performances/{id}     → show()    (détail d'une évaluation)
    // PUT    /api/performances/{id}     → update()  (modifier une évaluation)
    // DELETE /api/performances/{id}     → destroy() (supprimer)

    // Routes supplémentaires pour historique et moyennes
    Route::get('performances/employe/{id}/historique', [PerformanceController::class, 'historiqueEmploye']);
    Route::get('performances/equipe/{id}/moyenne', [PerformanceController::class, 'moyenneEquipe']);
    Route::get('performances/mois/moyenne', [PerformanceController::class, 'moyenneMois']);
    Route::get('performances/mois/moyennes-par-equipe', [PerformanceController::class, 'moyennesParEquipe']);

});
