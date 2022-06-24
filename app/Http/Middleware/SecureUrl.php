<?php

namespace App\Http\Middleware;

use Closure;

class SecureUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    // The URIs that should be excluded from force SSL.
    protected $except = [        
        //'/home/*',
    ];
    
    // The application environment that should be excluded from force SSL.
    protected $exceptEnv = [
        'development',
    ];
    
    
    
    public function handle($request, Closure $next)
    {
        
        if (!$request->secure() && !$this->envPassThrough() && env('APP_SECUREURL', false) == true) {            
            $secureUrl = 'https://' . $request->getHttpHost() . $request->getRequestUri();
            //$secureUrl = 'http://' . $request->getHttpHost() . $request->getRequestUri() ;
            return redirect($secureUrl);
        }
        
        
        return $next($request);
    }
    
    protected function shouldPassThrough($request)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
    
    protected function envPassThrough() 
    {
        $appEnv = \App::environment();

        foreach ($this->exceptEnv as $except) {
            if ($appEnv === $except)
                return true;
        }

        return false;  
    }
}
