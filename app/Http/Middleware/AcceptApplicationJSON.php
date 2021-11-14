<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptApplicationJSON
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->accepts("*/*") || !$request->accepts("application/json")){
            return response()->json([
                'errors' =>
                    ['message' =>'O método de comunicação deve ser application/json']
            ],
               406);
        }
        return $next($request);
    }
}
