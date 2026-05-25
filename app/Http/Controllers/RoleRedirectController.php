<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RoleRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
