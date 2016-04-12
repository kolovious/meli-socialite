<?php
/**
 * Created by PhpStorm.
 * User: Leonardo
 * Date: 12/04/2016
 * Time: 11:03 AM
 */

namespace Kolovious\MeliSocialite;


use Laravel\Socialite\Two\User;

class MeliUser extends User
{
    public $refresh_token;
    
    public $expires_in;

    /**
     * Set the token on the user.
     *
     * @param  string  $refresh_token
     * @return $this
     */
    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * Set the token on the user.
     *
     * @param  string  $expires_in
     * @return $this
     */
    public function setExpiresIn($expires_in)
    {
        $this->expires_in = $expires_in;

        return $this;
    }
}