<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Http\Middleware\Authenticate;

use Closure;

class JWTAuthenticate extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        try{
//            $user = auth()->userOrFail();
//        } catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
//            return response()->json(['error' =>  $e->getMessage()], 401);
//        }

//        $user = auth()->userOrFail();
        $user = auth()->user();
        if(!$user){
            abort(401, 'No user');
        }



//        $this->authenticate($request);

        return $next($request);
    }
}
