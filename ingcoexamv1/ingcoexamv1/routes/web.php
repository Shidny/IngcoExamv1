<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;

// Guest routes (only accessible if not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [BlogController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [BlogController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [BlogController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [BlogController::class, 'storeComment'])->name('posts.comments.store');
    Route::put('/comments/{comment}', [BlogController::class, 'updateComment'])->name('comments.update');
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});