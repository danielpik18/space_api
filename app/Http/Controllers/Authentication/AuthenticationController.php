<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Lcobucci\JWT\Parser;

use DB;

//

use GuzzleHttp\Client;

class AuthenticationController extends ApiController
{
    private $clientID = "2";
    private $clientSecret = "dozW7lwbKb3EvzyPZflq7JwWhGMwiYSqJFg7SOpW";

    //
    private $refreshTokenExpireTime = 1440 * 2; //1440 = 1 day

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $fields = $request->all();
        $fields['password'] = bcrypt($request->password);

        $user = User::create($fields);

        $success['token'] = $user->createToken('Personal Access Token')->accessToken;

        return $this->successResponse($success, 200);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function login(Request $request)
    {
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password])
        ) {
            $user = Auth::user();
            $token = $this->getToken($request->email, $request->password);

            return \response([
                'user' => $user,
                'token' => [
                    'token_type' => $token->token_type,
                    'access_token' => $token->access_token,
                    'expires_in' => $token->expires_in
                ]
            ])->cookie('refresh_token', $token->refresh_token, $this->refreshTokenExpireTime); //  1440 = 1 day
        } else {
            return $this->errorResponse('Wrong credentials', 401);
        }
    }

    public function getToken($username, $password)
    {
        $client = new Client();
        $url = "http://space.test/oauth/token";

        $form_params = [
            "grant_type" => "password",
            "client_id" => $this->clientID,
            "client_secret" => $this->clientSecret,
            "username" => $username,
            "password" => $password
        ];

        $response = $client->post($url, ["form_params" => $form_params]);

        $tokenObject = \json_decode($response->getBody());

        return $tokenObject;
    }

    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();

        $refreshToken = DB::table('oauth_refresh_tokens')
        ->where('access_token_id', $accessToken->id)
        ->update(['revoked' => '1']);

        return $refreshToken;
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!empty($refreshToken)) {
            $client = new Client();
            $url = "http://space.test/oauth/token";

            $form_params = [
            "grant_type" => "refresh_token",
            "refresh_token" => $refreshToken,
            "client_id" => $this->clientID,
            "client_secret" => $this->clientSecret,
            "scope" => ""
        ];

            try {
                $response = $client->post($url, ["form_params" => $form_params]);
                $tokenObject = \json_decode($response->getBody());

                return \response(['token' => $tokenObject])->cookie('refresh_token', $tokenObject->refresh_token, $this->refreshTokenExpireTime);
            } catch (\Exception $ex) {
                return $this->errorResponse($ex->getMessage(), 400);
            }
        } else {
            return $this->errorResponse('No refresh token cookie found', 404);
        }
    }
}
