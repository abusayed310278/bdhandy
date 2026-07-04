<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCustomerAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isCustomer()) {
            if (!$user->onboarding_profile_done) {
                return redirect()->route('customer.onboarding.profile');
            }

            if ($user->customerAddresses()->doesntExist()) {
                return redirect()->route('customer.onboarding.address');
            }
        }

        return $next($request);
    }
}
