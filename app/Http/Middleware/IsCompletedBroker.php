<?php

namespace App\Http\Middleware;

use App\Services\BrokerCompletenessChecker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCompletedBroker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $account = auth('account')->user(); // Assuming user is authenticated

        if (!BrokerCompletenessChecker::check($account)) {
            return redirect()->route('public.account.settings')->with(['error_msg', __('Please complete your profile first')]);
        }

        return $next($request);
    }
}
