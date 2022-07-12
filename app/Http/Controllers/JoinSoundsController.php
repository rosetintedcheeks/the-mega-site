<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\JoinSound;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JoinSoundsController extends Controller
{
    public function index() {
        $files = JoinSound::all();
        $joinSoundsArray = array(); // Array of stdClass
        foreach($files as $file) {
            $f = new \stdClass();
            $user = User::where('discord_id', $file->discord_id)->get()->first();
            if(!is_null($user)) {
                if(Auth::check()) { // Logged in
                    $f->this_user = Auth::user()->id == $user->id;
                    $f->user_name = $user->name;
                } else {
                    $f->this_user = false;
                }
            } else {
                $f->this_user = false;
            }
            $f->id = $file->id;
            $f->name = $file->name;
            $f->location = $file->location;
            $f->url = $file->url;
            $f->checked = $file->checked;
            array_push($joinSoundsArray, $f);
        }
        return view('joinsounds.index', compact('joinSoundsArray'));
    }
    public function upload(Request $request) {
        //TODO relationships
        if(!$request->has('file'))
            return back();
        $fileName = $request->file('file');
        $file = null;
        if(Auth::check()) { 
            $file = new JoinSound(); 
            $file->discord_id = Auth::user()->discord_id;
        } else {
            return back()->withErrors('Need to log in to add sound');
        }
        $file->name = $fileName->getClientOriginalName();
        $file->location = $fileName->storePublicly('files', 'public');
        $file->url = Storage::url($file->location);
        $file->save();
        return redirect('/joinsounds');
    }
    public function delete(Request $request) {
        $fileEntry = JoinSound::where('location', $request->get('sound_name'))->get();
        $fileEntry = $fileEntry->first();
        if(Auth::check()) {
            if(Auth::user()->id == $fileEntry->user_id) {
                Storage::disk('public')->delete($fileEntry->location);
                $fileEntry->delete();
                return redirect('/joinsounds');
            }
        }
        return redirect('/joinsounds')->withErrors(['error' => "Can't delete other people's files"]);
    }
    public function toggleCheck($id) {
        $fileEntry = JoinSound::find($id);
        if(Auth::check()) {
            if(Auth::user()->discord_id == $fileEntry->discord_id) {
                $fileEntry->checked = $fileEntry->checked ? 0 : 1;
                $fileEntry->save();
                return redirect('/joinsounds');
            }
        }
        return redirect('/joinsounds')->withErrors(['error' => "Can't delete other people's files"]);
    }
}
