<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DebtController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExportController;
use App\Http\Middleware\AdminMiddleware;

// Root URL: Redirect ke dashboard jika sudah login, atau ke login jika belum
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/mobile/login', [GoogleController::class, 'login']);
Route::get('/docs', function () {
    return view('docs.index');
})->name('docs.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/income', IncomeController::class)->except(['show']);
    Route::resource('/expense', ExpenseController::class)->except(['show']);
    Route::resource('/goals', GoalController::class)->except(['show']);
    Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
    Route::post('/goals/{goal}/transactions', [GoalController::class, 'storeTransaction'])->name('goals.storeTransaction');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('wallets', WalletController::class)->except(['show']);
    Route::post('/wallets/{wallet}/members', [WalletController::class, 'addMember'])->name('wallets.addMember');
    Route::delete('/wallets/{wallet}/members/{user}', [WalletController::class, 'removeMember'])->name('wallets.removeMember');

    // Family / Sub-User Sharing Routes
    Route::get('/family-members', [WalletController::class, 'familyMembers'])->name('family.members');
    Route::post('/family-members', [WalletController::class, 'storeFamilyMember'])->name('family.members.store');

    Route::resource('debts', DebtController::class)->except(['show']);
    Route::post('/debts/{debt}/convert-to-goal', [DebtController::class, 'convertToGoal'])->name('debts.convertToGoal');

    // Settings route
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Export & Backup & Import routes
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/categories', [ExportController::class, 'exportCategories'])->name('export.categories');
    Route::get('/export/incomes', [ExportController::class, 'exportIncomes'])->name('export.incomes');
    Route::get('/export/expenses', [ExportController::class, 'exportExpenses'])->name('export.expenses');
    Route::get('/export/wallets', [ExportController::class, 'exportWallets'])->name('export.wallets');
    Route::get('/export/full', [ExportController::class, 'exportFullBackup'])->name('export.full');
    Route::get('/export/template', [ExportController::class, 'downloadTemplate'])->name('export.template');
    Route::post('/export/import', [ExportController::class, 'importData'])->name('export.import');

    // Route for updating progress of a goal
    Route::put('/goals/{goal}/update-progress', [GoalController::class, 'updateProgress'])->name('goals.updateProgress');
});

// Admin routes
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/users/{user}/toggle-role', [AdminController::class, 'toggleRole'])->name('users.toggleRole');
});
require __DIR__.'/auth.php';
