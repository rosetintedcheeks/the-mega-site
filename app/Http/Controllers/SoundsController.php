<?php

namespace App\Http\Controllers;

use App\Models\Sound;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SoundsController extends Controller
{
    public function index() {
        $sounds = Sound::all();
        $soundsArray = array(); // Array of stdClass
        foreach($sounds as $sound) {
            $s = new \stdClass();
            $s->name = $sound->name;
            $s->url = $sound->url;
            $user = User::find($sound->user_id);
            $s->command_name = $sound->command_name;
            $s->uploader_name = $user->name ?? '';
            $s->this_user = false;
            if(Auth::check() && !is_null($user))
                $s->this_user = Auth::user()->id == $user->id;
            if(is_null($user))
                $s->this_user = true;
            $s->location = $sound->location;
            array_push($soundsArray, $s);
        }
        return view('sounds.index', compact('soundsArray'));
    }
    public function editCommandName(Request $request) {
        $soundEntry = Sound::where('location', $request->get('sound_name'))->get();
        $soundEntry = $soundEntry->first();
        if(Auth::check()) {
            if(is_null($soundEntry->user_id) || Auth::user()->id == $soundEntry->user_id || Auth::user()->admin == 1) {
                $soundEntry->command_name = $request->get('command_name');
                $soundEntry->save();
                return redirect('/sounds');
            }
        } else {
            if(is_null($soundEntry->user_id)) {
                $soundEntry->command_name = $request->get('command_name');
                $soundEntry->save();
                return redirect('/sounds');
            }
        }
        return redirect('/sounds')->withErrors(['error' => "Can't change the command of other people's sounds"]);
    }
    public function upload(Request $request) {
        //TODO relationships
        if(!$request->has('file'))
            return back();
        $soundName = $request->file('file');
        $sound = new Sound; 
        $sound->name = $soundName->getClientOriginalName();
        $sound->user_id = Auth::check() ? Auth::user()->id : null;
        $sound->location = $soundName->storePublicly('sounds', 'public');
        $sound->command_name = $request->get('command_name');
        $sound->url = Storage::url($sound->location);
        $sound->save();
        return redirect('/sounds');
    }
    public function delete(Request $request) {
        $soundEntry = Sound::where('location', $request->get('sound_name'))->get();
        $soundEntry = $soundEntry->first();
        if(Auth::check()) {
            if(Auth::user()->id == $soundEntry->user_id || Auth::user()->admin == 1) {
                Storage::disk('public')->delete($soundEntry->location);
                $soundEntry->delete();
                return redirect('/sounds');
            }
        } else {
            if(is_null($soundEntry->user_id)) {
                Storage::disk('public')->delete($soundEntry->location);
                $soundEntry->delete();
                return redirect('/sounds');
            }
        }
        return redirect('/sounds')->withErrors(['error' => "Can't delete other people's sounds"]);
    }
}
