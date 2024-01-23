<?php

namespace App\Http\Middleware;

use App\Models\LogApi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('local')) {
            $log = new LogApi();
            $log->uri = $request->getUri();
            $log->method = $request->getMethod();
            $log->request_body = json_encode($request->all());
            $log->response = $request->getContent();

            $log->save();

        }

        return $response;
    }
}
