<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function update() {
        $discordUser = Socialite::driver('discord')->user();

        $user = User::where('discord_id', $discordUser->id)->first();

        if ($user) {
            $user->update([
                'discord_token' => $discordUser->token,
                'discord_refresh_token' => $discordUser->refreshToken,
            ]);
        } else {
            $user = User::create([
                'name' => $discordUser->name,
                'avatar' => $discordUser->avatar ?? '',
                'discord_id' => $discordUser->id,
                'discord_token' => $discordUser->token,
                'discord_refresh_token' => $discordUser->refreshToken,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}
