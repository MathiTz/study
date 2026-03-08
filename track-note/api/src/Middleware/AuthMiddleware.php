<?php

declare(strict_types=1);

namespace TrackNote\Middleware;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Session::isAuthenticated()) {
            return Response::unauthorized('Authentication required');
        }

        return $next($request);
    }
}
