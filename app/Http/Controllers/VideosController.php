<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class VideosController extends Controller
{
    public $downloadInfo = null;
    public function index() {
        if(!Auth::check()) return abort(403);
        if(!Auth::user()->admin == 1) return abort(403);
        $videos = Storage::disk('remote')->allFiles();
        $videosArray = array(); // Array of stdClass
        foreach($videos as $video) {
            $v = new \stdClass();
            $v->title = basename($video);
            //$v->channel = $video->channel;
            //$v->original_url = $video->original_url;
            $v->url = '/remote/' . $video;
            //$v->location = $video;
            array_push($videosArray, $v);
        }
        return view('videos.index', compact('videosArray'));
    }
    public function upload(Request $request) {
        //TODO relationships
        if(!$request->has('file'))
            return back();
        $filename = $request->file('file');
        $file = new Video; 
        $file->name = $filename->getclientoriginalname();
        $file->user_id = Auth::check() ? Auth::user()->id : null;
        $file->location = $filename->storepublicly('files', 'public');
        $file->url = storage::url($file->location);
        $file->save();
        return redirect('/videos');
    }
    public function download(Request $request) {
        if(!Auth::check()) return back();
        if(!Auth::user()->admin == 1) return back();
        $request->validate([
            'url' => 'required|starts_with:https://www.youtube.com/watch?v='
        ]);
        $yt = new YoutubeDl();
        $this->downloadInfo = new \stdClass();
        $yt->setBinPath('/usr/bin/youtube-dl');
        $yt->onProgress(static function (?string $progressTarget, string $percentage, string $size, string $speed, string $eta, ?string $totalTime) use (&$downloadInfo) {
            $downloadInfo->progressTarget = $progressTarget;
            $downloadInfo->percentage = $percentage;
            $downloadInfo->size = $size;
            if ($speed) {
                $downloadInfo->speed = $speed;
            }
            if ($eta) {
                $downloadInfo->eta = $eta;
            }
            if ($totalTime !== null) {
                $downloadInfo->totalTime = $totalTime;
            }
        });
        $collection = $yt->download(
            Options::create()
                ->downloadPath('/srv/http/storage/app/tmp')
                ->url($request->get('url'))
        );
        foreach($collection as $video) {
            if($video->getError() !== null) {
                logger($video->getError());
                return;
            }
            $videoEntry = new Video;
            $videoEntry->title = $video->getTitle();
            $videoEntry->channel = $video->getChannel();
            $videoEntry->original_url = $video->getUrl();
            $videoEntry->location = Storage::putFile('videos', new File($video->getFile()->getPath()), 'public');
            $videoEntry->url = Storage::url($videoEntry->location);
            $videoEntry->discord_id = Auth::user()->id;
            $videoEntry->save();
        }
    }
    public function getDownloadInfo() {
        if($this->downloadInfo !== null) {
            return view('videos.download_info', $this->downloadInfo);
        } else {
            return view('videos.download_form');
        }
    }
    public function delete(Request $request) {
        $fileEntry = Video::where('location', $request->get('file_name'))->get();
        $fileEntry = $fileEntry->first();
        if(Auth::check()) {
            if(Auth::user()->discord_id == $fileEntry->discord_id) {
                Storage::disk('videos')->delete($fileEntry->location);
                $fileEntry->delete();
                return redirect('/files');
            }
        }
        return redirect('/videos')->withErrors(['error' => "Can't delete other people's files"]);
    }
}
