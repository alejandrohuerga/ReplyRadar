<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __invoke(Request $request, string $locale)
    {
        if (!in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        $request->session()->put('locale', $locale);
        $request->session()->save();

        app()->setLocale($locale);

        $referer = $request->headers->get('referer');
        $previous = $request->session()->previousUrl();

        if ($referer) {
            return redirect()->to($referer);
        }

        if ($previous) {
            return redirect()->to($previous);
        }

        return redirect('/');
    }
}
