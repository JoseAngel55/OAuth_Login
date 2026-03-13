<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord OAuth 2.0
    | Docs: https://docs.discord.com/developers/topics/oauth2
    |--------------------------------------------------------------------------
    */
    'discord' => [
        'client_id'     => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect'      => env('DISCORD_REDIRECT_URI', env('APP_URL') . '/auth/discord/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitch OAuth 2.0 (Authorization Code Flow)
    | Docs: https://dev.twitch.tv/docs/authentication/getting-tokens-oauth
    |--------------------------------------------------------------------------
    */
    'twitch' => [
        'client_id'     => env('TWITCH_CLIENT_ID'),
        'client_secret' => env('TWITCH_CLIENT_SECRET'),
        'redirect'      => env('TWITCH_REDIRECT_URI', env('APP_URL') . '/auth/twitch/callback'),
    ],

];