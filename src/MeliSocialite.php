<?php

namespace Kolovious\MeliSocialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class MeliSocialite extends AbstractProvider implements ProviderInterface
{
    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(Meli::$AUTH_URL, $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {

        $token_url = Meli::$API_ROOT_URL.Meli::$OAUTH_URL;

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
        $response = $this->getHttpClient()->get(Meli::$API_ROOT_URL.'/users/me?'.http_build_query(['access_token'=>$token]));
        $output = json_decode($response->getBody(), true);
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
