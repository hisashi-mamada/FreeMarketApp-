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
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\RatingController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/success', [PurchaseController::class, 'success'])
        ->name('purchase.success');
});

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


// 認証ユーザー用チャット表示
Route::middleware(['auth'])->group(function () {
    Route::post('/items/{product}/chat/complete', [ChatController::class, 'complete'])
        ->name('items.chat.complete');
});

// 開発・動作テスト用
Route::get('/test-chat', function () {
    return view('items.chat', ['itemId' => 999]);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{id}', [ItemController::class, 'chat'])->name('chat');
    Route::post('/chat/{id}', [ItemController::class, 'chatComplete'])->name('chat.complete');
});


// プレビューや管理者など別用途があれば分けて記述
Route::get('/items/{id}/chat-preview', [ItemController::class, 'chat'])->name('items.chat.preview');


Route::get('/test-chat', function () {
    $product = Product::find(1);
    $user = User::find(2); // 適当なユーザー
    return view('items.chat', [
        'product' => $product,
        'partner' => $user,
        'messages' => [],
        'isSeller' => true,
        'isBuyer' => false,
        'isTradeComplete' => false
    ]);
});

Route::post('/chat/complete', [ItemController::class, 'complete'])->name('chat.complete');

Route::get('/products/{product}/chat-test', function (\App\Models\Product $product) {
    dd('到達OK', $product);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/items/{product}/chat', [ChatController::class, 'store'])->name('chat.message.store');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/items/{product}/chat/message', [ChatController::class, 'storeMessage'])->name('chat.message.store');
});

Route::middleware(['auth'])->group(function () {
    // 編集画面（フォーム表示）
    Route::get('/items/{product}/chat/message/{comment}/edit', [ChatController::class, 'edit'])
        ->name('chat.message.edit');

    // 更新処理
    Route::put('/items/{product}/chat/message/{comment}', [ChatController::class, 'update'])
        ->name('chat.message.update');

    // 削除処理
    Route::delete('/items/{product}/chat/message/{comment}', [ChatController::class, 'destroy'])
        ->name('chat.message.destroy');
});

Route::get('/items/{product}/chat', [ChatController::class, 'show'])
    ->name('items.chat.show');

Route::post('/items/{product}/chat/complete', [ChatController::class, 'complete'])
    ->name('items.chat.complete');

Route::middleware('auth')->group(function () {
    Route::post('/items/{product}/rate', [RatingController::class, 'store'])
        ->name('rate.store');
});
