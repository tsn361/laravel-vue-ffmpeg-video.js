<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FraudChecking
{

    public function handle(Request $request, Closure $next)
    {
        \Log::info('FraudChecking');
        \Log::info($_SERVER);
        if(isset($_SERVER['HTTP_RANGE']) || !isset($_SERVER['HTTP_COOKIE'])) {
            \Log::info('HTTP_RANGE');
            abort(503, 'Hey cheating, I caught you');
        }else{
            return $next($request);
        }
        $proxy_headers = array(
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR_IP',
            'VIA',
            'X_FORWARDED_FOR',
            'FORWARDED_FOR',
            'X_FORWARDED',
            'FORWARDED',
            'CLIENT_IP',
            'FORWARDED_FOR_IP',
            'HTTP_PROXY_CONNECTION'
        );
        foreach($proxy_headers as $x){
            if (isset($_SERVER[$x])) {
                // \Log::info('FraudChecking: Hey cheating, I caught you');
                abort(503, 'Hey cheating, I caught you');
            }else{
                return $next($request);
            }
        }
        
        $mobile_agents = '!(tablet|pad|mobile|phone|symbian|android|ipod|ios|blackberry|webos)!i';
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE){
            // echo "Internet Explorer";
            return $next($request);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE){
            // echo "Firefox";
            return $next($request);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE){
            // echo "Google Chrome";
            return $next($request);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE){
            // echo "Safari";
            return $next($request);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE){
            // echo "Opera";
            return $next($request);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') !== FALSE){
            // echo "UCBrowser";
            return $next($request);
        }elseif (preg_match($mobile_agents, $_SERVER['HTTP_USER_AGENT'])) {
            // Mobile!
            return $next($request);
        }else{
           abort(503, 'Hey cheating, I caught you');
        }
    }


}