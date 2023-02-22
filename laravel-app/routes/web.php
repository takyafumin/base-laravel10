<?php

use App\Http\Controllers\ProfileController;
use BugReport\UI\Http\Controller\BugReportController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * BugReport
 */
Route::group(['prefix' => 'bug-reports', 'as' => 'bug-reports.'], function () {
    Route::get('/', [BugReportController::class, 'index'])->name('index');
    Route::get('all', [BugReportController::class, 'all'])->name('all');
    //Route::post('/', [BugReportController::class, 'store'])->name('store');
    //Route::get('{bug_id}', [BugReportController::class, 'show'])->name('show');
    //Route::post('{bug_id}/update', [BugReportController::class, 'update'])->name('update');
    //Route::post('{bug_id}/delete', [BugReportController::class, 'destroy'])->name('destroy');
});

require __DIR__ . '/auth.php';
