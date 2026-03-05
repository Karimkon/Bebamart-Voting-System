<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Supported social providers
     */
    protected $providers = ['google', 'facebook', 'apple', 'twitter-oauth-2'];

    /**
     * Redirect to social provider
     */
    public function redirect(Request $request, $provider)
    {
        if (!$this->isProviderAllowed($provider)) {
            return redirect()->route('login')
                ->with('error', 'Social login provider not supported.');
        }

        // Store the page they came from so we can redirect back after login
        if ($request->has('intended') && $request->intended) {
            session()->put('url.intended', $request->intended);
        } elseif ($request->headers->get('referer') && !str_contains($request->headers->get('referer'), '/auth/')) {
            session()->put('url.intended', $request->headers->get('referer'));
        }

        // Apple needs a generated JWT as client_secret
        if ($provider === 'apple') {
            return $this->redirectApple();
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle Apple Sign In redirect with JWT generation
     */
    protected function redirectApple()
    {
        $teamId = config('services.apple.team_id');
        $keyId = config('services.apple.key_id');
        $privateKey = config('services.apple.private_key');

        if (empty($teamId) || empty($keyId) || empty($privateKey)) {
            return redirect()->route('login')
                ->with('error', 'Apple Sign In is not configured yet.');
        }

        // Generate Apple JWT client secret
        $clientSecret = $this->generateAppleClientSecret($teamId, $keyId, $privateKey);
        config(['services.apple.client_secret' => $clientSecret]);

        return Socialite::driver('apple')->redirect();
    }

    /**
     * Generate Apple client_secret JWT
     */
    protected function generateAppleClientSecret(string $teamId, string $keyId, string $privateKey): string
    {
        $now = time();
        $header = base64_url_encode(json_encode(['alg' => 'ES256', 'kid' => $keyId]));
        $payload = base64_url_encode(json_encode([
            'iss' => $teamId,
            'iat' => $now,
            'exp' => $now + 86400 * 180,  // 6 months max
            'aud' => 'https://appleid.apple.com',
            'sub' => config('services.apple.client_id'),
        ]));
        $privateKey = str_replace('\n', "\n", $privateKey);
        $key = openssl_pkey_get_private($privateKey);
        openssl_sign($header . '.' . $payload, $signature, $key, OPENSSL_ALGO_SHA256);
        return $header . '.' . $payload . '.' . base64_url_encode($signature);
    }

    /**
     * Handle social provider callback
     */
    public function callback($provider)
    {
        if (!$this->isProviderAllowed($provider)) {
            return redirect()->route('login')
                ->with('error', 'Social login provider not supported.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to login using ' . ucfirst($provider) . '. Please try again.');
        }

        // Find or create user
        $user = $this->findOrCreateUser($socialUser, $provider);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Unable to create user account. Please contact support.');
        }

        // Check if user is active
        if (!$user->is_active) {
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // Log the user in
        Auth::login($user, true);

        // Update last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Redirect to the page they were trying to visit (e.g., voting page), or competitions by default
        return redirect()->intended(route('competitions.index'))
            ->with('success', 'Welcome, ' . $user->name . '! You can now vote.');
    }

    /**
     * Find or create user from social provider
     */
    protected function findOrCreateUser($socialUser, $provider)
    {
        // Normalize provider name for database
        $providerName = str_replace('-oauth-2', '', $provider);

        // Check if user exists with this provider ID
        $user = User::where('provider', $providerName)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            // Update user info
            $user->update([
                'name' => $socialUser->getName() ?? $user->name,
                'email' => $socialUser->getEmail() ?? $user->email,
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
                'provider_token' => $socialUser->token,
            ]);

            return $user;
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Link this social account to existing user
            $user->update([
                'provider' => $providerName,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ]);

            return $user;
        }

        // Create new user
        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $providerName,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'avatar' => $socialUser->getAvatar(),
            'role' => 'user',
            'is_active' => true,
            'password' => bcrypt(Str::random(32)), // Random password since we use social login
            'email_verified_at' => now(), // Social accounts are pre-verified
        ]);
    }

    /**
     * Check if provider is allowed
     */
    protected function isProviderAllowed($provider): bool
    {
        return in_array($provider, $this->providers);
    }

    /**
     * Base64 URL encode (no padding) for JWT
     */
    protected function base64_url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
