<?php

namespace App\Http\Middleware;

use Closure;
use \App\Modules\Account\Models\UserCs;

class AuthCs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_session   = \Session::get('email_auth_cs');
        $user_cs        = UserCs::where('user_email', $user_session)->first();

        if(empty($user_cs)) return redirect('/user/auth_cs');

        return $next($request);
    }
}
