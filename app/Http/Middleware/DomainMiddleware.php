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
        $domain = parse_url(config('app.url'), PHP_URL_HOST);
        
        $fileName = $request->route('filename');
        $host = $request->getHost(); // returns dev.site.com
        //$hostWithSchema = $request->getSchemeAndHttpHost(); // returns https://dev.site.com
        $getHost = Video::select('allow_hosts')->where("file_name", $fileName)->first();

        $allowHosts = explode(',',$getHost->allow_hosts);
        if ($getHost->allow_hosts == null || $getHost->allow_hosts == '') {
            return $next($request);
        }else if ($getHost->allow_hosts != null || $getHost->allow_hosts != '') {
            if (in_array($host, $allowHosts)) {
                return $next($request);
            }else if ($domain == $host) {
                return $next($request);
            }else{
                abort(503, 'authorization failed');
            }
        }else{
            abort(503, 'authorization failed');
        }
        
    }
}