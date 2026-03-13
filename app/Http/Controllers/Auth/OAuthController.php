<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    private array $providers = ['discord', 'twitch'];

    // ──────────────────────────────────────────────
    //  PASO 1 — Redirigir al proveedor
    // ──────────────────────────────────────────────
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $url = match ($provider) {
            'discord' => $this->discordAuthUrl($state),
            'twitch'  => $this->twitchAuthUrl($state),
        };

        return redirect($url);
    }

    // ──────────────────────────────────────────────
    //  PASO 2 — Recibir el callback del proveedor
    // ──────────────────────────────────────────────
    public function callback(Request $request, string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        if ($request->state !== session('oauth_state')) {
            return redirect('/login')->withErrors(['oauth' => 'Estado OAuth inválido. Intenta de nuevo.']);
        }
        session()->forget('oauth_state');

        if ($request->has('error')) {
            return redirect('/login')->withErrors([
                'oauth' => 'Acceso denegado: ' . $request->get('error_description', $request->error),
            ]);
        }

        try {
            $tokenData = $this->exchangeCodeForToken($provider, $request->code);

            if (!empty($tokenData['id_token'])) {
                $this->validateIdToken($tokenData['id_token'], $provider);
            }

            $providerUser = $this->fetchProviderUser($provider, $tokenData['access_token']);
            $user = $this->findOrCreateUser($provider, $providerUser, $tokenData);

            Auth::login($user, true);

            return redirect()->intended('/dashboard')
                ->with('success', "Sesion iniciada con {$provider}!");

        } catch (\Exception $e) {
            Log::error("OAuth {$provider} error: " . $e->getMessage());
            return redirect('/login')
                ->withErrors(['oauth' => 'Error al autenticar con ' . ucfirst($provider) . '. Intenta de nuevo.']);
        }
    }

    // ──────────────────────────────────────────────
    //  DESCONECTAR un proveedor
    // ──────────────────────────────────────────────
    public function disconnect(Request $request, string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        $user = $request->user();

        $providerCount = $user->oauthProviders()->count();
        if ($providerCount <= 1 && empty($user->password)) {
            return back()->withErrors(['oauth' => 'No puedes desconectar el unico metodo de inicio de sesion.']);
        }

        $user->oauthProviders()->where('provider', $provider)->delete();

        return back()->with('success', ucfirst($provider) . ' desconectado correctamente.');
    }

    // ══════════════════════════════════════════════
    //  DISCORD — URL de autorizacion
    // ══════════════════════════════════════════════
    private function discordAuthUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'     => config('oauth.discord.client_id'),
            'redirect_uri'  => config('oauth.discord.redirect'),
            'response_type' => 'code',
            'scope'         => 'identify email openid',
            'state'         => $state,
            'prompt'        => 'consent',
        ]);

        return 'https://discord.com/oauth2/authorize?' . $params;
    }

    // ══════════════════════════════════════════════
    //  TWITCH — URL de autorizacion
    // ══════════════════════════════════════════════
    private function twitchAuthUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'     => config('oauth.twitch.client_id'),
            'redirect_uri'  => config('oauth.twitch.redirect'),
            'response_type' => 'code',
            'scope'         => 'openid user:read:email',
            'state'         => $state,
        ]);

        return 'https://id.twitch.tv/oauth2/authorize?' . $params;
    }

    // ══════════════════════════════════════════════
    //  Intercambiar codigo por access_token
    // ══════════════════════════════════════════════
    private function exchangeCodeForToken(string $provider, string $code): array
    {
        $config = config("oauth.{$provider}");

        $payload = [
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code'          => $code,
            'redirect_uri'  => $config['redirect'],
        ];

        $url = match ($provider) {
            'discord' => 'https://discord.com/api/oauth2/token',
            'twitch'  => 'https://id.twitch.tv/oauth2/token',
        };

        $payload['grant_type'] = 'authorization_code';

        $response = Http::asForm()->post($url, $payload);

        if (!$response->successful()) {
            throw new \Exception("Error al obtener token de {$provider}: " . $response->body());
        }

        return $response->json();
    }

    // ══════════════════════════════════════════════
    //  Obtener datos del usuario desde el proveedor
    // ══════════════════════════════════════════════
    private function fetchProviderUser(string $provider, string $accessToken): array
    {
        return match ($provider) {
            'discord' => $this->fetchDiscordUser($accessToken),
            'twitch'  => $this->fetchTwitchUser($accessToken),
        };
    }

    private function fetchDiscordUser(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://discord.com/api/users/@me');

        if (!$response->successful()) {
            throw new \Exception('No se pudo obtener el perfil de Discord.');
        }

        $data = $response->json();

        $avatar = null;
        if (!empty($data['avatar'])) {
            $avatar = "https://cdn.discordapp.com/avatars/{$data['id']}/{$data['avatar']}.png";
        }

        return [
            'id'     => $data['id'],
            'name'   => $data['global_name'] ?? $data['username'],
            'email'  => $data['email'] ?? null,
            'avatar' => $avatar,
            'raw'    => $data,
        ];
    }

    private function fetchTwitchUser(string $accessToken): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Client-Id'     => config('oauth.twitch.client_id'),
        ])->get('https://api.twitch.tv/helix/users');

        if (!$response->successful()) {
            throw new \Exception('No se pudo obtener el perfil de Twitch.');
        }

        $data = $response->json('data.0');

        if (empty($data)) {
            throw new \Exception('Respuesta vacia de Twitch API.');
        }

        return [
            'id'     => $data['id'],
            'name'   => $data['display_name'],
            'email'  => $data['email'] ?? null,
            'avatar' => $data['profile_image_url'] ?? null,
            'raw'    => $data,
        ];
    }

    // ══════════════════════════════════════════════
    //  Encontrar o crear usuario local
    // ══════════════════════════════════════════════
    private function findOrCreateUser(string $provider, array $providerUser, array $tokenData): User
    {
        $oauthRecord = OAuthProvider::where('provider', $provider)
            ->where('provider_id', $providerUser['id'])
            ->first();

        if ($oauthRecord) {
            $oauthRecord->update([
                'access_token'     => $tokenData['access_token'],
                'refresh_token'    => $tokenData['refresh_token'] ?? null,
                'token_expires_at' => isset($tokenData['expires_in'])
                    ? now()->addSeconds($tokenData['expires_in'])
                    : null,
            ]);

            return $oauthRecord->user;
        }

        $user = null;
        if (!empty($providerUser['email'])) {
            $user = User::where('email', $providerUser['email'])->first();
        }

        if (!$user) {
            $user = User::create([
                'name'   => $providerUser['name'],
                'email'  => $providerUser['email'],
                'avatar' => $providerUser['avatar'],
            ]);
        }

        $user->oauthProviders()->create([
            'provider'         => $provider,
            'provider_id'      => $providerUser['id'],
            'access_token'     => $tokenData['access_token'],
            'refresh_token'    => $tokenData['refresh_token'] ?? null,
            'token_expires_at' => isset($tokenData['expires_in'])
                ? now()->addSeconds($tokenData['expires_in'])
                : null,
        ]);

        return $user;
    }

    // ══════════════════════════════════════════════
    //  OpenID Connect — Validar id_token (JWT)
    // ══════════════════════════════════════════════
    /**
     * Valida el id_token JWT devuelto por el proveedor (OIDC).
     * Verifica: estructura JWT, issuer, audience y expiracion.
     *
     * NOTA: Para produccion se recomienda verificar tambien la firma
     * criptografica usando las JWKS publicas del proveedor.
     */
    private function validateIdToken(string $idToken, string $provider): void
    {
        $parts = explode('.', $idToken);

        if (count($parts) !== 3) {
            throw new \Exception('id_token JWT malformado.');
        }

        $payload = json_decode(
            base64_decode(strtr($parts[1], '-_', '+/')),
            true
        );

        if (empty($payload)) {
            throw new \Exception('No se pudo decodificar el payload del id_token.');
        }

        $expectedIssuers = [
            'discord' => 'https://discord.com',
            'twitch'  => 'https://id.twitch.tv/oauth2',
        ];

        if (isset($expectedIssuers[$provider], $payload['iss'])) {
            if ($payload['iss'] !== $expectedIssuers[$provider]) {
                throw new \Exception("id_token issuer invalido para {$provider}.");
            }
        }

        $clientId = config("oauth.{$provider}.client_id");
        $aud = $payload['aud'] ?? null;

        if ($aud) {
            $audiences = is_array($aud) ? $aud : [$aud];
            if (!in_array($clientId, $audiences)) {
                throw new \Exception("id_token audience no coincide con client_id de {$provider}.");
            }
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new \Exception("id_token expirado para {$provider}.");
        }

        Log::info("OIDC id_token valido para {$provider}", [
            'sub' => $payload['sub'] ?? null,
            'iss' => $payload['iss'] ?? null,
            'exp' => $payload['exp'] ?? null,
        ]);
    }
}