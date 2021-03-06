<?php

namespace Kolovious\MeliSocialite;

use GuzzleHttp\ClientInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\ProviderInterface;

class MeliSocialite extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string Refresh Token
     */
    protected $refresh_token;
    /**
     * @var string Access Token Expires in
     */
    protected $expires_in;
    /**
     * @var string With Access Token, Refresh Token and Expires In.
     */
    protected $parsed_response;

    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = $this->getAccessToken($this->getCode())
        ));

        return $user->setToken($token)->setRefreshToken($this->getRefreshToken())->setExpiresIn($this->getExpiresIn());;
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new MeliUser)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'name' => trim($user['first_name'] . ' ' . $user['last_name']),
            'email' => (isset($user['email'])) ? $user['email'] : \Auth::user()->email,
        ]);
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(MeliManager::getApiUrl() . '/users/me?' . http_build_query(['access_token' => $token]));
        $output = json_decode($response->getBody(), true);
        return $output;
    }

    public function getAccessToken($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => $this->getTokenFields($code),
        ]);

        return $this->parseResponse($response->getBody())->parsedAccessToken();
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return MeliManager::getApiUrl(MeliManager::getOAuthUrl());
    }

    protected function getTokenFields($code)
    {
        return [
            'client_id' => $this->clientId, 'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code, 'redirect_uri' => $this->redirectUrl,
        ];
    }

    protected function parsedAccessToken()
    {
        return $this->parsed_response['access_token'];
    }

    protected function parseResponse($body)
    {
        $this->parsed_response = json_decode($body, true);
        return $this;
    }

    protected function getRefreshToken()
    {
        return $this->parsed_response['refresh_token'];
    }

    protected function getExpiresIn()
    {
        return $this->parsed_response['expires_in'];
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(MeliManager::getAuthUrlWithCountry() . '/authorization', $state);
    }
}
