<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;

class Module
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $access)
    {

        $permission = module($access);

        if (@$permission->status == Status::DISABLE) {
            if ($request->is('api/*')) {
                $message[] = "The module is disabled";
                return apiResponse('disabled','error',$message);
            } else {
                abort(404);
            }
        }
        return $next($request);
    }
}
 