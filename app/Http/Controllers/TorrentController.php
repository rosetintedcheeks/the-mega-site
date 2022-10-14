<?php

namespace App\Http\Controllers;

use App\Models\Torrent;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use BitTorrent;
use Illuminate\Support\Facades\Storage;
use phpseclib\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;


class TorrentController extends Controller
{
	private $decoder;

	public function __construct(BitTorrent\Decoder $decoder) {
		$this->decoder = $decoder;
	}
	public function doSearch(Request $request) {
		$client = new Client(['base_uri' => 'https://blutopia.xyz']);
		$path = '/api/torrents/filter?';
		$path .= $request->has('name') ? 'name=' . $request->post('name') . '&': '' ;
		$path .= $request->has('imdb') ? 'imdb=' . $request->post('imdb') . '&': '' ;
		$path .= 'api_token=' . getenv('BLUTOPIA_API_KEY');
		$res = $client->request('GET', $path);
		// JSON
		$resJson = json_decode((string) $res->getBody());
		array_walk($resJson->data, function($listItem) {
			$link = $listItem->attributes->download_link;
			$name = $listItem->attributes->name;
			$torr = Torrent::where('url', '=', $link)->get()->first();
			if(is_null($torr)) {
				$torr = Torrent::create([
					'url' => $link,
					'name' => $name,
				]);
			}
			$listItem->attributes->link_id = $torr->id;
		});
		return json_encode($resJson);
	}

	public function downloadLink(Request $request) {
		$linkId = $request->post('link_id');
		$torr = Torrent::find($linkId);
		if(is_null($torr)) return; // Don't know why this would happen xd
		return $this->downloadTorrent($request, $torr->url);
	}

	public function uploadTorrent(Request $request) {
		$path = $request->file('torrentFile')->storePublicly('torrents');
        $path = "https://bot.rosetintedcheeks.com/" . $path;
		return $this->downloadTorrent($request, $path);
	}

	public function downloadTorrent(Request $request, String $torrUrl = "") {
		$mediaType = $request->post('media_type');
		$mediaName = $request->post('media_name');
		$season = $request->post('season');
        if(empty($season)) $season = '1';
		$ssh = new SSH2('rosetintedcheeks.com');
		$key = PublicKeyLoader::load(file_get_contents('/srv/the-mega-site/storage/rtintedc'));

		if (!$ssh->login('oaks', $key)) {
			throw new \Exception('Login failed');
		}
		$toFolder = null;
		$fileLinkFolder = null;
		if($mediaType === "anime") {
			$toFolder = '/home/oaks/watch/';
			$fileLinkFolder = '/home/oaks/linked/Anime';
		}
		if($mediaType === "TV" || $mediaType === "movie") {
			$toFolder = '/home/oaks/private/watch/start/';
			if($mediaType === "TV") {
				$fileLinkFolder = '/home/oaks/linked/OtherTV';
			}
			if($mediaType === "movie") {
				$fileLinkFolder = '/home/oaks/linked/OtherMovies';
			}
		}
		//$torrFileName = str_replace(' ', '-', $torr_name) . '.torrent';
		$linkPath = $fileLinkFolder . '/' . $mediaName;
		if(is_null($toFolder)) return;
		//$localTorrPath = '/home/oaks/watch2/' . $torrFileName;
		if(!empty($torrUrl)) {
			//$ssh->exec("wget -o " . escapeshellarg($torrPath) . " https://bot.rosetintedcheeks.com/" . escapeshellarg($torrUrl));
			$res = $ssh->exec("wget -P " . escapeshellarg($toFolder) . " ". escapeshellarg($torrUrl));
			error_log($res);
		}
        $torrent = file_get_contents($torrUrl);
		$decodedFile = $this->decoder->decode($torrent);
		$mediaFilePath = '';
		if(isset($decodedFile['info']['files'])) {
			$filesArray = $decodedFile['info']['files'];
            $filesArray = array_map(function ($file) use ($decodedFile){
                return implode('/', array_intersect([$decodedFile['info']['name']], $file['path']));
            }, $filesArray);
		} else {
			$filesArray = [$decodedFile['info']['name']];
		}
        error_log(print_r($filesArray, true));
		$res = $ssh->exec("mkdir -p " . escapeshellarg($linkPath));
		error_log($res);
		foreach($filesArray as $file){
			if($mediaType === "anime") {
				$mediaFilePath = '/home/oaks/ADTorrents/' . $file;
			}
			else if($mediaType === "TV" || $mediaType === "movie") {
				$mediaFilePath = '/home/oaks/private/download/' . $file;
			}
			$command = "/home/oaks/linktv/rename.sh" .
                " -l " . escapeshellarg($fileLinkFolder) .
                " -n " . escapeshellarg($mediaName) .
                " -s " . escapeshellarg($season) .
                " " . escapeshellarg($mediaFilePath);
            error_log($command);
			$res = $ssh->exec($command);
            error_log($res);
		}
		return redirect()->route('torrent.index');
	}
}
