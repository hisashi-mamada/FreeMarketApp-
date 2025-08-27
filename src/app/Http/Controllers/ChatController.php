<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show($itemId)
    {
        // 今は画面表示が目的なので、最低限だけ渡す
        return view('items.chat', [
            'itemId' => $itemId,
        ]);
    }
}
