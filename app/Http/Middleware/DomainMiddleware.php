<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Route;
use App\Models\Video;

class DomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        \Log::info('DomainMiddleware');
        $fileName = $request->route('filename');
        $host = $request->getHost(); // returns dev.site.com
        // $hostWithSchema = $request->getSchemeAndHttpHost(); // returns https://dev.site.com
        $getHost = Video::where("file_name", $fileName)->where('allow_hosts', 'like', '%' . $host. '%')->first();
            
        if ($getHost == null) {
            abort(503, 'Host not allowed');
        }else if ($getHost->allow_hosts != '') {
            return $next($request);
        }else{
            abort(503, 'authorization failed');
        }
        
    }
}