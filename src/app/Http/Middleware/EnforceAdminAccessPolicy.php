<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Policies\AdminAccessPolicy;
use App\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;

class EnforceAdminAccessPolicy
{
    protected $policy;

    public function __construct(AdminAccessPolicy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check admin guard first (for AdminUser model)
        $user = Auth::guard('admin')->user();
        
        // Fallback to default guard if admin guard has no user
        if (!$user) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return $next($request);
        }

        $currentHost = $request->getHost();
        $adminDomain = config('all.domains.admin');
        $isAdminDomain = $currentHost === $adminDomain;

        // Check if we're on admin domain
        if ($isAdminDomain) {
            // Check if user should be redirected from admin domain
            if ($this->policy->shouldRedirectFromAdmin($user)) {
                $redirectUrl = $this->policy->getRedirectUrl($user);
                if ($redirectUrl) {
                    return redirect($redirectUrl);
                }
            }
        } else {
            // We're on a tenant domain
            $currentTenant = tenant();
            
            if ($currentTenant) {
                // Check if user should be redirected from tenant domain
                if ($this->policy->shouldRedirectFromTenant($user, $currentTenant)) {
                    $redirectUrl = $this->policy->getRedirectUrl($user);
                    if ($redirectUrl) {
                        return redirect($redirectUrl);
                    }
                }
            }
        }

        return $next($request);
    }
}
