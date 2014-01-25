<?php

class SiteController extends BaseController {
	public function home()
	{
		if (SessionManager::isAnyUserLoggedin())
			return Redirect::to('/gamecreation');
		else
			return View::make('website.login');
	}

	public function topPlayers(){
		return View::make('website.top');
	}

	public function rules(){
		return View::make('website.rules');
	}

	public function gamecreation(){
		$user = SessionManager::getAuthTwitterUser();

		if(!is_null($user))
		{
			$data = array('username'=> $user->username);
			return View::make('website.creation', $data);
		}else{
			return Redirect::to('/');
		}

	}
}