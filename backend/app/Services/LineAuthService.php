<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserIdentity;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Throwable;

class LineAuthService
{
    /**
     * Authenticate or register a user by strictly validating the LINE ID Token on backend.
     * NEVER trusts raw LINE user IDs passed directly from Angular client.
     *
     * @param string $idToken
     * @return User
     * @throws Throwable
     */
    public function authenticateWithIdToken(string $idToken): User
    {
        $channelId = config('services.line.channel_id') ?? env('LINE_CHANNEL_ID');

        // 1. Verify ID Token with LINE official verification endpoint
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/verify', [
            'id_token' => $idToken,
            'client_id' => $channelId,
        ]);

        if (! $response->successful()) {
            throw new InvalidArgumentException('LINE ID Token ไม่ถูกต้อง หรือหมดอายุแล้ว');
        }

        $claims = $response->json();
        $lineUserId = $claims['sub'] ?? null;
        $displayName = $claims['name'] ?? 'LINE User';
        $email = $claims['email'] ?? null;

        if (! $lineUserId) {
            throw new InvalidArgumentException('ไม่พบ Subject ID ใน LINE ID Token');
        }

        return DB::transaction(function () use ($lineUserId, $displayName, $email, $claims) {
            // 2. Find existing identity
            $identity = UserIdentity::where('provider', 'line')
                ->where('provider_subject', $lineUserId)
                ->first();

            if ($identity) {
                // Update login timestamp & sanitized claims
                $identity->update([
                    'last_login_at' => now(),
                    'id_token_claims' => [
                        'iss' => $claims['iss'] ?? null,
                        'aud' => $claims['aud'] ?? null,
                        'exp' => $claims['exp'] ?? null,
                        'name' => $displayName,
                    ],
                ]);

                $user = $identity->user;
                $user->update(['last_active_at' => now()]);

                return $user;
            }

            // 3. Register new user (Note: LINE Login does NOT verify phone number)
            $user = User::create([
                'status' => 'ACTIVE',
                'last_active_at' => now(),
            ]);

            // Create Profile
            UserProfile::create([
                'user_id' => $user->id,
                'display_name' => $displayName,
            ]);

            // Create Identity with Unique Constraint on (provider, provider_subject)
            UserIdentity::create([
                'user_id' => $user->id,
                'provider' => 'line',
                'provider_subject' => $lineUserId,
                'email' => $email,
                'id_token_claims' => [
                    'iss' => $claims['iss'] ?? null,
                    'aud' => $claims['aud'] ?? null,
                    'exp' => $claims['exp'] ?? null,
                    'name' => $displayName,
                ],
                'registered_at' => now(),
                'last_login_at' => now(),
            ]);

            return $user;
        });
    }
}
