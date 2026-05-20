<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PwdController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes — all roles
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PWD Registry — all roles can view; Encoder can create/edit
    Route::get('/pwd',            [PwdController::class, 'index'])  ->name('pwd.index');
    Route::get('/pwd/{pwd}',      [PwdController::class, 'pwdShow'])   ->name('pwd.show');

    Route::middleware('role:encoder,admin')->group(function () {
        Route::get('/pwd/create',        [PwdController::class, 'pwdCreate']) ->name('pwd.create');
        Route::post('/pwd',              [PwdController::class, 'pwdStore'])  ->name('pwd.store');
        Route::get('/pwd/{pwd}/edit',    [PwdController::class, 'pwdEdit'])   ->name('pwd.edit');
        Route::put('/pwd/{pwd}',         [PwdController::class, 'pwdUpdate']) ->name('pwd.update');
        Route::delete('/pwd/{pwd}',      [PwdController::class, 'pwdDestroy'])->name('pwd.destroy');
    });

    // Reports — all roles can view
    Route::get('/reports',        [ReportController::class, 'index']) ->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    /*
    |--------------------------------------------------------------------------
    | Admin-only routes
    |--------------------------------------------------------------------------
    |
    | Create RoleMiddleware at app/Http/Middleware/RoleMiddleware.php:
    |
    |   public function handle(Request $request, Closure $next, string ...$roles): Response
    |   {
    |       $userRole = strtolower(Auth::user()->usertype->name);
    |       if (!in_array($userRole, $roles)) abort(403, 'Unauthorized.');
    |       return $next($request);
    |   }
    |
    | Register in bootstrap/app.php (Laravel 11+):
    |
    |   ->withMiddleware(function (Middleware $middleware) {
    |       $middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);
    |   })
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });

});