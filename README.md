# Laravel Zoho
A lightweight (for now) wrapper around the Zoho PHP SDK

## Installation

```
composer require purplemountain/laravel-zoho
```

## Setup
1. If you haven't already, you need to create an Oauth2 client within Zoho. This can be done [here](https://api-console.zoho.com/).

2. You want to choose a "Server-based Application" client within Zoho.

3. For the "Authorized Redirect URIs" entry, you need to add `your-app-domain.tld/oauth/zoho`. Don't worry we handle this route for you within your app.

## Env
You will need to add the following details to your `.env` file and paste in the values from Zoho.

```
ZOHO_CLIENT_ID=
ZOHO_CLIENT_SECRET=
ZOHO_USER_EMAIL=
```

## Authorization
You need to authorize your app to access Zoho. You can do this by simply running `php artisan zoho:url` which will generate the url to authorize the app. Follow the prompts and you'll be be all set.

## Refreshing Tokens
Zoho tokens last for aprox 1 hour. To refresh the token using the refresh token provided, you can run `php artisan zoho:refresh`. We reccomend setting this up to be run every 5 minutes in your `App\Console\Kernel.php` file.
