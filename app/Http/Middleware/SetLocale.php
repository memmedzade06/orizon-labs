<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hər sorğuda saytın dilini sessiyadakı seçimə görə təyin edir.
 * Seçim yoxdursa və ya yanlışdırsa — əsas dil (az).
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', Locales::DEFAULT);

        if (! in_array($locale, Locales::codes(), true)) {
            $locale = Locales::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
