<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function index() {
        $files = File::all();
        $filesArray = array(); // Array of stdClass
        foreach($files as $file) {
            $f = new \stdClass();
            $f->name = $file->name;
            $f->url = $file->url;
            $user = User::find($file->user_id);
            $f->uploader_name = $user->name ?? '';
            if(Auth::check() && !is_null($user)) {
                $f->this_user = Auth::user()->id == $user->id;
            } else {
                $f->this_user = false;
            }
            $f->location = $file->location;
            array_push($filesArray, $f);
        }
        return view('files.index', compact('filesArray'));
    }
    public function upload(Request $request) {
        //TODO relationships
        if(!$request->has('file'))
            return back();
        $fileName = $request->file('file');
        $file = new File; 
        $file->name = $fileName->getClientOriginalName();
        $file->user_id = Auth::check() ? Auth::user()->id : null;
        $file->location = $fileName->storePublicly('files', 'public');
        $file->url = Storage::url($file->location);
        $file->save();
        return redirect('/files');
    }
    public function delete(Request $request) {
        $fileEntry = File::where('location', $request->get('file_name'))->get();
        $fileEntry = $fileEntry->first();
        if(Auth::check()) {
            if(Auth::user()->id == $fileEntry->user_id) {
                Storage::disk('public')->delete($fileEntry->location);
                $fileEntry->delete();
                return redirect('/files');
            }
        }
        return redirect('/files')->withErrors(['error' => "Can't delete other people's files"]);
    }
}
