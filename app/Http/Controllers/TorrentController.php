<?php

namespace App\Http\Controllers;

use App\Models\Torrent;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use BitTorrent;
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
		return $this->downloadTorrent($request, $torr->name, $torr->url);
	}

	public function uploadTorrent(Request $request ) {
		
	}

	public function downloadTorrent(Request $request, String $torr_name, String $torr_url = "", String $torrPath = "") {
		$mediaType = $request->post('media_type');
		$mediaName = $request->post('media_name');
		$ssh = new SSH2('rosetintedcheeks.com');
		$key = PublicKeyLoader::load(file_get_contents('/srv/http/storage/rtintedc'));

		if (!$ssh->login('oaks', $key)) {
			throw new \Exception('Login failed');
		}
		$toFolder = null;
		$fileLinkFolder = null;
		if($mediaType === "anime") {
			$toFolder = '/home/oaks/watch2/';
			//$fileLinkFolder = '/home/oaks/linked/Anime/';
			$fileLinkFolder = '/home/oaks/watch2/';
		}
		if($mediaType === "TV" || $mediaType === "movie") {
			$toFolder = '/home/oaks/private/watch/start/';
			if($mediaType === "TV") {
				$fileLinkFolder = '/home/oaks/linked/OtherTV/';
			}
			if($mediaType === "movie") {
				$fileLinkFolder = '/home/oaks/linked/OtherMovies/';
			}
		}
		$torrFileName = str_replace(' ', '-', $torr_name) . '.torrent';
		$torrPath = $toFolder . $torrFileName;
		$linkPath = $fileLinkFolder . $mediaName;
		if(is_null($toFolder)) return;
		$localTorrPath = '/home/oaks/watch2/' . $torrFileName;
		if(!empty($torr_url)) {
			$ssh->exec("wget -o " . escapeshellarg($torrPath) . " " . escapeshellarg($torr_url));
			copy($torr_url, $localTorrPath);
		}
		$decodedFile = $this->decoder->decodeFile($localTorrPath);
		$mediaFilePath = '';
		if($mediaType === "TV" || $mediaType === "movie") {
			$mediaFileName = $decodedFile['info']['name'];
			$mediaFilePath = '/home/oaks/private/download/' . $mediaFileName;
			$ssh->exec("ln -sf " . escapeshellarg($mediaFilePath) . " " . escapeshellarg($linkPath . $mediaFileName));
		}
		return redirect(route('torrents.index'));
	}
}
