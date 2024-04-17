<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Authentication extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (SymfonyResponse) $next
     */
    public function handle($request, Closure $next,...$guard): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            if (!$user->getAttribute('status')) {
                return $this->_response();
            }
        } else {
            return $this->_response();
        }
        return $next($request);
    }
    protected function redirectTo($request): ?string
    {
        throw new HttpResponseException($this->_response());
    }

    protected function _response(): \Illuminate\Http\Response|Application|ResponseFactory
    {
        return response('Authorization', SymfonyResponse::HTTP_UNAUTHORIZED);
    }
}
