# Mercadolibre Laravel Socialite Provider


## License

Mercadolibre Laravel Socialite is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

    composer require kolovious/meli-socialite

### Configuration

After installing the Socialite library, register the `Kolovious\MeliSocialite\MeliSocialiteServiceProvider` in your `config/app.php` configuration file after the Socialite Service Provider:

```php
'providers' => [

    // Other service providers...
    Laravel\Socialite\SocialiteServiceProvider::class,
    
    Kolovious\MeliSocialite\MeliSocialiteServiceProvider::class,
    
],
```
Also the Meli Facade is available for actions with the API

```php
'alias' => [
    // Other alias...
    
    'Meli' => Kolovious\MeliSocialite\Facade\Meli::class,
    
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
     * Redirect the user to the Meli authentication page.
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

// Tokens & Expire time
$token         = $user->token;
$refresh_token = $user->refresh_token;
$expires_at    = $user->expires_at; // UNIX TIMESTAMP

// Methods from Socialite User 
$user->getId();
$user->getNickname();
$user->getName();
$user->getEmail();

// Raw Data
$user->user // Provided by Meli

```

### Using the Facade to make API calls.

```php

// Items from User ( ALL ) 
$offset = 0;
$call= "/users/".$user_id."/items/search";
$result = Meli::get($call, ["offset"=>$offset, 'access_token'=>$access_token]);

// Update Item Description
// Will use the saved access_token in the MeliManager object.
$result = Meli::withToken()->put('/items/'.$item_id.'/description', [ 'text' => $this->description ]); 

or

// Will save this token for future uses. Same as above.
$result = Meli::withToken($token)->put('/items/'.$item_id.'/description', [ 'text' => $this->description ]);

or

// Will use the Access Token in the Auth user, and save it for future uses. You can call withToken() the next time and it will work as espected
$result = Meli::withAuthToken()->put('/items/'.$item_id.'/description', [ 'text' => $this->description ]);

```

