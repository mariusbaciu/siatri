<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
use Illuminate\Support\MessageBag;

Route::filter('check', function($route, $request)
{
    //host has one active game take game
    // check if member in pivot contains current user



    if(!SessionManager::isAnyUserLoggedin()){ // || the user is not allowed in this game
        Session::put('redirect', $request->path());


        return Redirect::to('/login')->withErrors(
            new MessageBag(array(
                'info' => 'You must be logged into the Siatri application via Twitter first!'
                )
            ));
    } elseif (!Request::ajax()){
        $tokens = explode("/",$request->path());

        $hostUsername = $tokens[count($tokens) -1];

        $host = User::where("username", "=", $hostUsername)->first();

        $activeGame = $host->games()->where("active", "=", true)->first();

        if (!$activeGame) 
            return Redirect::to('/login');

        $currentUser = SessionManager::getAuthTwitterUser();

        if(!$currentUser)
        {
            return Redirect::to('/login')->withErrors(
            new MessageBag(array(
                'info' => 'You must be logged into the Siatri application via Twitter first!'
                )
            ));
        }

        // if(!$activeGame->users()->where("user_id", "=", $currentUser->id)->first())
        //      return Redirect::to('/gamecreation');
     }
});

Route::group(array('prefix' => 'game', 'before' => 'check'), function()
{
    Route::post('syncWampSession','GameController@syncWampSession');
    Route::get('lobby/{host}','GameController@lobby');
});

Latchet::connection('Connection');
Latchet::topic('/game/{host}', 'GameRoom');


Route::get('/','SiteController@home');
Route::get('/logout', 'TwitterController@logout');


Route::get('/login', 'TwitterController@login');
Route::get('/twitter_auth', 'TwitterController@auth');

Route::post('/sendInvitation', 'TwitterController@sendInvitation');

Route::get('/top', 'SiteController@topPlayers');
Route::get('/rules', 'SiteController@rules');
Route::get('/gamecreation', 'SiteController@gamecreation');

Route::get('/getFriendsList', 'TwitterController@getFriendsList');
Route::get('/getHostDetails', 'TwitterController@getHostDetails');
Route::get('/getGameHistory', 'SiteController@getGameHistory');
Route::get('/getOtherPlayers', 'SiteController@getOtherPlayers');


Route::get('/questions', 'AlchemyController@test');
Route::get('/parse', 'SemanticController@testSparql');

Route::get('/creategame', 'SiteController@createGame');

Route::get('/testq', 'SiteController@testq');

//Test Routes
Route::get('/allusers', function(){

	$users = User::all();

	foreach ($users as $user ) {
        // $user->delete();
		var_dump($user->oauth_uid);
		var_dump($user->username);
	}
});
