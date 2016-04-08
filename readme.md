# Mercadolibre Laravel Socialite Provider


## License

Mercadolibre Laravel Socialite is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

    composer require kolovious/melisocialite

### Configuration

After installing the Socialite library, register the `Kolovious\MeliSocialiteServiceProvider` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...
    
    Kolovious\MeliSocialiteServiceProvider::class,
    
],
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your `config/services.php` configuration file:
```php
'meli' => [
    'client_id' => 'your-meli-app-id',
    'client_secret' => 'your-meli-secret-code',
    'redirect' => 'http://your-callback-url',
],
```
### Basic Usage

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication. We will access Socialite using the `Socialite` facade:

```php
<?php

namespace App\Http\Controllers;

use Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('meli')->redirect();
    }

    /**
     * Obtain the user information from Meli.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('meli')->user();

        // $user->token;
    }
}
```

The `redirect` method takes care of sending the user to the OAuth provider, while the `user` method will read the incoming request and retrieve the user's information from the provider.



Of course, you will need to define routes to your controller methods:

```php
Route::get('auth/meli', 'Auth\AuthController@redirectToProvider');
Route::get('auth/meli/callback', 'Auth\AuthController@handleProviderCallback');
```

#### Retrieving User Details

Once you have a user instance, you can grab a few more details about the user:

```php
$user = Socialite::driver('meli')->user();

// Access Token
$token = $user->token;

// All Providers
$user->getId();
$user->getNickname();
$user->getName();
$user->getEmail();

// Raw Data
$user->user // Provided by Meli

```
