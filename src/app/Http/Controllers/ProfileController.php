<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new \App\Models\Profile(); // 空のProfileで初期化
        return view('items.profile', compact('profile'));
    }


    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // 画像保存
        $imagePath = optional($user->profile)->image_path;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile_images', 'public');
            \Log::info('画像がアップロードされました: ' . $imagePath);
        }


        // 必須追加項目
        $validated += [
            'phone'       => '09012345678',
            'nickname'    => $validated['name'],
            'image_path'  => $imagePath,
            'building'    => $validated['building'] ?? '',
        ];

        unset($validated['name']);



        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('mypage.index')->with('message', 'プロフィールを更新しました');
    }
}
