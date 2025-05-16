<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
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


Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');
});
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::post('/item/{product}/favorite', [ItemController::class, 'toggleFavorite'])->name('favorites.toggle');
Route::post('/item/{product}/comment', [ItemController::class, 'addComment'])
    ->middleware('auth')
    ->name('comment.add');

Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::post('/favorites/{product_id}', [ItemController::class, 'addFavorite'])->name('favorites.add');
Route::post('/favorites/{product_id}/remove', [ItemController::class, 'removeFavorite'])->name('favorites.remove');

Route::get('/email-test', function () {
    $user = Auth::user();
    if ($user) {
        $user->sendEmailVerificationNotification();
        return '認証メールを送信しました。';
    }
    return 'ログインしてください。';
});


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/profile');
})->middleware(['signed'])->name('verification.verify');
