<?php

namespace Kolovious\MeliSocialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class MeliSocialite extends AbstractProvider implements ProviderInterface
{
    protected static $API_ROOT_URL = "https://api.mercadolibre.com";
    protected static $AUTH_URL     = "http://auth.mercadolibre.com/authorization";
    protected static $OAUTH_URL    = "/oauth/token";

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(self::$AUTH_URL, $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {

        $token_url = self::$API_ROOT_URL.self::$OAUTH_URL;

        return $token_url;
    }


    protected function getTokenFields($code)
    {
        return [
            'client_id' => $this->clientId, 'client_secret' => $this->clientSecret,
            'grant_type'=>'authorization_code',
            'code' => $code, 'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(self::$API_ROOT_URL.'/users/me?'.http_build_query(['access_token'=>$token]));
        // We need to make a hook to put the refresh_token in the user model.
        $output = json_decode($response->getBody(), true);
        dd($output);
        return $output;
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'                => $user['id'],
            'nickname'          => $user['nickname'],
            'name'              => trim($user['first_name'].' '.$user['last_name']),
            'email'             => $user['email'],
        ]);
    }
}
