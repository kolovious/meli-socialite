<?php
/**
 * Created by PhpStorm.
 * User: Leonardo
 * Date: 08/04/2016
 * Time: 12:04 PM
 */

namespace Kolovious\MeliSocialite;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;


/**
 * Class MeliManager
 * @package Kolovious\MeliSocialite
 * This class is cut off of Meli Official SDK, we removed all the auth part, because we only need the API interaction here.
 * When the Meli Official SDK were available via Composer, we will change this
 */
class MeliManager extends \Meli
{

    protected $client_id;
    protected $client_secret;
    protected $access_token;
    protected $refresh_token;

    /**
     * @var boolean Next is with token?
     */
    protected $call_with_token;

    /**
     * Constructor method.
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     * @param string $refresh_token
     */
    public function __construct($client_id, $client_secret, $access_token = null, $refresh_token = null)
    {
        parent::__construct($client_id, $client_secret, $access_token, $refresh_token);
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->call_with_token = false;
    }

    public static function getOAuthUrl()
    {
        return self::$OAUTH_URL;
    }

    public static function getApiUrl($path = null)
    {
        if (!is_null($path)) {
            return self::$API_ROOT_URL . $path;
        }
        return self::$API_ROOT_URL;
    }

    public static function getAuthUrlWithCountry()
    {
        $country = Config::get('services.meli.country') ?? 'CBT';
        return self::$AUTH_URL[$country];
    }

    /**
     * Wrapper for using the Actual user
     * @return MeliManager
     */
    public function withAuthToken()
    {
        $user = Auth::user();
        return $this->withToken($user->access_token)->withRefreshToken($user->refresh_token);
    }

    /**
     * Save refresh token for refreshToken Call
     * @param string|null $refresh_token to be saved
     * @return $this MeliManager
     */
    public function withRefreshToken($token)
    {
        $this->refresh_token = $token;
        return $this;
    }

    /**
     * Next call to the API will be sent with access_token as parameter.
     * @param string|null $token We can sent the token to set it up in the object for future calls.
     * @return $this MeliManager
     */
    public function withToken($token = null)
    {
        if ($token) {
            $this->access_token = $token;
        }

        $this->call_with_token = true;
        return $this;
    }

    /**
     * Check that the access token has been defined and construct an real URL to make request
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array())
    {
        if ($this->access_token && $this->call_with_token) {
            $params['access_token'] = $this->access_token;
            $this->call_with_token = false;
        }
        return parent::make_path($path, $params);
    }
}
