<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user && !$user->is_active) {

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Notification::make()
                    ->title('Account Suspended')
                    ->body('Your account is suspended. Please contact the administrator for further assistance.')
                    ->danger()
                    ->persistent()
                    ->send();

            return redirect('/login');
        }

        return $next($request);
    }
}
