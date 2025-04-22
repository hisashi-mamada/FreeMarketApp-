<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = Auth::user()->profile;
        return view('mypage.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();

        if ($user->profile){
            $user->profile->update($validated);
        }else{
            $user->profile()->create($validated);
        }

        return redirect('/mypage')->with('message', 'プロフィールを更新しました');
    }
}
