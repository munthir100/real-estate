<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Botble\RealEstate\Models\Broker;
use App\Services\ProfileCompletenessChecker;
use Symfony\Component\HttpFoundation\Response;

class CompletedAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $account = auth('account')->user();

        if (!ProfileCompletenessChecker::check($account)) {
            return redirect()->route('public.account.settings')->with(['error_msg', __('Please complete your profile first')]);
        }
        
        return $next($request);
    }
}
