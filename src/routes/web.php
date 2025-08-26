<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ChatController;


Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/purchase/success', function () {
    return view('items.purchase_success');
})->name('purchase.success');

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
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/profile', function () {
    $user = Auth::user();

    $profile = $user->profile()->firstOrCreate([
        'nickname'     => '',
        'phone'        => '',
        'postal_code'  => '',
        'address'      => '',
        'building'     => '',
        'image_path'   => 'images/default_user.png',
    ]);

    return view('items.profile', [
        'profile' => $user->profile,
    ]);
})->middleware('auth');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware(['auth'])->name('verification.notice');


Route::post('/purchase/checkout/{item_id}', [\App\Http\Controllers\PurchaseController::class, 'checkout'])->name('purchase.checkout');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/test-chat', function () {
    return view('items.chat', ['itemId' => 999]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/items/{item}/chat', [ChatController::class, 'show'])->name('items.chat.show');
});
