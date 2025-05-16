<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function edit($item_id)
    {
        $user = Auth::user();
        $address = $user->addresses()->latest()->first();
        return view('items.address', compact('address', 'item_id'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        $validated = $request->validated();
        $user = Auth::user();

        $fullAddress = $validated['address'];
        $prefecture = mb_substr($fullAddress, 0, 3);
        $city = mb_substr($fullAddress, 3, 3);
        $block = mb_substr($fullAddress, 6);

        $data = [
            'postal_code' => $validated['postal_code'],
            'prefecture' => $prefecture,
            'city' => $city,
            'block' => $block,
            'building' => $validated['building'] ?? null,
        ];

        $address = $user->addresses()->latest()->first();

        if ($address) {
            $address->update($data);
        } else {
            $user->addresses()->create($data);
        }

        return redirect("/purchase/{$item_id}")->with('message', '住所を更新しました');
    }
}
