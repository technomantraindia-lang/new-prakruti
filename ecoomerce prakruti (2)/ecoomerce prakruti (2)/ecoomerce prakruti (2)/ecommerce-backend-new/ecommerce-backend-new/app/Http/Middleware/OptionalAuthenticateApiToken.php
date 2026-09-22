<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! empty($token)) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken && $accessToken->tokenable) {
                $user = $accessToken->tokenable;
                $user->withAccessToken($accessToken);
                auth()->setUser($user);
                $accessToken->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
