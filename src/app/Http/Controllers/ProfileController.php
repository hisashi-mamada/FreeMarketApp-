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
        $profile = Auth::user()->profile;
        return view('items.profile', compact('profile'));
    }

    public function update(ProfileRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        if ($user->profile) {
            $user->profile->update($validated);
        } else {
            $user->profile()->create($validated);
        }

        return redirect('/mypage')->with('message', 'プロフィールを更新しました');
    }
}
