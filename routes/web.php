<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DistanceGoalController;
use App\Http\Controllers\StravaController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',  [DistanceGoalController::class, 'welcome'])->name('welcome');
Route::get('/refresh/goals',  [StravaController::class, 'refreshGoals'])->name('refresh.goals');

Route::get('/dashboard', [StravaController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/strava/auth', [StravaController::class, 'stravaAuth'])->middleware(['auth', 'verified'])->name('strava.auth');
Route::get('/strava/sync', [StravaController::class, 'syncActivities'])->middleware(['auth', 'verified'])->name('strava.sync');
Route::get('/strava/sync/all', [StravaController::class, 'syncAllActivities'])->middleware(['auth', 'verified'])->name('strava.sync.all');

Route::get('/stats', [StatsController::class, 'index'])->middleware(['auth', 'verified'])->name('stats');
Route::get('/stats/{year}', [StatsController::class, 'monthStats'])->middleware(['auth', 'verified'])->name('stats.months');

Route::get('/stats/{year}/{week}', [DistanceGoalController::class, 'weekStat'])->middleware(['auth', 'verified'])->name('stats.weeks');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('distance_goal', DistanceGoalController::class)->middleware(['auth', 'verified'])->name('index', 'distance_goal');

require __DIR__.'/auth.php';
