<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BootyRequest;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class BootyRequestController extends Controller
{
	public function index() {
		$bRequests = BootyRequest::all()->sortByDesc('created_at');
		$bRequestsArray = array();
		foreach($bRequests as $bRequest){
			$b = new \stdClass();
			$b->id = $bRequest->id;
			$b->name = $bRequest->request_name;
			$user = User::where('discord_id', $bRequest->discord_id)->get()->first();
			if(!is_null($user)) {
				$b->requester_name = $user->name ?? '';
				if(Auth::check()) {
					$b->this_user = Auth::user()->id == $user->id;
				} else {
					$b->this_user = false;
				}
			} else {
				$b->this_user = false;
			}
			$b->filled = $bRequest->filled == 1 ? true : false; // 0 or 1
			array_push($bRequestsArray, $b);
		}
		return view('request.index', compact('bRequestsArray'));
	}

	public function fill($id) {
		if(Auth::user()->admin ?? false) {
			$bRequest = BootyRequest::find($id);
			$bRequest->filled = $bRequest->filled == 1 ? 0 : 1;
			$bRequest->save();
		}
		return back();
	}
	public function delete($id) {
		$bRequest = BootyRequest::find($id);
		if(Auth::user()->admin ?? false || Auth::user()->id == $bRequest->discord_id) {
			$bRequest->delete();
		}
		return back();
	}
}
