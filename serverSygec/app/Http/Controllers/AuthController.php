<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Utilisateur;

class AuthController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Home Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders your application's "dashboard" for users that
      | are authenticated. Of course, you are free to change or remove the
      | controller as you wish. It is just here to get your app started!
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['signin']]);
    }

    public function certifier() {
        return array('statuts' =>'success');
    }

    public function user_data(Request $request) {
        $user = JWTAuth::toUser($request->token);
        $user->profil;
        $user->profil->fenetredroits;
        $user->agent;
        
        if(isset($user->agent))
        {
          $user->agent->fonction;
          $user->agent->uniteadmin;
          $user->agent->uniteadmin->typeuniteadmin;
          $user->agent->uniteadmin_patron;
          if(isset($user->agent->uniteadmin_patron))
            $user->agent->uniteadmin_patron->typeuniteadmin;
        }

        return $user;
    }

    public function user_data_by_token($token) {
        $user = JWTAuth::toUser($token);
        $user->agent;

        if(isset($user->agent))
          $user->agent->fonction;
        if(isset($user->agent))
          $user->agent->uniteadmin;
        
        return $user;
    }
    //authentifie un utilisateur
    public function signin(Request $request) {

        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {

          \Log::error($e->getMessage());
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));

    }

  }
